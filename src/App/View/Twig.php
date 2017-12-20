<?php 

namespace App\View;

/**
 * Renderer Twig pour le fonctionnement de la classe View (Facade)
 */
class Twig
{
    private $_twig;
    private $_loader;

    /**
     * Instanciation de l'objet Twig
     */
    public function __construct()
    {
        $this->_loader = new \Twig_Loader_Filesystem('..\src\Views');
		$this->_twig = new \Twig_Environment($this->_loader, array(
    		'cache' => false,
                'debug' => true
		));
        $this->_twig->addExtension(new \Twig_Extension_Debug());
        $this->_twig->addGlobal('session', $_SESSION);
        $this->_twig->addGlobal('domain', $_SERVER['HTTP_HOST']);
		return $this->_twig;
    }

    /**
     * Fonction de rendu d'une vue
     * @param  string $view  Vue à afficher
     * @param  array  $array Paramètres à passer à la vue
     * @return twigView      Vue twig
     */
    public function render($view, $array = array())
    {
    	if(empty($array)) {
    		echo $this->_twig->render($view);
    	} else {
    		echo $this->_twig->render($view, $array);
    	}
    }
}

?>