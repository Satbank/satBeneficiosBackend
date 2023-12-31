<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'email',
        'password',
        'users_id',
        'perfils_id'
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
        'password' => 'hashed',
    ];
   
    public function admin()
    {
        return $this->hasOne(Admin::class, 'users_id');
    }
    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
    public function prefeitura()
    {
        return $this->hasOne(Prefeitura::class);
    }
    public function comercio()
    {
        return $this->hasMany(Comercio::class, 'users_id',);
    }
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'users_id');
    }
}
