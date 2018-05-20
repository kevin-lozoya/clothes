<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

  protected $table = 'product';
  protected $fillable = ['category_id', 'subcategory_id', 'style_id', 'brand_id', 'color_id', 'name', 'image'];

  public static function allJoin() {
    $products = self::join('category', 'category.id', '=', 'product.category_id')
                    ->join('subcategory', 'subcategory.id', '=', 'product.subcategory_id')
                    ->join('style', 'style.id', '=', 'product.style_id')
                    ->join('brand', 'brand.id', '=', 'product.brand_id')
                    ->join('color', 'color.id', '=', 'product.color_id')
                    ->select('product.*', 'category.description as category_desc', 'subcategory.description as subcategory_desc', 'style.description as style_desc', 'brand.description as brand_desc', 'color.description as color_desc')
                    ->get();

    return $products;
  }

  public static function byIdJoin($id) {
    $product = self::join('category', 'category.id', '=', 'product.category_id')
                    ->join('subcategory', 'subcategory.id', '=', 'product.subcategory_id')
                    ->join('style', 'style.id', '=', 'product.style_id')
                    ->join('brand', 'brand.id', '=', 'product.brand_id')
                    ->join('color', 'color.id', '=', 'product.color_id')
                    ->select('product.*', 'category.description as category_desc', 'subcategory.description as subcategory_desc', 'style.description as style_desc', 'brand.description as brand_desc', 'color.description as color_desc')
                    ->where('product.id', $id)
                    ->get();

    return $product;
  }

}
?>