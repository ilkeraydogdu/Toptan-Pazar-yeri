<?php
class urunekleModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getKategoriler() {
        $query = "SELECT id, kategori_adi, ust_kategori FROM kategoriler";
        $statement = $this->db->prepare($query);
        $statement->execute();
        $kategoriler = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $kategoriler;
    }

    public function urunEkle($kategoriId, $isim, $gram, $foto) {
        $eklemeSorgusu = "INSERT INTO urunler (kategori_id, isim, gram, foto) VALUES (:kategori_id, :isim, :gram, :foto)";
        $statement = $this->db->prepare($eklemeSorgusu);
        $statement->bindParam(':kategori_id', $kategoriId);
        $statement->bindParam(':isim', $isim);
        $statement->bindParam(':gram', $gram);
        $dosyaAdi = strtolower(str_replace([' ', 'Ç', 'ç', 'Ğ', 'ğ', 'I', 'ı', 'İ', 'i', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü'], ['', 'c', 'c', 'g', 'g', 'i', 'i', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u'], $isim)) . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);

        $statement->bindParam(':foto', $dosyaAdi);
        $uploadsDirectory = '../../app/assets/images/products/';
        $uploadFilePath = $uploadsDirectory . $dosyaAdi;
        move_uploaded_file($foto['tmp_name'], $uploadFilePath);
        return $statement->execute();
    }
}
?>
