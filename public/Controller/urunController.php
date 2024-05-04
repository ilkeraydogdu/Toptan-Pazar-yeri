<?php
require_once '../Model/urunModel.php';
class urunController {
    private $urunModel;
    private $limit = 9;
    public function __construct() {
        $this->urunModel = new urunModel();
    }
    public function listele() {
        $sayfa = (isset($_GET['sayfa'])) ? $_GET['sayfa'] : 1;
        $offset = ($sayfa - 1) * $this->limit; // Limit of 6 products per pageßß
        $urunler = $this->urunModel->tumUrunler($offset, $this->limit);
        $toplamUrunSayisi = $this->urunModel->tumUrunSayisi();
        $toplamSayfa = ceil($toplamUrunSayisi / $this->limit);
        return array($urunler, $toplamSayfa, $sayfa);
    }

    public function detay($urunId) {
        return $this->urunModel->getir($urunId);
    }

    public function urunBilgileriniCek($urunId) {
        return $this->urunModel->getir($urunId);
    }
    
}
?>
