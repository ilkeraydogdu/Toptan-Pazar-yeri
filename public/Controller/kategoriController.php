<?php
require_once '../../app/config/DB.php';
require_once '../Model/kategoriModel.php';

$kategoriModel = new kategoriModel($db);

if (isset($_POST['ekle'])) {
	$kategoriAdi = $_POST['kategori_adi'];
	$ustKategori = $_POST['ust_kategori'];
	if ($kategoriModel->ekle($kategoriAdi, $ustKategori)) {
		$successMessage = 'Kategori başarıyla eklendi.';
	} else {
		$errorMessage = 'Kategori ekleme işlemi başarısız.';
	}
	header("Location: ../Views/kategoriler.php");
	exit;
}

if (isset($_GET['delete'])) {
	$id = $_GET['delete'];
	if ($kategoriModel->sil($id)) {
		$successMessage = 'Kategori başarıyla silindi.';
	} else {
		$errorMessage = 'Kategori silme işlemi başarısız.';
	}
	header("Location: ../Views/kategoriler.php");
	exit;
}
if (isset($_POST['duzenle'])) {
	$id = $_POST['edit_id'];
	$kategoriAdi = $_POST['kategori_adi'];
	$ustKategori = $_POST['ust_kategori'];

    // Kategori düzenleme işlemi
	if ($kategoriModel->duzenle($id, $kategoriAdi, $ustKategori)) {
		$successMessage = 'Kategori başarıyla düzenlendi.';
	} else {
		$errorMessage = 'Kategori düzenleme işlemi başarısız.';
	}
}
if (isset($_GET['edit'])) {
	$editId = $_GET['edit'];
	$editKategori = $kategoriModel->getKategoriById($editId);
}
header("Location: ../Views/kategoriler.php");
exit;

?>
