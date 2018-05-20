<?php
namespace App\Controllers\Admin;

use App\Modules\Log;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Style;
use App\Models\Product;
use App\Models\Category;
use App\Modules\Template;
use App\Models\Subcategory;
use Sirius\Validation\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Database\Capsule\Manager as Capsule;

class ProductsController extends Template {

  public function getIndex() {
    $products = Product::allJoin();

    return $this->render('admin/products/index.twig', [
      'products' => $products
    ]);
  }

  public function anyCreate() {
    $errors = null;
    $msgSuccess = null;
    $validator = new Validator();

    $validator->add('CreateProductForm', 'required');

    if ($validator->validate($_POST)) {
      $validator->add('n:Name', 'required');
      $validator->add('b:Brand', 'required');
      $validator->add('b:Brand', 'integer');
      $validator->add('c:Category', 'required');
      $validator->add('c:Category', 'integer');
      $validator->add('sc:Subcategory', 'required');
      $validator->add('sc:Subcategory', 'integer');
      $validator->add('s:Style', 'required');
      $validator->add('s:Style', 'integer');
      $validator->add('cl:Color', 'required');
      $validator->add('cl:Color', 'integer');
      $validator->add('i:Image', 'required');
      $validator->add('i:Image', 'url');

      if ($validator->validate($_POST)) {
        try {
          Product::create([
            'name' => $_POST['n'],
            'brand_id' => $_POST['b'],
            'category_id' => $_POST['c'],
            'subcategory_id' => $_POST['sc'],
            'style_id' => $_POST['s'],
            'color_id' => $_POST['cl'],
            'image' => $_POST['i'],
          ]);
          $msgSuccess = 'Product created';
        } catch (QueryException $e) {
          Log::logError($e->getMessage());
          $validator->addMessage('', 'This product already exists.');
        }
      }

      $errors = $validator->getMessages();
    }

    $brands = Brand::orderBy('description', 'asc')->get();
    $categories = Category::orderBy('description', 'asc')->get();
    $subcategories = Subcategory::orderBy('description', 'asc')->get();
    $styles = Style::orderBy('description', 'asc')->get();
    $colors = Color::orderBy('description', 'asc')->get();

    return $this->render('admin/products/create.twig', [
      'brands' => $brands,
      'categories' => $categories,
      'subcategories' => json_encode($subcategories),
      'styles' => $styles,
      'colors' => $colors,
      'errors' => $errors,
      'msgSuccess' => $msgSuccess,
    ]);
  }

  public function anyUpdate($id) {
    $errors = null;
    $msgSuccess = null;
    $validator = new Validator();

    $validator->add('UpdateProductForm', 'required');
    $validator->add('id:ID', 'required');
    $validator->add('id:ID', 'integer');

    if ($validator->validate($_POST)) {
      $validator->add('n:Name', 'required');
      $validator->add('b:Brand', 'required');
      $validator->add('b:Brand', 'integer');
      $validator->add('c:Category', 'required');
      $validator->add('c:Category', 'integer');
      $validator->add('sc:Subcategory', 'required');
      $validator->add('sc:Subcategory', 'integer');
      $validator->add('s:Style', 'required');
      $validator->add('s:Style', 'integer');
      $validator->add('cl:Color', 'required');
      $validator->add('cl:Color', 'integer');
      $validator->add('i:Image', 'required');
      $validator->add('i:Image', 'url');

      if ($validator->validate($_POST)) {
        try {
          Product::where('id', $_POST['id'])->update([
            'name' => $_POST['n'],
            'brand_id' => $_POST['b'],
            'category_id' => $_POST['c'],
            'subcategory_id' => $_POST['sc'],
            'style_id' => $_POST['s'],
            'color_id' => $_POST['cl'],
            'image' => $_POST['i'],
          ]);
          $msgSuccess = 'Product updated.';
        } catch (QueryException $e) {
          Log::logError($e->getMessage());
          $validator->addMessage('', 'Error updating.');
        }
      }

      $errors = $validator->getMessages();
    }

    $product = Product::where('id', $id)->first();
    $subcategoriesProduct = Subcategory::where('category_id', $product->category_id)->get();
    $brands = Brand::orderBy('description', 'asc')->get();
    $categories = Category::orderBy('description', 'asc')->get();
    $subcategories = Subcategory::orderBy('description', 'asc')->get();
    $styles = Style::orderBy('description', 'asc')->get();
    $colors = Color::orderBy('description', 'asc')->get();

    return $this->render('admin/products/update.twig', [
      'product' => $product,
      'subcategoriesProduct' => $subcategoriesProduct,
      'brands' => $brands,
      'categories' => $categories,
      'subcategories' => json_encode($subcategories),
      'styles' => $styles,
      'colors' => $colors,
      'errors' => $errors,
      'msgSuccess' => $msgSuccess,
    ]);
  }

  public function postDelete() {
    $validator = new Validator();
    $validator->add('id', 'required');
    $validator->add('id', 'integer');

    if ($validator->validate($_POST)) {
      try {
        Product::where('id', $_POST['id'])->delete();
      } catch (QueryException $e) {
        Log::logError($e->getMessage());
      }
    }

    header('Location: '.BASE_URL.'admin/products');
  }
  
}
?>