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
        $this->lesMois = $this->db->getLesMoisDisponibles("*");
        $this->lesVisiteurs = $this->db->getLesVisiteursAyantFichesFrais();
    }

    /**
     * Page d'accueil du suivi des paiements avec la liste de séléction de mois
     * @return view Vue du suivi de paiements avec la liste de séléction des mois
     */
    public function index() {
        View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
        'moisASelectionner' => $this->lesMois[0],
        'lesVisiteurs' => $this->lesVisiteurs));
    }

    /**
     * Affichage des fiches de frais d'un mois
     * @return view Vue de la page d'affichage des fiches de frais avec état
     */
    public function showEtat() {
        $lesMois = $this->db->getLesMoisDisponibles($this->idVisiteur);
        $leMois = $_POST['lstMois'];
        $lesInfosFicheFrais = $this->db->getLesInfosFicheFrais($this->idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $lesFraisHorsForfait = $this->db->getLesFraisHorsForfait($this->idVisiteur, $leMois);
        $lesFraisForfait = $this->db->getLesFraisForfait($this->idVisiteur, $leMois);
        $dateModif = \App\Utils\Date::EngToFr($lesInfosFicheFrais['dateModif']);
        View::make('validationFrais.twig', $array = array('lesMois' => $lesMois,
            'moisASelectionner' => $leMois,
            'showEtat' => true,
            'dateModif' => $dateModif,
            'libEtat' => $libEtat,
            'numAnnee' => $numAnnee,
            'numMois' => $numMois,
            'montantValide' => $montantValide,
            'lesFraisHorsForfait' => $lesFraisHorsForfait,
            'lesFraisForfait' => $lesFraisForfait,
            'nbJustificatifs' => $nbJustificatifs));
    }

}

?>