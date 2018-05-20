<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model {

  protected $table = 'subcategory';
  public $timestamps = false;
  protected $fillable = ['category_id', 'description'];
  
}

?>