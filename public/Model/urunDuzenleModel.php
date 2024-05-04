<?php
require_once '../../app/config/DB.php';

class urunDuzenleModel {
  private $db;

  public function __construct($db) {
    $this->db = $db;
  }

  public function urunBilgisiGetir($id) {
    $query = "SELECT * FROM urunler WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function urunBilgileriGuncelle($id, $kategori_id, $isim, $gram) {
    $query = "UPDATE urunler SET kategori_id = :kategori_id, isim = :isim, gram = :gram WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":kategori_id", $kategori_id);
    $stmt->bindParam(":isim", $isim);
    $stmt->bindParam(":gram", $gram);
    return $stmt->execute();
  }

  public function urunGorselGuncelle($id, $foto) {
    $query = "UPDATE urunler SET foto = :foto WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":foto", $foto);
    return $stmt->execute();
  }
}
?>
