@extends('layout.main')
@section('content')

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Labor Management</h3>
                <p>Manage labor employees and their attendance</p>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <a href="{{ route('labor.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Labor Employees
                        </a>
                    </div>
                </div>
                
                <!-- Statistics -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4>{{ $stats['total_employees'] ?? 0 }}</h4>
                                <p>Total Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4>{{ $stats['labor_employees'] ?? 0 }}</h4>
                                <p>Labor Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4>{{ $stats['regular_employees'] ?? 0 }}</h4>
                                <p>Regular Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4>{{ round($stats['labor_percentage'] ?? 0) }}%</h4>
                                <p>Labor Ratio</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-warning" id="removeShiftsBtn">
                            <i class="fa fa-times-circle"></i> Remove Shifts
                        </button>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#attendanceModal">
                            <i class="fa fa-calendar-check"></i> Process Attendance
                        </button>
                        <button type="button" class="btn btn-danger" id="removeLaborsBtn">
                            <i class="fa fa-user-minus"></i> Remove Labor Status
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Labor Employees Table -->
        @if(isset($laborEmployees) && $laborEmployees['count'] > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Labor Employees ({{ $laborEmployees['count'] }})</h3>
                <button class="btn btn-sm btn-secondary" id="selectAll">Select All</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="laborEmployeesTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllCheckbox"></th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Current Shift</th>
                                <th>Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laborEmployees['employees'] as $employee)
                            <tr>
                                <td>
                                    <input type="checkbox" class="employee-checkbox" value="{{ $employee['id'] }}">
                                </td>
                                <td>
                                    <strong>{{ $employee['name'] }}</strong><br>
                                    <small>ID: {{ $employee['id'] }} | {{ $employee['staff_id'] }}</small>
                                </td>
                                <td>{{ $employee['department'] }}</td>
                                <td>{{ $employee['designation'] }}</td>
                                <td>
                                    <span class="badge badge-{{ $employee['current_shift'] == 'No shift assigned' ? 'warning' : 'info' }}">
                                        {{ $employee['current_shift'] }}
                                    </span>
                                </td>
                                <td>₹{{ number_format($employee['basic_salary']) }}</td>
                                <td>
                                    <span class="badge badge-success">Labor</span>
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
                <h4>No Labor Employees Found</h4>
                <p>Start by marking employees as labor workers to enable automatic shift detection</p>
                <a href="{{ route('labor.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add Labor Employees
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Attendance Processing Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('labor.process-attendance') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Process Attendance with Auto-Shift Detection</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="attendance_date">Select Date:</label>
                                <input type="date" class="form-control" name="attendance_date" 
                                       id="attendance_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reprocess" name="reprocess">
                                    <label class="custom-control-label" for="reprocess">
                                        Reprocess existing attendance
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6>Processing Information</h6>
                                <ul>
                                    <li>Automatic shift detection</li>
                                    <li>Smart overtime calculation</li>
                                    <li>Early leave policies applied</li>
                                    <li>Salary deductions calculated</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // DataTable initialization
    $('#laborEmployeesTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']]
    });

    // Select all checkbox functionality
    $('#selectAllCheckbox').change(function() {
        $('.employee-checkbox').prop('checked', this.checked);
    });

    // Remove shifts for selected employees
    $('#removeShiftsBtn').click(function() {
        var selectedEmployees = $('.employee-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedEmployees.length === 0) {
            alert('Please select at least one employee.');
            return;
        }

        if (confirm('Remove shift assignments for ' + selectedEmployees.length + ' employees?')) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.post('{{ route("labor.remove-shifts") }}', {
                employee_ids: selectedEmployees
            }).done(function(response) {
                location.reload();
            }).fail(function() {
                alert('Error occurred while removing shift assignments.');
            });
        }
    });

    // Remove labor status for selected employees
    $('#removeLaborsBtn').click(function() {
        var selectedEmployees = $('.employee-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedEmployees.length === 0) {
            alert('Please select at least one employee.');
            return;
        }

        if (confirm('Remove labor status for ' + selectedEmployees.length + ' employees?')) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("labor.destroy") }}',
                type: 'DELETE',
                data: {
                    employee_ids: selectedEmployees
                }
            }).done(function(response) {
                location.reload();
            }).fail(function() {
                alert('Error occurred while removing labor status.');
            });
        }
    });
});
</script>
@endpush

@endsection