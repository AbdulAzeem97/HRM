<!-- Modern Header with Glassmorphism -->
<header class="modern-header">
    <nav class="modern-navbar">
        <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
                <!-- Mobile Menu Toggle -->
                <div class="left-section d-flex align-items-center">
                    <button id="toggle-btn" class="modern-menu-btn">
                        <i class="dripicons-menu"></i>
                    </button>
                    
                    <!-- Brand Logo -->
                    <div class="modern-brand" id="site_logo_main">
                        @if($general_settings->site_logo)
                            <img src="{{asset('/images/logo/'.$general_settings->site_logo)}}" alt="Logo" class="brand-logo">
                        @else
                            <div class="brand-text">
                                <h2>TTPHRM</h2>
                                <span class="brand-subtitle">Labor Management</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Navigation Menu -->
                <ul class="modern-nav-menu d-flex align-items-center">
                    <!-- Quick Create Dropdown -->
                    <li class="nav-item dropdown">
                        <button class="modern-nav-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" data-toggle="tooltip" title="Quick Create">
                            <i class="fa fa-plus-circle"></i>
                            <span class="btn-text d-none d-lg-inline">Create</span>
                        </button>
                        <div class="modern-dropdown-menu dropdown-menu">
                            <div class="dropdown-header">
                                <i class="fa fa-plus-circle"></i>
                                Quick Actions
                            </div>
                            @foreach ($addFrom as $item)
                                <a class="modern-dropdown-item" href="{{ $item['url'] }}">
                                    <i class="fa fa-plus"></i>
                                    Add {{ $item['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <!-- System Refresh -->
                    <li class="nav-item">
                        <a class="modern-nav-btn" href="{{url('/optimize')}}" data-toggle="tooltip" title="Clear Cache & Refresh">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </li>

                    <!-- Fullscreen Toggle -->
                    <li class="nav-item">
                        <button class="modern-nav-btn" id="btnFullscreen" data-toggle="tooltip" title="Full Screen">
                            <i class="dripicons-expand"></i>
                        </button>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown notifications-dropdown">
                        <button class="modern-nav-btn dropdown-toggle" id="notify-btn" data-toggle="dropdown" aria-expanded="false" data-toggle="tooltip" title="Notifications">
                            <i class="dripicons-bell"></i>
                            @if(auth()->user()->unreadNotifications->count())
                                <span class="notification-badge">
                                    {{auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count()}}
                                </span>
                            @endif
                        </button>
                        <div class="modern-dropdown-menu dropdown-menu">
                            <div class="dropdown-header">
                                <i class="dripicons-bell"></i>
                                Notifications
                                <div class="notification-actions">
                                    <a href="{{route('seeAllNoti')}}" class="action-btn">See All</a>
                                    <a href="{{route('clearAll')}}" class="action-btn">Clear</a>
                                </div>
                            </div>
                            <div class="notifications-container">
                                @if(auth()->user()->notifications->count() > 0)
                                    @foreach(auth()->user()->notifications->take(5) as $notification)
                                        <a class="modern-notification-item" href="{{$notification->data['link'] ?? '#'}}">
                                            <div class="notification-icon">
                                                <i class="fa fa-bell"></i>
                                            </div>
                                            <div class="notification-content">
                                                <p>{{$notification->data['data']}}</p>
                                                <small>{{$notification->created_at->diffForHumans()}}</small>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="no-notifications">
                                        <i class="dripicons-bell"></i>
                                        <p>No notifications</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>

                    <!-- Language Selector -->
                    <li class="nav-item dropdown">
                        <button class="modern-nav-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false" data-toggle="tooltip" title="Language">
                            <i class="dripicons-web"></i>
                            <span class="btn-text d-none d-lg-inline">Language</span>
                        </button>
                        <div class="modern-dropdown-menu dropdown-menu">
                            <div class="dropdown-header">
                                <i class="dripicons-web"></i>
                                Select Language
                            </div>
                            @foreach($languages as $lang)
                                <a class="modern-dropdown-item" href="{{route('language.switch',$lang)}}">
                                    <i class="fa fa-globe"></i>
                                    {{ucfirst($lang)}}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown user-dropdown">
                        <button class="modern-user-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                @if(!empty(auth()->user()->profile_photo))
                                    <img src="{{ asset('uploads/profile_photos/')}}/{{auth()->user()->profile_photo}}" alt="Profile">
                                @else
                                    <img src="{{ asset('uploads/profile_photos/avatar.jpg')}}" alt="Profile">
                                @endif
                            </div>
                            <div class="user-info d-none d-lg-block">
                                <span class="user-name">{{auth()->user()->username}}</span>
                                <small class="user-role">
                                    @if(auth()->user()->role_users_id == 1) Administrator @else Employee @endif
                                </small>
                            </div>
                            <i class="dripicons-chevron-down"></i>
                        </button>
                        <div class="modern-dropdown-menu dropdown-menu user-menu">
                            <div class="dropdown-header">
                                <div class="user-profile-header">
                                    <div class="user-avatar-large">
                                        @if(!empty(auth()->user()->profile_photo))
                                            <img src="{{ asset('uploads/profile_photos/')}}/{{auth()->user()->profile_photo}}" alt="Profile">
                                        @else
                                            <img src="{{ asset('uploads/profile_photos/avatar.jpg')}}" alt="Profile">
                                        @endif
                                    </div>
                                    <div class="user-details">
                                        <h6>{{auth()->user()->username}}</h6>
                                        <small>{{auth()->user()->email ?? 'No email'}}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <a class="modern-dropdown-item" href="{{route('profile')}}">
                                <i class="dripicons-user"></i>
                                {{trans('file.Profile')}}
                            </a>
                            
                            @if(auth()->user()->role_users_id == 1)
                                <div class="dropdown-divider"></div>
                                <a class="modern-dropdown-item admin-action" href="#" id="empty_database">
                                    <i class="dripicons-stack"></i>
                                    {{__('Empty Database')}}
                                </a>
                                <a class="modern-dropdown-item admin-action" href="{{route('export_database')}}">
                                    <i class="dripicons-cloud-download"></i>
                                    {{__('Export Database')}}
                                </a>
                            @endif
                            
                            <div class="dropdown-divider"></div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button class="modern-dropdown-item logout-btn" type="submit">
                                    <i class="dripicons-exit"></i>
                                    {{trans('file.logout')}}
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @include('shared.flash_message')
</header>

<!-- Modern Header Styles -->
<style>
/* Modern Header Base */
.modern-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
}

.modern-navbar {
    padding: 0.5rem 0;
}

.navbar-holder {
    min-height: 70px;
}

/* Left Section */
.left-section {
    gap: 1rem;
}

.modern-menu-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: white;
    padding: 12px 15px;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.modern-menu-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Brand Section */
.modern-brand {
    display: flex;
    align-items: center;
}

.brand-logo {
    max-height: 50px;
    width: auto;
    filter: brightness(1.1) contrast(1.1);
}

.brand-text h2 {
    color: white;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.brand-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 12px;
    display: block;
    margin-top: -4px;
}

/* Navigation Menu */
.modern-nav-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
}

.modern-nav-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    color: white;
    padding: 10px 15px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modern-nav-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
    text-decoration: none;
}

.btn-text {
    font-size: 14px;
    font-weight: 500;
}

/* User Button */
.modern-user-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 25px;
    color: white;
    padding: 8px 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.modern-user-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    color: white;
    text-decoration: none;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-info {
    text-align: left;
}

.user-name {
    display: block;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
}

.user-role {
    color: rgba(255, 255, 255, 0.8);
    font-size: 11px;
    line-height: 1;
}

/* Notification Badge */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    border-radius: 12px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Modern Dropdown Menus */
.modern-dropdown-menu {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    margin-top: 10px;
    padding: 0;
    min-width: 280px;
    animation: dropdownFadeIn 0.3s ease;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 15px 15px 0 0;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modern-dropdown-item {
    color: #2c3e50;
    padding: 12px 20px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    font-size: 14px;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

.modern-dropdown-item:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8ebff 100%);
    color: #667eea;
    transform: translateX(5px);
    text-decoration: none;
}

.modern-dropdown-item i {
    width: 16px;
    text-align: center;
}

.dropdown-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
    margin: 5px 0;
}

/* User Profile Header */
.user-profile-header {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar-large {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.user-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-details h6 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.user-details small {
    opacity: 0.8;
    font-size: 12px;
}

/* Notifications Container */
.notifications-container {
    max-height: 300px;
    overflow-y: auto;
}

.modern-notification-item {
    color: #2c3e50;
    padding: 15px 20px;
    text-decoration: none;
    display: flex;
    align-items: flex-start;
    gap: 15px;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.modern-notification-item:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8ebff 100%);
    text-decoration: none;
    color: #667eea;
}

.notification-icon {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.notification-content p {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
}

.notification-content small {
    color: #8695a4;
    font-size: 12px;
}

.no-notifications {
    text-align: center;
    padding: 40px 20px;
    color: #8695a4;
}

.no-notifications i {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.3;
}

/* Notification Actions */
.notification-actions {
    margin-left: auto;
    display: flex;
    gap: 10px;
}

.action-btn {
    color: rgba(255, 255, 255, 0.8);
    font-size: 12px;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
}

/* Admin Actions */
.admin-action {
    color: #e74c3c !important;
}

.admin-action:hover {
    background: rgba(231, 76, 60, 0.1) !important;
    color: #e74c3c !important;
}

/* Logout Button */
.logout-btn {
    color: #e74c3c !important;
    font-weight: 600;
}

.logout-btn:hover {
    background: rgba(231, 76, 60, 0.1) !important;
    color: #c0392b !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-navbar {
        padding: 0.25rem 0;
    }
    
    .navbar-holder {
        min-height: 60px;
    }
    
    .brand-text h2 {
        font-size: 20px;
    }
    
    .modern-nav-menu {
        gap: 0.25rem;
    }
    
    .modern-nav-btn {
        padding: 8px 12px;
        font-size: 14px;
    }
    
    .modern-user-btn {
        padding: 6px 10px;
    }
    
    .user-avatar {
        width: 35px;
        height: 35px;
    }
    
    .modern-dropdown-menu {
        min-width: 250px;
    }
}

@media (max-width: 576px) {
    .modern-dropdown-menu {
        min-width: 200px;
        max-width: 90vw;
    }
    
    .notifications-container {
        max-height: 200px;
    }
}

/* Smooth Transitions */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Loading States */
.modern-nav-btn.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Focus States */
.modern-nav-btn:focus,
.modern-user-btn:focus,
.modern-menu-btn:focus {
    outline: 2px solid rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
}

/* Custom Scrollbar for Notifications */
.notifications-container::-webkit-scrollbar {
    width: 6px;
}

.notifications-container::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
}

.notifications-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 3px;
}

.notifications-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8, #6b4190);
}
</style>

<!-- Modern Header JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fullscreen functionality
    const fullscreenBtn = document.getElementById('btnFullscreen');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error('Error attempting to enable fullscreen:', err);
                });
            } else {
                document.exitFullscreen().catch(err => {
                    console.error('Error attempting to exit fullscreen:', err);
                });
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target) && !event.target.matches('[data-toggle="dropdown"]')) {
                $(dropdown).dropdown('hide');
            }
        });
    });

    // Add loading states to action buttons
    document.querySelectorAll('.action-btn, .modern-dropdown-item').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.add('loading');
            setTimeout(() => {
                this.classList.remove('loading');
            }, 2000);
        });
    });

    // Smooth scroll for notification items
    document.querySelectorAll('.modern-notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                // Add a smooth transition effect
                this.style.opacity = '0.5';
                setTimeout(() => {
                    window.location.href = href;
                }, 150);
            }
        });
    });

    // Initialize tooltips
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Header scroll effect
    let lastScrollTop = 0;
    const header = document.querySelector('.modern-header');
    
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            header.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            header.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
});
</script>