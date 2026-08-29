<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'message', 'target_role', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function views() { return $this->hasMany(AnnouncementView::class); }

    /** Ambil pengumuman aktif untuk role tertentu yang BELUM dilihat user hari ini */
    public static function getUnseenForUser(User $user)
    {
        if ($user->is_super_admin) {
            return collect();
        }

        $roleName = $user->getRoleNames()->first();

        $announcements = static::where('is_active', true)
            ->where(function ($q) use ($roleName) {
                $q->where('target_role', 'all');
                if ($roleName) {
                    $q->orWhere('target_role', $roleName);
                }
            })
            ->get();

        $today = now()->toDateString();
        $seenIds = AnnouncementView::where('user_id', $user->id)
            ->where('viewed_date', $today)
            ->pluck('announcement_id');

        return $announcements->whereNotIn('id', $seenIds);
    }
}