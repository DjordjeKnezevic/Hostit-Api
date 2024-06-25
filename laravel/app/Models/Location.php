<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'network_zone', 'city', 'image'];

    protected $hidden = ['created_at', 'updated_at'];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }
}
