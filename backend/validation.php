<?php
function validerProduit($nom, $prix, $quantite) {
    return !empty($nom) && $prix >= 0 && is_numeric($quantite) && $quantite >= 0;
}



function validerCommande($nom_client, $quantite) {
    return !empty($nom_client) && is_numeric($quantite) && $quantite > 0;
}

?>
