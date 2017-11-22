<?php 

namespace App\Controllers;

use \App\View\View;

/**
 * Contrôleur de la page d'affichage des fiches de frais
 */
class AfficherFichesController
{
    /**
     * Récupération du singleton de base de données
     */
    public function __construct()
    {
        try {
            $this->db = \App\Database::getInstance();
            $this->idVisiteur = \App\Utils\Session::get('idVisiteur');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Affichage de la page de sélection de mois
     * @return view Vue de la page d'affichage de fiches de frais
     */
    public function index()
    {
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
    public function showEtat()
    {
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
        View::make('afficherFicheFrais.twig', $array = array('lesMois' => $lesMois,
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
    
    public function renderPdf($request) {
        $lesFraisForfait = $this->db->getLesFraisForfait($request['user'], $request['mois']);
        $visiteur = array('nom' => 'Julien', 'prenom' => 'Julien');
        $mois = 'Juillet 2017';
        View::make('pdfRemboursementFrais.twig', array('visiteur' => $visiteur, 'mois' => $mois, 'lesFraisForfait' => $lesFraisForfait));
    }
    
    public function genPdf() {
        $mpdf = new \Mpdf\Mpdf();
        $source = file_get_contents('http://gsb.ppe/frais/pdf/'.\App\Utils\Session::get('idVisiteur').'/'.$_POST['mois']);
        $mpdf->WriteHTML($source);
        $mpdf->Output();
    }
}


