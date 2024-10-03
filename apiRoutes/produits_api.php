<?php
include_once '../backend/produitController.php';
include_once '../backend/db.php';

$pdo = getDB();
$produitController = new ProduitController($pdo);

// Récupération de l'URI actuelle et de la méthode HTTP
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Extraction de l'ID du produit s'il est présent dans l'URL
$id = null;
if (preg_match('/\/api\/produits\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
}

// Gestion des différentes méthodes HTTP
switch ($method) {
    case 'GET':
        if ($id) {
            // Récupère un produit spécifique
            $produit = $produitController->getProduitById($id);
            if ($produit) {
                echo json_encode($produit);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Produit non trouvé']);
            }
        } else {
            // Liste tous les produits
            $produits = $produitController->listerProduits();
            echo json_encode($produits);
        }
        break;

    case 'POST':
        // Ajoute un nouveau produit
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $produitController->ajouterProduit(
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['quantite']
        );
        if ($result === true) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Produit ajouté avec succès']);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => $result]);
        }
        break;

    case 'PUT':
        if ($id) {
            // Modifie un produit existant
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $produitController->modifierProduit(
                $id,
                $data['nom'],
                $data['description'],
                $data['prix'],
                $data['quantite']
            );
            if ($result === true) {
                echo json_encode(['message' => 'Produit modifié avec succès']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'ID requis pour modifier un produit']);
        }
        break;

    case 'DELETE':
        if ($id) {
            // Supprime un produit spécifique
            $result = $produitController->supprimerProduit($id);
            if ($result) {
                http_response_code(204); // No Content, suppression réussie
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Produit non trouvé']);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'ID requis pour supprimer un produit']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Méthode non supportée']);
}
