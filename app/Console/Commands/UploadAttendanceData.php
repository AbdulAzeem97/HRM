<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use DateTime;

class UploadAttendanceData extends Command
{
    protected $signature = 'attendance:upload-data';
    
    protected $description = 'Upload user provided attendance data to database';

    public function handle()
    {
        $this->info('=== UPLOADING ATTENDANCE DATA ===');
        $this->newLine();

        // Raw attendance data
        $attendanceData = [
            ['35', '4/3/2025', '8:21', '17:16'],
            ['35', '4/4/2025', '8:09', '17:15'],
            ['35', '4/5/2025', '8:13', '17:16'],
            ['35', '4/7/2025', '8:10', '17:16'],
            ['35', '4/8/2025', '8:06', '17:16'],
            ['35', '4/10/2025', '8:18', '17:16'],
            ['35', '4/11/2025', '8:14', '17:16'],
            ['35', '4/12/2025', '8:14', '17:16'],
            ['35', '4/14/2025', '8:16', '17:18'],
            ['35', '4/15/2025', '8:14', '17:15'],
            ['35', '4/16/2025', '8:18', '17:15'],
            ['35', '4/17/2025', '8:15', '17:15'],
            ['35', '4/18/2025', '8:13', '17:15'],
            ['35', '4/19/2025', '8:14', '17:17'],
            ['35', '4/21/2025', '8:15', '17:17'],
            ['35', '4/22/2025', '8:13', '17:16'],
            ['35', '4/23/2025', '8:15', '17:17'],
            ['35', '4/24/2025', '8:12', '17:16'],
            ['35', '4/25/2025', '8:11', '17:15'],
            ['35', '4/26/2025', '8:24', '17:15'],
            ['35', '4/28/2025', '8:10', '17:15'],
            ['35', '4/29/2025', '8:13', '17:16'],
            ['35', '4/30/2025', '8:11', '19:15'],
            ['1', '4/3/2025', '8:10', '17:17'],
            ['1', '4/4/2025', '8:10', '17:16'],
            ['1', '4/5/2025', '8:11', '17:27'],
            ['1', '4/7/2025', '8:14', '17:17'],
            ['1', '4/8/2025', '8:14', '17:28'],
            ['1', '4/9/2025', '8:12', '17:19'],
            ['1', '4/11/2025', '8:08', '17:20'],
            ['1', '4/12/2025', '8:12', '17:20'],
            ['1', '4/14/2025', '8:10', '17:20'],
            ['1', '4/15/2025', '8:09', '17:20'],
            ['1', '4/16/2025', '8:13', '17:22'],
            ['1', '4/17/2025', '8:13', '17:19'],
            ['1', '4/18/2025', '8:13', '17:22'],
            ['1', '4/19/2025', '8:13', '17:19'],
            ['1', '4/21/2025', '8:14', '17:19'],
            ['1', '4/22/2025', '8:09', '17:18'],
            ['1', '4/24/2025', '8:08', '17:21'],
            ['1', '4/25/2025', '8:07', '17:22'],
            ['1', '4/26/2025', '8:09', '17:16'],
            ['1', '4/28/2025', '8:12', '17:18'],
            ['1', '4/29/2025', '8:11', '17:21'],
            ['1', '4/30/2025', '8:10', '17:18'],
            ['2', '4/3/2025', '7:54', '17:26'],
            ['2', '4/4/2025', '8:09', '17:26'],
            ['2', '4/5/2025', '8:04', '17:27'],
            ['2', '4/7/2025', '8:11', '17:28'],
            ['2', '4/8/2025', '8:08', '17:28'],
            ['2', '4/9/2025', '8:10', '17:32'],
            ['2', '4/10/2025', '8:02', '17:20'],
            ['2', '4/11/2025', '8:08', '12:14'],
            ['2', '4/12/2025', '8:12', '17:20'],
            ['2', '4/14/2025', '8:08', '17:31'],
            ['2', '4/15/2025', '8:10', '17:24'],
            ['2', '4/16/2025', '8:08', '17:33'],
            ['2', '4/17/2025', '7:57', '17:20'],
            ['2', '4/18/2025', '8:08', '17:01'],
            ['2', '4/19/2025', '8:09', '17:20'],
            ['2', '4/21/2025', '7:55', '17:19'],
            ['2', '4/22/2025', '8:09', '17:21'],
            ['2', '4/23/2025', '8:09', '17:26'],
            ['2', '4/24/2025', '8:08', '17:22'],
            ['2', '4/25/2025', '8:07', '16:56'],
            ['2', '4/26/2025', '8:12', '17:19'],
            ['2', '4/28/2025', '8:07', '17:27'],
            ['2', '4/29/2025', '8:11', '16:49'],
            ['2', '4/30/2025', '8:04', '17:30']
        ];

        // Check staff IDs and get employees
        $staffIds = array_unique(array_column($attendanceData, 0));
        $this->info('Staff IDs in data: ' . implode(', ', $staffIds));

        $employees = Employee::whereIn('staff_id', $staffIds)->get();
        $this->info('Found ' . $employees->count() . ' matching employees in database');

        if ($employees->count() == 0) {
            $this->error('No employees found with these staff IDs!');
            $this->info('Available staff IDs in database:');
            $availableStaffIds = Employee::where('is_active', 1)->pluck('staff_id')->take(20);
            foreach ($availableStaffIds as $id) {
                $this->line("  • $id");
            }
            return 1;
        }

        // Create employee ID mapping
        $staffToEmployeeId = [];
        foreach ($employees as $employee) {
            $staffToEmployeeId[$employee->staff_id] = $employee->id;
            $this->line("✓ Staff ID {$employee->staff_id} → Employee ID {$employee->id} ({$employee->first_name} {$employee->last_name})");
        }

        $this->newLine();
        $this->info('Processing attendance records...');

        $successCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $bar = $this->output->createProgressBar(count($attendanceData));

        foreach ($attendanceData as $record) {
            [$staffId, $dateStr, $clockIn, $clockOut] = $record;

            try {
                // Check if employee exists
                if (!isset($staffToEmployeeId[$staffId])) {
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                $employeeId = $staffToEmployeeId[$staffId];

                // Convert date format from "4/3/2025" to "2025-04-03"
                $date = DateTime::createFromFormat('n/j/Y', $dateStr);
                if (!$date) {
                    $date = DateTime::createFromFormat('m/d/Y', $dateStr);
                }

                if (!$date) {
                    $this->newLine();
                    $this->error("Invalid date format: $dateStr");
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                $attendanceDate = $date->format('Y-m-d');

                // Format times to HH:MM
                $clockInFormatted = $this->formatTime($clockIn);
                $clockOutFormatted = $this->formatTime($clockOut);

                // Check if attendance already exists
                $existingAttendance = Attendance::where('employee_id', $employeeId)
                    ->where('attendance_date', $attendanceDate)
                    ->first();

                if ($existingAttendance) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                // Get employee with shift info
                $employee = Employee::with('officeShift')->find($employeeId);
                
                // Calculate attendance metrics
                $clockInTime = new DateTime($clockInFormatted);
                $clockOutTime = new DateTime($clockOutFormatted);
                
                $timeLate = '00:00';
                $earlyLeaving = '00:00';
                $overtime = '00:00';
                $totalWork = $clockInTime->diff($clockOutTime)->format('%H:%I');

                // Calculate based on shift if available
                if ($employee->officeShift) {
                    $dayOfWeek = strtolower($date->format('l'));
                    $shiftInField = $dayOfWeek . '_in';
                    $shiftOutField = $dayOfWeek . '_out';
                    
                    $shiftInTime = $employee->officeShift->$shiftInField;
                    $shiftOutTime = $employee->officeShift->$shiftOutField;

                    if ($shiftInTime && $shiftOutTime) {
                        $shiftIn = new DateTime($shiftInTime);
                        $shiftOut = new DateTime($shiftOutTime);

                        // Calculate lateness
                        if ($clockInTime > $shiftIn) {
                            $timeLate = $shiftIn->diff($clockInTime)->format('%H:%I');
                        }

                        // Calculate early leaving
                        if ($clockOutTime < $shiftOut) {
                            $earlyLeaving = $shiftOut->diff($clockOutTime)->format('%H:%I');
                        }

                        // Calculate overtime
                        $totalWorkDt = new DateTime($totalWork);
                        $dutyTime = new DateTime($shiftIn->diff($shiftOut)->format('%H:%I'));
                        if ($totalWorkDt > $dutyTime) {
                            $overtime = $totalWorkDt->diff($dutyTime)->format('%H:%I');
                        }
                    }
                }

                // Create attendance record
                Attendance::create([
                    'employee_id' => $employeeId,
                    'attendance_date' => $attendanceDate,
                    'clock_in' => $clockInTime->format('H:i'),
                    'clock_out' => $clockOutTime->format('H:i'),
                    'clock_in_ip' => '127.0.0.1',
                    'clock_out_ip' => '127.0.0.1',
                    'clock_in_out' => 0,
                    'time_late' => $timeLate,
                    'early_leaving' => $earlyLeaving,
                    'overtime' => $overtime,
                    'total_work' => $totalWork,
                    'total_rest' => '00:00',
                    'attendance_status' => 'present'
                ]);

                $successCount++;

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error processing record: " . $e->getMessage());
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=== UPLOAD COMPLETED ===');
        $this->info("✅ Successfully uploaded: $successCount records");
        $this->info("⚠️  Skipped (duplicates): $skippedCount records");
        $this->info("❌ Errors: $errorCount records");
        $this->newLine();
        
        if ($successCount > 0) {
            $this->info('🎉 Attendance data has been successfully uploaded to the database!');
        }

        return 0;
    }

    private function formatTime($timeStr)
    {
        // Convert "8:21" to "08:21"
        $parts = explode(':', $timeStr);
        if (count($parts) == 2) {
            return sprintf('%02d:%02d', $parts[0], $parts[1]);
        }
        return $timeStr;
    }
}