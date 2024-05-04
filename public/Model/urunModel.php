<?php
require_once '../../app/config/DB.php';
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class urunModel {
  private $db;

  public function __construct() {
    $this->db = getDbConnection();
  }

  public function tumUrunSayisi() {
    $sql = "SELECT COUNT(*) as toplam FROM urunler";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function tumUrunler($offset = 0, $limit = 6) {
    $sql = "SELECT * FROM urunler ORDER BY id DESC LIMIT $offset, $limit";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getir($urunId) {
    $query = "SELECT * FROM urunler WHERE id = :urunId";
    $statement = $this->db->prepare($query);
    $statement->bindParam(":urunId", $urunId);
    $statement->execute();
    $urun = $statement->fetch(PDO::FETCH_ASSOC);
    return $urun ? $urun : null;
  }
  public function silUrunVeGorseli($urunId) {
    try {
        // Veritabanından ürünü sil
      $sorgu = "DELETE FROM urunler WHERE id = :urunId";
      $stmt = $this->db->prepare($sorgu);
      $stmt->bindParam(':urunId', $urunId, PDO::PARAM_INT);
      $stmt->execute();

        // Ürün görselini klasörden sil
      $urun = $this->getir($urunId);
      if ($urun && isset($urun['foto'])) {
        $dosyaYolu = '../../app/assets/images/products/' . $urun['foto'];
        if (file_exists($dosyaYolu)) {
          if (!unlink($dosyaYolu)) {
            throw new Exception("Görsel silinemedi: $dosyaYolu");
          }
        }
      }

      return true;
    } catch (PDOException $e) {
        // Veritabanı hatası
      error_log("Ürün silinirken veritabanı hatası oluştu: " . $e->getMessage());
      return false;
    } catch (Exception $ex) {
        // Görsel silme hatası
      error_log("Ürün görseli silinirken hata oluştu: " . $ex->getMessage());
      return false;
    }
  }



}
?>
