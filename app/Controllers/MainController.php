<?php
namespace App\Controllers;

use App\Modules\Log;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Style;
use App\Models\Closet;
use App\Models\Product;
use App\Models\Category;
use App\Modules\Template;
use App\Models\Subcategory;
use Sirius\Validation\Validator;
use Illuminate\Database\QueryException;


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

  public function postAddproduct() {
    $status = 'ERROR';
    $validator = new Validator();
    $validator->add('id:Product_Id', 'required');
    $validator->add('id:Product_Id', 'integer');

    if ($validator->validate($_POST)) {
      try {
        Closet::create([
          'user_id' => $_SESSION['user']['id'],
          'product_id' => $_POST['id'],
        ]);
        $status = 'OK';
      } catch (QueryException $e) {
        Log::logError($e->getMessage());
      }
    }
    
    header('Type-Content: application/json');
    echo json_encode(['status' => $status]);
  }
}
?>