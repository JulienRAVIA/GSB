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
    	View::make('suiviPaiementMulti.twig', array('lesMois' => $this->lesMois, 
                                                    'selectionMois' => $this->lesMois[0],
                                                    'listVisiteurs' => $this->lesVisiteurs,
                                                    'index' => true));
    }

    /**
     * Affichage des fiches de frais de chaque utilisateur l'ayant remplie
     * @return view Vue du suivi de paiements avec les informations nécessaires pour l'affichage
     */	
    public function showSuivi($singleUser = false) {
        if ($singleUser) {
            $lesFiches = $this->db->getLesInfosFicheFrais($_POST['user'], "*");
            if (empty($lesFiches)) {
                View::make('suiviPaiementUnique.twig', array('lesMois' => $this->lesMois, 
                                                             'selectionMois' => $this->lesMois[0],
                                                             'listVisiteurs' => $this->lesVisiteurs));
            }
            else{
                View::make('suiviPaiementUnique.twig', array('lesMois' => $this->lesMois, 
                                                             'selectionMois' => $this->lesMois[0],
                                                             'lesFiches' => $lesFiches,
                                                             'nom' => $lesFiches[0]['nom'],
                                                             'prenom' => $lesFiches[0]['prenom'],
                                                             'user' => $_POST['user'],
                                                             'listVisiteurs' => $this->lesVisiteurs));
            }
        }
        else{
            $lesFiches = $this->db->getLesInfosFicheFrais("*", $_POST['lstMois']);
            if (empty($lesFiches)) {
                View::make('suiviPaiementMulti.twig', array('lesMois' => $this->lesMois, 
                                                            'selectionMois' => $_POST['lstMois'],
                                                            'listVisiteurs' => $this->lesVisiteurs));
            }
            else{
                View::make('suiviPaiementMulti.twig', array('lesMois' => $this->lesMois, 
                                                            'selectionMois' => $_POST['lstMois'],
                                                            'lesFiches' => $lesFiches,
                                                            'listVisiteurs' => $this->lesVisiteurs));
            }
        }
    }
    
    public function SuivisMultiple() {
            $this->showSuivi();
    }
    
    public function SuivisUnique() {
            $this->showSuivi(true);
    }
    
    public function mepMultiple(){
        if (isset($_POST['id'])) {
            if (is_array($_POST['id'])) {
                for ($i=0; $i<count($_POST['id']); $i++) {
                     $this->db->majEtatFicheFrais($_POST['id'][$i], $_POST['lstMois'], "VA");
                } 
            }
            else{
                $this->db->majEtatFicheFrais($_POST['id'], $_POST['lstMois'], "VA");
            }       
        }
        $this->showSuivi();
    }
    
    public function mepUnique(){
        if (isset ($_POST['lstMois'])) {
            if (is_array($_POST['lstMois'])) {
                for ($i=0; $i<count($_POST['lstMois']); $i++) {
                    $this->db->majEtatFicheFrais($_POST['user'], $_POST['lstMois'][$i], "VA");
            }
            }
            else{        
              $this->db->majEtatFicheFrais($_POST['user'], $_POST['lstMois'], "VA");
            }  
        }
       $this->showSuivi(true);
    }
}

?>