<?php
require_once 'produitController.php';
require_once 'commandeController.php';

// Récupère l'URI actuelle pour identifier la route
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Route vers `/api/produits`
if (preg_match('/\/api\/produits\/?(\d+)?/', $requestUri, $matches)) {
    $id = $matches[1] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                detailProduit($id); // Si un ID est spécifié, récupère le produit correspondant
            } else {
                listerProduits(); // Sinon, liste tous les produits
            }
            break;
            case 'PUT':
                // Récupérer les données JSON brutes envoyées dans la requête
                $data = json_decode(file_get_contents("php://input"), true);
            
                // Validation des données
                if (empty($data['produitNom']) || !is_numeric($data['produitPrix']) || !is_numeric($data['produitQuantite'])) {
                    echo json_encode(['error' => 'Données invalides']);
                    return;
                }
            
                // Logique pour mettre à jour le produit dans la base de données ici
                modifierProduit(); // Ajoutez cette ligne pour appeler la fonction de modification
            
                echo json_encode(['success' => 'Produit mis à jour']);
                break;
            
        case 'DELETE':
            if ($id) {
                supprimerProduit($id); 
            }
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
    }
}

// Route vers `/api/commandes`
elseif (preg_match('/\/api\/commandes\/?(\d+)?/', $requestUri, $matches)) {
    $id = $matches[1] ?? null;

    switch ($method) {
        case 'GET':
            listerCommandes(); 
            break;
        case 'POST':
            ajouterCommande();
            break;
        case 'DELETE':
            if ($id) {
                supprimerCommande($id); 
            }
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
    }
} else {
    echo json_encode(['error' => 'Route non reconnue']);
}
?>
