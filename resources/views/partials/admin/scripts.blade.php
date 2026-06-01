<script src="{{ asset('assets/admin/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/icons/feather-icon/feather-icon.js') }}"></script>
<script src="{{ asset('assets/admin/js/scrollbar/simplebar.js') }}"></script>
<script src="{{ asset('assets/admin/js/scrollbar/custom.js') }}"></script>
<script src="{{ asset('assets/admin/js/config.js') }}"></script>
<script src="{{ asset('assets/admin/js/tooltip-init.js') }}"></script>
<script src="{{ asset('assets/admin/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('assets/admin/js/notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/notify/index.js') }}"></script>
<script src="{{ asset('assets/admin/js/chart/apex-chart/apex-chart1.js') }}"></script>
<script src="{{ asset('assets/admin/js/chart/apex-chart/moment.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/chart/apex-chart/apex-chart.js') }}"></script>
<script src="{{ asset('assets/admin/js/chart/apex-chart/stock-prices.js') }}"></script>
<script>
    window.__adminRevenueChart = @json($revenueChart);
    window.__adminEarningChart = @json($earningChart);
    window.__adminVisitorsChart = @json($visitorsChart);
</script>
<script src="{{ asset('assets/admin/js/chart/apex-chart/chart-custom1.js') }}"></script>
<script src="{{ asset('assets/admin/js/admin-revenue-chart.js') }}"></script>
<script src="{{ asset('assets/admin/js/admin-earning-chart.js') }}"></script>
<script src="{{ asset('assets/admin/js/admin-visitors-chart.js') }}"></script>
<script src="{{ asset('assets/admin/js/admin-tareas.js') }}"></script>
<script src="{{ asset('assets/admin/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/custom-slick.js') }}"></script>
<script src="{{ asset('assets/admin/js/ratio.js') }}"></script>
<script src="{{ asset('assets/admin/js/sidebareffect.js') }}"></script>
<script src="{{ asset('assets/admin/js/script.js') }}"></script>

@stack('scripts')
