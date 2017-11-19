<?php

namespace App\Controllers;

use \App\View\View as View;
use \App\Utils\Session as Session;
use \App\Utils\ErrorLogger as ErrorLogger;

/**
 * Contrôleur du suivi de paiement de fiche de frais
 */
class ValidationFraisController {

    /**
     * Récupération du singleton de base de données
     * Vérification du type de l'utilisateur
     * S'il n'est pas comptable, on le redirige vers la page d'accueil
     */
    public function __construct() {
        try {
            // on vérifie que l'utilisateur soit comptable, sinon on le redirige vers l'accueil
            Session::check('type', 'CPTBL');
            $this->db = \App\Database::getInstance();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // on récupère la liste des visiteurs possédant au moins une fiche de frais
        $this->lesVisiteurs = $this->db->getLesVisiteursAyantFichesFrais();        
    }

    /**
     * Page d'accueil de la validation des fiches de frais avec la sélection du visiteur et du mois
     * @return view Vue de la validation des fiches de frais avec la sélection du visiteur et du mois
     */
    public function index() {
        
        // Si on accède à la page pour le première fois, on génère la vue avec les infos basiques
        if (!isset($_POST['lstVisiteurs'])) {
            $this->idVisiteur = $this->lesVisiteurs[0];
            $this->lesMois = $this->db->getLesMoisDisponibles($this->idVisiteur['id']);
            $this->mois = $this->lesMois[0];

            // Définition des valeurs finales pour la création de la vue
            $this->idVisiteurFinal = $this->idVisiteur['id'];
            $this->moisFinal = $this->mois['mois'];
        } else {
            $this->idVisiteur = $_POST['lstVisiteurs'];
            // On récupère les mois pour lesquelles le visiteur choisi a une fiche de frais
            $this->lesMois = $this->db->getLesMoisDisponibles($this->idVisiteur);
            // Vérifications sur l'existence du mois passé en post dans la liste des mois du visiteur choisi
            $moisChoisi = $this->lesMois[0]["mois"];
            for ($i = 0; $i < count($this->lesMois); $i++) {
                if (in_array($_POST['lstMois'], $this->lesMois[$i])) {
                    $moisChoisi = $_POST['lstMois'];
                }
            }
            $this->mois = $moisChoisi;

            // Définition des valeurs finales pour la création de la vue
            $this->idVisiteurFinal = $this->idVisiteur;
            $this->moisFinal = $this->mois;
        }        
  
        if (isset($_POST['corrigerForfait'])) {
            $this->corrigerForfait($this->idVisiteurFinal, $this->moisFinal);
        } else if (isset($_POST['reinitForfait'])) {
            $this->reinitForfait();
        }
        
        // Création de la vue
        View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
            'moisASelectionner' => $this->mois,
            'lesVisiteurs' => $this->lesVisiteurs,
            'visiteurASelectionner' => $this->idVisiteur,
            'lesFraisForfait' => $this->db->getLesFraisForfait($this->idVisiteurFinal, $this->moisFinal)
        ));
    }

    /**
     * Fonction permettant de corriger un frais forfait pour un visiteur et un mois donné.
     * 
     * @param type $idVis
     * @param type $mois
     */
    public function corrigerForfait($idVis, $mois) {
        // on vérifie que le tableau de données envoyé soit un tableau d'entiers positif
        if (\App\Utils\ArrayUtils::isIntArray($_POST['lesFrais'])) {
            // on met à jour les frais
            $req = $this->db->majFraisForfait($idVis, $mois, $_POST['lesFrais']);
        } else {
            ErrorLogger::add('Les valeurs des frais doivent être numériques');
            // on affiche la vue renseignerFicheFrais avec les données à charger par défaut
            $this->index();
        }
    }
    
    
    public function reinitForfait() {
        // TODO: Annulation des modifications en cours sur la fiche de frais
    }
}
