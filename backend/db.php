<?php
function getDB() {
    static $db = null; 
    if ($db === null) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=gestion_produits_commandes;charset=utf8mb4', 'root', 'votre_mot_de_passe');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection error: " . $e->getMessage();
        }
    }
    return $db;
}
