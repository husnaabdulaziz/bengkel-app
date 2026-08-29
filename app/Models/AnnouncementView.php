<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementView extends Model
{
    protected $fillable = ['announcement_id', 'user_id', 'viewed_date'];

    protected function casts(): array
    {
        return ['viewed_date' => 'date'];
    }
}