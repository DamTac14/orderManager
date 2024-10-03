<?php
include_once '../backend/commandeController.php';
include_once '../backend/db.php';

$pdo = getDB();
$commandeController = new CommandeController($pdo);

// Récupération de l'URI actuelle et de la méthode HTTP
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Extraction de l'ID de la commande s'il est présent dans l'URL
$id = null;
if (preg_match('/\/api\/commandes\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
}

// Gestion des différentes méthodes HTTP
switch ($method) {
    case 'GET':
            // Récupère la liste de toutes les commandes
            $commandes = $commandeController->listerCommandes();
            echo json_encode($commandes);
        break;

    case 'POST':
        // Ajoute une nouvelle commande
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $commandeController->ajouterCommande(
            $data['nom_client'],
            $data['produit_id'],
            $data['quantite'],
            $data['prix_total']
        );
        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Commande ajoutée avec succès']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => $result]);
        }
        break;

    case 'DELETE':
        if ($id) {
            // Supprime une commande spécifique
            $result = $commandeController->supprimerCommande($id);
            if ($result) {
                http_response_code(204); // Pas de contenu, suppression réussie
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Commande non trouvée']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID requis pour supprimer une commande']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non supportée']);
}
