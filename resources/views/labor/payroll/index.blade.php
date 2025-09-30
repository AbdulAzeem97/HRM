@extends('layout.main')
@section('content')

<section>
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title">Labor Payroll System</h3>
                        <p>Automated salary calculations with auto-shift detection</p>
                    </div>
                    <div>
                        <a href="{{ route('labor.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Labor Management
                        </a>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-primary">{{ $payrollSummary->total_employees ?? 0 }}</h4>
                                <p>Total Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-success">₹{{ number_format($payrollSummary->total_payable ?? 0) }}</h4>
                                <p>Total Payable</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-danger">₹{{ number_format($payrollSummary->total_deductions ?? 0) }}</h4>
                                <p>Deductions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-warning">{{ number_format($payrollSummary->avg_attendance ?? 0, 1) }}</h4>
                                <p>Avg Attendance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fa fa-calculator text-primary"></i> Process Payroll</h5>
                                <p>Calculate salaries for all labor employees with auto-shift detection</p>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#processPayrollModal">
                                    Process Monthly Payroll
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fa fa-file-export text-success"></i> Generate Reports</h5>
                                <p>Export detailed payroll reports and salary summaries</p>
                                <button class="btn btn-success" data-toggle="modal" data-target="#reportModal">
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fa fa-credit-card text-warning"></i> Bulk Payments</h5>
                                <p>Process bulk salary payments for selected employees</p>
                                <button class="btn btn-warning" data-toggle="modal" data-target="#bulkPaymentModal">
                                    Process Payments
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-Shift Detection Stats -->
        @if($shiftStats->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Auto-Shift Detection Results</h3>
                <p>Automatic shift assignments based on punch times</p>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($shiftStats as $stat)
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <span class="badge badge-info">{{ $stat->auto_shift_detected }}</span>
                            <div class="mt-2">
                                <strong>{{ $stat->count }} employees</strong>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Payroll Records Table -->
        @if($payrollRecords->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }} Payroll Records</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="payrollTable">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Employee</th>
                                <th>Present Days</th>
                                <th>Overtime</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Auto Shift</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrollRecords as $record)
                            <tr>
                                <td>
                                    <input type="checkbox" name="employee_ids[]" value="{{ $record->employee_id }}" class="form-check-input employee-checkbox">
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $record->first_name }} {{ $record->last_name }}</strong>
                                        <br><small class="text-muted">ID: {{ $record->staff_id }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $record->present_days }}/{{ $record->total_working_days }}</span>
                                    @if($record->absent_days > 0)
                                        <br><small class="text-danger">{{ $record->absent_days }} absent</small>
                                    @endif
                                </td>
                                <td>
                                    @if($record->overtime_hours > 0)
                                        <div>{{ $record->overtime_hours }}h</div>
                                        <small class="text-success">+₹{{ number_format($record->overtime_amount) }}</small>
                                    @else
                                        <span class="text-muted">No OT</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalDed = $record->absent_deduction + $record->late_deduction + $record->early_leave_deduction;
                                    @endphp
                                    @if($totalDed > 0)
                                        <span class="text-danger">-₹{{ number_format($totalDed) }}</span>
                                        <br>
                                        @if($record->absent_deduction > 0)<small>Absent: ₹{{ number_format($record->absent_deduction) }}</small><br>@endif
                                        @if($record->late_deduction > 0)<small>Late: ₹{{ number_format($record->late_deduction) }}</small><br>@endif
                                        @if($record->early_leave_deduction > 0)<small>Early: ₹{{ number_format($record->early_leave_deduction) }}</small>@endif
                                    @else
                                        <span class="text-muted">No deductions</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success">₹{{ number_format($record->net_salary) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $record->auto_shift_detected ?? 'Auto-Detect' }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewPayslip({{ $record->employee_id }})">
                                        <i class="fa fa-file-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center">
                <i class="fa fa-calculator fa-4x text-muted mb-3"></i>
                <h4>No Payroll Records Found</h4>
                <p class="text-muted">Process payroll for {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }} to see results here.</p>
                <button class="btn btn-primary" data-toggle="modal" data-target="#processPayrollModal">
                    <i class="fa fa-plus"></i> Process Payroll Now
                </button>
            </div>
        </div>
        @endif

    </div>
</section>

<!-- Process Payroll Modal -->
<div class="modal fade" id="processPayrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('labor.payroll.process') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-calculator"></i> Process Monthly Payroll
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month:</label>
                                <select name="month" class="form-control" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year:</label>
                                <select name="year" class="form-control" required>
                                    @for($y = 2024; $y <= 2026; $y++)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="reprocess" name="reprocess">
                        <label class="form-check-label" for="reprocess">
                            Reprocess existing calculations (will overwrite current data)
                        </label>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle"></i>
                        This will calculate salaries for all labor employees using auto-shift detection, overtime, and deduction policies.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Payment Modal -->
<div class="modal fade" id="bulkPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('labor.payroll.bulk-payment') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-credit-card"></i> Process Bulk Payments
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Select employees from the table above and click "Process Selected Payments" to generate bulk salary payments.</p>
                    <div id="selectedEmployeesInfo">
                        <p class="text-muted">No employees selected</p>
                    </div>
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <input type="hidden" name="employee_ids" id="selectedEmployeeIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="processPaymentsBtn" disabled>
                        Process Selected Payments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#payrollTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[6, 'desc']] // Sort by net salary
    });

    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.employee-checkbox').prop('checked', this.checked);
        updateSelectedEmployees();
    });

    // Handle individual checkboxes
    $(document).on('change', '.employee-checkbox', function() {
        updateSelectedEmployees();
        
        // Update select all checkbox
        var totalCheckboxes = $('.employee-checkbox').length;
        var checkedCheckboxes = $('.employee-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    function updateSelectedEmployees() {
        var selected = $('.employee-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        var count = selected.length;
        var infoText = count > 0 ? `${count} employee(s) selected for payment` : 'No employees selected';
        
        $('#selectedEmployeesInfo').html(`<p class="text-info">${infoText}</p>`);
        $('#selectedEmployeeIds').val(selected.join(','));
        $('#processPaymentsBtn').prop('disabled', count === 0);
    }
});

function viewPayslip(employeeId) {
    // Implementation for viewing individual payslip
    alert('Payslip view for employee ID: ' + employeeId);
}
</script>
@endpush

@endsection