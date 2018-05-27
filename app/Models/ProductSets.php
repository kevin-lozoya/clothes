<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSets extends Model {

  protected $table = 'product_sets';
  public $timestamps = false;
  protected $fillable = ['sets_id', 'product_id'];
  
}

?>