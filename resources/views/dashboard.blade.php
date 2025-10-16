@extends('layouts.page')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('page_content')
    <div class="row">
        @can('user-list')
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $userCount }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endcan
        @can('role-list')
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $machineReportCount }}</h3>
                    <p>Machine Reports</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <a href="{{ route('machine-reports.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endcan
        @can('action-list')
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $actionCount }}</h3>
                    <p>Actions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="{{ route('actions.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endcan
        @can('spare-part-list')
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $sparePartCount }}</h3>
                    <p>Spare Parts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <a href="{{ route('spare-parts.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endcan
        @can('role-list')
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $roleCount }}</h3>
                    <p>Roles</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="{{ route('roles.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endcan
    </div>
    <div class="row mt-4">
        @can('machine-report-list')
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Recent Machine Reports</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Machine Name</th>
                                <th>User</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMachineReports as $report)
                                <tr>
                                    <td>{{ $report->machine_name }}</td>
                                    <td>{{ $report->user ? $report->user->name : '-' }}</td>
                                    <td>{{ $report->report_date }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No recent reports.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        @can('spare-part-list')
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Lowest Stock Spare Parts</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowestStockSpareParts as $sparePart)
                                <tr @if($sparePart->quantity == 0) class="table-danger" @endif>
                                    <td>{{ $sparePart->name }}</td>
                                    <td><span class="badge {{ $sparePart->quantity == 0 ? 'badge-danger' : 'badge-warning' }}">{{ $sparePart->quantity }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No spare parts data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
    </div>
@endsection 