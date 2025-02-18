<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'short_name',
        'cpf',
        'cell_phone',
        'email',
        'password',
        'confirmation_token',
        'cell_confirmed',
        'postal_code',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'country'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermisson(string $permission)
    {
        return $this->permissions()->where('name', $permission)->first() ? true : false;
    }

    public function isSuperAdmin()
    {
        return in_array($this->email, config('acl.admins'));
    }
}
