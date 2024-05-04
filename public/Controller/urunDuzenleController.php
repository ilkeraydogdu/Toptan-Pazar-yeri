<?php
require_once '../../app/config/DB.php';
require_once '../Model/urunDuzenleModel.php';

class urunDuzenleController {
  private $model;

  public function __construct($db) {
    $this->model = new urunDuzenleModel($db);
  }

  public function urunBilgileriGuncelle($id, $kategori_id, $isim, $gram) {
    return $this->model->urunBilgileriGuncelle($id, $kategori_id, $isim, $gram);
  }

  public function urunGorselGuncelle($id, $foto) {
    return $this->model->urunGorselGuncelle($id, $foto);
  }

  public function urunBilgisiGetir($id) {
    return $this->model->urunBilgisiGetir($id);
  }
}
?>
