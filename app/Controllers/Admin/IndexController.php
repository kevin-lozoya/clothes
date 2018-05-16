<?php
namespace App\Controllers\Admin;

use App\Modules\Template;

class IndexController extends Template {

  public function getIndex() {
    return $this->render('admin/index.twig');
  }
  
}
?>