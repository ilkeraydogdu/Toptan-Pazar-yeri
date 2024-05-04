<?php
class kategoriModel {
	private $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function listele() {
		$query = "SELECT * FROM kategoriler";
		$stmt = $this->db->query($query);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function sil($id) {
		$query = "DELETE FROM kategoriler WHERE id = :id";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':id', $id);
		return $stmt->execute();
	}
	public function ekle($kategoriAdi, $ustKategori) {
		if (empty($ustKategori)) {
			$ustKategori = null;
		}
		$query = "INSERT INTO kategoriler (kategori_adi, ust_kategori) VALUES (:kategoriAdi, :ustKategori)";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':kategoriAdi', $kategoriAdi);
		$stmt->bindParam(':ustKategori', $ustKategori, PDO::PARAM_INT);
		return $stmt->execute();
	}

	public function duzenle($id, $kategoriAdi, $ustKategori) {
		if (empty($ustKategori)) {
			$ustKategori = null;
		}
		$query = "UPDATE kategoriler SET kategori_adi = :kategoriAdi, ust_kategori = :ustKategori WHERE id = :id";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':kategoriAdi', $kategoriAdi);
		$stmt->bindParam(':ustKategori', $ustKategori, PDO::PARAM_INT);
		$stmt->bindParam(':id', $id);
		return $stmt->execute();
	}


	public function getUstKategoriAdi($ustKategoriId) {
		$query = "SELECT kategori_adi FROM kategoriler WHERE id = :ust_kategori";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':ust_kategori', $ustKategoriId);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['kategori_adi'] : null;
	}

	public function getKategoriById($id) {
		$query = "SELECT * FROM kategoriler WHERE id = :id";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function hiyerarsikListele() {
        // Tüm kategorileri sorgulayın
		$query = "SELECT * FROM kategoriler";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$kategoriler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hiyerarşik bir şekilde kategorileri düzenleyin
		$hiyerarsikKategoriler = array();
		foreach ($kategoriler as $kategori) {
            // Ana kategori ise doğrudan hiyerarşik listeye ekleyin
			if ($kategori['ust_kategori'] == 0) {
				$hiyerarsikKategoriler[$kategori['id']] = array(
					'kategori_adi' => $kategori['kategori_adi'],
					'alt_kategoriler' => array()
				);
			} else {
                // Alt kategori ise, üst kategorinin altına ekleyin
				$ustKategoriId = $kategori['ust_kategori'];
				$hiyerarsikKategoriler[$ustKategoriId]['alt_kategoriler'][] = $kategori;
			}
		}

		return $hiyerarsikKategoriler;
	}
	
}
?>
