<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
  protected $table = 'user';
  protected $fillable = ['username', 'email', 'password', 'token', 'registration', 'reset_password'];
}
?>