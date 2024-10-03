<?php
include_once '../backend/produitController.php';
include_once '../backend/db.php'; 

$pdo = getDB();

$produitController = new ProduitController($pdo);

$produits = $produitController->listerProduits();

echo json_encode($produits);
