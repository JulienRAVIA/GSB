<?php 

namespace App\View;

/**
 * Classe View (Facade) pour le rendu des vues et la redirection
 */
class View
{
	private static $twig = null;

	/**
	 * Fonction de rendu d'une vue
	 * @param  string $view  Vue à afficher
	 * @param  array $datas  Paramètres à passer à la vue
	 * @return twigView      Vue à afficher
	 */
    static function make($view, $datas = null) {
    	if(self::$twig == null) {
    		self::$twig = new Twig();
    	}
    	echo self::$twig->render($view, $datas);
    }

    /**
     * Fonction de redirection vers la route choisie
     * @param  string $route Route ou rediriger l'utilisateur
     */
    static function redirect($route, $timeout = 0) {
    	if (empty($timeout)) {
            header('Location: '.$route);
        } else {
            header("Refresh: $timeout;URL=$route");
        }
    } 
}

?>