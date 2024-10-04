<?php
include_once '../backend/produitController.php';
include_once '../backend/db.php';

$pdo = getDB();
$produitController = new ProduitController($pdo);

$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$id = null;
if (preg_match('/\/api\/produits\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
}

switch ($method) {
    case 'GET':
        if ($id) {
            $produit = $produitController->getProduitById($id);
            if ($produit) {
                echo json_encode($produit);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Produit non trouvé']);
            }
        } else {
            $produits = $produitController->listerProduits();
            echo json_encode($produits);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $produitController->ajouterProduit(
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['quantite']
        );
        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Produit ajouté avec succès']);
        } else {
            http_response_code(400); 
            echo json_encode(['error' => $result]);
        }
        break;

    case 'PUT':
        if ($id) {
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
            http_response_code(400); 
            echo json_encode(['error' => 'ID requis pour modifier un produit']);
        }
        break;

    case 'DELETE':
        if ($id) {
            $result = $produitController->supprimerProduit($id);
            if ($result) {
                http_response_code(204); 
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Produit non trouvé']);
            }
        } else {
            http_response_code(400); 
            echo json_encode(['error' => 'ID requis pour supprimer un produit']);
        }
        break;

    default:
        http_response_code(405); 
        echo json_encode(['error' => 'Méthode non supportée']);
}
