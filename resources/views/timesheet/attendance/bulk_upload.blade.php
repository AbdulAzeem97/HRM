@extends('layout.main')
@section('content')
    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{__('Bulk Attendance Upload')}}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">Upload attendance for multiple employees across multiple days quickly and easily.</p>
                    
                    <form id="bulkAttendanceForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employees">{{__('Select Employees')}} <span class="text-danger">*</span></label>
                                    <select name="employees[]" id="employees" class="form-control select2" multiple required>
                                        @foreach(\App\Models\Employee::where('is_active', 1)->get() as $employee)
                                            <option value="{{$employee->id}}">{{$employee->full_name}} ({{$employee->staff_id}})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select one or more employees</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">{{__('Start Date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">{{__('End Date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="clock_in_time">{{__('Clock In Time')}} <span class="text-danger">*</span></label>
                                    <input type="time" name="clock_in_time" id="clock_in_time" class="form-control" value="08:00" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="clock_out_time">{{__('Clock Out Time')}} <span class="text-danger">*</span></label>
                                    <input type="time" name="clock_out_time" id="clock_out_time" class="form-control" value="17:00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="skip_weekends" id="skip_weekends" class="form-check-input" checked>
                                        <label for="skip_weekends" class="form-check-label">{{__('Skip Weekends (Saturday & Sunday)')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div id="preview-section" style="display: none;">
                                <h5>{{__('Preview')}}</h5>
                                <div id="preview-content" class="alert alert-info"></div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" id="preview-btn" class="btn btn-secondary">
                                <i class="fa fa-eye"></i> {{__('Preview')}}
                            </button>
                            <button type="submit" id="upload-btn" class="btn btn-primary" disabled>
                                <i class="fa fa-upload"></i> {{__('Upload Attendance')}}
                            </button>
                        </div>
                    </form>
                    
                    <div id="result-section" class="mt-4" style="display: none;">
                        <div id="result-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .select2-container .select2-selection--multiple {
            min-height: 40px;
        }
    </style>

    <script>
    $(document).ready(function() {
        // Initialize Select2
        $('#employees').select2({
            placeholder: 'Select employees...'
        });
        
        // Preview functionality
        $('#preview-btn').on('click', function() {
            const formData = new FormData($('#bulkAttendanceForm')[0]);
            const employees = $('#employees').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const skipWeekends = $('#skip_weekends').is(':checked');
            
            if (!employees || employees.length === 0 || !startDate || !endDate) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Calculate number of days
            const start = new Date(startDate);
            const end = new Date(endDate);
            let totalDays = 0;
            
            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                if (skipWeekends && (d.getDay() === 0 || d.getDay() === 6)) {
                    continue; // Skip weekends
                }
                totalDays++;
            }
            
            const totalRecords = employees.length * totalDays;
            
            $('#preview-content').html(`
                <strong>Preview:</strong><br>
                • Employees: ${employees.length} selected<br>
                • Date range: ${startDate} to ${endDate}<br>
                • Working days: ${totalDays} days<br>
                • Total records to create: ${totalRecords}<br>
                • Clock in: ${$('#clock_in_time').val()}<br>
                • Clock out: ${$('#clock_out_time').val()}<br>
                • Skip weekends: ${skipWeekends ? 'Yes' : 'No'}
            `);
            
            $('#preview-section').show();
            $('#upload-btn').prop('disabled', false);
        });
        
        // Form submission
        $('#bulkAttendanceForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#upload-btn');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{__("Uploading...")}}');
            
            $.ajax({
                url: '{{ route("attendance.bulk-upload.store") }}',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#result-content').html(`
                            <div class="alert alert-success">
                                <h5><i class="fa fa-check-circle"></i> Success!</h5>
                                ${response.message}
                            </div>
                        `);
                        // Reset form
                        $('#bulkAttendanceForm')[0].reset();
                        $('#employees').val(null).trigger('change');
                        $('#preview-section').hide();
                        $('#upload-btn').prop('disabled', true);
                    } else {
                        $('#result-content').html(`
                            <div class="alert alert-danger">
                                <h5><i class="fa fa-exclamation-triangle"></i> Error!</h5>
                                ${response.message || 'An error occurred'}
                            </div>
                        `);
                    }
                    $('#result-section').show();
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = xhr.responseJSON.errors.join('<br>');
                    }
                    $('#result-content').html(`
                        <div class="alert alert-danger">
                            <h5><i class="fa fa-exclamation-triangle"></i> Error!</h5>
                            ${errorMessage}
                        </div>
                    `);
                    $('#result-section').show();
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Form validation
        $('#start_date, #end_date').on('change', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date');
                $('#end_date').val('');
            }
        });
    });
    </script>
@endsection