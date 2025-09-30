@extends('layout.main')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Labor Attendance Processing with Auto-Shift Detection</h3>
                    <div class="card-tools">
                        <a href="{{ route('labor.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Labor Management
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- Processing Form -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-play-circle"></i> Process Attendance</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('labor.process-attendance') }}" id="processForm">
                                        @csrf
                                        <div class="form-group">
                                            <label for="attendance_date">Select Date:</label>
                                            <input type="date" class="form-control" name="attendance_date" 
                                                   id="attendance_date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Processing Options:</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="reprocess" name="reprocess" value="1">
                                                <label class="custom-control-label" for="reprocess">
                                                    Reprocess existing attendance (override previous calculations)
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-cogs"></i> Process Labor Attendance
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> How It Works</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            <strong>Auto-Shift Detection:</strong> System identifies best shift based on punch times
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            <strong>Flexible Hours:</strong> Works with any working duration (not just 9+ hours)
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            <strong>Smart Policies:</strong> Automatic overtime, early leave, half-day calculation
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            <strong>Accurate Deductions:</strong> Precise salary deductions for early leave
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Display -->
                    @if(session('attendance_results'))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Processing Results</h5>
                                </div>
                                <div class="card-body">
                                    
                                    <!-- Summary Stats -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <div class="info-box bg-primary">
                                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Processed</span>
                                                    <span class="info-box-number">{{ count(session('attendance_results')) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Success</span>
                                                    <span class="info-box-number" id="successCount">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Skipped</span>
                                                    <span class="info-box-number" id="skippedCount">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Errors</span>
                                                    <span class="info-box-number" id="errorCount">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detailed Results Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="resultsTable">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Status</th>
                                                    <th>Shift Detected</th>
                                                    <th>Working Hours</th>
                                                    <th>Attendance Status</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(session('attendance_results') as $employeeId => $result)
                                                <tr class="result-row" data-status="{{ $result['status'] }}">
                                                    <td>{{ $result['employee_name'] }}</td>
                                                    <td>
                                                        @if($result['status'] === 'success')
                                                            <span class="badge badge-success">Success</span>
                                                        @elseif($result['status'] === 'error')
                                                            <span class="badge badge-danger">Error</span>
                                                        @else
                                                            <span class="badge badge-warning">Skipped</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($result['status'] === 'success')
                                                            <span class="badge badge-info">{{ $result['shift_detected'] }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($result['working_hours']))
                                                            {{ $result['working_hours'] }}h
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($result['attendance_status']))
                                                            @if($result['attendance_status'] === 'Present')
                                                                <span class="badge badge-success">Present</span>
                                                            @elseif($result['attendance_status'] === 'Half Day')
                                                                <span class="badge badge-warning">Half Day</span>
                                                            @elseif($result['attendance_status'] === 'Early Leave')
                                                                <span class="badge badge-info">Early Leave</span>
                                                            @else
                                                                {{ $result['attendance_status'] }}
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($result['status'] === 'error')
                                                            <small class="text-danger">{{ $result['error'] ?? 'Unknown error' }}</small>
                                                        @elseif($result['status'] === 'skipped')
                                                            <small class="text-muted">{{ $result['reason'] ?? 'Skipped' }}</small>
                                                        @else
                                                            <small class="text-success">Processed successfully</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Export Results -->
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-success" id="exportResults">
                                            <i class="fas fa-download"></i> Export Results
                                        </button>
                                        <button type="button" class="btn btn-outline-info" id="emailResults">
                                            <i class="fas fa-envelope"></i> Email Summary
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for results
    if ($('#resultsTable').length) {
        $('#resultsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']]
        });

        // Count results by status
        var successCount = $('.result-row[data-status="success"]').length;
        var errorCount = $('.result-row[data-status="error"]').length;
        var skippedCount = $('.result-row[data-status="skipped"]').length;

        $('#successCount').text(successCount);
        $('#errorCount').text(errorCount);
        $('#skippedCount').text(skippedCount);
    }

    // Process form submission with loading
    $('#processForm').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        // Re-enable after a delay (in case of errors)
        setTimeout(function() {
            submitBtn.prop('disabled', false)
                     .html('<i class="fas fa-cogs"></i> Process Labor Attendance');
        }, 30000);
    });

    // Export results functionality
    $('#exportResults').click(function() {
        // Convert table to CSV
        var csv = [];
        var rows = $('#resultsTable tr');
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (var j = 0; j < cols.length; j++) {
                row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
            }
            
            csv.push(row.join(','));
        }

        // Download CSV
        var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
        var downloadLink = document.createElement('a');
        downloadLink.download = 'labor_attendance_results_' + $('#attendance_date').val() + '.csv';
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    });

    // Email results functionality (placeholder)
    $('#emailResults').click(function() {
        alert('Email functionality would be implemented here to send summary to HR/Admin.');
    });

    // Set default date to today
    $('#attendance_date').val(new Date().toISOString().split('T')[0]);
});
</script>
@endpush

@endsection