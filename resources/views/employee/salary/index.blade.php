<div class="row">
    <div class="col-md-3">
        @can('view-details-employee')
            <ul class="nav nav-tabs vertical" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="salary-tab" data-toggle="tab" href="#Salary" role="tab"
                       aria-controls="Salary" aria-selected="true">{{__('Basic Salary')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('salary_allowance.show',$employee)}}" id="salary_allowance-tab"
                       data-toggle="tab" data-table="salary_allowance" data-target="#Salary_allowance" role="tab"
                       aria-controls="Salary_allowance" aria-selected="false">{{trans('file.Allowances')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('salary_commission.show',$employee)}}" id="salary_commission-tab"
                       data-toggle="tab" data-table="salary_commission" data-target="#Salary_commission" role="tab"
                       aria-controls="Salary_commission" aria-selected="false">{{trans('file.Commissions')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('salary_loan.show',$employee)}}" id="salary_loan-tab"
                       data-toggle="tab" data-table="salary_loan" data-target="#Salary_loan" role="tab"
                       aria-controls="Salary_loan" aria-selected="false">{{trans('file.Loan')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('salary_deduction.show',$employee)}}" id="salary_deduction-tab"
                       data-toggle="tab" data-table="salary_deduction" data-target="#Salary_deduction" role="tab"
                       aria-controls="Salary_deduction" aria-selected="false">{{__('Statutory Deductions')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('other_payment.show',$employee)}}" id="other_payment-tab"
                       data-toggle="tab" data-table="other_payment" data-target="#Other_payment" role="tab"
                       aria-controls="Other_payment" aria-selected="false">{{__('Other Payment')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('salary_overtime.show',$employee)}}" id="salary_overtime-tab"
                       data-toggle="tab" data-table="salary_overtime" data-target="#Salary_overtime" role="tab"
                       aria-controls="Salary_overtime" aria-selected="false">{{__('Overtime')}}</a>
                </li>

                <!-- New -->
                <li class="nav-item">
                    <a class="nav-link" href="#" id="salary_pension-tab"
                        data-toggle="tab" data-table="salary_pension" data-target="#salary_pension" role="tab"
                        aria-controls="salary_pension" aria-selected="true">{{__('Salary Pension')}}
                    </a>
                </li>
                <!--/ New -->
                
                <!-- Overtime Settings -->
                <li class="nav-item">
                    <a class="nav-link" href="#" id="overtime_settings-tab"
                        data-toggle="tab" data-table="overtime_settings" data-target="#overtime_settings" role="tab"
                        aria-controls="overtime_settings" aria-selected="false">{{__('Overtime Settings')}}
                    </a>
                </li>
                <!--/ Overtime Settings -->
            </ul>
        @endcan
    </div>

    <div class="col-md-9">
        <div class="tab-content" id="myTabContent">
            @can('set-salary')
            <div class="tab-pane fade show active" id="Salary" role="tabpanel" aria-labelledby="salary-tab">
                {{__('All Basic Salary')}}
                <hr>
                @include('employee.salary.basic.index')
            </div>
            @endcan

            <!-- New Pension-->
            <div class="tab-pane fade" id="salary_pension" role="tabpanel" aria-labelledby="salary_pension-tab">
                <!--Contents for Basic starts here-->
                {{trans('file.Update')}} {{__('Pension')}}

                <div class="modal-body">
                    <span id="pension_form_result"></span>
                    <form method="post" id="salary_pension_form" class="form-horizontal" autocomplete="off">

                        @csrf
                        <div class="row">

                            <div class="col-md-4 form-group">
                                <label>{{__('Pension Type')}}</label>
                                <input type="hidden" name="pension_type_hidden" value="{{ $employee->pension_type ?? '' }}"/>
                                <select name="pension_type" id="pension_type" required class="selectpicker form-control"  title="{{__('Selecting',['key'=>__('Pension Type')])}}...">
                                    <option value="fixed" @if($employee->pension_type=='fixed') selected @endif>{{__('Fixed')}}</option>
                                    <option value="percentage" @if($employee->pension_type=='percentage') selected @endif>{{__('Percentage')}}</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                @if(config('variable.currency_format')=='suffix')
                                    <label>{{__('Amount')}} ({{config('variable.currency')}})</label>
                                @else
                                    <label>({{config('variable.currency')}}) {{__('Amount')}}</label>
                                @endif
                                <input type="text" min="0" name="pension_amount" id="pension_amount" placeholder="{{__('Amount')}}" required class="form-control" value="{{ $employee->pension_amount ?? '' }}">
                            </div>
                        </div>

                        <div class="container mt-5px">
                            <span class="text-danger"></span> <br><br>
                            <div class="form-group">
                                <input type="submit" class="btn btn-warning" value={{trans('file.Add')}} />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ New Pension -->

            <!-- Overtime Settings -->
            <div class="tab-pane fade" id="overtime_settings" role="tabpanel" aria-labelledby="overtime_settings-tab">
                <!--Contents for Overtime Settings starts here-->
                {{trans('file.Update')}} {{__('Overtime Settings')}}

                <div class="modal-body">
                    <span id="overtime_settings_form_result"></span>
                    <form method="post" id="overtime_settings_form" class="form-horizontal" autocomplete="off" action="{{ route('employees.overtime_settings_update', $employee->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="overtime_allowed" id="overtime_allowed" 
                                           class="form-check-input" value="1" 
                                           @if($employee->overtime_allowed ?? true) checked @endif>
                                    <label class="form-check-label" for="overtime_allowed">
                                        {{__('Allow Overtime')}}
                                    </label>
                                </div>
                                <small class="text-muted">{{__('Check to allow this employee to earn overtime pay')}}</small>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{__('Required Hours Per Day')}}</label>
                                <input type="number" min="1" max="24" name="required_hours_per_day" 
                                       id="required_hours_per_day" placeholder="{{__('Hours')}}" 
                                       class="form-control" value="{{ $employee->required_hours_per_day ?? 9 }}">
                                <small class="text-muted">{{__('Daily shift hours required before overtime starts')}}</small>
                            </div>
                        </div>

                        <div class="container mt-5px">
                            <div class="alert alert-info">
                                <strong>{{__('New Overtime Rules:')}}</strong><br>
                                • {{__('Late = clocks in more than 15 minutes after shift start')}}<br>
                                • {{__('Half-Day = clocks in more than 2 hours late (50% salary deduction)')}}<br>
                                • {{__('Every 3 late days = 1 full day salary deduction')}}<br>
                                • {{__('Overtime starts only after completing required shift hours')}}<br>
                                • {{__('Overtime rate = (daily salary ÷ required hours) × 2 (double pay)')}}<br>
                                • {{__('If late and overtime on same day, late time is subtracted from overtime first')}}
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-warning" value="{{trans('file.Update')}}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Overtime Settings -->

            <div class="tab-pane fade" id="Salary_allowance" role="tabpanel" aria-labelledby="salary_allowance-tab">
                {{__('All allowances')}}
                <hr>
                @include('employee.salary.allowance.index')
            </div>

            <div class="tab-pane fade" id="Salary_commission" role="tabpanel" aria-labelledby="Salary_commission-tab">
                {{__('All commission')}}
                <hr>

                @include('employee.salary.commission.index')

            </div>

            <div class="tab-pane fade" id="Salary_loan" role="tabpanel" aria-labelledby="Salary_loan-tab">
                {{__('All Loan')}}
                <hr>

                @include('employee.salary.loan.index')

            </div>


            <div class="tab-pane fade" id="Salary_deduction" role="tabpanel" aria-labelledby="Salary_deduction-tab">
                {{__('All Statutory Deduction')}}
                <hr>

                @include('employee.salary.deduction.index')
            </div>
            <div class="tab-pane fade" id="Other_payment" role="tabpanel" aria-labelledby="Other_payment-tab">
                {{__('Other Payment')}}
                <hr>

                @include('employee.salary.other_payment.index')
            </div>
            <div class="tab-pane fade" id="Salary_overtime" role="tabpanel" aria-labelledby="Salary_overtime-tab">
                {{__('Overtime')}}
                <hr>
                @include('employee.salary.overtime.index')
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Overtime settings script loaded');

    var form = document.getElementById('overtime_settings_form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Overtime form submitted');

            var formData = new FormData(form);
            var submitUrl = '{{ route("employees.overtime_settings_update", $employee->id) }}';

            console.log('Submitting to URL:', submitUrl);

            fetch(submitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    // Show success toast
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.success,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        alert('Success: ' + data.success);
                    }
                } else if (data.error) {
                    // Show error message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    } else {
                        alert('Error: ' + data.error);
                    }
                } else if (data.errors) {
                    // Show validation errors
                    var errorMsg = data.errors.join('\n');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errorMsg,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    } else {
                        alert('Validation Errors:\n' + errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Request failed:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Failed to update overtime settings. Please try again.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                } else {
                    alert('Network error: Failed to update overtime settings. Please try again.');
                }
            });
        });
    } else {
        console.error('Overtime form not found');
    }
});
</script>


