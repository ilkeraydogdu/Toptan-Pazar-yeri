<?php
require_once '../../app/config/DB.php';

class kullaniciModel {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function getKullanicilar($arananKelime = '') {
        $sql = 'SELECT * FROM kullanici';
        if (!empty($arananKelime)) {
            $sql .= " WHERE firma LIKE '%$arananKelime%'";
        }

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function silKullanici($id) {
        $stmt = $this->db->prepare('DELETE FROM kullanici WHERE id = :id');
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
     // Sipariş veren kullanıcıları getir
    public function siparisVerenKullanicilariGetir() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM kullanici WHERE id IN (SELECT DISTINCT kullanici_id FROM siparisler)");
            $stmt->execute();
            $kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $kullanicilar;
        } catch (PDOException $e) {
            echo "Kullanıcıları getirme hatası: " . $e->getMessage();
            return [];
        }
    }
    //Siparişler tablosundaki en son siparişler
    public function enSonSiparisleriListele() {
        try {
            $stmt = $this->db->prepare("SELECT s.siparisNumarasi, MAX(s.tarih) AS son_tarih, k.firma AS kullanici_adi, s.kullanici_id, s.durum as durum
                FROM siparisler s
                INNER JOIN kullanici k ON s.kullanici_id = k.id
                GROUP BY s.siparisNumarasi
                ORDER BY son_tarih DESC");
            $stmt->execute();
            $enSonSiparisler = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $enSonSiparisler;
        } catch (PDOException $e) {
            echo "Siparişleri getirme hatası: " . $e->getMessage();
            return [];
        }
    }


}

?>
