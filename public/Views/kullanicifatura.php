<?php 
ob_start();
session_start();
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once '../../app/config/DB.php';

if (isset($_GET['kullanici_id']) && isset($_GET['siparis_numarasi'])) {
    $kullanici_id = $_GET['kullanici_id'];
    $siparis_numarasi = $_GET['siparis_numarasi'];

    try {
        // Kategori bilgilerini çeken fonksiyon
        function fetchCategoryInfo($categoryId, $db) {
            $categoryInfo = array();
            $currentCategory = $categoryId;

            while ($currentCategory !== NULL) {
                $sorgu = "SELECT * FROM kategoriler WHERE id = :categoryId";
                $stmt = $db->prepare($sorgu);
                $stmt->bindParam(':categoryId', $currentCategory, PDO::PARAM_INT);
                $stmt->execute();
                $kategori = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($kategori) {
                    $categoryInfo[] = array(
                        'kategori_adi' => $kategori['kategori_adi'],
                        'ust_kategori_id' => $kategori['ust_kategori']
                    );
                    $currentCategory = $kategori['ust_kategori'];
                } else {
                    $currentCategory = NULL;
                }
            }

            return $categoryInfo;
        }

        // Sipariş bilgisini çeken sorgu
        $sql = "SELECT sip.*, kul.firma AS kullanici_adi, kul.adres, kul.tel, kul.email, urun.isim AS urun_adi, urun.foto AS foto, urun.gram AS urun_gram, urun.kategori_id AS alt_kategori_id, kat2.kategori_adi AS ust_kategori
        FROM siparisler sip
        INNER JOIN kullanici kul ON sip.kullanici_id = kul.id
        INNER JOIN urunler urun ON sip.urun_id = urun.id
        LEFT JOIN kategoriler kat2 ON urun.kategori_id = kat2.id
        WHERE sip.kullanici_id = :kullanici_id AND sip.siparisNumarasi = :siparis_numarasi
        ORDER BY sip.tarih";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':kullanici_id', $kullanici_id);
        $stmt->bindParam(':siparis_numarasi', $siparis_numarasi);
        $stmt->execute();
        $siparisler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($siparisler) {
            $toplam_gram = 0;
            ?>
            <br><br>
            <div class="row flex-lg-nowrap">
                <div class="col-12">
                    <div class="row flex-lg-nowrap">
                        <div class="col-12 mb-3">
                            <div class="e-panel card">
                                <div class="card-header">
                                    <h3 class="card-title">Sipariş Dökümü</h3>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-body">
                                            <h1 class="invoice-title font-weight-bold text-uppercase mb-1">#<?php echo $siparis_numarasi; ?></h1>
                                            <div class="row mt-4">
                                                <?php $ilk_siparis = reset($siparisler); ?>
                                                <div class="col-md">
                                                    <label class="font-weight-bold"><?php echo $ilk_siparis['kullanici_adi']; ?></label><br>
                                                    <!-- Müşteri bilgileri -->
                                                    <div class="billed-to">
                                                        <p>Adres: <?php echo $ilk_siparis['adres']; ?></p>
                                                        <p>Tel: <?php echo $ilk_siparis['tel']; ?></p>
                                                        <p>Email: <?php echo $ilk_siparis['email']; ?></p>
                                                        <p><b>Sipariş Tarihi: <?php echo $ilk_siparis['tarih']; ?></b></p>
                                                    </div>
                                                </div>

                                                <div class="col-md">
                                                    <div class="billed-from text-md-right">
                                                        <label class="font-weight-bold">Kabaloğlu Kuyumculuk</label>
                                                        <p>Adres: Yenibosna Merkez, Kuyumcular sk Vizyonpark Atölyeler Bloğu <br>C2 Blok 3.Kat No:305, <br>34197 Küçükçekmece/İstanbul<br>
                                                        Tel:  0531 597 89 48</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!empty($ilk_siparis["note"])) {?>
                                                <div class="float-left mb-5 col-lg-7">
                                                    <textarea class="form-control mb-4 mt-4" disabled  rows="3"><?php echo $ilk_siparis["note"]; ?></textarea>
                                                </div>
                                            <?php } ?>

                                            <div class="float-right mb-5">
                                                <?php if ($_SESSION['rol'] == 'admin'): ?>
                                                    <button type="button" class="btn btn-secondary mt-4" id="cancelOrderBtn"><i class="si si-printer"></i>İptal Et</button>
                                                    <button type="button" class="btn btn-warning mt-4" id="prepareOrderBtn"><i class="si si-printer"></i>Hazırlanıyor</button>
                                                    <button type="button" class="btn btn-success mt-4" id="approveOrderBtn"><i class="si si-printer"></i>Onayla</button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-info mt-4" onClick="printPage()"><i class="si si-printer"></i> Yazdır</button>
                                                <script>
                                                    function printPage() {
                                                        var style = document.createElement('style');
                                                        style.innerHTML = `
                                                        @media print {
                                                            header, footer {
                                                                display: none;
                                                            }
                                                            .btn {
                                                                display: none;
                                                            }
                                                        }
                                                        `;
                                                        document.head.appendChild(style);
                                                        window.print();
                                                        document.head.removeChild(style);
                                                    }
                                                </script>

                                                <div id="notificationContainer"></div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function () {
                                                        document.getElementById('cancelOrderBtn').addEventListener('click', function () {
                                                            updateOrderStatus(3);
                                                        });

                                                        document.getElementById('prepareOrderBtn').addEventListener('click', function () {
                                                            updateOrderStatus(2);
                                                        });

                                                        document.getElementById('approveOrderBtn').addEventListener('click', function () {
                                                            updateOrderStatus(1);
                                                        });

                                                        function updateOrderStatus(status) {
                                                            var xmlhttp = new XMLHttpRequest();
                                                            var url = "faturaonay.php";
                                                            xmlhttp.onreadystatechange = function () {
                                                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                                                    console.log(xmlhttp.responseText);
                                                                    if (status == 1) {
                                                                        var message = "Fatura onaylandı!";
                                                                        showNotification(message, 'success');
                                                                        window.location.href = 'onaylanansiparislerim.php';
                                                                    }
                                                                    if (status == 2) {
                                                                        var message = "Fatura onaylandı!";
                                                                        showNotification(message, 'success');
                                                                        window.location.href = 'hazirlaniyorsiparislerim.php';
                                                                    }
                                                                    if (status == 3) {
                                                                        var message = "Fatura onaylandı!";
                                                                        showNotification(message, 'success');
                                                                        window.location.href = 'iptalsiparislerim.php';
                                                                    }
                                                                }
                                                            };
                                                            var params = "siparis_numarasi=<?php echo $siparis_numarasi; ?>&durum=" + status;
                                                            xmlhttp.open("POST", url, true);
                                                            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                                            xmlhttp.send(params);
                                                        }

                                                        function showNotification(message, type) {
                                                            var notificationContainer = document.getElementById('notificationContainer');

                                                            var notification = document.createElement('div');
                                                            notification.classList.add('notification', 'notification-' + type);
                                                            notification.innerHTML = message;

                                                            notificationContainer.appendChild(notification);

                                                            setTimeout(function () {
                                                                notification.remove();
                                                            }, 3000);
                                                        }
                                                    });
                                                </script>

                                            </div>
                                            <div class="table-responsive mt-4">
                                                <table class="table table-bordered border text-nowrap mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="wd-20p text-center">Ürün Görseli</th>
                                                            <th class="wd-20p text-center">Üst Kategori</th>
                                                            <th class="wd-20p text-center">Ürün Adı</th>
                                                            <th class="tx-right text-center">Ürün Gramı</th>
                                                            <th class="tx-right text-center">Satın Alınan Adet</th>
                                                            <th class="tx-right text-center">Satın Alınan Gram</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($siparisler as $urun) {
                                                            $toplam_gram += $urun['adet'] * $urun['urun_gram'];
                                                            $kategoriInfo = fetchCategoryInfo($urun['alt_kategori_id'], $db);
                                                            ?>
                                                            <tr>
                                                                <td class="text-center font-weight-bold"><img width="190" height="200" src="<?php echo URL; ?>/app/assets/images/products/<?php echo $urun['foto']; ?>"></td>
                                                                <td class="text-center"><?php echo end($kategoriInfo)['kategori_adi']; ?></td>
                                                                <td class="text-center font-weight-bold"><?php echo $urun['urun_adi']; ?></td>
                                                                <td class="text-center"><?php echo $urun['urun_gram']; ?></td>
                                                                <td class="text-center"><?php echo $urun['adet']; ?></td>
                                                                <td class="text-center"><?php echo $urun['adet'] * $urun['urun_gram']; ?></td>
                                                            </tr>
                                                        <?php } ?>

                                                        <tr>
                                                            <td colspan="3" class="text-uppercase font-weight-semibold">Toplam Gram</td>
                                                            <td colspan="3" class="text-center">
                                                                <h4 class="text-primary font-weight-bold"><?php echo  number_format($toplam_gram, 2, ",", "."); ?></h4>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "Sipariş bulunamadı!";
    }
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
    exit();
}
} else {
    echo "Gerekli parametreler eksik!";
}
require_once 'inc/footer.php';
?>
