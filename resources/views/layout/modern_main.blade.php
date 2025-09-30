<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="icon" type="image/png" href="{{asset('/images/logo/'.$general_settings->site_logo)}}"/>
    <title>{{$general_settings->site_title ?? "NO Title"}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}"></noscript>

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') }}"></noscript>

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}"></noscript>

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/bootstrap-datepicker.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/bootstrap-datepicker.min.css') }}"></noscript>

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/jquery-clockpicker/bootstrap-clockpicker.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/jquery-clockpicker/bootstrap-clockpicker.min.css') }}"></noscript>

    <!-- Boostrap Tag Inputs-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/Tag_input/tagsinput.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/Tag_input/tagsinput.css') }}"></noscript>

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/bootstrap-select.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/bootstrap/css/bootstrap-select.min.css') }}"></noscript>

    <!-- Font Awesome CSS-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}"></noscript>

    <!-- Dripicons icon font-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/dripicons/webfont.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/dripicons/webfont.css') }}"></noscript>

    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700">

    <!-- jQuery Circle-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('css/grasp_mobile_progress_circle-1.0.0.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('css/grasp_mobile_progress_circle-1.0.0.min.css') }}"></noscript>

    <!-- Custom Scrollbar-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}"></noscript>

    <!-- date range stylesheet-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/daterange/css/daterangepicker.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/daterange/css/daterangepicker.min.css') }}"></noscript>

    <!-- datatable stylesheet start-->
    @if (isset($isDataTableExist) && $isDataTableExist)
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/dataTables.bootstrap4.min.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/dataTables.bootstrap4.min.css') }}"></noscript>

        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/buttons.bootstrap4.min.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/buttons.bootstrap4.min.css') }}"></noscript>

        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/select.bootstrap4.min.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/select.bootstrap4.min.css') }}"></noscript>

        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/dataTables.checkboxes.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/dataTables.checkboxes.css') }}"></noscript>

        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/datatables.flexheader.boostrap.min.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/datatables.flexheader.boostrap.min.css') }}"></noscript>

        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/datatable.responsive.boostrap.min.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/datatable/datatable.responsive.boostrap.min.css') }}"></noscript>
    @endif
    <!-- datatable stylesheet End-->

    @stack('css')

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/select2/dist/css/select2.min.css') }}"></noscript>

    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/RangeSlider/ion.rangeSlider.min.css') }}">
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/RangeSlider/ion.rangeSlider.min.css') }}"></noscript>

    <!-- theme stylesheet-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('css/style.default.css') }}" id="theme-stylesheet" >
    <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('css/style.default.css') }}" id="theme-stylesheet" ></noscript>

    @if (env('RTL_LAYOUT')!=NULL)
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/bootstrap/css/bootstrap-rtl.min.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('vendor/bootstrap/css/bootstrap-rtl.min.css') }}"></noscript>

        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('css/custom-rtl.css') }}">
        <noscript><link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"  href="{{ asset('css/custom-rtl.css') }}"></noscript>
    @endif

    @if((request()->is('admin/dashboard*')) || (request()->is('calendar*')) )
        @include('calendarable.css')
    @endif

</head>

<body class="modern-body">
<div id="loader"></div>

<!-- Modern HRMS Layout -->
<div class="modern-hrms-container">
    @include('layout.modern_header')
    @include('layout.modern_sidebar')
    
    <!-- Main Content Area -->
    <main class="modern-content" id="content">
        <div class="content-wrapper animate-bottom d-none">
            @yield('content')
            @include('layout.main_partials.footer')
        </div>
    </main>
</div>

<!-- Modern Layout Styles -->
<style>
/* Modern HRMS Container */
.modern-body {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

.modern-hrms-container {
    display: grid;
    grid-template-areas: 
        "header header"
        "sidebar content";
    grid-template-columns: 280px 1fr;
    grid-template-rows: auto 1fr;
    min-height: 100vh;
    transition: grid-template-columns 0.3s ease;
}

/* Content Area */
.modern-content {
    grid-area: content;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: calc(100vh - 70px);
    border-radius: 20px 0 0 0;
    margin-top: -1px;
    padding: 20px;
    box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    overflow-y: auto;
    position: relative;
}

.content-wrapper {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 30px;
    min-height: calc(100vh - 140px);
    position: relative;
    overflow: hidden;
}

.content-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
}

/* Sidebar Toggle States */
.modern-hrms-container.sidebar-collapsed {
    grid-template-columns: 80px 1fr;
}

.modern-hrms-container.sidebar-hidden {
    grid-template-columns: 0 1fr;
}

/* Content Animation */
.animate-bottom {
    position: relative;
    animation: animatebottom 0.4s ease-out;
}

@keyframes animatebottom {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced Scrollbar */
.modern-content::-webkit-scrollbar {
    width: 8px;
}

.modern-content::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.modern-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 10px;
}

.modern-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8, #6b4190);
}

/* Card Enhancements */
.card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    border: none;
    font-weight: 600;
}

/* Button Enhancements */
.btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 1px solid rgba(102, 126, 234, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: 1px solid rgba(17, 153, 142, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffecb3 100%);
    border: 1px solid rgba(255, 193, 7, 0.3);
    color: #212529;
}

.btn-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    border: 1px solid rgba(255, 107, 107, 0.3);
}

/* Form Enhancements */
.form-control {
    border-radius: 10px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: rgba(255, 255, 255, 0.95);
    transform: translateY(-1px);
}

.form-group label {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 8px;
}

/* Table Enhancements */
.table {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 15px;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateX(5px);
}

/* Modal Enhancements */
.modal-content {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    border: none;
}

/* Alert Enhancements */
.alert {
    border-radius: 15px;
    border: none;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background: linear-gradient(135deg, rgba(17, 153, 142, 0.9) 0%, rgba(56, 239, 125, 0.9) 100%);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.9) 0%, rgba(238, 90, 82, 0.9) 100%);
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.9) 0%, rgba(255, 236, 179, 0.9) 100%);
    color: #212529;
}

.alert-info {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
    color: white;
}

/* Loading Spinner Enhancement */
#loader {
    position: fixed;
    left: 50%;
    top: 50%;
    z-index: 9999;
    width: 80px;
    height: 80px;
    margin: -40px 0 0 -40px;
    border: 8px solid rgba(102, 126, 234, 0.2);
    border-radius: 50%;
    border-top: 8px solid #667eea;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Mobile Sidebar Styles */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.sidebar-overlay.show {
    display: block;
}

.sidebar-open {
    overflow: hidden;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .modern-hrms-container {
        grid-template-areas: 
            "header"
            "content";
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr;
    }
    
    .modern-sidebar {
        transform: translateX(-100%);
        z-index: 1001;
    }
    
    .modern-sidebar.show {
        transform: translateX(0);
    }
    
    .modern-content {
        border-radius: 0;
        margin-top: 0;
        padding: 15px;
    }
    
    .content-wrapper {
        padding: 20px;
    }
}

@media (max-width: 768px) {
    .modern-content {
        padding: 10px;
    }
    
    .content-wrapper {
        padding: 15px;
        border-radius: 10px;
    }
}

@media (max-width: 576px) {
    .content-wrapper {
        padding: 10px;
        margin: 5px;
        border-radius: 10px;
    }
}

/* Print Styles */
@media print {
    .modern-header,
    .modern-sidebar {
        display: none !important;
    }
    
    .modern-hrms-container {
        display: block;
    }
    
    .modern-content {
        padding: 0;
        background: white;
        box-shadow: none;
    }
    
    .content-wrapper {
        background: white;
        box-shadow: none;
        border: none;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .modern-body {
        background: #f8f9fa;
    }
    
    .content-wrapper {
        background: white;
        border: 2px solid #dee2e6;
    }
    
    .card {
        background: white;
        border: 1px solid #dee2e6;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

/* Focus Styles for Accessibility */
*:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Custom Focus for Interactive Elements */
.btn:focus,
.form-control:focus,
.modern-nav-btn:focus {
    outline: 2px solid rgba(102, 126, 234, 0.5);
    outline-offset: 2px;
}
</style>

{{-- All JavaScript includes remain the same --}}
<script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery-clockpicker/bootstrap-clockpicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/popper.js/umd/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap/js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/charts-custom.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/front.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/daterange/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/daterange/js/knockout-3.4.2.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/daterange/js/daterangepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>

<!-- JS for Boostrap Tag Inputs-->
<script type="text/javascript" src="{{ asset('vendor/Tag_input/tagsinput.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/RangeSlider/ion.rangeSlider.min.js') }}"></script>

<!-- datatable Script Start-->
@if (isset($isDataTableExist) && $isDataTableExist)

    @if(Config::get('app.locale') == 'Arabic')
        <script type="text/javascript" src="{{ asset('vendor/datatable/pdfmake_arabic.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/datatable/vfs_fonts_arabic.js') }}"></script>
    @else
        <script type="text/javascript" src="{{ asset('vendor/datatable/pdfmake.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/datatable/vfs_fonts.js') }}"></script>
    @endif

    <script type="text/javascript" src="{{ asset('vendor/datatable/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/buttons.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/buttons.print.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/dataTables.select.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/sum().js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/dataTables.checkboxes.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatable.fixedheader.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatable.responsive.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatable.responsive.boostrap.min.js') }}"></script>
@endif
<!-- datatable Script End-->

<script type="text/javascript" src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if((request()->is('admin/dashboard*')) || (request()->is('calendar*')) )
    @include('calendarable.js')
@endif

<!-- Modern Layout JavaScript -->
<script type="text/javascript">
(function ($) {
    "use strict";

    $(document).ready(function () {
        // Initialize modern layout
        initModernLayout();
        
        // Existing functionality
        if (window.location.href.indexOf('#formModal') != -1) {
            $('#formModal').modal('show');
        }
        else if(window.location.href.indexOf('#createModal') != -1) {
            $('#createModal').modal('show');
        }

        // Hide loader after content loads
        setTimeout(function() {
            $('#loader').fadeOut();
            $('.animate-bottom').removeClass('d-none');
        }, 500);
    });

    // Modern layout initialization
    function initModernLayout() {
        // Add modern classes to existing elements
        $('.page').addClass('modern-page');
        
        // Enhanced sidebar toggle functionality
        $(document).on('click', '#toggle-btn', function(e) {
            e.preventDefault();
            
            if (window.innerWidth <= 1024) {
                // Mobile behavior - show/hide sidebar
                $('.modern-sidebar').toggleClass('show');
                $('.sidebar-overlay').toggleClass('show');
                $('body').toggleClass('sidebar-open');
            } else {
                // Desktop behavior - collapse/expand sidebar
                $('.modern-sidebar').toggleClass('sidebar-collapsed');
                $('.modern-hrms-container').toggleClass('sidebar-collapsed');
                
                // Store sidebar state
                localStorage.setItem('sidebar-collapsed', $('.modern-sidebar').hasClass('sidebar-collapsed'));
            }
        });

        // Restore sidebar state
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            $('.modern-sidebar').addClass('sidebar-collapsed');
            $('.modern-hrms-container').addClass('sidebar-collapsed');
        }

        // Mobile sidebar handling
        if (window.innerWidth <= 1024) {
            $('.modern-sidebar').addClass('sidebar-hidden');
            $('.modern-hrms-container').addClass('sidebar-hidden');
        }

        // Window resize handler
        $(window).resize(function() {
            if (window.innerWidth <= 1024) {
                $('.modern-sidebar').addClass('sidebar-hidden');
                $('.modern-hrms-container').addClass('sidebar-hidden');
            } else {
                $('.modern-sidebar').removeClass('sidebar-hidden');
                $('.modern-hrms-container').removeClass('sidebar-hidden');
            }
        });

        // Enhanced card animations
        $('.card').hover(
            function() { $(this).addClass('card-hover'); },
            function() { $(this).removeClass('card-hover'); }
        );

        // Smooth form focus transitions
        $('.form-control').on('focus blur', function() {
            $(this).closest('.form-group').toggleClass('focused');
        });

        // Enhanced button interactions
        $('.btn').on('click', function() {
            $(this).addClass('btn-clicked');
            setTimeout(() => {
                $(this).removeClass('btn-clicked');
            }, 150);
        });
    }

    // Existing functionality
    $('#empty_database').on('click', function () {
        if (confirm('{{__('Delete Selection',['key'=>__('Empty Database')])}}')) {
            let url = '{{route('empty_database')}}';
            document.location.href = url;
        }
    });

    $('#notify-btn').on('click', function () {
        $.ajax({
            url: '{{route('markAsRead')}}',
            dataType: "json",
            success: function (result) {
                // Handle notification read
            },
        });
    });

    // Enhanced loading states for AJAX
    $(document).ajaxStart(function() {
        $('body').addClass('loading');
    }).ajaxStop(function() {
        $('body').removeClass('loading');
    });

    // Add enhanced interactions for tables
    $('.table tbody tr').hover(
        function() { $(this).addClass('table-row-hover'); },
        function() { $(this).removeClass('table-row-hover'); }
    );

    // Modern notification system
    function showModernNotification(message, type = 'info') {
        const notification = $(`
            <div class="modern-notification ${type}" style="display: none;">
                <div class="notification-content">
                    <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        notification.fadeIn().delay(3000).fadeOut(function() {
            $(this).remove();
        });
    }

    // Make function globally available
    window.showModernNotification = showModernNotification;

})(jQuery);
</script>

@stack('scripts')

</body>
</html>