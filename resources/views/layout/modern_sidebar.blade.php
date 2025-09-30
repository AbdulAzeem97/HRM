<!-- Modern Enhanced Sidebar -->
<style>
/* Modern Sidebar Styles */
.modern-sidebar {
    background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    width: 280px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
}

.modern-sidebar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMiIgZmlsbD0icmdiYSgyNTUsIDI1NSwgMjU1LCAwLjA1KSIvPgo8L3N2Zz4K') repeat;
    opacity: 0.3;
    pointer-events: none;
}

.sidebar-brand {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
}

.brand-logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.brand-logo:hover {
    transform: scale(1.02);
}

.brand-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.brand-icon:hover {
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
    transform: translateY(-2px);
}

.brand-text {
    color: white;
    font-size: 1.4rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.modern-menu {
    padding: 1rem 0;
    height: calc(100vh - 120px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

.modern-menu::-webkit-scrollbar {
    width: 6px;
}

.modern-menu::-webkit-scrollbar-track {
    background: transparent;
}

.modern-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.modern-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

.menu-item {
    position: relative;
    margin: 0.5rem 1rem;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.menu-item.active {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.05));
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.menu-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.875rem 1.25rem;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.menu-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.menu-link:hover::before {
    left: 100%;
}

.menu-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.menu-icon {
    width: 20px;
    height: 20px;
    margin-right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    opacity: 0.9;
    transition: all 0.3s ease;
}

.menu-link:hover .menu-icon {
    opacity: 1;
    transform: scale(1.1);
}

.menu-text {
    flex: 1;
    font-weight: 500;
    letter-spacing: 0.3px;
}

.menu-arrow {
    font-size: 0.8rem;
    opacity: 0.7;
    transition: all 0.3s ease;
}

.menu-item.active .menu-arrow {
    transform: rotate(90deg);
}

/* Submenu Styles */
.submenu {
    background: rgba(0, 0, 0, 0.1);
    margin: 0.5rem 0 0 0;
    border-radius: 0 0 12px 12px;
    padding: 0.5rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.submenu-item {
    margin: 0.25rem 1rem 0.25rem 3rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.submenu-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 400;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
}

.submenu-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    margin-right: 12px;
    transition: all 0.3s ease;
}

.submenu-link:hover::before {
    background: #667eea;
    box-shadow: 0 0 8px rgba(102, 126, 234, 0.5);
    transform: translateY(-50%) scale(1.3);
}

.submenu-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
    padding-left: 1.5rem;
}

.submenu-text {
    margin-left: 1.5rem;
    font-weight: 450;
    letter-spacing: 0.2px;
}

/* Special Badges */
.menu-badge {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    font-weight: 600;
    margin-left: auto;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

/* Quick Access Panel */
.quick-access {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1rem;
}

.quick-access-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.quick-access-item:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
}

.quick-access-icon {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    opacity: 0.8;
}

/* Collapsed Sidebar States */
.modern-sidebar.sidebar-collapsed {
    width: 70px;
}

.modern-sidebar.sidebar-collapsed .brand-text,
.modern-sidebar.sidebar-collapsed .menu-text,
.modern-sidebar.sidebar-collapsed .menu-arrow {
    opacity: 0;
    visibility: hidden;
}

.modern-sidebar.sidebar-collapsed .submenu {
    display: none !important;
}

.modern-sidebar.sidebar-collapsed .menu-link {
    justify-content: center;
    padding: 12px;
}

.modern-sidebar.sidebar-collapsed .brand-logo {
    justify-content: center;
}

.modern-sidebar.sidebar-collapsed .brand-icon {
    margin-right: 0;
}

.modern-sidebar.sidebar-collapsed .menu-icon {
    margin-right: 0;
}

/* Dropdown Menu Functionality */
.has-dropdown > .menu-link .menu-arrow {
    transition: transform 0.3s ease;
}

.has-dropdown.show > .menu-link .menu-arrow {
    transform: rotate(90deg);
}

.submenu {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    background: rgba(0, 0, 0, 0.1);
}

.submenu.show {
    max-height: 500px;
    opacity: 1;
    padding: 5px 0;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .modern-sidebar {
        transform: translateX(-100%);
    }
    
    .modern-sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
}

@media (max-width: 768px) {
    .modern-sidebar {
        width: 260px;
    }
}

/* Theme variations */
.sidebar-theme-dark {
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
}

.sidebar-theme-purple {
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
}

.sidebar-theme-green {
    background: linear-gradient(180deg, #56ab2f 0%, #a8e6cf 100%);
}
</style>

<nav class="side-navbar modern-sidebar sidebar-theme-purple">
    <div class="side-navbar-wrapper">
        
        <!-- Modern Brand Header -->
        <div class="sidebar-brand">
            <a href="{{ auth()->user()->role_users_id == 1 ? route('admin.dashboard') : url('/employee/dashboard') }}" class="brand-logo">
                <div class="brand-icon">
                    <i class="fas fa-building text-white"></i>
                </div>
                <div class="brand-text">
                    HRMS Pro
                </div>
            </a>
        </div>

        <!-- Modern Navigation Menu -->
        <div class="modern-menu">
            <ul class="list-unstyled mb-0">

                <!-- Dashboard -->
                @if(auth()->user()->role_users_id == 1)
                    <li class="menu-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <span class="menu-text">{{ trans('file.Dashboard') }}</span>
                        </a>
                    </li>
                @else
                    <li class="menu-item {{ request()->is('employee/dashboard*') ? 'active' : '' }}">
                        <a href="{{ url('/employee/dashboard') }}" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <span class="menu-text">{{ trans('file.Dashboard') }}</span>
                        </a>
                    </li>
                @endif

                <!-- User Management -->
                @can('user')
                    <li class="menu-item has-dropdown {{ request()->is('user*') || request()->is('add-user*') ? 'active' : '' }}">
                        @if(auth()->user()->can('view-user'))
                            <a href="#users" aria-expanded="false" data-toggle="collapse" class="menu-link">
                                <div class="menu-icon">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <span class="menu-text">{{ trans('file.User') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        @endif
                        <ul id="users" class="collapse submenu">
                            @can('view-user')
                                <li class="submenu-item">
                                    <a href="{{ route('users-list') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Users List') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('role-access-user')
                                <li class="submenu-item">
                                    <a href="{{ route('user-roles') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Assign Role') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('last-login-user')
                                <li class="submenu-item">
                                    <a href="{{ route('login-info') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Users Last Login') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Employee Management -->
                @can('view-details-employee')
                    <li class="menu-item has-dropdown {{ request()->is('staff*') ? 'active' : '' }}">
                        <a href="#employees" aria-expanded="false" data-toggle="collapse" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <span class="menu-text">{{ trans('file.Employees') }}</span>
                            <i class="fas fa-chevron-right menu-arrow"></i>
                        </a>
                        <ul id="employees" class="collapse submenu">
                            @can('view-details-employee')
                                <li class="submenu-item">
                                    <a href="{{ route('employees.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Employee Lists') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('import-employee')
                                <li class="submenu-item">
                                    <a href="{{ route('employees.import') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Import Employees') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Labor Employee Management - Enhanced -->
                @can('view-details-employee')
                    <li class="menu-item has-dropdown {{ request()->is('labor*') ? 'active' : '' }}">
                        <a href="#labor_management" aria-expanded="false" data-toggle="collapse" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-hard-hat"></i>
                            </div>
                            <span class="menu-text">{{ __('Labor Management') }}</span>
                            <span class="menu-badge">New</span>
                            <i class="fas fa-chevron-right menu-arrow"></i>
                        </a>
                        <ul id="labor_management" class="collapse submenu">
                            <li class="submenu-item">
                                <a href="{{ route('labor.index') }}" class="submenu-link">
                                    <span class="submenu-text">{{ __('Labor Dashboard') }}</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('labor.create') }}" class="submenu-link">
                                    <span class="submenu-text">{{ __('Add Labor Employees') }}</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('labor.attendance') }}" class="submenu-link">
                                    <span class="submenu-text">{{ __('Process Attendance') }}</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('labor.payroll.index') }}" class="submenu-link">
                                    <span class="submenu-text">{{ __('Payroll System') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Organization -->
                <li class="menu-item has-dropdown {{ request()->is('organization*') ? 'active' : '' }}">
                    <a href="#Organization" aria-expanded="false" data-toggle="collapse" class="menu-link">
                        <div class="menu-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="menu-text">{{ trans('file.Organization') }}</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </a>
                    <ul id="Organization" class="collapse submenu">
                        @can('view-location')
                            <li class="submenu-item">
                                <a href="{{ route('locations.index') }}" class="submenu-link">
                                    <span class="submenu-text">{{ trans('file.Location') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view-company')
                            <li class="submenu-item">
                                <a href="{{ route('companies.index') }}" class="submenu-link">
                                    <span class="submenu-text">{{ trans('file.Company') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view-department')
                            <li class="submenu-item">
                                <a href="{{ route('departments.index') }}" class="submenu-link">
                                    <span class="submenu-text">{{ trans('file.Department') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view-designation')
                            <li class="submenu-item">
                                <a href="{{ route('designations.index') }}" class="submenu-link">
                                    <span class="submenu-text">{{ trans('file.Designation') }}</span>
                                </a>
                            </li>
                        @endcan
                        <li class="submenu-item">
                            <a href="{{ route('announcements.index') }}" class="submenu-link">
                                <span class="submenu-text">{{ trans('file.Announcements') }}</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('policy.index') }}" class="submenu-link">
                                <span class="submenu-text">{{ __('Company Policy') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Core HR -->
                @can('core_hr')
                    <li class="menu-item has-dropdown {{ request()->is('core_hr*') ? 'active' : '' }}">
                        @if(auth()->user()->can('view-promotion')||auth()->user()->can('view-award') || auth()->user()->can('view-travel')||auth()->user()->can('view-transfer')||auth()->user()->can('view-resignation')||auth()->user()->can('view-complaint')||auth()->user()->can('view-warning')||auth()->user()->can('view-termination'))
                            <a href="#Core_hr" aria-expanded="false" data-toggle="collapse" class="menu-link">
                                <div class="menu-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <span class="menu-text">{{ __('Core HR') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        @endif
                        <ul id="Core_hr" class="collapse submenu">
                            @can('view-promotion')
                                <li class="submenu-item">
                                    <a href="{{ route('promotions.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Promotion') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-award')
                                <li class="submenu-item">
                                    <a href="{{ route('awards.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Award') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-travel')
                                <li class="submenu-item">
                                    <a href="{{ route('travels.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Travel') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-transfer')
                                <li class="submenu-item">
                                    <a href="{{ route('transfers.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Transfer') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-resignation')
                                <li class="submenu-item">
                                    <a href="{{ route('resignations.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Resignations') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-complaint')
                                <li class="submenu-item">
                                    <a href="{{ route('complaints.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Complaints') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-warning')
                                <li class="submenu-item">
                                    <a href="{{ route('warnings.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Warnings') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-termination')
                                <li class="submenu-item">
                                    <a href="{{ route('terminations.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ trans('file.Terminations') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Timesheets -->
                @can('timesheet')
                    <li class="menu-item has-dropdown {{ request()->is('timesheet*') ? 'active' : '' }}">
                        <a href="#Timesheets" aria-expanded="false" data-toggle="collapse" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="menu-text">{{ trans('file.Timesheets') }}</span>
                            <i class="fas fa-chevron-right menu-arrow"></i>
                        </a>
                        <ul id="Timesheets" class="collapse submenu">
                            @can('edit-attendance')
                                <li class="submenu-item">
                                    <a href="{{ route('update_attendances.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Add/Update Attendances') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('import-attendance')
                                <li class="submenu-item">
                                    <a href="{{ route('attendances.import') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Import Attendances') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-office_shift')
                                <li class="submenu-item">
                                    <a href="{{ route('office_shift.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Office Shift') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-holiday')
                                <li class="submenu-item">
                                    <a href="{{ route('holidays.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Manage Holiday') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-leave')
                                <li class="submenu-item">
                                    <a href="{{ route('leaves.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Manage Leaves') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Payroll -->
                @can('payment-module')
                    <li class="menu-item has-dropdown {{ request()->is('payroll*') ? 'active' : '' }}">
                        @if(auth()->user()->can('view-payslip') || auth()->user()->can('view-paylist'))
                            <a href="#Payroll" aria-expanded="false" data-toggle="collapse" class="menu-link">
                                <div class="menu-icon">
                                    <i class="fas fa-money-check-alt"></i>
                                </div>
                                <span class="menu-text">{{ trans('file.Payroll') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        @endif
                        <ul id="Payroll" class="collapse submenu">
                            @can('view-payslip')
                                <li class="submenu-item">
                                    <a href="{{ route('payroll.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('New Payment') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-paylist')
                                <li class="submenu-item">
                                    <a href="{{ route('payment_history.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Payment History') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Performance -->
                @can('performance')
                    <li class="menu-item has-dropdown {{ request()->is('performance*') ? 'active' : '' }}">
                        @if(auth()->user()->can('view-goal-type') || auth()->user()->can('view-goal-tracking') || auth()->user()->can('view-indicator') || auth()->user()->can('view-appraisal'))
                            <a href="#performance" aria-expanded="false" data-toggle="collapse" class="menu-link">
                                <div class="menu-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span class="menu-text">Performance</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        @endif
                        <ul id="performance" class="collapse submenu">
                            @can('view-goal-type')
                                <li class="submenu-item">
                                    <a href="{{ route('performance.goal-type.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Goal type') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-goal-tracking')
                                <li class="submenu-item">
                                    <a href="{{ route('performance.goal-tracking.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Goal Tracking') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-indicator')
                                <li class="submenu-item">
                                    <a href="{{ route('performance.indicator.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Indicator') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-appraisal')
                                <li class="submenu-item">
                                    <a href="{{ route('performance.appraisal.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Appraisal') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Settings -->
                @can('customize-setting')
                    <li class="menu-item has-dropdown {{ request()->is('settings*') ? 'active' : '' }}">
                        @if(auth()->user()->can('view-role')||auth()->user()->can('view-general-setting')||auth()->user()->can('access-language')||auth()->user()->can('access-variable_type')||auth()->user()->can('access-variable_method')||auth()->user()->can('view-general-setting'))
                            <a href="#Customize_settings" aria-expanded="false" data-toggle="collapse" class="menu-link">
                                <div class="menu-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <span class="menu-text">{{ __('Customize Setting') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        @endif
                        <ul id="Customize_settings" class="collapse submenu">
                            @can('view-role')
                                <li class="submenu-item">
                                    <a href="{{ route('roles.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Roles and Access') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-general-setting')
                                <li class="submenu-item">
                                    <a href="{{ route('general_settings.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('General Settings') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-mail-setting')
                                <li class="submenu-item">
                                    <a href="{{ route('setting.mail') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Mail Setting') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('access-language')
                                <li class="submenu-item">
                                    <a href="{{ route('languages.translations.index','English') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Language Settings') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('access-variable_type')
                                <li class="submenu-item">
                                    <a href="{{ route('variables.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Variable Type') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('access-variable_method')
                                <li class="submenu-item">
                                    <a href="{{ route('variables_method.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('Variable Method') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view-general-setting')
                                <li class="submenu-item">
                                    <a href="{{ route('ip_setting.index') }}" class="submenu-link">
                                        <span class="submenu-text">{{ __('IP Settings') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

            </ul>
        </div>

        <!-- Quick Access Panel -->
        <div class="quick-access">
            <a href="{{ auth()->user()->role_users_id == 1 ? route('admin.dashboard') : url('/employee/dashboard') }}" class="quick-access-item">
                <i class="fas fa-home quick-access-icon"></i>
                <span>Home</span>
            </a>
            <a href="#" class="quick-access-item" onclick="toggleTheme()">
                <i class="fas fa-palette quick-access-icon"></i>
                <span>Theme</span>
            </a>
        </div>
    </div>
</nav>

<script>
// Modern sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.modern-sidebar');
    
    // Handle dropdown menus
    const dropdownToggles = document.querySelectorAll('[data-toggle="collapse"]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href');
            const targetElement = document.querySelector(target);
            const parent = this.closest('.menu-item');
            
            if (targetElement) {
                if (targetElement.classList.contains('show')) {
                    targetElement.classList.remove('show');
                    parent.classList.remove('active');
                    parent.classList.remove('show');
                } else {
                    // Close other open dropdowns
                    document.querySelectorAll('.submenu.show').forEach(menu => {
                        menu.classList.remove('show');
                        menu.closest('.menu-item').classList.remove('active', 'show');
                    });
                    targetElement.classList.add('show');
                    parent.classList.add('active', 'show');
                }
            }
        });
    });

    // Handle menu items with submenus
    const menuItems = document.querySelectorAll('.has-dropdown');
    menuItems.forEach(item => {
        const menuLink = item.querySelector('.menu-link');
        const submenu = item.querySelector('.submenu');
        
        if (menuLink && submenu) {
            menuLink.addEventListener('click', function(e) {
                if (this.getAttribute('data-toggle') === 'collapse') {
                    e.preventDefault();
                    
                    // Toggle current menu
                    if (submenu.classList.contains('show')) {
                        submenu.classList.remove('show');
                        item.classList.remove('active', 'show');
                    } else {
                        // Close other menus
                        document.querySelectorAll('.has-dropdown').forEach(otherItem => {
                            const otherSubmenu = otherItem.querySelector('.submenu');
                            if (otherSubmenu && otherItem !== item) {
                                otherSubmenu.classList.remove('show');
                                otherItem.classList.remove('active', 'show');
                            }
                        });
                        
                        // Open current menu
                        submenu.classList.add('show');
                        item.classList.add('active', 'show');
                    }
                }
            });
        }
    });

    // Set active menu item based on current URL
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.menu-link, .submenu-link');
    menuLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href === currentPath || currentPath.includes(href.replace(/^.*\//, '')))) {
            const menuItem = link.closest('.menu-item');
            if (menuItem) {
                menuItem.classList.add('active');
                
                // If it's a submenu link, also open the parent menu
                const submenu = link.closest('.submenu');
                if (submenu) {
                    submenu.classList.add('show');
                    const parentMenuItem = submenu.closest('.menu-item');
                    if (parentMenuItem) {
                        parentMenuItem.classList.add('active', 'show');
                    }
                }
            }
        }
    });

    // Sidebar toggle functionality for mobile
    window.toggleSidebar = function() {
        sidebar.classList.toggle('show');
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.classList.toggle('show');
        }
    };

    // Close sidebar when clicking overlay
    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('show');
            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) {
                overlay.classList.remove('show');
            }
        }
    });
});

// Theme toggle function
function toggleTheme() {
    const sidebar = document.querySelector('.modern-sidebar');
    const themes = ['sidebar-theme-purple', 'sidebar-theme-dark', 'sidebar-theme-green'];
    let currentTheme = 0;
    
    themes.forEach((theme, index) => {
        if (sidebar.classList.contains(theme)) {
            currentTheme = index;
        }
        sidebar.classList.remove(theme);
    });
    
    const nextTheme = (currentTheme + 1) % themes.length;
    sidebar.classList.add(themes[nextTheme]);
    
    // Store theme preference
    localStorage.setItem('sidebar-theme', themes[nextTheme]);
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('sidebar-theme');
    if (savedTheme) {
        const sidebar = document.querySelector('.modern-sidebar');
        sidebar.classList.remove('sidebar-theme-purple', 'sidebar-theme-dark', 'sidebar-theme-green');
        sidebar.classList.add(savedTheme);
    }
});

// Sidebar collapse toggle function
function toggleSidebarCollapse() {
    const sidebar = document.querySelector('.modern-sidebar');
    sidebar.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('sidebar-collapsed'));
}

// Load saved collapse state
document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    if (isCollapsed) {
        document.querySelector('.modern-sidebar').classList.add('sidebar-collapsed');
    }
});
</script>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>