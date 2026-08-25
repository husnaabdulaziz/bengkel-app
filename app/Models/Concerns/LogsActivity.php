<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Support\Str;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->recordActivity('create');
        });

        static::updated(function ($model) {
            $model->recordActivity('update');
        });

        static::deleted(function ($model) {
            $model->recordActivity('delete');
        });
    }

    public function recordActivity(string $action): void
    {
        if (!auth()->check()) {
            return;
        }

        $label = $this->nama ?? $this->name ?? $this->nama_toko ?? $this->kode_opname ?? $this->kode_transfer ?? $this->invoice_number ?? $this->id;
        $verb = ['create' => 'Menambahkan', 'update' => 'Mengubah', 'delete' => 'Menghapus'][$action] ?? $action;
        $modelName = Str::title(str_replace('_', ' ', Str::snake(class_basename($this))));

        ActivityLog::create([
            'company_id' => auth()->user()->company_id,
            'branch_id' => $this->branch_id ?? null,
            'user_id' => auth()->id(),
            'action' => $action . '_' . Str::snake(class_basename($this)),
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => "{$verb} {$modelName} \"{$label}\"",
            'ip_address' => request()->ip(),
        ]);
    }
}