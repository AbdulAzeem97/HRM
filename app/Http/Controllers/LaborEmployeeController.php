<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Services\LaborEmployeeService;
use Carbon\Carbon;

class LaborEmployeeController extends Controller
{
    /**
     * Display labor employee management interface
     */
    public function index()
    {
        $stats = LaborEmployeeService::getLaborEmployeeStats();
        $laborEmployees = LaborEmployeeService::getAllLaborEmployees();
        
        return view('labor.index', compact('stats', 'laborEmployees'));
    }

    /**
     * Show form to mark employees as labor employees
     */
    public function create()
    {
        $employees = Employee::regularEmployees()
            ->with(['department', 'designation'])
            ->get();
            
        $departments = Department::all();
        $designations = Designation::all();
        
        return view('labor.create', compact('employees', 'departments', 'designations'));
    }

    /**
     * Mark selected employees as labor employees
     */
    public function store(Request $request)
    {
        $request->validate([
            'selection_type' => 'required|in:individual,department,designation',
        ]);

        if ($request->selection_type === 'individual') {
            $request->validate([
                'employee_ids' => 'required|array',
                'employee_ids.*' => 'exists:employees,id'
            ]);

            $result = LaborEmployeeService::markEmployeesAsLabor($request->employee_ids);
        } else {
            $request->validate([
                'category_ids' => 'required|array',
            ]);

            $result = LaborEmployeeService::markEmployeesByCategory(
                $request->selection_type,
                $request->category_ids,
                auth()->user()->employee->company_id ?? null
            );
        }

        if (isset($result['error'])) {
            return back()->withErrors(['error' => $result['error']]);
        }

        return redirect()->route('labor.index')
            ->with('success', $result['message']);
    }

    /**
     * Remove labor employee designation
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        $updated = Employee::unmarkAsLaborEmployee($request->employee_ids);
        
        return back()->with('success', "Removed labor designation from {$updated} employees");
    }

    /**
     * Remove shift assignments from labor employees
     */
    public function removeShifts(Request $request)
    {
        $employeeIds = $request->employee_ids;
        $result = LaborEmployeeService::removeShiftAssignments($employeeIds);
        
        return back()->with('success', $result['message']);
    }

    /**
     * Process attendance for labor employees on specific date
     */
    public function processAttendance(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date'
        ]);

        $result = LaborEmployeeService::processLaborAttendanceForDate(
            $request->attendance_date,
            auth()->user()->employee->company_id ?? null
        );

        if (isset($result['error'])) {
            return back()->withErrors(['error' => $result['error']]);
        }

        return back()->with('success', 
            "Processed attendance for {$result['success_count']} labor employees on {$result['processed_date']}"
        )->with('attendance_results', $result['results']);
    }

    /**
     * API endpoint to get labor employee statistics
     */
    public function stats()
    {
        $stats = LaborEmployeeService::getLaborEmployeeStats();
        return response()->json($stats);
    }

    /**
     * API endpoint to get all labor employees
     */
    public function laborEmployees()
    {
        $employees = LaborEmployeeService::getAllLaborEmployees();
        return response()->json($employees);
    }

    /**
     * Bulk process - mark employees as labor by department
     */
    public function bulkMarkByDepartment(Request $request)
    {
        $request->validate([
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:departments,id'
        ]);

        $result = LaborEmployeeService::markEmployeesByCategory(
            'department',
            $request->department_ids
        );

        return response()->json($result);
    }

    /**
     * Bulk process - mark employees as labor by designation
     */
    public function bulkMarkByDesignation(Request $request)
    {
        $request->validate([
            'designation_ids' => 'required|array',
            'designation_ids.*' => 'exists:designations,id'
        ]);

        $result = LaborEmployeeService::markEmployeesByCategory(
            'designation',
            $request->designation_ids
        );

        return response()->json($result);
    }

    /**
     * Show attendance processing page
     */
    public function attendancePage()
    {
        return view('labor.attendance');
    }
}