<?php 

namespace App\Controllers;

use \App\View\View;
use \App\Utils\Form;
use \App\Utils\ErrorLogger;

/**
 * Contrôleur de la page de renseignement de fiche de frais
 */
class RenseignerFraisController
{
    /**
     * Initialisation des données
     * Récupèration du singleton de base de donnée
     */
    public function __construct() {
        try {
            $this->db = \App\Database::getInstance();
            $this->mois = \App\Utils\Date::getMois(date('d/m/Y'));
            $this->numAnnee = substr($this->mois, 0, 4);
            $this->numMois = substr($this->mois, 4, 2);
            $this->idVisiteur = \App\Utils\Session::get('idVisiteur');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Page d'accueil de la page de renseignemenet de fiche de frais
     * 
     * On appellera cette méthode régulièrement dans les autres méthodes
     * pour éviter la duplication de code
     * @return view Page d'accueil avec les donées à charger par défaut, 
     */
    public function index()
    {
        // on ajoute une fiche de frais du mois actuel si elle n'existe pas
        if ($this->db->estPremierFraisMois($this->idVisiteur, $this->mois)) {
            $this->db->creeNouvellesLignesFrais($this->idVisiteur, $this->mois);
        }

        // on charge les données de la fiche de frais du mois actuel
        $this->lesFraisHorsForfait = $this->db->getLesFraisHorsForfait($this->idVisiteur, $this->mois);
        $this->lesFraisForfait = $this->db->getLesFraisForfait($this->idVisiteur, $this->mois);

        // on affiche les infos de la fiche de frais du mois actuel
        View::make('renseignerFicheFrais.twig', array('erreurs' => ErrorLogger::get(), 
                                                      'lesFraisForfait' => $this->lesFraisForfait,
                                                      'lesFraisHorsForfait' => $this->lesFraisHorsForfait,
                                                      'numAnnee' => $this->numAnnee,
                                                      'numMois' => $this->numMois));

        
    }

    /**
     * Suppression d'un élément hors forfait
     * @param  array $request Tableau comportant l'id à à supprimer
     * @return view  Redirection vers l'accueil 
     */
    public function deleteFrais($request) 
    {
        // suppression du hors forfait ayant l'id passé en paramètre
    	$this->db->supprimerFraisHorsForfait($request['id']);
        // on redirige vers l'accueil
        View::redirect('/frais/saisir');
    }

    /**
     * Mise à jour des éléments forfaitisés
     * @return view Redirection vers l'accueil ou vue d'accueil
     */
    public function updateFraisForfait()
    {
        // on vérifie que le tableau de données envoyé soit un tableau d'entiers positif
        if(\App\Utils\ArrayUtils::isIntArray($_POST['lesFrais'])) {
            // on met à jour les frais
            $req = $this->db->majFraisForfait($this->idVisiteur, $this->mois, $_POST['lesFrais']);
            // on redirige vers l'accueil
            View::redirect('/frais/saisir');
        } else {
            ErrorLogger::add('Les valeurs des frais doivent être numériques');
            // on affiche la vue renseignerFicheFrais avec les données à charger par défaut
            $this->index(); 
        }
    }

    /**
     * Création d'une fiche de frais
     * @return view Affichage de la vue d'accueil ou redirection vers l'accueil
     */
    public function createFrais()
    {
        /* si toutes les données sont remplies, on vérifie que chaque champ soit
        correctement rempli, si */
        if(Form::isNotEmpty($_POST, array('dateFrais', 'montant', 'libelle'))) {
            $dateFrais = Form::isDate($_POST['dateFrais']);
            if(\App\Utils\Date::outDated($dateFrais)) {
                ErrorLogger::add("Date d'enregistrement du frais dépassé, plus de 1 an");
            }
            $libelle = Form::isString($_POST['libelle'], 3);
            $montant = Form::isInt($_POST['montant']);
        } else {
            ErrorLogger::add('Toutes les données ne sont pas renseignées');
        }

        if(ErrorLogger::count() == 0) { // si aucune erreur
            // on ajoute le frais hors forfait à la base de données
            $this->db->creeNouveauFraisHorsForfait($this->idVisiteur, $this->mois, $libelle, $dateFrais, $montant);
            // on redirige vers l'accueil
            View::redirect('/frais/saisir');
        } else {
            // on affiche la vue renseignerFicheFrais avec les données à charger par défaut
            $this->index();
        }
    }
}