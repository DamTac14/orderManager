<?php

require_once __DIR__ . '/../backend/CommandeController.php';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/ProduitController.php';
// CommandeControllerTest.php
use PHPUnit\Framework\TestCase;

class CommandeTest extends TestCase {
    private $commandeController;
    private $pdoMock;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->commandeController = new CommandeController($this->pdoMock);
    }

    public function testAjoutCommandeValide() {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        $result = $this->commandeController->ajouterCommande('Client Test', 1, 2, 100);
        $this->assertTrue($result);
    }

    public function testAjoutCommandeInvalide() {
        $result = $this->commandeController->ajouterCommande('', 1, 2, 100);
        $this->assertEquals('Données invalides', $result);
    }

    public function testAjoutCommandeQuantiteNegatif() {
        $result = $this->commandeController->ajouterCommande('Client Test', 1, -2, 100);
        $this->assertEquals('Données invalides', $result);
    }

    public function testAjoutCommandePrixNegatif() {
        $result = $this->commandeController->ajouterCommande('Client Test', 1, 2, -100);
        $this->assertEquals('Données invalides', $result);
    }

    public function testListerCommandes() {
        $stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock->method('query')->willReturn($stmtMock);
        $result = $this->commandeController->listerCommandes();
        $this->assertIsArray($result);
    }

    
}

?>
