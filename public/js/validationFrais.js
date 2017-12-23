/**
 * Ajout de [REFUSÉ] au libellé d'une ligne de frais hors forfait
 * @param  int ligne Libellé de la ligne à changer
 */
function refus(ligne) {
    var horsForfait = document.getElementById('libelleFraisHorsForfait_' + ligne);
    var libelle = horsForfait.value;
    if (horsForfait.value.substring(0, 8) != '[REFUSÉ]') {
        horsForfait.value = "[REFUSÉ] " + libelle;
        changeButton(ligne, true);
    } else {
        changeButton(ligne, false);
        horsForfait.value = horsForfait.value.substring(9);
    }
}

/**
 * Fonction pour changer les données d'un bouton
 * @param  int ligne Bouton de la ligne à changer
 * @param  boolean refus 
 */
function changeButton(ligne, refus) {
    var btn = document.getElementById("buttonRefus_" + ligne);
    if (refus == true) {
        btn.className = 'btn btn-success';
        btn.innerHTML = '<i class="fa fa-check"></i>';
        btn.title = 'Accepter';
    } else {
        btn.className = 'btn btn-danger';
        btn.innerHTML = '<i class="fa fa-ban"></i>';
        btn.title = 'Refuser';
    }
}

/**
 * Fonction pour désactiver/activer les données hors forfait pour empêcher la modification 
 * @param  int ligne Elements de la ligne à desactiver
 */
function validerForfait(ligne) {
    var libelle = document.getElementById('libelleFraisHorsForfait_' + ligne);
    var date = document.getElementById('dateFraisHorsForfait_' + ligne);
    var montant = document.getElementById('montantFraisHorsForfait_' + ligne);
    var reset = document.getElementById('buttonReset_' + ligne);
    var refus = document.getElementById('buttonRefus_' + ligne);
    if (libelle.getAttribute('readonly') == null) {
        libelle.setAttribute('readonly', true);
        date.setAttribute('readonly', true);
        montant.setAttribute('readonly', true);
    } else {
        libelle.removeAttribute('readonly');
        date.removeAttribute('readonly');
        montant.removeAttribute('readonly');
    }
    reset.disabled = !reset.disabled;
    refus.disabled = !refus.disabled;
}

function resetInput(ligne) {
    var libelle = document.getElementById('libelleFraisHorsForfait_' + ligne);
    libelle.value = libelle.defaultValue;
    var date = document.getElementById('dateFraisHorsForfait_' + ligne);
    date.value = date.defaultValue;
    var montant = document.getElementById('montantFraisHorsForfait_' + ligne);
    montant.value = montant.defaultValue;
}
