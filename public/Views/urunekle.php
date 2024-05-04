<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once '../Controller/urunekleController.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$controller = new urunekleController($db);

// Ürün ekleme formu gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$kategoriId = $_POST['kategori_id'];
	$isim = $_POST['isim'];
	$gram = $_POST['gram'];
	$foto = $_FILES['foto'];

	$controller->urunEkle($kategoriId, $isim, $gram, $foto);
} else {
    // Ürün ekleme formu gönderilmediğinde
	$model = new urunekleModel($controller->db);
	$kategoriler = $model->getKategoriler();

	function kategoriListe($kategoriler, $ust_kategori_id = 0, $depth = 0) {
        // Verilen üst kategori ID'sine sahip kategorileri bul
		$altKategoriler = array_filter($kategoriler, function ($kategori) use ($ust_kategori_id) {
			return $kategori['ust_kategori'] == $ust_kategori_id;
		});

        // Eğer alt kategoriler varsa, liste oluştur
		if (!empty($altKategoriler)) {
            // İç içe geçmiş listeleme için gerekli boşlukları ekle
			$indent = str_repeat('&nbsp;&nbsp;', $depth);

            // Her bir alt kategori için işlem yap
			foreach ($altKategoriler as $kategori) {
				echo '<option value="' . $kategori['id'] . '">' . $indent . $kategori['kategori_adi'] . '</option>';

                // Alt kategorileri recursive olarak çağırarak iç içe geçmiş listeleme sağla
				kategoriListe($kategoriler, $kategori['id'], $depth + 1);
			}
		}
	}
	?>

	<!-- Ürün ekleme formu -->
	<br><br>
	<div class="row">
		<div class="col-lg-6 col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Ürün Ekle</h3>
				</div>
				<div class="card-body">
					<form method="POST" action="" enctype="multipart/form-data">
						<div class="form-group">
							<label for="kategori_id">Kategori:</label>
							<select class="form-control" id="kategori_id" name="kategori_id">
								<!-- kategoriListe() fonksiyonu tarafından oluşturulan seçenekler buraya eklenecek -->
								<?php kategoriListe($kategoriler); ?>
							</select>
						</div>
						<div class="form-group">
							<label for="isim">Ürün İsmi:</label>
							<input type="text" class="form-control" id="isim" name="isim">
						</div>
						<div class="form-group">
							<label for="gram">Ürün Gramı:</label>
							<input type="text" class="form-control" id="gram" name="gram">
						</div>
						<div class="form-group">
							<label for="foto">Ürün Fotoğrafı:</label>
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="foto" name="foto">
								<label class="custom-file-label">Yeni Ürün Fotoğrafı Seç</label>
							</div>
						</div>
						<button type="submit" class="btn btn-primary">Ürünü Ekle</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php
}
require_once 'inc/footer.php';
?>
