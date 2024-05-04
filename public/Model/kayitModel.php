<?php
require_once '../../app/config/DB.php';

function kaydet($firma, $email, $tel, $adres, $sifre, $ip) {
  global $db;
  $sql = "INSERT INTO kullanici (firma, email, tel, adres, sifre, ip) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$firma, $email, $tel, $adres, $sifre, $ip]);
  if ($stmt->rowCount() > 0) {
    return true;
  } else {
    return false;
  }
}
?>
