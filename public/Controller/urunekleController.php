<?php
require_once '../Model/urunekleModel.php';

class urunekleController {
    public $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new UrunEkleModel($this->db);
    }

    public function index() {
        // Kategorileri al ve view dosyasına gönder
        $kategoriler = $this->model->getKategoriler();
        require_once '../Views/urunekle.php';
    }

    public function urunEkle($kategoriId, $isim, $gram, $foto) {
        // Formdan gelen verileri alıp modele iletebilir ve ekleme işlemini gerçekleştirebilirsiniz.
        $ekleSonucu = $this->model->urunEkle($kategoriId, $isim, $gram, $foto);
        if ($ekleSonucu) {
            echo '<script>setTimeout(function() { window.location.href = "../Views/urunler.php"; }, 50);</script>';
        } else {
            echo 'Ürün eklenirken bir hata oluştu.';
        }
    }
}
?>
