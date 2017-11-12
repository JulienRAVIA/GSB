<?php

namespace App\Utils;

/**
 * Utilitaire pour les sessions
 */
class Session {
    
    /**
     * Création d'une variable de session
     * @param string $key   Clé de la variable de session
     * @param string $value Valeur de la variable de session
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Récupération d'une variable de session à partir de sa clé
     * @param  string $key Clé de la variable de session à récupérer
     * @return string      Variable de session
     */
    public static function get($key) {
        return $_SESSION[$key];
    }
    
    /**
     * Vérification que l'utilisateur est connecté
     * @return boolean Si connecté, on renvoie true, sinon false
     */
    public static function isConnected() {
        return isset($_SESSION['idVisiteur']);
    }
    
    /**
     * Destruction d'une variable en particulier ou alors de toute la session
     * @param  string $key Clé de la variable de session à détruire
     */
    public static function destroy($key = '') {
        if(empty($key)) { 
            /* si on ne renseigne pas de variable particulière à detruire 
            on détruit toute la session */
            session_destroy();
        } else {
            // sinon on détruit la variable de session avec la clé renseignée
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Attribution des variables de session rapide à partir des données en paramètres
     * @param  string $id     Identifiant de l'utilisateur à connecter
     * @param  string $nom    Nom de l'utilisateur à connecter
     * @param  string $prenom Prénom de l'utilisateur à connecter
     */
    public static function connect($id, $nom, $prenom, $type) {
        self::set('idVisiteur', $id);
        self::set('nom', $nom);
        self::set('prenom', $prenom);
        self::set('type', $type);
    }

    /**
     * Fonction de vérification que la clé pâssée en paramètre ait pour valeur
     * la valeur attendue, si ce n'est pas le cas on redirige vers la route
     * passée en paramètre
     * @param  string $key   Clé du tableau de session à verifier
     * @param  string $value Valeur attendue
     * @param  string $route Route vers lequel rediriger l'utilisateur
     * @return view          Redirection si la clé attendue n'est pas correctement renseignée
     */
    public static function check($key, $value, $route = '/') {
        if(self::get($key) != $value) {
            \App\View\View::redirect($route);
        }
    }
}
