<?php 

namespace App\Controllers;

use \App\View\View as View;
use \App\Utils\Session as Session;
use \App\Utils\ErrorLogger as ErrorLogger;

/**
* Contrôleur de la page de connexion
*/
class ConnexionController
{
    /**
     * Récupèration du singleton de base de donnée
     */
	public function __construct() {
        try {
            $this->db = \App\Database::getInstance();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Affiche la page de connexion dans le cas ou on est pas connecté
     * Sinon on redirige vers l'accueil
     * @return view Vue de connexion
     */
	public function index() {
        if (!Session::isConnected()) {
            View::make('connexion.twig');
        } else {
            View::redirect('/');
        }
	}

    /**
     * Déconnexion de l'application
     * @return view Vue de confirmation de déconnexion
     */
	public function logout() {
        if(!Session::isConnected()) {
        	ErrorLogger::add('Vous n\'êtes pas connecté');
        	View::make('connexion.twig', array('erreurs' => ErrorLogger::get()));
        } else {
        	Session::destroy();
        	View::make('deconnexion.twig');
            View::redirect('/', 3);
        }
	}

    /**
     * Connexion de l'utilisateur et création d'une session
     * @return view Page de connexion ou page d'accueil si connexion avec succès
     */
	public function connect() {
        /* on vérifie que les données rentrées coordonnent avec les données dans la
        base de données */
        $visiteur = $this->db->getInfosVisiteur($_POST['login'], $_POST['mdp']);
        // si le login et le mdp sont corrects
        if($visiteur) {
            // on lui créé sa session
        	Session::connect($visiteur['id'], $visiteur['nom'], $visiteur['prenom']);
        	// on le redirige vers l'accueil
            View::redirect('/accueil');
        } else {
        	ErrorLogger::add('Login ou mot de passe incorrects');
            // on affiche la page de connexion avec les erreurs
        	View::make('connexion.twig', array('erreurs' => ErrorLogger::get()));
        }	
	}
}

?>