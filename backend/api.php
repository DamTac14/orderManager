<?php
require_once 'db.php';
require_once 'produitController.php';
require_once 'commandeController.php';

// Récupération de l'instance PDO à partir de la fonction getDB
$pdo = getDB();

// Création des instances des contrôleurs
$produitController = new ProduitController($pdo);
$commandeController = new CommandeController($pdo);

// Récupère l'URI actuelle pour identifier la route
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Route vers `/api/produits`
if (preg_match('/\/api\/produits\/?(\d+)?/', $requestUri, $matches)) {
    $id = $matches[1] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                // Appel à la méthode de détail pour un produit spécifique
                $produit = $produitController->listerProduits(); // Ajustez ceci pour récupérer un produit spécifique
                echo json_encode($produit);
            } else {
                // Liste tous les produits
                $produits = $produitController->listerProduits();
                echo json_encode($produits);
            }
            http_response_code(200); // OK
            break;
        case 'POST':
            // Ajouter un produit, les données doivent être envoyées dans le corps de la requête
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $produitController->ajouterProduit($data['nom'], $data['description'], $data['prix'], $data['quantite']);
            if ($result === true) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Produit ajouté avec succès']);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => $result]); // Retourne le message d'erreur
            }
            break;
        case 'PUT':
            // Modifier un produit, les données doivent être envoyées dans le corps de la requête
            if ($id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $produitController->modifierProduit($id, $data['nom'], $data['description'], $data['prix'], $data['quantite']);
                if ($result === true) {
                    echo json_encode(['message' => 'Produit modifié avec succès']);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => $result]); // Retourne le message d'erreur
                }
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'ID requis pour modifier un produit']);
            }
            break;
        case 'DELETE':
            if ($id) {
                $produitController->supprimerProduit($id);
                http_response_code(204); // No Content
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'ID requis pour supprimer un produit']);
            }
            break;
        default:
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Méthode non supportée']);
    }
}

// Route vers `/api/commandes`
elseif (preg_match('/\/api\/commandes\/?(\d+)?/', $requestUri, $matches)) {
    $id = $matches[1] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                // Détails d'une commande spécifique (à implémenter si nécessaire)
                $commandes = $commandeController->listerCommandes(); // Ajustez ceci pour récupérer une commande spécifique
                echo json_encode($commandes);
            } else {
                // Liste toutes les commandes
                $commandes = $commandeController->listerCommandes();
                echo json_encode($commandes);
            }
            http_response_code(200); // OK
            break;
        case 'POST':
            // Ajouter une commande, les données doivent être envoyées dans le corps de la requête
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $commandeController->ajouterCommande($data['nom_client'], $data['produit_id'], $data['quantite'], $data['prix_total']);
            if ($result === true) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Commande ajoutée avec succès']);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => $result]); // Retourne le message d'erreur
            }
            break;
        case 'DELETE':
            if ($id) {
                $commandeController->supprimerCommande($id);
                http_response_code(204); // No Content
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'ID requis pour supprimer une commande']);
            }
            break;
        default:
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Méthode non supportée']);
    }
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Route non reconnue']);
}
?>
