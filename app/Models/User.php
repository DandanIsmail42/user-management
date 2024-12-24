<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  use HasFactory;

  protected $fillable = ['email', 'password', 'name', 'active'];

  public function orders()
  {
    return $this->hasMany(Order::class);
  }
}
