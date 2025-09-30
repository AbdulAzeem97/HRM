<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class LaborPayrollController extends Controller
{
    /**
     * Display payroll dashboard
     */
    public function index()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        // Get payroll summary
        $payrollSummary = DB::table('payroll_calculations')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->selectRaw('
                COUNT(*) as total_employees,
                SUM(basic_salary) as total_basic,
                SUM(overtime_amount) as total_overtime,
                SUM(gross_salary) as total_gross,
                SUM(absent_deduction + late_deduction + early_leave_deduction) as total_deductions,
                SUM(net_salary) as total_payable,
                AVG(present_days) as avg_attendance
            ')
            ->first();

        // Get recent payroll records
        $payrollRecords = DB::table('payroll_calculations as p')
            ->join('employees as e', 'p.employee_id', '=', 'e.id')
            ->where('p.month', $currentMonth)
            ->where('p.year', $currentYear)
            ->select([
                'p.*',
                'e.first_name',
                'e.last_name',
                'e.staff_id'
            ])
            ->orderBy('p.net_salary', 'desc')
            ->get();

        // Auto-shift detection stats
        $shiftStats = DB::table('payroll_calculations')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->whereNotNull('auto_shift_detected')
            ->selectRaw('auto_shift_detected, COUNT(*) as count')
            ->groupBy('auto_shift_detected')
            ->get();

        return view('labor.payroll.index', [
            'payrollSummary' => $payrollSummary,
            'payrollRecords' => $payrollRecords,
            'shiftStats' => $shiftStats,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear
        ]);
    }

    /**
     * Process bulk payroll calculations
     */
    public function processBulkPayroll(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        $reprocess = $request->boolean('reprocess', false);
        
        try {
            // Delete existing records if reprocessing
            if ($reprocess) {
                DB::table('payroll_calculations')
                    ->where('month', $month)
                    ->where('year', $year)
                    ->delete();
            }

            // Get all labor employees
            $laborEmployees = Employee::where('is_labor_employee', 1)
                ->where('is_active', 1)
                ->get();

            $processedCount = 0;
            $results = [];

            foreach ($laborEmployees as $employee) {
                $result = $this->calculateEmployeePayroll($employee, $month, $year);
                $results[$employee->id] = $result;
                
                if ($result['status'] === 'success') {
                    $processedCount++;
                }
            }

            return redirect()->back()->with([
                'success' => "Successfully processed payroll for {$processedCount} employees",
                'payroll_results' => $results
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing payroll: ' . $e->getMessage());
        }
    }

    /**
     * Calculate payroll for individual employee
     */
    private function calculateEmployeePayroll($employee, $month, $year)
    {
        try {
            // Get attendance records for the month
            $attendances = DB::table('attendances')
                ->where('employee_id', $employee->id)
                ->whereMonth('attendance_date', $month)
                ->whereYear('attendance_date', $year)
                ->get();

            if ($attendances->isEmpty()) {
                return [
                    'status' => 'skipped',
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'reason' => 'No attendance records found'
                ];
            }

            // Calculate working days in month (excluding Sundays)
            $totalWorkingDays = $this->getWorkingDaysInMonth($month, $year);
            $presentDays = $attendances->count();
            $absentDays = $totalWorkingDays - $presentDays;

            // Initialize counters
            $lateDays = 0;
            $earlyLeaveDays = 0;
            $totalOvertimeHours = 0;
            $lateMinutes = 0;
            $earlyLeaveMinutes = 0;
            $shiftDetections = [];

            // Process each attendance record
            foreach ($attendances as $attendance) {
                $workingHours = $this->calculateWorkingHours($attendance->clock_in, $attendance->clock_out);
                $detectedShift = $this->detectBestShift($attendance->clock_in, $attendance->clock_out, $workingHours);
                
                if ($detectedShift) {
                    $shiftDetections[] = $detectedShift['shift_name'];
                }

                // Standard expected shift duration
                $standardHours = 9.25; // Default for most shifts
                
                // Late arrival detection (more than 15 minutes)
                if ($this->isLateArrival($attendance->clock_in, '11:00', 15)) {
                    $lateDays++;
                    $lateMinutes += $this->calculateLateMinutes($attendance->clock_in, '11:00');
                }

                // Early leave detection
                if ($workingHours < $standardHours) {
                    $shortage = ($standardHours - $workingHours) * 60;
                    if ($shortage > 30) { // More than 30 minutes short
                        $earlyLeaveDays++;
                        $earlyLeaveMinutes += $shortage;
                    }
                }

                // Overtime calculation (max 2 hours per day for salary)
                if ($workingHours > $standardHours) {
                    $overtime = $workingHours - $standardHours;
                    $totalOvertimeHours += min($overtime, 2);
                }
            }

            // Most frequently detected shift
            $autoDetectedShift = null;
            if (!empty($shiftDetections)) {
                $shiftCounts = array_count_values($shiftDetections);
                $autoDetectedShift = array_keys($shiftCounts, max($shiftCounts))[0];
            }

            // Calculate salary components
            $basicSalary = $employee->basic_salary;
            $perDaySalary = $basicSalary / 26;
            $perMinuteSalary = $basicSalary / (26 * 8 * 60);
            $hourlyRate = $basicSalary / (26 * 8);

            // Calculate deductions
            $absentDeduction = $absentDays * $perDaySalary;
            $lateDeduction = max(0, $lateMinutes - (15 * $lateDays)) * $perMinuteSalary; // Grace period
            $earlyLeaveDeduction = $earlyLeaveMinutes * $perMinuteSalary;
            
            // Calculate overtime amount (2x hourly rate)
            $overtimeAmount = $totalOvertimeHours * $hourlyRate * 2;

            // Final calculations
            $grossSalary = $basicSalary + $overtimeAmount;
            $totalDeductions = $absentDeduction + $lateDeduction + $earlyLeaveDeduction;
            $netSalary = $grossSalary - $totalDeductions;

            // Insert payroll record
            DB::table('payroll_calculations')->insert([
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
                'basic_salary' => $basicSalary,
                'total_working_days' => $totalWorkingDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'early_leave_days' => $earlyLeaveDays,
                'overtime_hours' => round($totalOvertimeHours, 2),
                'overtime_amount' => round($overtimeAmount, 2),
                'late_deduction' => round($lateDeduction, 2),
                'early_leave_deduction' => round($earlyLeaveDeduction, 2),
                'absent_deduction' => round($absentDeduction, 2),
                'gross_salary' => round($grossSalary, 2),
                'net_salary' => round($netSalary, 2),
                'auto_shift_detected' => $autoDetectedShift,
                'created_at' => now()
            ]);

            return [
                'status' => 'success',
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'overtime_hours' => round($totalOvertimeHours, 2),
                'net_salary' => round($netSalary, 2),
                'auto_shift_detected' => $autoDetectedShift ?? 'Auto-Detect'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate working hours between clock in and clock out
     */
    private function calculateWorkingHours($clockIn, $clockOut)
    {
        $in = Carbon::parse($clockIn);
        $out = Carbon::parse($clockOut);
        
        // Handle overnight shifts
        if ($out->lt($in)) {
            $out->addDay();
        }
        
        return $in->diffInMinutes($out) / 60;
    }

    /**
     * Detect best shift based on punch times (simplified version)
     */
    private function detectBestShift($clockIn, $clockOut, $workingHours)
    {
        $shifts = [
            'Shift-A' => ['start' => '07:00', 'end' => '15:45', 'duration' => 8.75],
            'General' => ['start' => '08:00', 'end' => '17:15', 'duration' => 9.25],
            '11:00-20:15' => ['start' => '11:00', 'end' => '20:15', 'duration' => 9.25],
            'Shift-B' => ['start' => '15:00', 'end' => '23:45', 'duration' => 8.75],
            '19:00-04:15' => ['start' => '19:00', 'end' => '04:15', 'duration' => 9.25],
            'Shift-C' => ['start' => '23:00', 'end' => '07:15', 'duration' => 8.25]
        ];

        $bestShift = null;
        $bestScore = -1;
        
        $punchIn = Carbon::parse($clockIn);
        
        foreach ($shifts as $name => $shift) {
            $shiftStart = Carbon::parse($shift['start']);
            $timeDiff = abs($punchIn->diffInMinutes($shiftStart));
            
            $score = 0;
            
            // Time alignment score (within 3 hours)
            if ($timeDiff <= 180) {
                $score += (180 - $timeDiff) / 180 * 50;
            }
            
            // Working hours coverage
            $score += min($workingHours / 9, 1) * 30;
            
            // Duration match
            $durationDiff = abs($workingHours - $shift['duration']) * 60;
            if ($durationDiff <= 240) {
                $score += (240 - $durationDiff) / 240 * 20;
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestShift = ['shift_name' => $name, 'score' => $score];
            }
        }
        
        return $bestShift;
    }

    /**
     * Check if arrival is late
     */
    private function isLateArrival($actualTime, $expectedTime, $graceMinutes)
    {
        $actual = Carbon::parse($actualTime);
        $expected = Carbon::parse($expectedTime);
        
        return $actual->diffInMinutes($expected) > $graceMinutes;
    }

    /**
     * Calculate late minutes
     */
    private function calculateLateMinutes($actualTime, $expectedTime)
    {
        $actual = Carbon::parse($actualTime);
        $expected = Carbon::parse($expectedTime);
        
        return max(0, $actual->diffInMinutes($expected));
    }

    /**
     * Get working days in month (excluding Sundays)
     */
    private function getWorkingDaysInMonth($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $workingDays = 0;
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
        }
        
        return $workingDays;
    }

    /**
     * Generate bulk payment report
     */
    public function generatePaymentReport(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $payrollData = DB::table('payroll_calculations as p')
            ->join('employees as e', 'p.employee_id', '=', 'e.id')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->select([
                'e.staff_id',
                'e.first_name',
                'e.last_name',
                'p.*'
            ])
            ->orderBy('e.staff_id')
            ->get();

        if ($request->input('format') === 'csv') {
            return $this->exportCSV($payrollData, $month, $year);
        }

        return view('labor.payroll.report', [
            'payrollData' => $payrollData,
            'month' => $month,
            'year' => $year
        ]);
    }

    /**
     * Export payroll data as CSV
     */
    private function exportCSV($data, $month, $year)
    {
        $monthName = Carbon::create($year, $month, 1)->format('F');
        $filename = "Labor_Payroll_{$monthName}_{$year}.csv";
        
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Staff ID', 'Employee Name', 'Basic Salary', 'Present Days', 
                'Absent Days', 'Late Days', 'Early Leave Days', 'Overtime Hours',
                'Overtime Amount', 'Late Deduction', 'Early Leave Deduction', 
                'Absent Deduction', 'Gross Salary', 'Net Salary', 'Auto Shift'
            ]);
            
            // CSV Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->staff_id,
                    $row->first_name . ' ' . $row->last_name,
                    $row->basic_salary,
                    $row->present_days,
                    $row->absent_days,
                    $row->late_days,
                    $row->early_leave_days,
                    $row->overtime_hours,
                    $row->overtime_amount,
                    $row->late_deduction,
                    $row->early_leave_deduction,
                    $row->absent_deduction,
                    $row->gross_salary,
                    $row->net_salary,
                    $row->auto_shift_detected ?? 'Auto-Detect'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process bulk payments
     */
    public function processBulkPayments(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        $selectedEmployees = $request->input('employee_ids', []);
        
        try {
            // Create payment batch record
            $batchId = 'PAY_' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . time();
            
            $processedPayments = [];
            $totalAmount = 0;
            
            // Get payroll records for selected employees
            $payrollRecords = DB::table('payroll_calculations as p')
                ->join('employees as e', 'p.employee_id', '=', 'e.id')
                ->where('p.month', $month)
                ->where('p.year', $year)
                ->whereIn('p.employee_id', $selectedEmployees)
                ->select('p.*', 'e.first_name', 'e.last_name', 'e.staff_id')
                ->get();

            foreach ($payrollRecords as $record) {
                $processedPayments[] = [
                    'batch_id' => $batchId,
                    'employee_id' => $record->employee_id,
                    'employee_name' => $record->first_name . ' ' . $record->last_name,
                    'staff_id' => $record->staff_id,
                    'net_salary' => $record->net_salary,
                    'payment_status' => 'Processed',
                    'processed_at' => now()
                ];
                
                $totalAmount += $record->net_salary;
            }

            return redirect()->back()->with([
                'success' => "Bulk payment processed successfully for " . count($processedPayments) . " employees",
                'payment_batch' => $batchId,
                'total_amount' => $totalAmount,
                'processed_payments' => $processedPayments
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing bulk payments: ' . $e->getMessage());
        }
    }
}