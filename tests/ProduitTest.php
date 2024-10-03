<?php

require_once __DIR__ . '/../backend/ProduitController.php';
require_once __DIR__ . '/../backend/db.php';

// ProduitControllerTest.php
use PHPUnit\Framework\TestCase;

class ProduitTest extends TestCase {
    private $produitController;
    private $pdoMock;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->produitController = new ProduitController($this->pdoMock);
    }

    public function testAjoutProduitValide() {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        $result = $this->produitController->ajouterProduit('Produit Test', 'Description Test', 50, 10);
        $this->assertTrue($result);
    }

    public function testAjoutProduitInvalide() {
        $result = $this->produitController->ajouterProduit('', 'Description Test', 50, 10);
        $this->assertEquals('Données invalides', $result);
    }

    public function testAjoutProduitPrixNegatif() {
        $result = $this->produitController->ajouterProduit('Produit Test', 'Description Test', -50, 10);
        $this->assertEquals('Données invalides', $result);
    }

    public function testAjoutProduitQuantiteNegatif() {
        $result = $this->produitController->ajouterProduit('Produit Test', 'Description Test', 50, -10);
        $this->assertEquals('Données invalides', $result);
    }

    public function testModificationProduit() {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $this->pdoMock->method('prepare')->willReturn($stmtMock);
        
        $result = $this->produitController->modifierProduit(1, 'Produit Modifié', 'Nouvelle Description', 60, 15);
        $this->assertTrue($result);
    }

    public function testSuppressionProduit() {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $this->pdoMock->method('prepare')->willReturn($stmtMock);
        
        $result = $this->produitController->supprimerProduit(1);
        $this->assertTrue($result);
    }

    public function testListerProduits() {
        $stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock->method('query')->willReturn($stmtMock);
        $result = $this->produitController->listerProduits();
        $this->assertIsArray($result);
    }
}

?>
