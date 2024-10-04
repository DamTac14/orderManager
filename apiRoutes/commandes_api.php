<?php
include_once '../backend/commandeController.php';
include_once '../backend/db.php';

$pdo = getDB();
$commandeController = new CommandeController($pdo);

$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$id = null;
if (preg_match('/\/api\/commandes\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
}

switch ($method) {
    case 'GET':
            $commandes = $commandeController->listerCommandes();
            echo json_encode($commandes);
        break;

    case 'POST':
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
            $result = $commandeController->supprimerCommande($id);
            if ($result) {
                http_response_code(204); 
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
