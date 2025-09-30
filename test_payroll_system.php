<?php

// Test script to verify payroll calculations for all employees
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Employee;
use Carbon\Carbon;

// Database configuration
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'u902429527_ttphrm',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Test employees
$testEmployees = [61, 62, 63, 64, 65];
$monthYear = 'September-2025';

echo "=== PAYROLL SYSTEM TESTING ===\n";
echo "Month: $monthYear\n";
echo "Testing employees: " . implode(', ', $testEmployees) . "\n\n";

foreach ($testEmployees as $employeeId) {
    try {
        // Get employee
        $employee = Employee::with([
            'salaryBasic',
            'allowances',
            'deductions',
            'commissions',
            'loans',
            'otherPayments',
            'employeeAttendance'
        ])->find($employeeId);

        if (!$employee) {
            echo "Employee $employeeId not found\n\n";
            continue;
        }

        // Get salary basic for September 2025
        $firstDate = '2025-09-01';
        $salaryBasic = null;
        $basicSalary = 0;
        $payslipType = 'Monthly';

        foreach ($employee->salaryBasic as $sb) {
            if ($sb->first_date <= $firstDate) {
                $salaryBasic = $sb;
                $basicSalary = (float) $sb->basic_salary;
                $payslipType = $sb->payslip_type;
            }
        }

        if (!$salaryBasic) {
            echo "No salary basic found for Employee $employeeId\n\n";
            continue;
        }

        // Calculate other components (for September, most will be 0)
        $allowanceAmount = 0;
        $deductionAmount = 0;
        $pensionAmount = (float) ($employee->pension_amount ?? 0);

        // Use the TotalSalaryTrait calculation
        $trait = new class {
            use App\Http\traits\TotalSalaryTrait;
        };

        $totalSalary = $trait->totalSalary(
            $employee,
            $payslipType,
            $basicSalary,
            $allowanceAmount,
            $deductionAmount,
            $pensionAmount,
            1, // total_minutes (not used for monthly)
            $monthYear
        );

        echo "Employee $employeeId: {$employee->first_name}\n";
        echo "Basic Salary: " . number_format($basicSalary, 2) . "\n";
        echo "Calculated Total: " . number_format($totalSalary, 2) . "\n";

        // Get attendance summary for verification
        $attendances = $employee->employeeAttendance()
            ->whereBetween('attendance_date', ['2025-09-01', '2025-09-30'])
            ->get();

        $totalDays = $attendances->count();
        $lateDays = $attendances->where('time_late', '>', 15)->where('time_late', '<=', 120)->count();
        $halfDays = $attendances->where('time_late', '>', 120)->count();
        $totalOvertimeMinutes = $attendances->sum('overtime');

        echo "Attendance: $totalDays days, $lateDays late, $halfDays half-days, " .
             round($totalOvertimeMinutes/60, 2) . "h OT\n";
        echo str_repeat('-', 50) . "\n\n";

    } catch (Exception $e) {
        echo "Error testing Employee $employeeId: " . $e->getMessage() . "\n\n";
    }
}

echo "Testing completed.\n";
?>