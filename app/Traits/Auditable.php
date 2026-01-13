<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::logActivity($model, 'created', self::sanitize($model->getAttributes()));
        });

        static::updated(function (Model $model) {
            $ignored = ['updated_at', 'created_at', 'remember_token'];
            $dirty = collect($model->getDirty())
                ->reject(function ($v, $k) use ($ignored) { return in_array($k, $ignored, true); })
                ->map(function ($new, $key) use ($model) {
                    return ['from' => $model->getOriginal($key), 'to' => $new];
                })->all();
            if (!empty($dirty)) {
                self::logActivity($model, 'updated', $dirty);
            }
        });

        static::deleted(function (Model $model) {
            self::logActivity($model, 'deleted', self::sanitize($model->getOriginal()));
        });
    }

    protected static function logActivity(Model $model, string $action, array $changes)
    {
        try {
            ActivityLog::create([
                'user_id' => optional(auth()->user())->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'url' => request()->fullUrl(),
                'route_name' => optional(request()->route())->getName(),
                'status_code' => null,
                'request_data' => null,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'changes' => $changes,
                'message' => null,
            ]);
        } catch (\Throwable $e) {
        }
    }

    protected static function sanitize(array $attrs): array
    {
        $remove = ['password', 'remember_token'];
        foreach ($remove as $key) {
            if (array_key_exists($key, $attrs)) {
                unset($attrs[$key]);
            }
        }
        return $attrs;
    }
}

