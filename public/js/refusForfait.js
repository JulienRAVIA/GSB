function refus(ligne) {

    var horsForfait = document.getElementById('fraisHorsForfait_' + ligne);
    var libelle = horsForfait.value;
    var btn = document.getElementById("buttonRefus_" + ligne);
    if (horsForfait.value.substring(0, 8) != '[REFUSÉ]') {
    	horsForfait.value = "[REFUSÉ] " + libelle; 
    	btn.className = 'btn btn-success';
    	btn.innerHTML = '<i class="fa fa-check"></i>';
    	btn.title = 'Accepter';
    } else {
    	btn.className = 'btn btn-danger';
    	btn.innerHTML = '<i class="fa fa-ban"></i>';
    	btn.title = 'Refuser';
    	horsForfait.value = horsForfait.value.substring(9);
    }
}