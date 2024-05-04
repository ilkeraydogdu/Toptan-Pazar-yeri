<?php

require_once '../../app/config/DB.php';

function kaydetModel($isim, $tc, $email, $tel, $vDairesi, $vNo, $adres, $sifre, $rol, $durum, $ip) {
  global $db;
  // SQL sorgusu
  $sql = "INSERT INTO kullanici (isim, tc, email, tel, vDairesi, vNo, adres, $sifre, $rol, $durum, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";

  // Sorguyu çalıştırma
  $stmt = $db->prepare($sql);
  $stmt->execute([$isim, $tc, $email, $tel, $vDairesi, $vNo, $adres, $sifre,  $rol, $durum, $ip]);

  // Sonucu kontrol etme
  if ($stmt->rowCount() > 0) {
    header("location: ../Views/index.php?durum=basarili");
  } else {
    header("location: ../Views/kayit.php?durum=basarisiz");
  }
  
}
?>