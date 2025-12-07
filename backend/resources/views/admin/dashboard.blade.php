@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('contents')
    <!-- Statistik Cards -->
    <div class="row">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            <small class="text-muted">{{ $totalAdmins }} Admin</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings }}</div>
                            <small class="text-muted">{{ $bookingsThisMonth }} bulan ini</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Destinations -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Destinations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDestinations }}</div>
                            <small class="text-muted">Paket Wisata</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }} bulan ini</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingBookings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedBookings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Cancelled
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $cancelledBookings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart dan Recent Bookings -->
    <div class="row">
        <!-- Chart Booking per Bulan -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Booking per Bulan (6 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    @if($bookingsPerMonth->count() > 0)
                        <div class="chart-area">
                            <canvas id="bookingChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada data booking untuk ditampilkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-gray-600">Booking Rate</span>
                            <span class="font-weight-bold">
                                {{ $totalBookings > 0 ? number_format(($bookingsThisMonth / $totalBookings) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                style="width: {{ $totalBookings > 0 ? ($bookingsThisMonth / $totalBookings) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-gray-600">Completion Rate</span>
                            <span class="font-weight-bold">
                                {{ $totalBookings > 0 ? number_format(($completedBookings / $totalBookings) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-gray-600">Average Revenue</span>
                            <span class="font-weight-bold">
                                Rp {{ $confirmedBookings + $completedBookings > 0 ? number_format($totalRevenue / ($confirmedBookings + $completedBookings), 0, ',', '.') : 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Kode Booking</th>
                                        <th>User</th>
                                        <th>Destination</th>
                                        <th>Tanggal Booking</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td><strong>{{ $booking->kode_booking }}</strong></td>
                                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->destination->nama ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}</td>
                                            <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge badge-secondary">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge badge-primary">Confirmed</span>
                                                @elseif($booking->status == 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @else
                                                    <span class="badge badge-info">{{ ucfirst($booking->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada booking</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@if($bookingsPerMonth->count() > 0)
<script>
    // Chart Booking per Bulan (Chart.js v2 syntax)
    var ctx = document.getElementById("bookingChart");
    var bookingChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach($bookingsPerMonth as $data)
                    '{{ \Carbon\Carbon::create($data->year, $data->month, 1)->format('M Y') }}',
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Booking',
                data: [
                    @foreach($bookingsPerMonth as $data)
                        {{ $data->total }},
                    @endforeach
                ],
                borderColor: 'rgb(30, 136, 229)',
                backgroundColor: 'rgba(30, 136, 229, 0.1)',
                lineTension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }]
            },
            legend: {
                display: true,
                position: 'top'
            }
        }
    });
</script>
@endif
@endpush
