<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Style extends Model {

  protected $table = 'style';
  public $timestamps = false;
  protected $fillable = ['description'];
  
}

?>