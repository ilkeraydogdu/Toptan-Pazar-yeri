<?php 
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once '../Controller/urunDuzenleController.php';

$db = getDbConnection();
$urunDuzenleController = new urunDuzenleController($db);

if (!isset($_GET['id'])) {
    // Eğer ürün ID'si sağlanmamışsa, urunler.php sayfasına yönlendir
    header('Location: urunler.php');
    exit;
}

$id = $_GET['id'];
$urun = $urunDuzenleController->urunBilgisiGetir($id);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bilgileriGuncelle'])) {
        // Form verilerini al
        $kategori_id = $_POST['kategori_id'];
        $isim = $_POST['isim'];
        $gram = $_POST['gram'];

        // Ürün bilgilerini güncelle
        $sonuc = $urunDuzenleController->urunBilgileriGuncelle($id, $kategori_id, $isim, $gram);
        
        if ($sonuc) {
            echo "<script>alert('Ürün bilgileri başarıyla güncellendi.'); window.location.href = 'urunler.php';</script>";
        } else {
            echo "<script>alert('Ürün bilgileri güncellenirken hata oluştu.');</script>";
        }
    } elseif (isset($_POST['gorselGuncelle'])) {
        // Yeni görseli yükle ve güncelle
        if(isset($_FILES['foto'])) {
            $uploadDir = '../../app/assets/images/products/';
            $fotoDosya = $_FILES['foto'];
            $fotoDosyaAdi = basename($fotoDosya['name']);
            $fotoYol = $uploadDir . $fotoDosyaAdi;
            $fotoDosyaTipi = strtolower(pathinfo($fotoYol,PATHINFO_EXTENSION));
            
            if($fotoDosyaTipi == "jpg" || $fotoDosyaTipi == "png" || $fotoDosyaTipi == "jpeg" || $fotoDosyaTipi == "gif" ) {
                if (move_uploaded_file($fotoDosya['tmp_name'], $fotoYol)) {
                    // Eski görseli sil
                    $eskiGorselAdi = $urun['foto'];
                    if ($eskiGorselAdi && file_exists($uploadDir . $eskiGorselAdi)) {
                        unlink($uploadDir . $eskiGorselAdi);
                    }
                    // Yeni görseli güncelle
                    $sonuc = $urunDuzenleController->urunGorselGuncelle($id, $fotoDosyaAdi);
                    if($sonuc) {
                        echo "<script>alert('Ürün görseli başarıyla güncellendi.'); window.location.href = 'urunler.php';</script>";
                    } else {
                        echo "<script>alert('Ürün görseli güncellenirken hata oluştu.');</script>";
                    }
                } else {
                    echo "<script>alert('Dosya yükleme hatası.');</script>";
                }
            } else {
                echo "<script>alert('Yalnızca JPG, JPEG, PNG & GIF dosya türleri izin verilir.');</script>";
            }
        } else {
            echo "<script>alert('Dosya seçilmedi.');</script>";
        }
    }
}

?>

<div class="page-header">
    <div class="page-leftheader">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fe fe-shopping-cart mr-2 fs-14"></i>Kabaloğlu Kuyumculuk Toptan</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="#">Ürün Düzenle</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Ürün Görsel Güncelle</div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-5">
                        <img src="<?php echo URL; ?>/app/assets/images/products/<?php echo $urun['foto']; ?>" alt="Ürün Görseli">
                    </div>
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="foto" name="foto">
                        <label class="custom-file-label" for="foto">Yeni Görsel Seç</label>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" name="gorselGuncelle" class="btn btn-primary">Görsel Değiştir</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Ürün Bilgilerini Düzenle</div>
            </div>
            <form method="POST">
                <div class="card-body">
                    <div class="card-title font-weight-bold">Ürün Bilgileri:</div>
                    <div class="form-group row">
                        <label for="kategori_id" class="col-sm-3 col-form-label">Kategori:</label>
                        <div class="col-sm-9">
                            <select name="kategori_id" id="kategori_id" class="form-control">
                                <option value="0">--Kategori Seç--</option>
                                <?php 
                                // Kategori listesini gösterme
                                listCategories($kategoriler, 0, $urun['kategori_id']);
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="isim" class="col-sm-3 col-form-label">Ürün Adı:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="isim" name="isim" value="<?php echo $urun['isim']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gram" class="col-sm-3 col-form-label">Gram:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="gram" name="gram" value="<?php echo $urun['gram']; ?>">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" name="bilgileriGuncelle" class="btn btn-primary">Ürünü Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
require_once 'inc/footer.php'; 
function listCategories($categories, $indent = 0, $selectedCategory = null) {
    foreach ($categories as $kategori_id => $kategori) {
        $selected = ($kategori_id == $selectedCategory) ? 'selected' : '';
        echo '<option value="' . $kategori_id . '" ' . $selected . '>' . str_repeat("&nbsp;&nbsp;&nbsp;", $indent) . $kategori['kategori_adi'] . '</option>';
        if (!empty($kategori['alt_kategoriler'])) {
            listCategories($kategori['alt_kategoriler'], $indent + 1, $selectedCategory);
        }
    }
}
?>

