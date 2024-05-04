<?php
ob_start();
session_start();
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once '../Model/urunModel.php';
require_once '../Controller/urunController.php';
require_once '../../app/config/DB.php';

if (!isset($_SESSION['sepet'])) {
    $_SESSION['sepet'] = [];
}

$urunController = new urunController();
list($urunler, $toplamSayfa, $sayfa) = $urunController->listele();


// Kategorileri çekme
$sorgu = "SELECT * FROM kategoriler WHERE ust_kategori IS NULL"; // sorguyu değiştirdik
$sonuc = $db->query($sorgu);
$kategoriler = array();
while ($satir = $sonuc->fetch(PDO::FETCH_ASSOC)) {
    $kategori_id = $satir['id'];
    $kategoriler[$kategori_id] = array(
        'kategori_adi' => $satir['kategori_adi'],
        'ust_kategori' => $satir['ust_kategori']
    );

    // Alt kategorileri getir
    $altKategorilerSorgu = "SELECT * FROM kategoriler WHERE ust_kategori = $kategori_id";
    $altKategorilerSonuc = $db->query($altKategorilerSorgu);
    $altKategoriler = array();
    while ($altSatir = $altKategorilerSonuc->fetch(PDO::FETCH_ASSOC)) {
        $altKategoriId = $altSatir['id'];
        $altKategoriler[$altKategoriId] = array(
            'kategori_adi' => $altSatir['kategori_adi'],
            'ust_kategori' => $altSatir['ust_kategori']
        );

        // Alt kategorilerin alt kategorilerini de getir
        $altAltKategorilerSorgu = "SELECT * FROM kategoriler WHERE ust_kategori = $altKategoriId";
        $altAltKategorilerSonuc = $db->query($altAltKategorilerSorgu);
        $altAltKategoriler = array();
        while ($altAltSatir = $altAltKategorilerSonuc->fetch(PDO::FETCH_ASSOC)) {
            $altAltKategoriId = $altAltSatir['id'];
            $altAltKategoriler[$altAltKategoriId] = array(
                'kategori_adi' => $altAltSatir['kategori_adi'],
                'ust_kategori' => $altAltSatir['ust_kategori']
            );
        }
        $altKategoriler[$altKategoriId]['alt_kategoriler'] = $altAltKategoriler;
    }
    $kategoriler[$kategori_id]['alt_kategoriler'] = $altKategoriler;
}

// Kategori seçimi için form gönderildiğinde ve kategori seçildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kategori_id'])) {
    $kategoriId = $_POST['kategori_id'];

    // Kategoriye göre ürünleri filtrelemek için sorgu
    $sorgu = "SELECT * FROM urunler WHERE kategori_id = :kategoriId";
    $stmt = $db->prepare($sorgu);
    $stmt->bindParam(':kategoriId', $kategoriId, PDO::PARAM_INT);
    $stmt->execute();

    // Seçilen kategoride ürün varsa, bu ürünleri listele
    if ($stmt->rowCount() > 0) {
        $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Seçilen kategoride ürün yoksa, tüm ürünleri listele
        $urunController = new urunController();
        list($urunler, $toplamSayfa, $sayfa) = $urunController->listele();
    }
} else {
    // Eğer bir kategori seçilmediyse veya seçilen kategoriye göre ürün bulunamadıysa, tüm ürünleri listele
    $urunController = new urunController();
    list($urunler, $toplamSayfa, $sayfa) = $urunController->listele();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['urun_id']) && isset($_POST['miktar'])) {
        $urunId = $_POST['urun_id'];
        $miktar = $_POST['miktar'];

        if ($miktar <= 0) {
            unset($_SESSION['sepet'][$urunId]);
        } else {
            // Eğer aynı ürün sepete ekleniyorsa, mevcut miktarın üzerine ekleyin
            if (isset($_SESSION['sepet'][$urunId])) {
                $_SESSION['sepet'][$urunId] += $miktar;
            } else {
                $_SESSION['sepet'][$urunId] = $miktar;
            }
            
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Ürün Sepete Eklendi!',
                text: 'Ürün sepete başarıyla eklendi.',
                showConfirmButton: true,
                confirmButtonText: 'Tamam'
                });
                </script>";
            }
        } elseif (isset($_POST['sil_urun_id'])) {
            $urunId = $_POST['sil_urun_id'];
            $urunModel = new urunModel();
            $silindiMi = $urunModel->silUrunVeGorseli($urunId);

            if ($silindiMi) {
            // Ürün başarıyla silindiği zaman, değişkeni true olarak ayarla
                $silindiMi = true;
            // Ürün başarıyla silindiği zaman, sepetten de kaldır
                unset($_SESSION['sepet'][$urunId]);
                echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Ürün Silindi!',
                    text: 'Ürün başarıyla silindi.',
                    showConfirmButton: true,
                    confirmButtonText: 'Tamam'
                    });
                    </script>";
                } else {
            // Ürün silinemezse, değişkeni false olarak ayarla
                    $silindiMi = false;
                    echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Ürün silinirken bir hata oluştu.',
                        showConfirmButton: true,
                        confirmButtonText: 'Tamam'
                        });
                        </script>";
                    }
                }
            }

            function listCategories($categories, $indent = 0) {
                foreach ($categories as $kategori_id => $kategori) {
                    echo '<option value="' . $kategori_id . '">' . str_repeat("-", $indent) . $kategori['kategori_adi'] . '</option>';
                    if (!empty($kategori['alt_kategoriler'])) {
                        listCategories($kategori['alt_kategoriler'], $indent + 1);
                    }
                }
            }
            ?>

            <div class="page-header">
                <div class="page-leftheader">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fe fe-shopping-cart mr-2 fs-14"></i>Kabaloğlu Kuyumculuk Toptan</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Ürünler</a></li>
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
                                            <form method="POST">
                                                <div class="input-group mb-2">
                                                    <select name="kategori_id" class="form-control custom-select" id="kategori_id">
                                                        <option value="0">--Kategori Seç--</option>
                                                        <?php 
                                                        listCategories($kategoriler);
                                                        ?>
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
                                    <script>
                                        function sepeteEkle(form) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Ürün Sepete Eklendi!',
                                                text: 'Ürün sepete başarıyla eklendi.',
                                                showConfirmButton: true,
                                                confirmButtonText: 'Tamam'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    form.submit();
                                                }
                                            });
                                        }
                                    </script>

                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="event.preventDefault(); sepeteEkle(this);">
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
                                            <button type="submit" class="btn btn-secondary btn-block mt-4" id="ekle_<?php echo $urun['id']; ?>">
                                                <i class="fe fe-shopping-cart mr-1"></i>Sepete Ekle
                                            </button>
                                        </div>
                                    </form>
                                    <?php if ($_SESSION['rol'] == 'admin'): ?>
                                        <div class="text-center pb-6 pl-6 pr-6">
                                            <div class="btn-list">
                                                <a href="urunDuzenle.php?id=<?php echo $urun['id']; ?>" class="btn btn-primary notice">Düzenle</a>
                                                <form method="POST" onsubmit="return confirm('Ürünü Silmek İstediğinize Emin misiniz?')" id="silForm_<?php echo $urun['id']; ?>">
                                                    <input type="hidden" name="sil_urun_id" value="<?php echo $urun['id']; ?>">
                                                    <button type="button" class="btn btn-secondary warning" onclick="silUrunConfirm(<?php echo $urun['id']; ?>)">Sil</button>
                                                </form>

                                                <?php
                                                if (isset($_POST['sil_urun_id']) && $_POST['sil_urun_id'] == $urun['id']) {
                                                    if ($silindiMi) {
                                                        echo "<div class='text-success'>Ürün başarıyla silindi.</div>";
                                                    } else {
                                                        echo "<div class='text-danger'>Ürün silinirken bir hata oluştu.</div>";
                                                    }
                                                }
                                                ?>
                                                <script>
                                                    function silUrunConfirm(urunId) {
                                                        Swal.fire({
                                                            icon: 'warning',
                                                            title: 'Ürünü Sil',
                                                            text: 'Ürünü silmek istediğinize emin misiniz?',
                                                            showCancelButton: true,
                                                            confirmButtonText: 'Evet, Sil',
                                                            cancelButtonText: 'İptal',
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                var form = document.getElementById("silForm_" + urunId);
                                                                form.submit();
                                                            }
                                                        });
                                                    }
                                                </script>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- SweetAlert2 Türkçe dil dosyası -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/src/sweetalert2.scss"></script>
            <?php
            require_once 'inc/footer.php';
            ?>

