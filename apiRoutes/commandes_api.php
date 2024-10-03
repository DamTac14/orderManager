<?php
include_once '../backend/commandeController.php';
include_once '../backend/db.php'; 

$pdo = getDB();

$commandeController = new commandeController($pdo);

$commandes = $commandeController->listerCommandes();

echo json_encode($commandes);
