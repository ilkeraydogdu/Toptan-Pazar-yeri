<?php
require_once '../Model/kullaniciEkleModel.php';

$firma = validateInput($_POST['firma']);
$email = validateInput($_POST['email']);
$tel = validateInput($_POST['tel']);
$adres = validateInput($_POST['adres']);
$sifre = validateInput($_POST['sifre']);
$sifre2 = validateInput($_POST['sifre2']);
$rol = validateInput($_POST['rol']);
$durum = validateInput($_POST['durum']);
$ip = $_SERVER['REMOTE_ADDR'];

if ($sifre === $sifre2) {
  $sifre = hashPassword($sifre);
  $stmt = $db->prepare("INSERT INTO kullanici (firma, email, tel, adres, sifre, rol, durum, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$firma, $email, $tel, $adres, $sifre, $rol, $durum, $ip]);

  if ($stmt->rowCount() > 0) {
    header("location: ../Views/kullanici.php?durum=basarili");
  } else {
    header("location: ../Views/kullanici.php?durum=basarisiz");
  }
} else {
  header("location: ../Views/kullaniciEkle.php?durum=sifreHatali");
}

function validateInput($data){
  $data = trim($data);
  $data = htmlspecialchars($data);
  $data = strip_tags($data);
  return $data;
}

function hashPassword($password){
  return password_hash($password, PASSWORD_DEFAULT);
}

function kaydet($firma, $email, $tel, $adres, $sifre, $rol, $durum, $ip){
  kaydetModel($firma, $email, $tel, $adres, $sifre, $rol, $durum, $ip);
}
function kullaniciyiGetir($id) {
  $stmt = $db->prepare("SELECT * FROM kullanici WHERE id = ?");
  $stmt->execute([$id]);
  $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
  return $kullanici;
}

?>
