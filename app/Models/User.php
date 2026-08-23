<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = ['company_id','name','inisial','email','phone','password','is_super_admin','status'];
    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_super_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function company()  { return $this->belongsTo(Company::class); }
    public function branches() { return $this->belongsToMany(Branch::class, 'user_branches'); }
    public function isSuperAdmin(): bool { return (bool) $this->is_super_admin; }
}