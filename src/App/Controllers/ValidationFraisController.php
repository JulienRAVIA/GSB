<?php

namespace App\Controllers;

use \App\View\View;
use \App\Utils\ErrorLogger;
use \App\Utils\ArrayUtils;
use \App\Utils\Form;

/**
 * Contrôleur du suivi de paiement de fiche de frais
 */
class ValidationFraisController extends BaseController {

    /**
     * Récupération du singleton de base de données
     * Vérification du type de l'utilisateur
     * S'il n'est pas comptable, on le redirige vers la page d'accueil
     */
    public function __construct() {
        parent::__construct('CPTBL');
            // on vérifie que l'utilisateur soit comptable, sinon on le redirige vers l'accueil
        // on récupère la liste des visiteurs possédant au moins une fiche de frais
        $this->lesVisiteurs = $this->db->getLesVisiteursAyantFichesFrais();
    }

    /**
     * Page d'accueil de la validation des fiches de frais avec la sélection du visiteur et du mois
     * @return view Vue de la validation des fiches de frais avec la sélection du visiteur et du mois
     */
    public function index() {

        // TODO: Factoriser ce code
        // Si on accède à la page pour le première fois, on génère la vue avec les infos basiques
        if (!isset($_POST['lstVisiteurs'])) {
            $this->idVisiteurFinal = $this->lesVisiteurs[0]['id'];
            $this->lesMois = $this->db->getLesMoisDisponibles($this->idVisiteurFinal);
            $this->moisFinal = $this->lesMois[0]['mois'];
        } else {
            $this->idVisiteurFinal = $_POST['lstVisiteurs'];
            // On récupère les mois pour lesquelles le visiteur choisi a une fiche de frais
            $this->lesMois = $this->db->getLesMoisDisponibles($this->idVisiteurFinal);
            // Vérifications sur l'existence du mois passé en post dans la liste des mois du visiteur choisi
            $moisChoisi = $this->lesMois[0]["mois"];
            for ($i = 0; $i < count($this->lesMois); $i++) {
                if (in_array($_POST['lstMois'], $this->lesMois[$i])) {
                    $moisChoisi = $_POST['lstMois'];
                }
            }
            $this->moisFinal = $moisChoisi;
        }
        
        $this->majFraisForfaitSucces = "";
        $this->majFraisHorsForfaitSucces = "";
        // TODO: Si aucune modif de faite, pas la peine de faire la correction
        if (isset($_POST['corrigerForfait'])) {
            $this->corrigerForfait($this->idVisiteurFinal, $this->moisFinal);
            // TODO: ajouter un test avant d'afficher le succès de l'opération?
            $this->majFraisForfaitSucces = "La correction du frais forfaitisé a été prise en compte";
        } else if (isset($_POST['corrigerLesHorsForfait'])) {
            $this->corrigerHorsForfait($this->idVisiteurFinal, $this->moisFinal);
            $this->majFraisHorsForfaitSucces = "La correction du frais non forfaitisé a été prise en compte";
        } else if (isset($_POST['validerNbJustificatifs'])) {
            $this->validerNbJustificatifs($this->idVisiteurFinal, $this->moisFinal, $_POST['nbJustificatifs']);
        } else if (isset($_POST['reporterLesHorsForfait'])) {
            $this->reporterLesHorsForfait($this->idVisiteurFinal, $this->moisFinal);
        }
        $this->vehicule = $this->db->getVehicule($this->idVisiteurFinal, $this->moisFinal);
        // Création de la vue
        View::make('validationFrais.twig', array('lesMois' => $this->lesMois,
            'moisASelectionner' => $this->moisFinal,
            'lesVisiteurs' => $this->lesVisiteurs,
            'vehicule' => $this->vehicule,
            'visiteurASelectionner' => $this->idVisiteurFinal,
            'lesFraisForfait' => $this->db->getLesFraisForfait($this->idVisiteurFinal, $this->moisFinal),
            'lesFraisHorsForfait' => $this->db->getLesFraisHorsForfait($this->idVisiteurFinal, $this->moisFinal),
            'nbJustificatifs' => $this->db->getNbjustificatifs($this->idVisiteurFinal, $this->moisFinal),
            'majFraisForfaitSucces' => $this->majFraisForfaitSucces,
            'erreurs' => ErrorLogger::get(),
            'majFraisHorsForfaitSucces' => $this->majFraisHorsForfaitSucces
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
        if (ArrayUtils::isIntArray($_POST['lesFrais'])) {
            // on met à jour les frais
            if (isset($_POST['vehicule'])) {
                $req = $this->db->majFraisForfait($idVis, $mois, $_POST['lesFrais'],$_POST['vehicule']);
            }
            else $req = $this->db->majFraisForfait($idVis, $mois, $_POST['lesFrais']);
        } else {
            ErrorLogger::add('Les valeurs des frais doivent être numériques');
        }
    }

    /**
     * Fonction permettant de corriger un frais forfait pour un visiteur et un mois donné.
     * 
     * @param type $idVis
     * @param type $mois
     */
    public function corrigerHorsForfait($idVis, $mois) {
        if(isset($_POST['fraisHorsForfait'])) {
            $lesFraisHorsForfait = $_POST['fraisHorsForfait'];

            foreach ($lesFraisHorsForfait as $id => $unFraisHorsForfait) {
                $date = \App\Utils\Form::isDate($unFraisHorsForfait['date']);
                $date = \App\Utils\Date::FrToEng($date);
                $libelle = Form::isString($unFraisHorsForfait['libelle']);
                $montant = Form::isInt($unFraisHorsForfait['montant']);
                if(ErrorLogger::count() == 0) {
                    $this->db->majFraisHorsForfait($idVis, $mois, $id, $date, $libelle, $montant);
                    if (isset($unFraisHorsForfait['reporter'])) {
                        $nvMois = \App\Utils\Date::report($mois);
                        if ($this->db->estPremierFraisMois($idVis, $nvMois)) {
                            $this->db->creeNouvellesLignesFrais($idVis, $nvMois);
                        }
                        $this->db->reporteLeHorsForfait($id, $nvMois);
                    }
                }
            }
        } else {
            ErrorLogger::add('Il n\'y à pas de frais hors forfait à mettre à jour');
        }
        View::redirect('/frais/valider');
    }
    

    /**
     * Fonction permettant de reporter plusieurs hors forfait d'un visiteur et du mois selectionné
     * 
     * @param type $idVis
     * @param type $mois
     */
    public function reporterLesHorsForfait($idVis, $mois) {
        if(isset($_POST['fraisHorsForfait'])) {
            $lesFraisHorsForfait = $_POST['fraisHorsForfait'];
            foreach ($lesFraisHorsForfait as $id => $unFraisHorsForfait) {
                if (isset($unFraisHorsForfait['reporter'])) {
                    $nvMois = \App\Utils\Date::report($mois);
                    if ($this->db->estPremierFraisMois($idVis, $nvMois)) {
                        $this->db->creeNouvellesLignesFrais($idVis, $nvMois);
                    }
                    $this->db->reporteLeHorsForfait($id, $nvMois);
                }
            }
        } else {
            ErrorLogger::add('Il n\'y à pas de frais hors forfait à reporter');
        }
        View::redirect('/frais/valider');
    }

    /**
     * Fonction pour changer le nombre de justificatifs 
     * 
     * @param type $idVis
     * @param type $mois
     * @param type $nbJustificatifs
     */
    public function validerNbJustificatifs($idVis, $mois, $nbJustificatifs) {
        // on vérifie que le tableau de données envoyé soit un tableau d'entiers positif
        if (Form::isInt($nbJustificatifs)) {
            // on met à jour les frais
            $this->db->majNbJustificatifs($idVis, $mois, $nbJustificatifs);
        }
    }

}
