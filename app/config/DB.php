<?php
define("URL","http://localhost/kabaloglukuyumculuk");
define("DB_HOST", "localhost:3306");
define("DB_DATABASE", "projem");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");

function getDbConnection() {
	try {
		$db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_DATABASE.";charset=utf8", DB_USERNAME, DB_PASSWORD);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	} catch (PDOException $e) {
		// Hata oluştuğunda hatayı ekrana yazdır
		die("Veritabanı bağlantısı sırasında bir hata oluştu: " . $e->getMessage());
	}
}


global $db;
$db = getDbConnection();

?>
