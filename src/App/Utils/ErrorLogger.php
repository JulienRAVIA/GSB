<?php 

namespace App\Utils;

/**
 * Classe d'enregistrement et de récupération d'erreurs
 */
class ErrorLogger
{
    /**
     * Ajoute une erreur au tableau d'erreurs
     * @param string $message Message de l'erreur
     */
    public static function add($message)
    {
    	if (!isset($_REQUEST['erreurs'])) {
        	$_REQUEST['erreurs'] = array();
    	}
    	$_REQUEST['erreurs'][] = $message;
    }

    /**
     * Récupère le tableau d'erreurs
     * @return array Erreurs enregistrées
     */
    public static function get() {
    	if(!empty($_REQUEST['erreurs'])) {
    		return $_REQUEST['erreurs'];
    	} else {
    		return null;
    	}
    }

    /**
     * Retourne le nombre d'erreurs enregistrées            
     * @return int Nombre d'erreurs enregistrées
     */
    public static function count()
    {
        if(isset($_REQUEST['erreurs'])) {
            return count($_REQUEST['erreurs']);
        } else {
            return 0;
        }
    }
}
