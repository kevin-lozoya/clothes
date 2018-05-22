<?php
namespace App\Controllers;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Style;
use App\Models\Product;
use App\Models\Category;
use App\Modules\Template;
use App\Models\Subcategory;

class MainController extends Template {
  
  public function getIndex() {
    $products = Product::orderBy('id', 'desc')->get();
    $brands = Brand::orderBy('description', 'asc')->get();
    $categories = Category::orderBy('description', 'asc')->get();
    $subcategories = Subcategory::orderBy('description', 'asc')->get();
    $styles = Style::orderBy('description', 'asc')->get();
    $colors = Color::orderBy('description', 'asc')->get();

    return $this->render('main/index.twig', [
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