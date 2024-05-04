<?php
// Oturumu başlat
session_start();

// Eğer sepet varsa, sepet içeriğini döndür
if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {
    // Sepet içeriği HTML formatında oluşturulur
    $cartContent = '<div class="dropdown-header"><h6 class="mb-0">Sepet</h6></div>';
    foreach ($_SESSION['sepet'] as $urunId => $miktar) {
        // Her bir ürün için gerekli bilgiler alınır ve HTML olarak biçimlendirilir
        $cartContent .= '<a href="index.html#" class="dropdown-item border-bottom d-flex pl-4 sepet-urun" data-urun-id="' . $urunId . '">';
        // Ürünün resmi
        $cartContent .= '<div class="notifyimg bg-primary-transparent text-primary"><img src="' . URL . '/app/assets/images/products/' . $foto . '" alt="img" class="avatar avatar-md brround"></div>';
        // Ürünün adı ve gramı
        $cartContent .= '<div><div class="font-weight-normal1">' . $urunAdi . ' (' . $urunGram . ' gr)</div>';
        // Ürün miktarı
        $cartContent .= '<div class="small text-muted">' . $miktar . ' Adet</div></div></a>';
    }
} else {
    // Sepet boşsa bildirim göster
    $cartContent = '<div class="dropdown-item text-center">Sepetinizde ürün bulunmamaktadır.</div>';
}

// Oluşturulan sepet içeriği JSON formatında döndürülür
echo json_encode($cartContent);
?>
