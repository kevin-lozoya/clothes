<?php
namespace App\Controllers\User;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Style;
use App\Models\Closet;
use App\Models\Category;
use App\Modules\Template;
use App\Models\Subcategory;

class ClosetController extends Template {

  public function getIndex() {
    $products = Closet::join('product', 'product.id', '=', 'closet.product_id')
                      ->where('user_id', $_SESSION['user']['id'])
                      ->select('product.*')->get();
    $brands = Closet::join('product', 'product.id', '=', 'closet.product_id')
                    ->join('brand', 'brand.id', '=', 'product.brand_id')
                    ->where('closet.user_id', $_SESSION['user']['id'])
                    ->select('brand.*')->orderBy('description', 'asc')
                    ->distinct()->get();

    $categories = Closet::join('product', 'product.id', '=', 'closet.product_id')
                        ->join('category', 'category.id', '=', 'product.category_id')
                        ->where('closet.user_id', $_SESSION['user']['id'])
                        ->select('category.*')->orderBy('description', 'asc')
                        ->distinct()->get();
    $subcategories = Closet::join('product', 'product.id', '=', 'closet.product_id')
                          ->join('subcategory', 'subcategory.id', '=', 'product.subcategory_id')
                          ->where('closet.user_id', $_SESSION['user']['id'])
                          ->select('subcategory.*')->orderBy('description', 'asc')
                          ->distinct()->get();
    $styles = Closet::join('product', 'product.id', '=', 'closet.product_id')
                    ->join('style', 'style.id', '=', 'product.style_id')
                    ->where('closet.user_id', $_SESSION['user']['id'])
                    ->select('style.*')->orderBy('description', 'asc')
                    ->distinct()->get();
    $colors = Closet::join('product', 'product.id', '=', 'closet.product_id')
                    ->join('color', 'color.id', '=', 'product.color_id')
                    ->where('closet.user_id', $_SESSION['user']['id'])
                    ->select('color.*')->orderBy('description', 'asc')
                    ->distinct()->get();

    return $this->render('user/closet/index.twig', [
      'products' => json_encode($products),
      'brands' => $brands,
      'categories' => $categories,
      'subcategories' => json_encode($subcategories),
      'styles' => $styles,
      'colors' => $colors,
    ]);
  }
  
}

?>