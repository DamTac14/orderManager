<?php
require_once 'validation.php';

function getCommandesData() {
    $jsonData = file_get_contents(__DIR__ . '/db/commandes.json');
    return json_decode($jsonData, true);
}

function saveCommandesData($data) {
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/db/commandes.json', $jsonData);
}

function ajouterCommande() {
    $nom_client = $_POST['nom_client'] ?? null;
    $produit_id = $_POST['produit_id'] ?? null;
    $quantite = $_POST['quantite'] ?? null;

    if (!validerCommande($nom_client, $quantite)) {
        echo json_encode(['error' => 'Donnees invalides']);
        return;
    }

    $commandes = getCommandesData();

    $nouvelle_commande = [
        'id' => count($commandes) + 1, 
        'nom_client' => $nom_client,
        'produit_id' => $produit_id,
        'quantite' => $quantite,
        'date_commande' => date('Y-m-d H:i:s')
    ];

    $commandes[] = $nouvelle_commande;

    saveCommandesData($commandes);

    echo json_encode(['success' => 'Commande ajoutee']);
}

function listerCommandes() {
    try {
        $commandes = getCommandesData();

        $produits = json_decode(file_get_contents(__DIR__ . '/db/produits.json'), true);

        foreach ($commandes as &$commande) {
            foreach ($produits as $produit) {
                if ($produit['id'] == $commande['produit_id']) {
                    $commande['produit'] = $produit['nom'];
                    break;
                }
            }
        }

        echo json_encode($commandes);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erreur lors de la recuperation des commandes : ' . $e->getMessage()]);
    }
}

function supprimerCommande() {
    $id = $_GET['id'] ?? null;

    if (empty($id)) {
        echo json_encode(['error' => 'ID de la commande manquant']);
        return;
    }

    try {
        $commandes = getCommandesData();

        // Vérifiez si la commande existe
        $commandeExists = false;
        foreach ($commandes as $commande) {
            if ($commande['id'] == intval($id)) { // Assurez-vous de comparer en tant qu'entier
                $commandeExists = true;
                break;
            }
        }

        if (!$commandeExists) {
            echo json_encode(['error' => 'Commande non trouvée']);
            return;
        }

        // Supprimer la commande
        $commandes = array_filter($commandes, function($commande) use ($id) {
            return $commande['id'] != intval($id); // Assurez-vous de comparer en tant qu'entier
        });

        $commandes = array_values($commandes);
        saveCommandesData($commandes);

        echo json_encode(['success' => 'Commande supprimée']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erreur lors de la suppression de la commande : ' . $e->getMessage()]);
    }
}


?>
