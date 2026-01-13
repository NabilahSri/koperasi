<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivityLogsExport;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        $logs = $query->limit(2000)->get();

        $users = User::orderBy('name')->get(['id', 'name']);

        // Statistik Ringkas
        $todayCount = ActivityLog::whereDate('created_at', today())->count();
        $monthCount = ActivityLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $topUser = ActivityLog::select('user_id')
            ->selectRaw('count(*) as total')
            ->whereMonth('created_at', now()->month)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user:id,name')
            ->first();

        // Data Grafik 7 Hari Terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = ActivityLog::whereDate('created_at', $date)->count();
            $chartData['categories'][] = now()->subDays($i)->locale('id')->isoFormat('dddd');
            $chartData['data'][] = $count;
        }

        return view('pages.activity_logs', [
            'logs' => $logs,
            'users' => $users,
            'stats' => [
                'today' => $todayCount,
                'month' => $monthCount,
                'top_user' => $topUser ? $topUser->user->name : '-',
                'top_user_count' => $topUser ? $topUser->total : 0,
            ],
            'chartData' => $chartData,
            'filters' => [
                'user_id' => $request->input('user_id'),
                'action' => $request->input('action'),
            ],
        ]);
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        $logs = $query->limit(10000)->get();
        $export = new ActivityLogsExport($logs);
        return Excel::download($export, 'activity_logs.xlsx');
    }
}
