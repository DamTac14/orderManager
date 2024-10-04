# Fichiers de test
CommandeTest.php : tests des fonctionnalités liées aux commandes.
ProduitTest.php : tests des fonctionnalités liées aux produits.

# Cas de Test

## Commandes
- testAjoutCommandeValide() : Vérifie l'ajout d'une commande valide.
- testAjoutCommandeInvalide() : Vérifie la gestion des erreurs lors de l'ajout d'une commande avec des données invalides.
- testAjoutCommandeSansNomClient() : Vérifie que l'ajout d'une commande sans nom client retourne une erreur.
- testAjoutCommandeQuantiteNegatif() : Vérifie que l'ajout d'une commande avec une quantité négative retourne une erreur.
- testAjoutCommandePrixNegatif() : Vérifie que l'ajout d'une commande avec un prix négatif retourne une erreur.
- testListerCommandes() : Vérifie que la liste des commandes est retournée correctement.

## Produits
- testAjoutProduitValide() : Vérifie l'ajout d'un produit valide.
- testAjoutProduitInvalide() : Vérifie la gestion des erreurs lors de l'ajout d'un produit avec des données invalides.
- testAjoutProduitPrixNegatif() : Vérifie que l'ajout d'un produit avec un prix négatif retourne une erreur.
- testAjoutProduitQuantiteNegatif() : Vérifie que l'ajout d'un produit avec une quantité négative retourne une erreur.
- testModificationProduit() : Vérifie que les produits peuvent être modifiés.
- testSuppressionProduit() : Vérifie que les produits peuvent être supprimés.
- testListerProduits() : Vérifie que la liste des produits est retournée correctement.


# Exécution des Tests
# Utilisez la commande suivante pour exécuter les tests :

vendor/bin/phpunit --bootstrap vendor/autoload.php tests/

# Voir db.php pour la connexion en bdd sur serveur distant