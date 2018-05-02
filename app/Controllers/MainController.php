<?php
namespace App\Controllers;

use App\Template;

class MainController extends Template {
  
  public function getIndex() {
    return $this->render('main/index.twig');
  }
}
?>