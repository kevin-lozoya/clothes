<?php
namespace App\Controllers\User;

use App\Models\Sets;
use App\Modules\Log;
use App\Models\Closet;
use App\Modules\Template;
use App\Models\ProductSets;
use Illuminate\Database\Capsule\Manager as Capsule;

class SetsController extends Template {

  public function getIndex() {
    $sets = Sets::where('user_id', $_SESSION['user']['id'])->get();
    $productsSets = [];

    foreach ($sets as $key => $value) {
      $productsSets[] = [
        'setId' => $value->id,
        'products' => ProductSets::join('product', 'product.id', '=', 'product_sets.product_id')
                                  ->where('product_sets.sets_id', $value->id)
                                  ->select('product.*')->get()
      ];
    }

    return $this->render('user/sets/index.twig', [
      'productsSets' => $productsSets
    ]);
  }

  public function getCreate() { 
    $products = Closet::join('product', 'product.id', '=', 'closet.product_id')
                      ->where('user_id', $_SESSION['user']['id'])
                      ->select('product.*')->get();

    return $this->render('user/sets/create.twig', [
      'products' => json_encode($products),
    ]);
  }

  public function postCreate() {
    $status = 'ERROR';
    $products = json_decode($_POST['productsSet']);

    try {
      Capsule::beginTransaction();
      $set = Sets::create([
        'user_id' => $_SESSION['user']['id']
      ]);
      foreach ($products as $key => $value) {
        ProductSets::create([
          'sets_id' => $set->id,
          'product_id' => $value
        ]);
      }
      Capsule::commit();
      $status = 'OK';
    } catch (QueryException $e) {
      Log::logError($e->getMessage());
    }

    echo json_encode(['status' => $status]);

  }

  public function postRemove() {
    $status = 'ERROR';

    try {
      Sets::where('id', $_POST['id'])->delete();
      $status = 'OK';
    } catch (QueryException $e) {
      Log::logError($e->getMessage());
    }

    echo json_encode(['status' => $status]);
  }
  
}

?>