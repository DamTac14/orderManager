<?php
include_once 'db.php';

class CommandeController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function ajouterCommande($nom_client, $produit_id, $quantite, $prix_total) {
        if (empty($nom_client) || $quantite < 0 || $prix_total < 0) {
            return 'Données invalides'; 
        }

        $sql = "INSERT INTO commandes (nom_client, produit_id, quantite, prix_total, date_commande) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nom_client, $produit_id, $quantite, $prix_total]);
        return true; 
    }
    public function listerCommandes() {
        $sql = "SELECT c.*, p.nom AS produit_nom 
                FROM commandes c 
                JOIN produits p ON c.produit_id = p.id";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function modifierCommande($id, $nom_client, $produit_id, $quantite, $prix_total) {
        if (empty($nom_client) || $quantite < 0 || $prix_total < 0) {
            return 'Données invalides'; 
        }

        $sql = "UPDATE commandes SET nom_client = ?, produit_id = ?, quantite = ?, prix_total = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom_client, $produit_id, $quantite, $prix_total, $id]);
    }

    public function supprimerCommande($id) {
        $sql = "DELETE FROM commandes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
