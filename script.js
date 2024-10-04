document.addEventListener("DOMContentLoaded", function () {
    const produitsTableBody = document.querySelector("#produitsTable tbody");
    const commandesTableBody = document.querySelector("#commandesTable tbody");
    const produitForm = document.getElementById("produitForm");
    const commandeForm = document.getElementById("commandeForm"); 
    const submitButton = document.getElementById("submitButton");
    let currentProduitId = null;

    function fetchProduits() {
        fetch('/api/produits')
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
        fetch('/api/commandes')
            .then(response => response.json())
            .then(data => {
                commandesTableBody.innerHTML = "";
                data.forEach(commande => {
                    commandesTableBody.innerHTML += `
                        <tr>
                            <td>${commande.nom_client}</td>
                            <td>${commande.produit_nom}</td> 
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
        fetch('/api/produits')
            .then(response => response.json())
            .then(data => {
                const commandeProduitSelect = document.getElementById("commandeProduit");
                commandeProduitSelect.innerHTML = "";
    
                data.forEach(produit => {
                    commandeProduitSelect.innerHTML += `
                        <option value="${produit.id}" data-prix="${produit.prix}">${produit.nom}</option>
                    `;
                });
    
                commandeProduitSelect.addEventListener('change', updatePrixTotal);
            })
            .catch(error => console.error('Erreur lors de la récupération des produits :', error));
    }
    
    function updatePrixTotal() {
        const produitSelect = document.getElementById("commandeProduit");
        const quantiteInput = document.getElementById("commandeQuantite");
        const prixTotalInput = document.getElementById("prix_total");
    
        const prixProduit = produitSelect.options[produitSelect.selectedIndex].dataset.prix;
        const quantite = quantiteInput.value;
    
        const prixTotal = prixProduit * quantite;
        prixTotalInput.value = prixTotal.toFixed(2); 
    }
    
    document.getElementById("commandeQuantite").addEventListener('input', updatePrixTotal);
    
    
    produitForm.addEventListener("submit", function (e) {
        e.preventDefault(); 
        const formData = new FormData(produitForm);
        const formDataObject = {
            nom: formData.get('produitNom'),
            description: formData.get('produitDescription'),
            prix: formData.get('produitPrix'),
            quantite: formData.get('produitQuantite')
        };
    
        const actionUrl = currentProduitId 
            ? `/api/produits/${currentProduitId}` 
            : '/api/produits'; 
    
        fetch(actionUrl, {
            method: currentProduitId ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formDataObject)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur de réseau'); 
            }
            return response.json();
        })
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
        const formDataObject = Object.fromEntries(formData.entries());
    
        const produitSelect = document.getElementById("commandeProduit");
        const prixProduit = produitSelect.options[produitSelect.selectedIndex].dataset.prix;
        const quantite = formDataObject.quantite;
    
        const prixTotal = prixProduit * quantite;
    
        formDataObject.prix_total = prixTotal.toFixed(2); 
    
        fetch('/api/commandes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formDataObject)
        })
        .then(response => response.json())
        .then(data => {
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
        console.log('Modifier produit ID :', id); 
        fetch(`/api/produits/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la récupération du produit');
                }
                return response.json();
            })
            .then(produit => {
                console.log('Produit récupéré :', produit); 
    
                if (!produit) {
                    console.error('Produit non trouvé.');
                    alert('Erreur : produit non trouvé.');
                    return;
                }
    
                document.getElementById("produitId").value = produit.id || ''; 
                document.getElementById("produitNom").value = produit.nom || '';
                document.getElementById("produitDescription").value = produit.description || '';
                document.getElementById("produitPrix").value = produit.prix || '';
                document.getElementById("produitQuantite").value = produit.quantite || '';
    
                currentProduitId = produit.id;
                submitButton.textContent = "Modifier"; 
            })
            .catch(error => console.error('Erreur lors de la récupération du produit :', error));
    };
    
    
    window.supprimerProduit = function(id) {
        fetch(`/api/produits/${id}`, { method: 'DELETE' })
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
        fetch(`/api/commandes/${id}`, { method: 'DELETE' })
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
