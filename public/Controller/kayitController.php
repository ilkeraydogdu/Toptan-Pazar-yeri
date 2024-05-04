<?php
require_once '../Model/kayitModel.php';

// Giriş verilerini alma ve doğrulama
$firma = validateInput($_POST['firma']);
$email = validateInput($_POST['email']);
$tel = validateInput($_POST['tel']);
$adres = validateInput($_POST['adres']);
$sifre = validateInput($_POST['sifre']);
$sifre2 = validateInput($_POST['sifre2']);
$ip = $_SERVER['REMOTE_ADDR'];

// E-posta adresinin domain kısmını al
$emailParts = explode("@", $email);
$domain = $emailParts[1];

// Domain kısmının var olup olmadığını kontrol et
if ($email && $domain && !checkdnsrr($domain, "MX")) {
  header("location: ../Views/kayit.php?durum=domainHatali");
  exit;
}

if ($sifre === $sifre2) {
    // Şifreleri hashleme
  $sifre = hashPassword($sifre);
    // Veri kaydetme işlemi
  $kayitSonuc = kaydet($firma, $email, $tel, $adres, $sifre, $ip);

  if ($kayitSonuc === true) {
    header("location: ../Views/giris.php?durum=pasif");
    exit;
  } else {
    header("location: ../Views/kayit.php?durum=basarisiz");
    exit;
  }
} else {
    // Şifreler eşleşmiyorsa hata mesajı ve yönlendirme
  header("location: ../Views/kayit.php?durum=sifreHatali");
  exit;
}

// Giriş verilerini doğrulama fonksiyonu
function validateInput($data) {
    $data = trim($data); // Boşlukları temizleme
    $data = htmlspecialchars($data); // HTML karakterlerini dönüştürme
    $data = strip_tags($data); // HTML etiketlerini silme
    return $data;
  }

// Şifre hashleme fonksiyonu
  function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }
  ?>
