<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Closet extends Model {

  protected $table = 'closet';
  public $timestamps = false;
  protected $fillable = ['user_id', 'product_id'];
  
}

?>