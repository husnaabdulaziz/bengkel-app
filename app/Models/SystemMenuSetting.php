<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMenuSetting extends Model
{
    protected $fillable = ['menu_key', 'enabled'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean'];
    }

    public static function isEnabled(string $menuKey): bool
    {
        return static::where('menu_key', $menuKey)->value('enabled') ?? true;
    }
}