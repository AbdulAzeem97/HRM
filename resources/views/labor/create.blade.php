@extends('layout.main')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add Labor Employees</h3>
                    <div class="card-tools">
                        <a href="{{ route('labor.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('labor.store') }}" id="laborForm">
                    @csrf
                    <div class="card-body">
                        
                        <!-- Selection Type Tabs -->
                        <ul class="nav nav-tabs" id="selectionTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="individual-tab" data-toggle="tab" href="#individual" role="tab">
                                    <i class="fas fa-user"></i> Individual Selection
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="department-tab" data-toggle="tab" href="#department" role="tab">
                                    <i class="fas fa-building"></i> By Department
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="designation-tab" data-toggle="tab" href="#designation" role="tab">
                                    <i class="fas fa-briefcase"></i> By Designation
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="selectionTabsContent">
                            
                            <!-- Individual Selection Tab -->
                            <div class="tab-pane fade show active" id="individual" role="tabpanel">
                                <input type="hidden" name="selection_type" value="individual">
                                
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Search Employees:</label>
                                            <input type="text" class="form-control" id="employeeSearch" placeholder="Search by name, staff ID, or email...">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllEmployees">
                                            <i class="fas fa-check-square"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllEmployees">
                                            <i class="fas fa-square"></i> Deselect All
                                        </button>
                                        <span class="ml-3 text-muted">
                                            Selected: <span id="selectedCount">0</span> employees
                                        </span>
                                    </div>
                                </div>

                                @if(isset($employees) && $employees->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="employeesTable">
                                        <thead>
                                            <tr>
                                                <th width="5%">Select</th>
                                                <th>Staff ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Department</th>
                                                <th>Designation</th>
                                                <th>Current Shift</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employees as $employee)
                                            <tr class="employee-row">
                                                <td>
                                                    <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" 
                                                           class="employee-selection">
                                                </td>
                                                <td>{{ $employee->staff_id }}</td>
                                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                                <td>{{ $employee->email }}</td>
                                                <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $employee->designation->designation ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $employee->officeShift->shift_name ?? 'No Shift' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No regular employees found. All employees may already be marked as labor employees.
                                </div>
                                @endif
                            </div>

                            <!-- Department Selection Tab -->
                            <div class="tab-pane fade" id="department" role="tabpanel">
                                <input type="hidden" name="selection_type" value="department">
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Select departments to mark ALL employees in those departments as labor employees.
                                </div>

                                <div class="row">
                                    @if(isset($departments) && $departments->count() > 0)
                                        @foreach($departments as $department)
                                        <div class="col-md-4 mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       name="category_ids[]" value="{{ $department->id }}" 
                                                       id="dept_{{ $department->id }}">
                                                <label class="custom-control-label" for="dept_{{ $department->id }}">
                                                    <strong>{{ $department->department_name }}</strong>
                                                    <br><small class="text-muted">{{ $department->employees_count ?? 0 }} employees</small>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                    <div class="col-md-12">
                                        <div class="alert alert-warning">
                                            No departments found.
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Designation Selection Tab -->
                            <div class="tab-pane fade" id="designation" role="tabpanel">
                                <input type="hidden" name="selection_type" value="designation">
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Select job designations to mark ALL employees with those roles as labor employees.
                                </div>

                                <div class="row">
                                    @if(isset($designations) && $designations->count() > 0)
                                        @foreach($designations as $designation)
                                        <div class="col-md-4 mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       name="category_ids[]" value="{{ $designation->id }}" 
                                                       id="desig_{{ $designation->id }}">
                                                <label class="custom-control-label" for="desig_{{ $designation->id }}">
                                                    <strong>{{ $designation->designation }}</strong>
                                                    <br><small class="text-muted">{{ $designation->employees_count ?? 0 }} employees</small>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                    <div class="col-md-12">
                                        <div class="alert alert-warning">
                                            No designations found.
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mark as Labor Employees
                        </button>
                        <a href="{{ route('labor.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        
                        <div class="float-right">
                            <div class="alert alert-warning d-inline-block mb-0 py-2">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Labor employees will use automatic shift detection based on their punch times.
                                </small>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for individual selection
    var table = $('#employeesTable').DataTable({
        responsive: true,
        pageLength: 15,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: 0 }
        ]
    });

    // Custom search functionality
    $('#employeeSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Tab switching functionality
    $('#selectionTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        var selectionType = target.replace('#', '');
        $('input[name="selection_type"]').val(selectionType);
        
        // Clear previous selections when switching tabs
        $('input[type="checkbox"]').prop('checked', false);
        updateSelectedCount();
    });

    // Select all employees
    $('#selectAllEmployees').click(function() {
        $('.employee-selection:visible').prop('checked', true);
        updateSelectedCount();
    });

    // Deselect all employees
    $('#deselectAllEmployees').click(function() {
        $('.employee-selection').prop('checked', false);
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        var count = $('.employee-selection:checked').length;
        $('#selectedCount').text(count);
    }

    // Update count when checkboxes change
    $(document).on('change', '.employee-selection', updateSelectedCount);

    // Form validation
    $('#laborForm').on('submit', function(e) {
        var selectionType = $('input[name="selection_type"]').val();
        var hasSelection = false;

        if (selectionType === 'individual') {
            hasSelection = $('.employee-selection:checked').length > 0;
            if (!hasSelection) {
                alert('Please select at least one employee.');
                e.preventDefault();
            }
        } else {
            hasSelection = $('input[name="category_ids[]"]:checked').length > 0;
            if (!hasSelection) {
                alert('Please select at least one ' + selectionType + '.');
                e.preventDefault();
            }
        }
    });

    // Initialize selected count
    updateSelectedCount();
});
</script>
@endpush

@endsection