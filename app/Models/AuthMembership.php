<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthMembership extends Model {

  protected $table = 'auth_membership';
  public $timestamps = false;
  protected $fillable = ['user_id', 'group_id'];

}
?>