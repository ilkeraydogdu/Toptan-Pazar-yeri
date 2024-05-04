<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once '../../app/config/DB.php';
require_once '../Model/kategoriModel.php';

$kategoriModel = new kategoriModel($db);

if (isset($_GET['delete'])) {
	$id = $_GET['delete'];
	if ($kategoriModel->sil($id)) {
		$successMessage = 'Kategori başarıyla silindi.';
	} else {
		$errorMessage = 'Kategori silme işlemi başarısız.';
	}
	echo '<script>setTimeout(function() { window.location.href = "kategoriler.php"; }, 50);</script>';
	exit;
}

if (isset($_GET['edit'])) {
	$editId = $_GET['edit'];
	$editKategori = $kategoriModel->getKategoriById($editId);
}

$kategoriler = $kategoriModel->listele();

if (isset($successMessage)) {
	echo '<div class="alert alert-success" role="alert">' . $successMessage . '</div>';
}
if (isset($errorMessage)) {
	echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
}
?>

<div class="page-header">
	<div class="page-leftheader">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fe fe-shopping-cart mr-2 fs-14"></i>Kabaloğlu Kuyumculuk Toptan</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="#">Kategoriler</a></li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-lg-6">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Kategoriler</h3>
			</div>
			<div class="table-responsive">
				<table class="table card-table table-vcenter text-nowrap">
					<?php 
					if (!empty($kategoriler)) { 
						
						?>

						<thead>
							<tr>
								<th>Kategori Adı</th>
								<th>Üst Kategori Adı</th>
								<th>İşlemler</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($kategoriler as $kategori) {  ?>
								<tr>
									<td><?php echo $kategori['kategori_adi']; ?></td>
									<td>
										<?php if ($kategori['ust_kategori'] == 0) {
											echo 'Ana Kategori';
										} else {
											$ustKategori = $kategoriModel->getUstKategoriAdi($kategori['ust_kategori']);
											echo $ustKategori;
										} ?>
									</td>
									<td>
										<div class="btn-list">
											<a href="kategoriler.php?delete=<?php echo $kategori['id']; ?>" class="btn btn-pill btn-secondary" name="delete">Sil</a>
											<a href="kategoriler.php?edit=<?php echo $kategori['id']; ?>" class="btn btn-pill btn-info" name="duzenle">Düzenle</a>
										</div>
									</td>
								</tr>
							<?php } ?>
						</tbody>
						<?php 
					} else {
						echo '<tr><td colspan="3"><div class="alert alert-info" role="alert">Kategori bulunamadı!</div></td></tr>';
					} ?>
				</table>
			</div>
		</div>
	</div>
	<div class="col-xl-6 col-lg-6">
		<div class="card">
			<div class="card-header">
				<div class="card-title">Kategori Ekle</div>
			</div>
			<div class="card-body">
				<form action="../Controller/kategoriController.php" method="POST">
					<div class="row">
						<div class="col-sm-6 col-md-6">
							<div class="form-group">
								<label class="form-label">Kategori Adı:</label>
								<input type="text" class="form-control" name="kategori_adi">
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label class="form-label">Üst Kategori</label>
								<select class="form-control" name="ust_kategori">
									<optgroup label="Kategoriler">
										<option value="0">Ana Kategori YOK</option>
										<?php foreach ($kategoriler as $kategori) { ?>
											<option value="<?php echo $kategori['id']; ?>"><?php echo $kategori['kategori_adi']; ?></option>
										<?php } ?>
									</optgroup>
								</select>
							</div>
						</div>
					</div>
					<div class="card-footer text-right">
						<button type="submit" name="ekle" class="btn btn-success">Ekle</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php if (isset($_GET['edit'])): ?>
		<div class="col-md-12 col-lg-12">
			<?php if (isset($editKategori)) { ?>
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Kategori Düzenle</h3>
					</div>
					<div class="card-body">
						<form action="../Controller/kategoriController.php" method="POST">
							<input type="hidden" name="edit_id" value="<?php echo $editKategori['id']; ?>">
							<div class="row">
								<div class="col-sm-6 col-md-6">
									<div class="form-group">
										<label class="form-label">Kategori Adı:</label>
										<input type="text" class="form-control" name="kategori_adi" value="<?php echo $editKategori['kategori_adi']; ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label">Üst Kategori</label>
										<select class="form-control" name="ust_kategori">
											<optgroup label="Ana Kategoriler">
												<option value="0" <?php echo ($editKategori['ust_kategori'] == 0) ? 'selected' : ''; ?>>Ana Kategori YOK</option>
												<?php foreach ($kategoriler as $kategori) { ?>
													<?php if ($kategori['ust_kategori'] == 0) { ?>
														<option value="<?php echo $kategori['id']; ?>" <?php echo ($editKategori['ust_kategori'] == $kategori['id']) ? 'selected' : ''; ?>><?php echo $kategori['kategori_adi']; ?></option>
														<?php foreach ($kategoriler as $altKategori) { ?>
															<?php if ($altKategori['ust_kategori'] == $kategori['id']) { ?>
																<option value="<?php echo $altKategori['id']; ?>" <?php echo ($editKategori['ust_kategori'] == $altKategori['id']) ? 'selected' : ''; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $altKategori['kategori_adi']; ?></option>
															<?php } ?>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											</optgroup>

										</select>
									</div>
								</div>
							</div>
							<div class="card-footer text-right">
								<button type="submit" name="duzenle" class="btn btn-success">Düzenle</button>
							</div>
						</form>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php endif ?>
</div>

<?php require_once 'inc/footer.php'; ?>
