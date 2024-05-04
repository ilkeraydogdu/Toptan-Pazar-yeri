<?php
ob_start();
session_start();
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once '../Model/urunModel.php';
require_once '../Controller/urunController.php';
require_once '../../app/config/DB.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kategorileri çekme
$sorgu = "SELECT * FROM kategoriler";
$sonuc = $db->query($sorgu);
$kategoriler = array();
while ($satir = $sonuc->fetch(PDO::FETCH_ASSOC)) {
    $kategoriler[$satir['id']] = array(
        'kategori_adi' => $satir['kategori_adi'],
        'ust_kategori' => $satir['ust_kategori']
    );
}

// Kategori seçimi için form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kategori_id'])) {
    $kategoriId = $_POST['kategori_id'];

    // Kategoriye göre ürünleri filtrelemek için sorgu
    $sorgu = "SELECT * FROM urunler WHERE kategori_id = :kategoriId";
    $stmt = $db->prepare($sorgu);
    $stmt->bindParam(':kategoriId', $kategoriId, PDO::PARAM_INT);
    $stmt->execute();

    // Sonuçları al ve ürünleri listele
    $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Eğer bir kategori seçilmediyse veya seçilen kategoriye göre ürün bulunamadıysa, tüm ürünleri listele
    $urunController = new urunController();
    list($urunler, $toplamSayfa, $sayfa) = $urunController->listele();
}
?>

<div class="page-header">
    <div class="page-leftheader">
        <ol class="breadcrumb">
        </ol>
    </div>
</div>

<div class="row flex-lg-nowrap">
    <div class="col-12">
        <div class="row flex-lg-nowrap">
            <div class="col-12 mb-6">
                <div class="e-panel card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-auto">
                            </div>
                            <div class="col-3 col-auto">
                            </div>
                            <div class="col-3 col-auto">
                                <!-- Kategori seçimi için form -->
                                <form method="POST">
                                    <div class="input-group mb-2">
                                        <select name="kategori_id" class="form-control custom-select">
                                            <option value="0">--Kategori Seç--</option>
                                            <?php foreach ($kategoriler as $kategori_id => $kategori) { ?>
                                                <option value="<?php echo $kategori_id; ?>"><?php echo $kategori['kategori_adi']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="input-group-append">
                                            <button class="btn ripple btn-primary" type="submit" name="ara">Ara</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($urunler as $urun) { ?>
                <div class="col-xl-3">
                    <div class="card item-card ">
                        <div class="card-body pb-0">
                            <div class="text-center">
                                <a href="<?php echo URL; ?>/app/assets/images/products/<?php echo $urun['foto']; ?>" download class="btn btn-sm position-relative">
                                    <img src="<?php echo URL; ?>/app/assets/images/products/<?php echo $urun['foto']; ?>" class="img-fluid w-100 img-hover position-relative" onmouseover="zoomIn(this)" style="border-radius: 15px;">
                                </a>
                            </div>
                            <div class="card-body px-0">
                                <div class="cardtitle">
                                    <a class="shop-title"><?php echo $urun['isim']; ?>(<?php echo $urun['gram']; ?> Gram)</a>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        </div>

                        <style type="text/css">
                            .img-hover {
                                transition: transform 0.5s;
                                position: relative;
                                z-index: 1;
                            }

                            .img-hover:hover {
                                transform: scale(1.7);
                                z-index: 2;
                            }
                        </style>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <input type="hidden" name="urun_id" value="<?php echo $urun['id']; ?>">
                            <div class="text-center pb-6 pl-6 pr-6">
                                <span class="input-group-btn ">
                                    <button type="button" class="btn btn-light border-0 br-0 minus" name="urun_id" id="minus_<?php echo $urun['id']; ?>">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </span>

                                <input type="text" name="miktar" id="miktar_<?php echo $urun['id']; ?>" class="form-control text-center qty" value="1">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-light border-0 br-0 add" id="plus_<?php echo $urun['id']; ?>">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </span>

                                <button type="submit" class="btn btn-secondary btn-block mt-4" id="ekle_<?php echo $urun['id']; ?>"><i class="fe fe-shopping-cart mr-1"></i>Sepete Ekle</button>
                                <br>
                                <?php if ($_SESSION['rol'] == 'admin'): ?>
                                    <div class="btn-list">
                                        <a href="urunDuzenle.php?id=<?php echo $urun['id']; ?>" class="btn btn-primary notice">Düzenle</a>
                                        <a href="urunSil.php?id=<?php echo $urun['id']; ?>" class="btn btn-secondary warning">Sil</a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="d-flex justify-content-end mt-5">
            <ul class="pagination">
                <?php if ($sayfa > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?sayfa=<?php echo $sayfa - 1; ?>">‹</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $toplamSayfa; $i++): ?>
                    <li class="page-item <?php echo ($sayfa == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?sayfa=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($sayfa < $toplamSayfa): ?>
                    <li class="page-item">
                        <a class="page-link" href="?sayfa=<?php echo $sayfa + 1; ?>">›</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>
