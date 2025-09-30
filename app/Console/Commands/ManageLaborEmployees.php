<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Services\LaborEmployeeService;

class ManageLaborEmployees extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'labor:manage 
                            {action : Action to perform (list|mark|unmark|stats|remove-shifts|process-attendance)}
                            {--employees= : Comma-separated employee IDs}
                            {--departments= : Comma-separated department IDs}
                            {--designations= : Comma-separated designation IDs}
                            {--date= : Date for attendance processing (YYYY-MM-DD)}
                            {--company= : Company ID}';

    /**
     * The console command description.
     */
    protected $description = 'Manage labor employees - mark, unmark, and process attendance with auto-shift detection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                $this->listLaborEmployees();
                break;
            case 'mark':
                $this->markLaborEmployees();
                break;
            case 'unmark':
                $this->unmarkLaborEmployees();
                break;
            case 'stats':
                $this->showStats();
                break;
            case 'remove-shifts':
                $this->removeShiftAssignments();
                break;
            case 'process-attendance':
                $this->processAttendance();
                break;
            default:
                $this->error('Invalid action. Available actions: list, mark, unmark, stats, remove-shifts, process-attendance');
        }
    }

    private function listLaborEmployees()
    {
        $companyId = $this->option('company');
        $result = LaborEmployeeService::getAllLaborEmployees($companyId);
        
        if ($result['count'] == 0) {
            $this->info('No labor employees found.');
            return;
        }

        $this->info("Found {$result['count']} labor employees:");
        $this->table(
            ['ID', 'Name', 'Staff ID', 'Department', 'Designation', 'Current Shift'],
            $result['employees']->map(function ($emp) {
                return [
                    $emp['id'],
                    $emp['name'],
                    $emp['staff_id'],
                    $emp['department'],
                    $emp['designation'],
                    $emp['current_shift']
                ];
            })
        );
    }

    private function markLaborEmployees()
    {
        if ($employees = $this->option('employees')) {
            $employeeIds = explode(',', $employees);
            $result = LaborEmployeeService::markEmployeesAsLabor($employeeIds);
        } elseif ($departments = $this->option('departments')) {
            $departmentIds = explode(',', $departments);
            $result = LaborEmployeeService::markEmployeesByCategory('department', $departmentIds, $this->option('company'));
        } elseif ($designations = $this->option('designations')) {
            $designationIds = explode(',', $designations);
            $result = LaborEmployeeService::markEmployeesByCategory('designation', $designationIds, $this->option('company'));
        } else {
            $this->error('Please specify --employees, --departments, or --designations');
            return;
        }

        if (isset($result['error'])) {
            $this->error($result['error']);
        } else {
            $this->info($result['message']);
        }
    }

    private function unmarkLaborEmployees()
    {
        if (!$employees = $this->option('employees')) {
            $this->error('Please specify employee IDs with --employees');
            return;
        }

        $employeeIds = explode(',', $employees);
        $updated = Employee::unmarkAsLaborEmployee($employeeIds);
        
        $this->info("Removed labor designation from {$updated} employees");
    }

    private function showStats()
    {
        $companyId = $this->option('company');
        $stats = LaborEmployeeService::getLaborEmployeeStats($companyId);
        
        $this->info('Labor Employee Statistics:');
        $this->table(
            ['Metric', 'Count', 'Percentage'],
            [
                ['Total Employees', $stats['total_employees'], '100%'],
                ['Labor Employees', $stats['labor_employees'], $stats['labor_percentage'] . '%'],
                ['Regular Employees', $stats['regular_employees'], (100 - $stats['labor_percentage']) . '%']
            ]
        );
    }

    private function removeShiftAssignments()
    {
        $employeeIds = null;
        if ($employees = $this->option('employees')) {
            $employeeIds = explode(',', $employees);
        }

        $result = LaborEmployeeService::removeShiftAssignments($employeeIds);
        $this->info($result['message']);
    }

    private function processAttendance()
    {
        $date = $this->option('date') ?: date('Y-m-d');
        $companyId = $this->option('company');
        
        $this->info("Processing attendance for labor employees on {$date}...");
        
        $result = LaborEmployeeService::processLaborAttendanceForDate($date, $companyId);
        
        if (isset($result['error'])) {
            $this->error($result['error']);
            return;
        }

        $this->info("Processing completed:");
        $this->info("- Total labor employees: {$result['total_labor_employees']}");
        $this->info("- Successfully processed: {$result['success_count']}");
        $this->info("- Errors: {$result['error_count']}");

        if (!empty($result['results'])) {
            $tableData = [];
            foreach ($result['results'] as $employeeId => $data) {
                $tableData[] = [
                    $employeeId,
                    $data['employee_name'],
                    $data['status'],
                    $data['shift_detected'] ?? 'N/A',
                    isset($data['working_hours']) ? $data['working_hours'] . 'h' : 'N/A',
                    $data['attendance_status'] ?? ($data['error'] ?? $data['reason'] ?? 'N/A')
                ];
            }

            $this->table(
                ['Employee ID', 'Name', 'Status', 'Shift Detected', 'Working Hours', 'Details'],
                $tableData
            );
        }
    }
}