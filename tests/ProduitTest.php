<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/produitController.php';

class ProduitTest extends TestCase {

    protected function setUp(): void {
        // Réinitialiser le fichier JSON pour les tests
        file_put_contents(__DIR__ . '/../backend/db/produits.json', json_encode([]));

        // Ajouter un produit initial
        $produits = getProduitsData(); // Supposons que cette fonction lit les données JSON
        $produits[] = [
            'nom' => 'Produit 1',
            'description' => 'Description 1',
            'prix' => 10.5,
            'quantite' => 20
        ];
        saveProduitsData($produits); // Fonction pour sauvegarder les données
    }

    protected function tearDown(): void {
        // Réinitialiser le fichier JSON après chaque test
        file_put_contents(__DIR__ . '/../backend/db/produits.json', json_encode([]));
    }

    public function testAjoutProduitValide() {
        $_POST['produitNom'] = 'Produit Test';
        $_POST['produitDescription'] = 'Description Test';
        $_POST['produitPrix'] = 15.0;
        $_POST['produitQuantite'] = 30;

        ob_start();
        ajouterProduit();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Produit ajouté', $response['success']);

        $produits = getProduitsData();
        $produit = array_filter($produits, function($p) {
            return $p['nom'] === 'Produit Test';
        });
        
        $this->assertNotEmpty($produit);
        $this->assertEquals('Produit Test', reset($produit)['nom']);
    }

    public function testAjoutProduitInvalide() {
        $_POST['produitNom'] = 'Produit Invalide';
        $_POST['produitDescription'] = 'Description';
        $_POST['produitPrix'] = -5.0; // Prix invalide
        $_POST['produitQuantite'] = 10;

        ob_start();
        ajouterProduit();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Données invalides', $response['error']);

        $produits = getProduitsData();
        $produit = array_filter($produits, function($p) {
            return $p['nom'] === 'Produit Invalide';
        });
        
        $this->assertEmpty($produit);
    }

    public function testModificationProduit() {
        $produits = getProduitsData();
        $produitId = 0; // On suppose que le produit a un ID basé sur sa position dans le tableau

        $_POST['produitId'] = $produitId;
        $_POST['produitNom'] = 'Produit Modifié';
        $_POST['produitDescription'] = 'Description Modifiée';
        $_POST['produitPrix'] = 20.0;
        $_POST['produitQuantite'] = 25;

        ob_start();
        modifierProduit();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Produit modifié', $response['success']);

        $produits = getProduitsData();
        $produitModifie = $produits[$produitId];
        $this->assertEquals('Produit Modifié', $produitModifie['nom']);
        $this->assertEquals(20.0, $produitModifie['prix']);
    }

    public function testSuppressionProduit() {
        $produits = getProduitsData();
        $produitId = 0; // On suppose que le produit a un ID basé sur sa position dans le tableau

        $_GET['id'] = $produitId;

        ob_start();
        supprimerProduit();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertStringContainsString('Produit supprimé', $response['success']);

        $produits = getProduitsData();
        $this->assertArrayNotHasKey($produitId, $produits);
    }

    public function testListerProduits() {
        ob_start();
        listerProduits();
        $output = ob_get_clean();

        $produits = json_decode($output, true);
        $this->assertIsArray($produits);
        $this->assertCount(1, $produits);
        $this->assertEquals('Produit 1', $produits[0]['nom']);
    }
}
