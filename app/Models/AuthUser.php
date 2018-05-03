<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthUser extends Model {

  protected $table = 'auth_user';
  protected $fillable = ['username', 'email', 'password', 'registration_key', 'reset_password_key'];

}
?>