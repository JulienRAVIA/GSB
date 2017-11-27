<?php 

namespace App\Controllers;

use \App\View\View as View;
use \App\Utils\Session as Session;
use \App\Utils\ErrorLogger as ErrorLogger;

/**
 * Contrôleur du suivi de paiement de fiche de frais
 */
class SuivrePaiementController
{
    
    /**
     * Récupération du singleton de base de données
     * Vérification du type de l'utilisateur
     * S'il n'est pas comptable, on le redirige vers la page d'accueil
     */
    public function __construct()
    {
        try {
        	// on vérifie que l'utilisateur soit comptable, sinon on le redirige vers l'accueil
    		Session::check('type', 'CPTBL');
            $this->db = \App\Database::getInstance();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    	$this->lesMois = $this->db->getLesMoisDisponibles("*");
        $this->lesVisiteurs = $this->db->getVisiteursList();
    }

    /**
     * Page d'accueil du suivi des paiements avec la liste de séléction de mois
     * @return view Vue du suivi de paiements avec la liste de séléction des mois
     */
    public function index() {
    	View::make('suiviPaiement.twig', array('lesMois' => $this->lesMois, 
    						'moisASelectionner' => $this->lesMois[0],
    						'listVisiteurs' => $this->lesVisiteurs));
    }

    /**
     * Affichage des fiches de frais de chaque utilisateur l'ayant remplie
     * @return view Vue du suivi de paiements avec les informations nécessaires pour l'affichage
     */	
    public function showSuivi($user = 0) {
    	$leMois = $_POST['lstMois'];
        $singleUser = true;
        if ($user == 0) {
            $lesFiches = $this->db->getLesInfosFicheFrais("*", $leMois);
            $singleUser = false;
        }
        else{
            $lesFiches = $this->db->getLesInfosFicheFrais($user, $leMois);
        }            
    	View::make('suiviPaiement.twig', array('lesMois' => $this->lesMois, 
                    'moisASelectionner' => $leMois,
                    'lesFiches' => $lesFiches,
                    'singleUser' => $singleUser));
    }
    
    public function miseEnPaiement(){
        $leMois = $_POST['lstMois'];
        //FAIRE UN CHECK SUR LES ETAT
        $id = $_POST['id'];
        foreach($id as $unId){
            $this->db->majEtatFicheFrais($unId, $leMois, "VA");
        }
        $this->showSuivi($user);
    }
}

?>