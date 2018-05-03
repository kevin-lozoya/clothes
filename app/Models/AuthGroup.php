<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthGroup extends Model {

  protected $table = 'auth_group';
  public $timestamps = false;
  protected $fillable = ['role', 'description'];

}
?>