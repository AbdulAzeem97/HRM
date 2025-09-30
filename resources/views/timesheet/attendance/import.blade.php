@extends('layout.main')
@section('content')
    <section>
        <div class="container-fluid">

            <!-- Import CSV File (Device) -->
            {{-- <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{__('Import CSV file (Device)')}}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">Please take a note of the date format you get in the CSV file downloaded/exported from your attendance device(CSV). Now from within PeoplePro, go to- customize settings > general Settings and select the same date format from dropdown for the option named- 'Attendance device date format'</p>
                    <p class="card-text">The first line in downloaded file should remain as it is. Please do not change
                        the order of columns in file.</p>
                    <form action="{{ route('attendances.importDeviceCsv') }}" autocomplete="off" enctype="multipart/form-data"
                          method="post" accept-charset="utf-8">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <fieldset class="form-group">
                                        <label for="logo">{{trans('file.Upload')}} {{trans('file.File')}}</label>
                                        <input type="file" class="form-control-file" name="file"
                                               accept=".xlsx, .xls, .csv">
                                        <small>{{__('Please select csv/excel')}} file (allowed file size 2MB)</small>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-actions box-footer">
                                <button name="import_form" type="submit" class="btn btn-primary"><i
                                            class="fa fa fa-check-square-o"></i> {{trans('file.Save')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> --}}


            <!-- Import EXCEL/CSV file (Manual) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{__('Import EXCEL/CSV file (Manual)')}}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><a href="{{url('sample_file/sample_attendance.xlsx')}}" class="btn btn-primary"> <i
                                class="fa fa-download"></i> {{__('Download Sample Excel File')}} </a></h6>
                        </div>
                        <div class="col-md-6">
                            <h6><a href="{{url('sample_file/sample_attendance_30days.csv')}}" class="btn btn-success"> <i
                                class="fa fa-download"></i> {{__('Download 30-Day CSV Template')}} </a></h6>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5><i class="fa fa-info-circle"></i> {{__('For Multiple Days Upload (Recommended)')}}</h5>
                        <p class="mb-2"><strong>Use the 30-Day CSV Template above for bulk upload of multiple employees across multiple dates.</strong></p>
                        <p class="mb-1">CSV Format: <code>staff_id, attendance_date, clock_in, clock_out</code></p>
                        <p class="mb-1">Example: <code>EMP001, 2024-01-01, 08:00, 17:00</code></p>
                    </div>
                    
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <h6 class="text-primary">{{__('CSV Format Requirements:')}}</h6>
                            <ul class="mb-2">
                                <li><strong>staff_id:</strong> Employee's staff ID (must exist in system)</li>
                                <li><strong>attendance_date:</strong> Date in YYYY-MM-DD format (e.g., 2024-01-01)</li>
                                <li><strong>clock_in:</strong> Clock in time in HH:MM format (e.g., 08:00)</li>
                                <li><strong>clock_out:</strong> Clock out time in HH:MM format (e.g., 17:00)</li>
                            </ul>
                            <p class="text-muted small">💡 The system will automatically calculate overtime, late time, early leaving, and total work hours based on employee shifts.</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <h6><i class="fa fa-exclamation-triangle"></i> {{__('Important Notes:')}}</h6>
                        <ul class="mb-0">
                            <li>Keep the first header line as-is: <code>staff_id,attendance_date,clock_in,clock_out</code></li>
                            <li>Do not change the column order</li>
                            <li>Date format must be YYYY-MM-DD (e.g., 2024-01-01)</li>
                            <li>Time format must be HH:MM (24-hour format)</li>
                            <li>Staff IDs must exist in your employee database</li>
                            <li>Duplicate records for the same employee and date will be skipped</li>
                        </ul>
                    </div>

                    <form action="{{ route('attendances.importPost') }}" autocomplete="off" enctype="multipart/form-data"
                          method="post" accept-charset="utf-8">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <fieldset class="form-group">
                                        <label for="logo">{{trans('file.Upload')}} {{trans('file.File')}}</label>
                                        <input type="file" class="form-control-file" id="file" name="file"
                                               accept=".xlsx, .xls, .csv">
                                        <small>{{__('Please select excel/csv')}} file (allowed file size 2MB)</small>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-actions box-footer">
                                <button name="import_form" type="submit" class="btn btn-primary"><i
                                            class="fa fa fa-check-square-o"></i> {{trans('file.Save')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>


@endsection
