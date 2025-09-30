@extends('layout.modern_main')
@section('content')

<!-- Modern Admin Dashboard -->
<div class="modern-dashboard">
    <!-- Alert Section for version upgrade-->
    @if (env('USER_VERIFIED'))
        <div id="alertSection" class="{{ $versionUpgradeData['alert_version_upgrade_enable']==true ? null : 'd-none' }} modern-alert modern-alert-primary alert-dismissible fade show" role="alert">
            <div class="alert-icon">
                <i class="fa fa-bullhorn"></i>
            </div>
            <div class="alert-content">
                <strong>Announcement!</strong> A new version {{$versionUpgradeData['demo_version']}} has been released. 
                <a href="{{route('new-release')}}" class="alert-link">Check upgrade details</a>
            </div>
            <button type="button" id="closeButtonUpgrade" class="modern-close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="welcome-section">
                <h1 class="welcome-title">
                    <span class="greeting">{{trans('file.Welcome')}},</span>
                    <span class="username">{{auth()->user()->username}}</span>
                </h1>
                <p class="welcome-subtitle">Manage your HR operations efficiently</p>
            </div>
            <div class="date-section">
                <div class="today-info">
                    <div class="date-label">{{__('Today is')}}</div>
                    <div class="current-date">{{now()->englishDayOfWeek}}, {{now()->format(env('Date_Format'))}}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-section">
        <div class="row">
            <!-- Employees Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="modern-stats-card employees-card">
                    <div class="card-icon">
                        <i class="dripicons-user-group"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="stat-number">{{$employees->count()}}</h3>
                        <p class="stat-label">{{ trans('file.Employees') }}</p>
                        <a href="{{route('employees.index')}}" class="card-link">
                            View All <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-gradient employees-gradient"></div>
                </div>
            </div>

            <!-- Attendance Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="modern-stats-card attendance-card">
                    <div class="card-icon">
                        <i class="dripicons-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="stat-number">{{$attendance_count}}</h3>
                        <p class="stat-label">{{trans('file.Attendance')}}</p>
                        <div class="sub-stats">
                            <span class="present">P: {{$attendance_count}}</span>
                            <span class="absent">A: {{$employees->count() - $attendance_count}}</span>
                        </div>
                        <a href="{{route('attendances.index')}}" class="card-link">
                            View Details <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-gradient attendance-gradient"></div>
                </div>
            </div>

            <!-- Leave Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="modern-stats-card leave-card">
                    <div class="card-icon">
                        <i class="dripicons-calendar"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="stat-number">{{$leave_count}}</h3>
                        <p class="stat-label">{{__('Total Leave')}}</p>
                        <a href="{{route('leaves.index')}}" class="card-link">
                            Manage Leaves <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-gradient leave-gradient"></div>
                </div>
            </div>

            <!-- Labor Employees Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="modern-stats-card labor-card">
                    <div class="card-icon">
                        <i class="dripicons-user-id"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="stat-number">15</h3>
                        <p class="stat-label">Labor Employees</p>
                        <div class="new-badge">New Feature!</div>
                        <a href="{{route('labor.index')}}" class="card-link">
                            View Labor <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-gradient labor-gradient"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fa fa-lightning"></i>
                Quick Actions
            </h2>
            <p class="section-subtitle">Frequently used operations</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon employees-bg">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="action-content">
                        <h4>Add Employee</h4>
                        <p>Register new employees</p>
                        <a href="{{url('/staff/employees')}}#formModal" class="action-btn">
                            Add Now
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon attendance-bg">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="action-content">
                        <h4>Process Attendance</h4>
                        <p>Update daily attendance</p>
                        <a href="{{route('update_attendances.index')}}" class="action-btn">
                            Process Now
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon labor-bg">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h4>Labor Management</h4>
                        <p>Manage labor employees & payroll</p>
                        <a href="{{route('labor.index')}}" class="action-btn">
                            Manage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="system-status-section">
        <div class="row">
            <div class="col-lg-8">
                <div class="status-card">
                    <div class="status-header">
                        <h3>
                            <i class="fa fa-chart-line"></i>
                            System Overview
                        </h3>
                    </div>
                    <div class="status-content">
                        <div class="status-grid">
                            <div class="status-item">
                                <div class="status-icon success">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                                <div class="status-info">
                                    <h6>Auto-Shift Detection</h6>
                                    <p>Active & Running</p>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-icon success">
                                    <i class="fa fa-calculator"></i>
                                </div>
                                <div class="status-info">
                                    <h6>Payroll System</h6>
                                    <p>Ready for Processing</p>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-icon warning">
                                    <i class="fa fa-clock"></i>
                                </div>
                                <div class="status-info">
                                    <h6>Attendance Sync</h6>
                                    <p>Last sync: 2 hours ago</p>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-icon success">
                                    <i class="fa fa-database"></i>
                                </div>
                                <div class="status-info">
                                    <h6>Database</h6>
                                    <p>Optimal Performance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="notifications-card">
                    <div class="notifications-header">
                        <h3>
                            <i class="fa fa-bell"></i>
                            Recent Activity
                        </h3>
                    </div>
                    <div class="notifications-content">
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fa fa-user-plus text-success"></i>
                            </div>
                            <div class="notification-text">
                                <p>5 new employees added</p>
                                <small>2 hours ago</small>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fa fa-calculator text-info"></i>
                            </div>
                            <div class="notification-text">
                                <p>Payroll processed for August</p>
                                <small>1 day ago</small>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fa fa-clock-o text-warning"></i>
                            </div>
                            <div class="notification-text">
                                <p>Late attendance detected</p>
                                <small>3 hours ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Dashboard Styles -->
<style>
/* Modern Dashboard Base */
.modern-dashboard {
    padding: 0;
    margin: -30px -30px 0 -30px;
    min-height: 100vh;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Modern Alert */
.modern-alert {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 15px;
    margin: 30px 30px 20px 30px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.modern-alert-primary {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-left: 4px solid #667eea;
}

.alert-icon {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 20px;
}

.alert-content {
    flex: 1;
    font-size: 15px;
    line-height: 1.5;
}

.alert-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}

.alert-link:hover {
    color: #5a6fd8;
    text-decoration: underline;
}

.modern-close {
    background: none;
    border: none;
    color: #8695a4;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.modern-close:hover {
    background: rgba(0, 0, 0, 0.1);
    color: #2c3e50;
}

/* Dashboard Header */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 30px;
    margin-bottom: 0;
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.03"/><circle cx="10" cy="50" r="0.5" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateX(0) translateY(0); }
    25% { transform: translateX(-5px) translateY(-10px); }
    50% { transform: translateX(5px) translateY(-15px); }
    75% { transform: translateX(-3px) translateY(-5px); }
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.welcome-title {
    font-size: 36px;
    font-weight: 300;
    margin: 0;
    line-height: 1.2;
}

.greeting {
    opacity: 0.9;
}

.username {
    font-weight: 700;
    color: #ffd700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.welcome-subtitle {
    font-size: 16px;
    opacity: 0.8;
    margin: 10px 0 0 0;
}

.date-section {
    text-align: right;
}

.date-label {
    font-size: 14px;
    opacity: 0.8;
    margin-bottom: 5px;
}

.current-date {
    font-size: 20px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.1);
    padding: 10px 20px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Stats Section */
.stats-section {
    padding: 30px;
    margin-top: -30px;
    position: relative;
    z-index: 2;
}

.modern-stats-card {
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 20px;
    padding: 30px;
    height: 200px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    cursor: pointer;
}

.modern-stats-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
}

.card-icon {
    position: absolute;
    top: 25px;
    right: 25px;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.modern-stats-card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
}

.card-content {
    position: relative;
    z-index: 2;
}

.stat-number {
    font-size: 48px;
    font-weight: 700;
    margin: 0 0 10px 0;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.stat-label {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.9);
    margin: 0 0 10px 0;
    font-weight: 500;
}

.sub-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.present, .absent {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    color: white;
}

.card-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 15px;
    border-radius: 20px;
    margin-top: 10px;
}

.card-link:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
    transform: translateX(5px);
}

.new-badge {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 10px;
    display: inline-block;
    margin-bottom: 10px;
    animation: pulse 2s infinite;
}

/* Card Gradients */
.card-gradient {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    z-index: 1;
}

.employees-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.attendance-gradient {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.leave-gradient {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.labor-gradient {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

/* Quick Actions Section */
.quick-actions-section {
    padding: 0 30px 30px 30px;
}

.section-header {
    text-align: center;
    margin-bottom: 40px;
}

.section-title {
    font-size: 32px;
    font-weight: 300;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.section-title i {
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-subtitle {
    color: #8695a4;
    font-size: 16px;
    margin: 0;
}

.action-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    transition: all 0.4s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.action-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.action-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.employees-bg {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.attendance-bg {
    background: linear-gradient(135deg, #f093fb, #f5576c);
}

.labor-bg {
    background: linear-gradient(135deg, #43e97b, #38f9d7);
}

.action-card:hover .action-icon {
    transform: scale(1.1) rotate(5deg);
}

.action-content h4 {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.action-content p {
    color: #8695a4;
    margin-bottom: 20px;
    flex: 1;
}

.action-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 12px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

/* System Status Section */
.system-status-section {
    padding: 0 30px 30px 30px;
}

.status-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 100%;
}

.status-header h3 {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: rgba(248, 249, 250, 0.8);
    border-radius: 15px;
    transition: all 0.3s ease;
}

.status-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.status-icon.success {
    background: linear-gradient(135deg, #11998e, #38ef7d);
}

.status-icon.warning {
    background: linear-gradient(135deg, #ffc107, #ffecb3);
    color: #212529;
}

.status-info h6 {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.status-info p {
    color: #8695a4;
    margin: 0;
    font-size: 14px;
}

/* Notifications Card */
.notifications-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 100%;
}

.notifications-header h3 {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.notification-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: rgba(102, 126, 234, 0.05);
    border-radius: 10px;
    padding: 15px;
    margin: 0 -15px;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(248, 249, 250, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.notification-text p {
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 5px;
}

.notification-text small {
    color: #8695a4;
    font-size: 12px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .status-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .modern-dashboard {
        margin: -20px -20px 0 -20px;
    }
    
    .dashboard-header {
        padding: 30px 20px;
    }
    
    .welcome-title {
        font-size: 28px;
    }
    
    .stats-section,
    .quick-actions-section,
    .system-status-section {
        padding: 20px;
    }
    
    .modern-stats-card {
        padding: 25px;
        height: 180px;
    }
    
    .stat-number {
        font-size: 36px;
    }
    
    .section-title {
        font-size: 26px;
        flex-direction: column;
        gap: 10px;
    }
}

@media (max-width: 576px) {
    .welcome-title {
        font-size: 24px;
    }
    
    .current-date {
        font-size: 16px;
        padding: 8px 15px;
    }
    
    .modern-stats-card {
        padding: 20px;
        height: 160px;
    }
    
    .stat-number {
        font-size: 30px;
    }
    
    .action-card {
        padding: 25px;
    }
    
    .status-card,
    .notifications-card {
        padding: 25px;
    }
}

/* Animation Classes */
.animate-stats {
    animation: slideInUp 0.6s ease-out;
}

.animate-actions {
    animation: slideInUp 0.8s ease-out;
}

.animate-status {
    animation: slideInUp 1s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Pulse Animation for New Features */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<!-- Modern Dashboard JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation classes with delays
    setTimeout(() => {
        document.querySelector('.stats-section').classList.add('animate-stats');
    }, 200);
    
    setTimeout(() => {
        document.querySelector('.quick-actions-section').classList.add('animate-actions');
    }, 400);
    
    setTimeout(() => {
        document.querySelector('.system-status-section').classList.add('animate-status');
    }, 600);
    
    // Stats card hover effects
    const statsCards = document.querySelectorAll('.modern-stats-card');
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Action card interactions
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function() {
            const actionBtn = this.querySelector('.action-btn');
            if (actionBtn) {
                window.location.href = actionBtn.getAttribute('href');
            }
        });
    });
    
    // Real-time updates (example)
    setInterval(updateSystemStatus, 60000); // Update every minute
    
    function updateSystemStatus() {
        // This would typically fetch real data from an API
        console.log('Updating system status...');
    }
});
</script>

@endsection