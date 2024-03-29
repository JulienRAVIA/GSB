<?php

namespace App\Controllers;

use \App\View\View;
use Jenssegers\Date\Date;

/**
 * Contrôleur de la page d'affichage des fiches de frais
 */
class AfficherFichesController extends BaseController {

    /**
     * Récupération du singleton de base de données
     */
    public function __construct() {
        parent::__construct();
        $this->idVisiteur = \App\Utils\Session::get('idVisiteur');
    }

    /**
     * Affichage de la page de sélection de mois
     * @return view Vue de la page d'affichage de fiches de frais
     */
    public function index() {
        $lesMois = $this->db->getLesMoisDisponibles($this->idVisiteur);
        $lesCles = array_keys($lesMois);
        $moisASelectionner = $lesCles[0];
        View::make('afficherFicheFrais.twig', array('lesMois' => $lesMois,
                                                    'moisASelectionner' => $moisASelectionner));
    }

    /**
     * Affichage des fiches de frais d'un mois
     * @return view Vue de la page d'affichage des fiches de frais avec état
     */
    public function showEtat() {
        $lesMois = $this->db->getLesMoisDisponibles($this->idVisiteur);
        $leMois = $_POST['lstMois'];
        $lesInfosFicheFrais = $this->db->getInfosUneFicheFrais($this->idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $idEtat = $lesInfosFicheFrais['idEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $lesFraisHorsForfait = $this->db->getLesFraisHorsForfait($this->idVisiteur, $leMois);
        $lesFraisForfait = $this->db->getLesFraisForfait($this->idVisiteur, $leMois);
        $dateModif = \App\Utils\Date::EngToFr($lesInfosFicheFrais['dateModif']);
        View::make('afficherFicheFrais.twig', $array = array('lesMois' => $lesMois,
                                                             'moisASelectionner' => $leMois,
                                                             'showEtat' => true,
                                                             'dateModif' => $dateModif,
                                                             'libEtat' => $libEtat,
                                                             'idEtat' => $idEtat,
                                                             'numAnnee' => $numAnnee,
                                                             'numMois' => $numMois,
                                                             'montantValide' => $montantValide,
                                                             'lesFraisHorsForfait' => $lesFraisHorsForfait,
                                                             'lesFraisForfait' => $lesFraisForfait,
                                                             'nbJustificatifs' => $nbJustificatifs));
    }
    
    /**
     * Création du rendu du PDF avec les informations de la fiche de frais
     * @param  Request $request Mois et utilisateur pour récupèrer les informations
     */
    public function renderPdf($request) {
        /* Informations à récupérer */
        $lesFraisForfait = $this->db->getLesFraisForfait($request['user'], $request['mois']);
        $autresFrais = $this->db->getLesFraisHorsForfait($request['user'], $request['mois']);
        $lesInfosFicheFrais = $this->db->getLesInfosFicheFrais($request['user'], $request['mois']);
        $infosVisiteur = $this->db->getInfosVisiteurFromId($request['user']);
        $montantF = $this->db->montantTotalForfait($request['mois'], $request['user']);
        $montantHF = $this->db->montantTotalHorsForfaitNonRefuses($request['mois'], $request['user']);
        /* Récupération des dates au bon format à afficher sur le PDF */
        Date::setLocale('fr');
        $dateFiche = Date::createFromFormat('Ym', $request['mois']);
        $dateCreation = Date::createFromFormat('Ym', $request['mois']);
        $date['numAnnee'] = $dateFiche->year;
        $date['numMois'] = $dateFiche->format('m');
        $date['text'] = $dateFiche->format('F Y');
        $date['total'] = $dateFiche->format('m/Y');
        // Création du rendu du PDF
        View::make('pdfRemboursementFrais.twig', array('visiteur' => $infosVisiteur, 
                                                       'lesFraisForfaitaires' => $lesFraisForfait, 
                                                       'autresFrais' => $autresFrais, 
                                                       'infosFicheFrais' => $lesInfosFicheFrais, 
                                                       'date' => $date,
                                                       'totalF' => $montantF,
                                                       'totalHF' => $montantHF,
                                                       'montant' => ($montantF + $montantHF)
                                                       ));
    }
    
    /**
     * Fonction de génération de PDF à partir du rendu créé avec la fonction renderPdf()
     */
    public function genPdf() {
        // On génére le nom de la fiche de frais
        $filename = 'Remboursement_de_frais_engages_'.$_POST['mois'].'_'.\App\Utils\Session::get('idVisiteur').'_'.sha1(\App\Utils\Session::get('idVisiteur')).'.pdf'; 

        // on instancie la source et la classe de génération de pdf
        $mpdf = new \Mpdf\Mpdf();
        $source = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/frais/pdf/'.\App\Utils\Session::get('idVisiteur').'/'.$_POST['mois']);
        
        // on créé un pdf temporaire correspondant au fichier qui serait généré 
        $temp = 'pdf/temp_'.$filename;
        $mpdf->WriteHTML($source);
        $mpdf->Output($temp, 'F');
    
        // Si le fichier n'existe pas ou que la taille du fichier temporaire est différente du fichier existant, on renomme le pdf
        if(!file_exists('pdf/'.$filename) || file_exists('pdf/'.$filename) && filesize($temp) != filesize('pdf/'.$filename)) {
            rename($temp, 'pdf/'.$filename); 
        }

        // on permet à l'utilisateur de télécharger le pdf
        header("Content-type: application/octet-stream");                       
        header("Content-Disposition:inline;filename='".basename($filename)."'");            
        header('Content-Length: ' . filesize('pdf/'.$filename));
        header("Cache-control: private"); //use this to open files directly                     
        readfile('pdf/'.$filename);
    }
}
