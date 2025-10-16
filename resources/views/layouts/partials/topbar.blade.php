<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notification Bell for Low Stock -->
        @role('admin')
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if(isset($lowStockSpareParts) && $lowStockSpareParts->count() > 0)
                    <span class="badge badge-danger navbar-badge">{{ $lowStockSpareParts->count() }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">Low Stock Notifications</span>
                <div class="dropdown-divider"></div>
                @if(isset($lowStockSpareParts) && $lowStockSpareParts->count() > 0)
                    @foreach($lowStockSpareParts as $sparePart)
                        <a href="{{ route('spare-parts.edit', $sparePart->id) }}" class="dropdown-item">
                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                            {{ $sparePart->name }} ({{ $sparePart->quantity }})
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                @else
                    <span class="dropdown-item">No low stock items.</span>
                @endif
            </div>
        </li>
        @endrole
        <!-- User Dropdown Menu Simple -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image" style="width: 32px; height: 32px;">
                <span class="ml-2">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

@push('scripts')
<script>
function updateLowStockNotifications() {
    $.getJSON("{{ route('api.low-stock-notifications') }}", function(data) {
        var bell = $(".nav-item.dropdown .fa-bell").closest('li');
        var badge = bell.find('.navbar-badge');
        var dropdown = bell.find('.dropdown-menu');
        // Update badge
        if (data.count > 0) {
            if (badge.length === 0) {
                bell.find('.nav-link').append('<span class="badge badge-danger navbar-badge">'+data.count+'</span>');
            } else {
                badge.text(data.count);
            }
        } else {
            badge.remove();
        }
        // Update dropdown
        var html = '<span class="dropdown-header">Low Stock Notifications</span><div class="dropdown-divider"></div>';
        if (data.count > 0) {
            data.items.forEach(function(item) {
                html += '<a href="'+item.edit_url+'" class="dropdown-item">';
                html += '<i class="fas fa-exclamation-triangle text-warning mr-2"></i>';
                html += item.name + ' (' + item.quantity + ')';
                html += '</a><div class="dropdown-divider"></div>';
            });
        } else {
            html += '<span class="dropdown-item">No low stock items.</span>';
        }
        dropdown.html(html);
    });
}
setInterval(updateLowStockNotifications, 15000);
$(document).ready(function() {
    updateLowStockNotifications();
});
</script>
@endpush 