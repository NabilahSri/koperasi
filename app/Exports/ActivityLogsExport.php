<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActivityLogsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Collection $logs;

    public function __construct(Collection $logs)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        return $this->logs->map(function ($log) {
            return [
                'Waktu' => optional($log->created_at)->format('Y-m-d H:i:s'),
                'User' => optional($log->user)->name,
                'IP' => $log->ip_address,
                'Device' => $log->user_agent,
                'Method' => $log->method,
                'URL' => $log->url,
                'Route' => $log->route_name,
                'Status' => $log->status_code,
                'Aksi' => $log->action,
                'Model' => $log->model_type ? class_basename($log->model_type) : null,
                'Model ID' => $log->model_id,
                'Perubahan' => $log->changes ? json_encode($log->changes, JSON_UNESCAPED_UNICODE) : null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'User',
            'IP',
            'Device',
            'Method',
            'URL',
            'Route',
            'Status',
            'Aksi',
            'Model',
            'Model ID',
            'Perubahan',
        ];
    }
}

