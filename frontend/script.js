document.addEventListener("DOMContentLoaded", function () {
    const produitsTableBody = document.querySelector("#produitsTable tbody");
    const commandesTableBody = document.querySelector("#commandesTable tbody");
    const produitForm = document.getElementById("produitForm");
    const commandeForm = document.getElementById("commandeForm"); 
    const submitButton = document.getElementById("submitButton");
    let currentProduitId = null;

    function fetchProduits() {
        fetch('../backend/api.php/api/produits')
            .then(response => response.json())
            .then(data => {
                produitsTableBody.innerHTML = "";
                data.forEach(produit => {
                    produitsTableBody.innerHTML += `
                        <tr>
                            <td>${produit.nom}</td>
                            <td>${produit.description}</td>
                            <td>${produit.prix}</td>
                            <td>${produit.quantite}</td>
                            <td>
                                <button onclick="modifierProduit(${produit.id})">Modifier</button>
                                <button onclick="supprimerProduit(${produit.id})">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error('Erreur lors de la récupération des produits :', error));
    }


    function fetchCommandes() {
        fetch('../backend/api.php/api/commandes')
            .then(response => response.json())
            .then(data => {
                commandesTableBody.innerHTML = "";
                data.forEach(commande => {
                    commandesTableBody.innerHTML += `
                        <tr>
                            <td>${commande.nom_client}</td>
                            <td>${commande.produit}</td>
                            <td>${commande.quantite}</td>
                            <td>${commande.date_commande}</td>
                            <td>
                                <button onclick="supprimerCommande(${commande.id})">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error('Erreur lors de la récupération des commandes :', error));
    }

    function fetchProduitsForCommande() {
        fetch('../backend/api.php/api/produits')
            .then(response => response.json())
            .then(data => {
                const commandeProduitSelect = document.getElementById("commandeProduit");
                commandeProduitSelect.innerHTML = ""; 
                
                data.forEach(produit => {
                    commandeProduitSelect.innerHTML += `
                        <option value="${produit.id}">${produit.nom}</option>
                    `;
                });
            })
            .catch(error => console.error('Erreur lors de la récupération des produits :', error));
    }
    
    produitForm.addEventListener("submit", function (e) {
        e.preventDefault(); 
        const formData = new FormData(produitForm);
        const formDataObject = Object.fromEntries(formData.entries()); // Crée un objet à partir de FormData
    
        const actionUrl = currentProduitId 
            ? `../backend/api.php/api/produits/${currentProduitId}` // Pour le PUT
            : '../backend/api.php/api/produits'; // Pour le POST
    
        fetch(actionUrl, {
            method: currentProduitId ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formDataObject) // Convertir en JSON
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                fetchProduits(); 
                produitForm.reset(); 
                currentProduitId = null; 
                submitButton.textContent = "Enregistrer"; 
            }
        })
        .catch(error => console.error('Erreur lors de l\'ajout ou de la modification :', error));
    });
    
    
    commandeForm.addEventListener("submit", function (e) {
        e.preventDefault(); 
        const formData = new FormData(commandeForm);
    
        fetch('../backend/api.php/api/commandes', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
          .then(data => {
              console.log(data);
              if (data.error) {
                  alert(data.error);
              } else {
                  fetchCommandes(); 
                  commandeForm.reset(); 
              }
          })
          .catch(error => console.error('Erreur lors de la création de la commande :', error));
    });

      window.modifierProduit = function(id) {
        fetch(`../backend/api.php/api/produits/${id}`)
            .then(response => response.json())
            .then(produit => {
                if (produit.error) {
                    alert(produit.error);
                    return;
                }
    
                document.getElementById("produitId").value = produit.id; 
                document.getElementById("produitNom").value = produit.nom;
                document.getElementById("produitDescription").value = produit.description;
                document.getElementById("produitPrix").value = produit.prix; // Assurez-vous que ce champ est de type nombre
                document.getElementById("produitQuantite").value = produit.quantite; // Idem ici
    
                currentProduitId = produit.id;
                submitButton.textContent = "Modifier"; // Ceci devrait fonctionner maintenant
            })
            .catch(error => console.error('Erreur lors de la récupération du produit :', error));
    };
    

    window.supprimerProduit = function(id) {
        fetch(`../backend/api.php/api/produits/${id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    fetchProduits();
                }
            })
            .catch(error => console.error('Erreur lors de la suppression du produit :', error));
    };

    window.supprimerCommande = function(id) {
        fetch(`../backend/api.php/api/commandes/${id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    fetchCommandes();
                }
            })
            .catch(error => console.error('Erreur lors de la suppression de la commande :', error));
    };

    fetchProduits();
    fetchProduitsForCommande();
    fetchCommandes();
});
