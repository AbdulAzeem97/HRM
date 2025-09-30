<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendancesCsvImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts
{
    private $successCount = 0;
    private $errorCount = 0;
    private $skippedCount = 0;
    private $errors = [];
    private $debugInfo = [];

    public function collection(Collection $rows)
    {
        $this->debugInfo[] = "Total rows in CSV: " . $rows->count();
        
        foreach ($rows as $rowIndex => $row) {
            try {
                $this->debugInfo[] = "Processing row " . ($rowIndex + 2) . ": " . json_encode($row->toArray());
                
                // Convert row data to strings and trim whitespace
                $staffId = trim(strval($row['staff_id'] ?? ''));
                $attendanceDate = trim(strval($row['attendance_date'] ?? ''));
                $clockIn = trim(strval($row['clock_in'] ?? ''));
                $clockOut = trim(strval($row['clock_out'] ?? ''));

                $this->debugInfo[] = "Parsed data - Staff ID: '$staffId', Date: '$attendanceDate', Clock In: '$clockIn', Clock Out: '$clockOut'";

                // Skip empty rows
                if (empty($staffId) || empty($attendanceDate) || empty($clockIn) || empty($clockOut)) {
                    $this->skippedCount++;
                    $this->debugInfo[] = "Row " . ($rowIndex + 2) . " skipped - empty fields";
                    continue;
                }

                // Find employee
                $employee = Employee::with('officeShift')
                    ->select('id', 'office_shift_id', 'staff_id', 'first_name', 'last_name')
                    ->where('staff_id', $staffId)
                    ->first();

                if (!$employee) {
                    $this->errorCount++;
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": Employee with staff_id '{$staffId}' not found";
                    
                    // Add available staff IDs to debug info
                    $availableStaffIds = Employee::where('is_active', 1)->pluck('staff_id')->take(10)->toArray();
                    $this->debugInfo[] = "Available staff IDs (first 10): " . implode(', ', $availableStaffIds);
                    
                    continue;
                }

                // Parse date - try multiple formats
                $attendanceDateObj = null;
                $dateFormats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];
                
                foreach ($dateFormats as $format) {
                    try {
                        $attendanceDateObj = Carbon::createFromFormat($format, $attendanceDate);
                        break;
                    } catch (Exception $e) {
                        continue;
                    }
                }

                if (!$attendanceDateObj) {
                    $this->errorCount++;
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": Invalid date format '{$attendanceDate}'. Use YYYY-MM-DD format.";
                    continue;
                }

                // Parse times
                try {
                    $clockInTime = new DateTime($clockIn);
                    $clockOutTime = new DateTime($clockOut);
                } catch (Exception $e) {
                    $this->errorCount++;
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": Invalid time format. Use HH:MM format (e.g., 08:00, 17:00)";
                    continue;
                }

                // Check if attendance already exists
                $existingAttendance = Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', $attendanceDateObj->format('Y-m-d'))
                    ->first();

                if ($existingAttendance) {
                    $this->skippedCount++;
                    continue; // Skip duplicate records
                }

                // Get shift information
                $attendanceDayIn = strtolower($attendanceDateObj->format('l')) . '_in';
                $attendanceDayOut = strtolower($attendanceDateObj->format('l')) . '_out';
                
                $shiftIn = null;
                $shiftOut = null;
                
                if ($employee->officeShift) {
                    try {
                        $shiftInTime = $employee->officeShift->$attendanceDayIn;
                        $shiftOutTime = $employee->officeShift->$attendanceDayOut;
                        
                        if ($shiftInTime && $shiftOutTime) {
                            $shiftIn = new DateTime($shiftInTime);
                            $shiftOut = new DateTime($shiftOutTime);
                        }
                    } catch (Exception $e) {
                        // Continue without shift-based calculations if shift times are invalid
                    }
                }

                // Calculate attendance metrics
                $timeLate = '00:00';
                $earlyLeaving = '00:00';
                $overtime = '00:00';
                $totalWork = $clockInTime->diff($clockOutTime)->format('%H:%I');

                // Calculate lateness
                if ($shiftIn && $clockInTime > $shiftIn) {
                    $timeLate = $shiftIn->diff($clockInTime)->format('%H:%I');
                } else if (!env('ENABLE_EARLY_CLOCKIN') && $shiftIn) {
                    $clockInTime = $shiftIn; // Adjust to shift start if early clock-in is disabled
                }

                // Calculate early leaving
                if ($shiftOut && $clockOutTime < $shiftOut) {
                    $earlyLeaving = $shiftOut->diff($clockOutTime)->format('%H:%I');
                }

                // Calculate overtime
                if ($shiftIn && $shiftOut) {
                    $totalWorkDt = new DateTime($totalWork);
                    $dutyTime = new DateTime($shiftIn->diff($shiftOut)->format('%H:%I'));
                    if ($totalWorkDt > $dutyTime) {
                        $overtime = $totalWorkDt->diff($dutyTime)->format('%H:%I');
                    }
                }

                // Create attendance record
                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $attendanceDateObj->format('Y-m-d'),
                    'clock_in' => $clockInTime->format('H:i'),
                    'clock_out' => $clockOutTime->format('H:i'),
                    'clock_in_ip' => request()->ip(),
                    'clock_out_ip' => request()->ip(),
                    'clock_in_out' => 0,
                    'time_late' => $timeLate,
                    'early_leaving' => $earlyLeaving,
                    'overtime' => $overtime,
                    'total_work' => $totalWork,
                    'total_rest' => '00:00',
                    'attendance_status' => 'present'
                ]);

                $this->successCount++;

            } catch (Exception $e) {
                $this->errorCount++;
                $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                Log::error('Attendance import error', [
                    'row' => $rowIndex + 2,
                    'data' => $row,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }


    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getImportSummary(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'skipped_count' => $this->skippedCount,
            'errors' => $this->errors,
            'debug_info' => $this->debugInfo
        ];
    }
}