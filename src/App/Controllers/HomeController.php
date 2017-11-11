<?php

namespace App\Controllers;

use \App\View\View;

/**
 * Controleur de la page d'accueil
 */
class HomeController {
    
    /**
     * On récupère le singleton de base de données
     */
    public function __construct() {
        try {
            $this->db = \App\Database::getInstance();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * On affiche la page d'accueil si connecté, 
     * sinon on affiche la page de connexion
     * @return view Page de connexion ou page d'accueil
     */
    public function index() {
    	if (\App\Utils\Session::isConnected()) {
            View::make('index.twig');
    	} else {
            View::make('connexion.twig');
    	}
   }
}
