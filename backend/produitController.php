<?php
require_once 'validation.php';

function getProduitsData() {
    $jsonData = file_get_contents(__DIR__ . '/db/produits.json');
    return json_decode($jsonData, true);
}

function saveProduitsData($data) {
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/db/produits.json', $jsonData);
}

function ajouterProduit() {
    $nom = $_POST['produitNom'] ?? '';
    $description = $_POST['produitDescription'] ?? '';
    $prix = $_POST['produitPrix'] ?? 0;
    $quantite = $_POST['produitQuantite'] ?? 0;

    error_log("Nom: $nom, Prix: $prix, Quantité: $quantite");

    if (!validerProduit($nom, $prix, $quantite)) {
        $erreurs = [];
        if (empty($nom)) $erreurs[] = 'Nom est vide';
        if (!is_numeric($prix) || $prix < 0) $erreurs[] = 'Prix est invalide';
        if (!is_numeric($quantite) || $quantite < 0) $erreurs[] = 'Quantité est invalide';
        
        echo json_encode(['error' => 'Données invalides: ' . implode(', ', $erreurs)]);
        return;
    }

    $produits = getProduitsData();

    $nouveau_produit = [
        'id' => count($produits) + 1, 
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'quantite' => $quantite
    ];

    $produits[] = $nouveau_produit;

    saveProduitsData($produits);

    echo json_encode(['success' => 'Produit ajouté']);
}


function modifierProduit() {
    // Récupérer les données envoyées dans la requête
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['produitId'] ?? null;
    $nom = $data['produitNom'] ?? '';
    $description = $data['produitDescription'] ?? '';
    $prix = $data['produitPrix'] ?? 0;
    $quantite = $data['produitQuantite'] ?? 0;

    error_log("ID: $id, Nom: $nom, Description: $description, Prix: $prix, Quantité: $quantite");

    // Validation des données
    if (!validerProduit($nom, $prix, $quantite)) {
        echo json_encode(['error' => 'Données invalides']);
        return;
    }

    $produits = getProduitsData();
    $produitTrouve = false;

    foreach ($produits as &$produit) {
        if ($produit['id'] == $id) {
            $produit['nom'] = $nom;
            $produit['description'] = $description;
            $produit['prix'] = $prix;
            $produit['quantite'] = $quantite;
            $produitTrouve = true;
            break;
        }
    }

    if (!$produitTrouve) {
        echo json_encode(['error' => 'Produit non trouvé']);
        return;
    }

    saveProduitsData($produits);
    echo json_encode(['success' => 'Produit modifié']);
}


function supprimerProduit() {
    $id = $_GET['id'] ?? 0;

    $produits = getProduitsData();

    $produits = array_filter($produits, function($produit) use ($id) {
        return $produit['id'] != $id;
    });

    $produits = array_values($produits);

    saveProduitsData($produits);

    echo json_encode(['success' => 'Produit supprimé']);
}

function listerProduits() {
    $produits = getProduitsData();
    echo json_encode($produits);
}

function detailProduit($id) {
    $produits = getProduitsData();

    $produit = null;
    foreach ($produits as $p) {
        if ($p['id'] == $id) {
            $produit = $p;
            break;
        }
    }

    if ($produit) {
        echo json_encode($produit);
    } else {
        echo json_encode(['error' => 'Produit non trouvé.']);
    }
}

?>
