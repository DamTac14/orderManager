<?php
include_once 'db.php';

class ProduitController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function ajouterProduit($nom, $description, $prix, $quantite) {
        if (empty($nom) || $prix < 0 || $quantite < 0) {
            return 'Données invalides';
        }

        $sql = "INSERT INTO produits (nom, description, prix, quantite, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nom, $description, $prix, $quantite]);
        return true; 
    }

    public function listerProduits() {
        $sql = "SELECT * FROM produits";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modifierProduit($id, $nom, $description, $prix, $quantite) {
        if (empty($nom) || $prix < 0 || $quantite < 0) {
            return 'Données invalides'; 
        }

        $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, quantite = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $description, $prix, $quantite, $id]);
    }

    public function getProduitById($id) {
        $sql = "SELECT * FROM produits WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function supprimerProduit($id) {
        $sql = "DELETE FROM produits WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
