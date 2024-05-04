<?php
require_once '../Model/kullaniciModel.php';

class kullaniciController {

    private $kullaniciModel;

    public function __construct() {
        $this->kullaniciModel = new kullaniciModel;
    }

    public function listele($arananKelime = '') {
        $kullanicilar = $this->kullaniciModel->getKullanicilar($arananKelime);
        return $kullanicilar;
    }

    public function sil($id) {
        if ($this->kullaniciModel->silKullanici($id)) {
            echo '<script>window.location.href = "kullanici.php";</script>';
            exit;
        } else {
            die('Kullanıcı silme işlemi başarısız.');
        }
    }
     // Sipariş veren kullanıcıları listele
    public function siparisVerenKullanicilariListele() {
        return $this->kullaniciModel->siparisVerenKullanicilariGetir();
    }
    // EN SON Sipariş veren kullanıcıları listele
    public function enSonSiparisleriListele() {
        return $this->kullaniciModel->enSonSiparisleriListele();
    }
}
?>
