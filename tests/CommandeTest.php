<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/commandeController.php';
require_once __DIR__ . '/../backend/produitController.php'; 

class CommandeTest extends TestCase {
    protected function setUp(): void {
        file_put_contents(__DIR__ . '/../backend/db/commandes.json', json_encode([]));
        
        $produitsFilePath = __DIR__ . '/../backend/db/produits.json';
        $produits = json_decode(file_get_contents($produitsFilePath), true);
        $produitExists = false;

        foreach ($produits as $produit) {
            if ($produit['nom'] === 'Produit A') {
                $produitExists = true;
                break;
            }
        }

        
        if (!$produitExists) {
            $produits[] = [
                'nom' => 'Produit A',
                'description' => 'Description du produit A',
                'prix' => 15.00,
                'quantite' => 10,
                'created_at' => date('Y-m-d') 
            ];
            file_put_contents($produitsFilePath, json_encode($produits));
        }

        // Ajouter une commande initiale
        $commandes = getCommandesData(); 
        $commandes[] = [
            'nom_client' => 'Client 1',
            'produit_id' => 1, 
            'quantite' => 2,
            'prix_total' => 30.00, 
            'date_commande' => date('Y-m-d H:i:s')
        ];
        saveCommandesData($commandes); 
    }

    protected function tearDown(): void {
        // Reinitialiser le fichier JSON après chaque test
        file_put_contents(__DIR__ . '/../backend/db/commandes.json', json_encode([]));
    }

    public function testAjoutCommandeValide() {
        $_POST['nom_client'] = 'Client Test';
        $_POST['produit_id'] = 1; 
        $_POST['quantite'] = 5;

        ob_start();
        ajouterCommande();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Commande ajoutee', $response['success']);

        $commandes = getCommandesData();
        $commande = array_filter($commandes, function($c) {
            return $c['nom_client'] === 'Client Test';
        });
        
        $this->assertNotEmpty($commande);
        $this->assertEquals('Client Test', reset($commande)['nom_client']);
    }

    public function testAjoutCommandeInvalide() {
        $_POST['nom_client'] = 'Client Invalide';
        $_POST['produit_id'] = 1; 
        $_POST['quantite'] = -5;

        ob_start();
        ajouterCommande();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Donnees invalides', $response['error']);

        $commandes = getCommandesData();
        $commande = array_filter($commandes, function($c) {
            return $c['nom_client'] === 'Client Invalide';
        });
        
        $this->assertEmpty($commande);
    }

    public function testAjoutCommandeSansNomClient() {
        $_POST['nom_client'] = ''; 
        $_POST['produit_id'] = 1; 
        $_POST['quantite'] = 2;

        ob_start();
        ajouterCommande();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Donnees invalides', $response['error']);
    }

    public function testAjoutCommandeQuantiteNegatif() {
        $_POST['nom_client'] = 'Client Test';
        $_POST['produit_id'] = 1; 
        $_POST['quantite'] = -10;

        ob_start();
        ajouterCommande();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Donnees invalides', $response['error']);
    }

    public function testListerCommandes() {
        ob_start();
        listerCommandes();
        $output = ob_get_clean();

        $commandes = json_decode($output, true);
        $this->assertIsArray($commandes);
        $this->assertCount(1, $commandes); 
        $this->assertEquals('Client 1', $commandes[0]['nom_client']);
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
    
}
