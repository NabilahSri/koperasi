@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Activity Log</h4>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Aktivitas Hari Ini</h6>
                                        <h2 class="mb-0">{{ number_format($stats['today']) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Aktivitas Bulan Ini</h6>
                                        <h2 class="mb-0">{{ number_format($stats['month']) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">User Teraktif (Bulan Ini)</h6>
                                        <h4 class="mb-0 text-truncate">{{ $stats['top_user'] }}</h4>
                                        <small>{{ $stats['top_user_count'] }} aktivitas</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Aktivitas 7 Hari Terakhir</h5>
                                        <div id="activity-chart" style="min-height: 350px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ url('activity/logs') }}" method="get" class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <select name="user_id" class="form-select select2">
                                        <option value="">Semua User</option>
                                        @isset($users)
                                            @foreach ($users as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ isset($filters['user_id']) && $filters['user_id'] == $u->id ? 'selected' : '' }}>
                                                    {{ $u->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="action" class="form-select">
                                        <option value="">Semua Aksi</option>
                                        @php $actions = ['created','updated','deleted']; @endphp
                                        @foreach ($actions as $a)
                                            <option value="{{ $a }}"
                                                {{ isset($filters['action']) && $filters['action'] == $a ? 'selected' : '' }}>
                                                {{ ucfirst($a) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7 d-flex justify-content-end gap-2">
                                    <a href="{{ url('activity/logs') }}" class="btn btn-outline-secondary">Reset</a>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('activity.logs.export', request()->query()) }}"
                                        class="btn btn-success text-white">Export Excel</a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>User</th>
                                        <th>IP</th>
                                        <th>Device</th>
                                        <th>Method</th>
                                        {{-- <th>URL</th> --}}
                                        <th>Route</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                        <th>Objek</th>
                                        <th>Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td
                                                data-order="{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}">
                                                {{ \Carbon\Carbon::parse($log->created_at)->locale('id')->isoFormat('D MMM YYYY HH:mm') }}
                                            </td>
                                            <td>{{ $log->user ? $log->user->name : '-' }}</td>
                                            <td>{{ $log->ip_address }}</td>
                                            <td class="text-start">
                                                <span class="text-truncate d-inline-block" style="max-width:220px"
                                                    title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                            </td>
                                            <td>{{ $log->method }}</td>
                                            {{-- <td class="text-start">
                                                <span class="text-truncate d-inline-block" style="max-width:260px"
                                                    title="{{ $log->url }}">{{ $log->url }}</span>
                                            </td> --}}
                                            <td>{{ $log->route_name ?: '-' }}</td>
                                            <td>{{ $log->status_code ?: '-' }}</td>
                                            <td>
                                                @if ($log->action == 'created')
                                                    <span class="badge bg-success">Created</span>
                                                @elseif($log->action == 'updated')
                                                    <span class="badge bg-primary">Updated</span>
                                                @elseif($log->action == 'deleted')
                                                    <span class="badge bg-danger">Deleted</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->model_type)
                                                    @php
                                                        $modelName = class_basename($log->model_type);
                                                        // Map common models to friendly names if needed
                                                        $friendlyName = match ($modelName) {
                                                            'User' => 'Pengguna',
                                                            'TransaksiS' => 'Transaksi Simpanan',
                                                            'TransaksiT' => 'Transaksi Pinjaman',
                                                            default => $modelName,
                                                        };
                                                    @endphp
                                                    {{ $friendlyName }} <small
                                                        class="text-muted">#{{ $log->model_id }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                @php $count = is_array($log->changes) ? count($log->changes) : 0; @endphp
                                                @if ($count > 0)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary btn-view-changes"
                                                        data-bs-toggle="modal" data-bs-target="#changesModal"
                                                        data-action="{{ $log->action }}"
                                                        data-changes='{{ json_encode($log->changes, JSON_UNESCAPED_UNICODE) }}'>Detail
                                                        ({{ $count }})
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="changesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Detail Perubahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="bg-light p-3 rounded"><code id="jsonContent"></code></pre>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('plugins/apexchart/apexcharts.min.js') }}"></script>
    <script>
        // Activity Chart
        @if (isset($chartData))
            var options = {
                series: [{
                    name: 'Aktivitas',
                    data: @json($chartData['data'])
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: @json($chartData['categories']),
                    tooltip: {
                        enabled: false
                    }
                },
                colors: ['#435ebe'], // Primary color matches theme usually
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100]
                    }
                },
            };
            var chart = new ApexCharts(document.querySelector("#activity-chart"), options);
            chart.render();
        @endif

        $(document).on('click', '.btn-view-changes', function() {
            var data = $(this).attr('data-changes');
            try {
                var obj = JSON.parse(data);
                $('#jsonContent').text(JSON.stringify(obj, null, 4));
            } catch (e) {
                $('#jsonContent').text(data);
            }
        });
        $(function() {
            var dt = $('#table-6').DataTable();
            dt.order([0, 'desc']).draw();
        });
    </script>
@endsection
