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
            $this->lesMois = $this->db->getLesMoisDisponibles($this->lesVisiteurs[0]["id"]);
            View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
                'moisASelectionner' => $this->lesMois[0],
                'lesVisiteurs' => $this->lesVisiteurs,
                'visiteurASelectionner' => $this->lesVisiteurs[0],
                'lesFraisForfait' => $this->db->getLesFraisForfait($this->lesVisiteurs[0]["id"], $this->lesMois[0]["mois"])
            ));
        } else {
            $this->lesMois = $this->db->getLesMoisDisponibles($_POST['lstVisiteurs']);
            View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
                'moisASelectionner' => $_POST['lstMois'],
                'lesVisiteurs' => $this->lesVisiteurs,
                'visiteurASelectionner' => $_POST['lstVisiteurs'],
                'lesFraisForfait' => $this->getFraisForfaitsDuMois($_POST['lstVisiteurs'], $_POST['lstMois'])
            ));
        }
    }
}

?>