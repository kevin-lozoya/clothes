<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sets extends Model {

  protected $table = 'sets';
  protected $fillable = ['user_id', 'description'];
  
}

?>