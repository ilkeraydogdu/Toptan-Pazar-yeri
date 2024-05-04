<?php
require_once '../Model/kullaniciDuzenleModel.php';

class KullaniciDuzenleController {
  private $kullaniciDuzenleModel;

  public function __construct() {
    $this->kullaniciDuzenleModel = new KullaniciDuzenleModel();
  }

  public function kullaniciBilgisiGetir($id) {
    return $this->kullaniciDuzenleModel->kullaniciBilgisiGetir($id);
  }

  public function kullaniciBilgileriGuncelle($id, $firma, $email, $tel, $adres, $durum) {
    return $this->kullaniciDuzenleModel->kullaniciBilgileriGuncelle($id, $firma, $email, $tel, $adres, $durum);
  }

  public function kullaniciOnayla($id) {
    $success = $this->kullaniciDuzenleModel->kullaniciOnayla($id);
    return $success;
  }

  public function sifreGuncelle($id, $sifre) {
    return $this->kullaniciDuzenleModel->sifreGuncelle($id, $sifre);
  }
}
?>
