<?php
session_start();
require_once '../Controller/siparisController.php';
require_once '../Model/siparisModel.php';

if (isset($_POST['siparis_ver'])) {
    $kullaniciId = $_SESSION['id']; 
    $urunIdler = $_POST['urun_id'];
    $adetler = $_POST['adet']; 
    $gramlar = $_POST['gram']; 
    $note = $_POST['note'];

    // Sipariş numarasını oluştur
    $siparisController = new siparisController();
    $siparisNumarasi = $siparisController->generateSiparisNumarasi();
    $siparisController = new siparisController();

    for ($i = 0; $i < count($urunIdler); $i++) {
        $urunId = $urunIdler[$i];
        $adet = $adetler[$i];
        $gram = $gramlar[$i];

    // Siparişi oluştur
        $siparisController->siparisOlustur($kullaniciId, $urunId, $adet, $gram, $note, $siparisNumarasi);

    // Sepetten ürünü çıkar
        unset($_SESSION['sepet'][$urunId]);
    }
    
    // İşlem tamamlandıktan sonra kullanıcıyı istediğiniz sayfaya yönlendir
    header("Location: siparislerim.php");
    exit;
} else {
    header("Location: sepet.php"); // Form gönderilmemişse sepet sayfasına yönlendir
    exit;
}
?>
