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
            // On récupère les mois pour lesquelles le premier visiteur de la liste visiteur a une fiche de frais
            $this->lesMois = $this->db->getLesMoisDisponibles($this->lesVisiteurs[0]["id"]);

            // Création de la vue
            View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
                'moisASelectionner' => $this->lesMois[0],
                'lesVisiteurs' => $this->lesVisiteurs,
                'visiteurASelectionner' => $this->lesVisiteurs[0],
                'lesFraisForfait' => $this->db->getLesFraisForfait($this->lesVisiteurs[0]["id"], $this->lesMois[0]["mois"])
            ));
        } else {
            // On récupère les mois pour lesquelles le visiteur choisi a une fiche de frais
            $this->lesMois = $this->db->getLesMoisDisponibles($_POST['lstVisiteurs']);

            // Vérifications sur l'existence du mois passé en post dans la liste des mois du visiteur choisi
            $moisChoisi = $this->lesMois[0]["mois"];
            for ($i = 0; $i < count($this->lesMois); $i++) {
                if (in_array($_POST['lstMois'], $this->lesMois[$i])) {
                    $moisChoisi = $_POST['lstMois'];
                }
            }

            // Création de la vue
            View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
                'moisASelectionner' => $_POST['lstMois'],
                'lesVisiteurs' => $this->lesVisiteurs,
                'visiteurASelectionner' => $_POST['lstVisiteurs'],
                'lesFraisForfait' => $this->db->getLesFraisForfait($_POST['lstVisiteurs'], $moisChoisi)
            ));
        }
    }

}
