<?php
function getDB() {
    static $db = null; // Utiliser static pour garder la connexion entre les appels
    if ($db === null) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=gestion_produits_commandes;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection error: " . $e->getMessage();
        }
    }
    return $db;
}
