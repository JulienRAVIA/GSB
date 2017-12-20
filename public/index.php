<?php 

include_once '../bootstrap.php';

$router = new AltoRouter();
// Si il n'y à pas de virtual host et que le projet est dans www
// $router->setBasePath('/GSB/public');

// Page d'accueil et déconnexion
$router->addRoutes(array(
    array('GET','/', 'App\\Controllers\\HomeController@index', 'index'), // affichage de la page d'accueil ou de la page de connexion si non connecté
    array('GET','/connexion', 'App\\Controllers\\ConnexionController@index', 'login.show'), // affichage de la page de connexion
    array('POST','/connexion', 'App\\Controllers\\ConnexionController@connect', 'connexion'), // on procède à la connexion si on est pas connecté
    array('GET','/deconnexion', 'App\\Controllers\\ConnexionController@logout', 'logout') // on procède à la deconnexion
));

// Renseigner la fiche de Frais
$router->addRoutes(array(
    // page d'accueil du renseignement de fiche de frais
    array('GET','/frais/saisir', 'App\\Controllers\\RenseignerFraisController@index', 'frais.index'),
    // si on tente d'accèder à une des deux routes via la barre d'adresse (GET), on affiche la page d'accueil du 
    array('GET','/frais/create', 'App\\Controllers\\RenseignerFraisController@index', 'frais.create.get'),
    array('GET','/frais/update', 'App\\Controllers\\RenseignerFraisController@index', 'frais.update.get'),
    // création d'un frais 
    array('POST','/frais/create', 'App\\Controllers\\RenseignerFraisController@createFrais', 'frais.create.post'),
    // mise à jour des frais forfait
    array('POST','/frais/update', 'App\\Controllers\\RenseignerFraisController@updateFraisForfait', 'frais.update.post'),
    // suppression d'un frais hors forfait
    array('GET','/frais/delete/[i:id]', 'App\\Controllers\\RenseignerFraisController@deleteFrais', 'frais.delete')
));

// Afficher les fiches de frais
$router->addRoutes(array(
    // page d'accueil du renseignement de fiche de frais
    array('GET','/frais/afficher', 'App\\Controllers\\AfficherFichesController@index', 'frais.afficher'),
    array('GET','/frais/pdf/[a:user]/[i:mois]', 'App\\Controllers\\AfficherFichesController@renderPdf', 'frais.pdf'),
    array('POST','/frais/pdf', 'App\\Controllers\\AfficherFichesController@genPdf', 'frais.pdf.gen'),
    array('POST','/frais/etat', 'App\\Controllers\\AfficherFichesController@showEtat', 'frais.etat'),
    array('GET','/frais/etat', 'App\\Controllers\\AfficherFichesController@index', 'frais.etat.get')
));


// Suivi des fiches de frais
$router->addRoutes(array(
    // page d'accueil du suivi des fiches de frais
    array('GET','/frais/suivre', 'App\\Controllers\\SuivrePaiementController@index', 'frais.suivre'),
    array('POST','/frais/suivre', 'App\\Controllers\\SuivrePaiementController@showSuivi', 'frais.suivre.post')
));


$match = $router->match();

if (!$match) {
    App\View\View::redirect('/');
} else {
    if(!\App\Utils\Session::isConnected() && $match['name'] != 'connexion' && $match['name'] != 'frais.pdf') {
        \App\View\View::make('connexion.twig');
    } else {
        list($controller, $action) = explode('@', $match['target']);
        $controller = new $controller;
        if (is_callable(array($controller, $action))) {
            try {
                call_user_func_array(array($controller, $action), array($match['params']));
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'Error: can not call ' . get_class($controller) . '@' . $action;
            // here your routes are wrong.
            // Throw an exception in debug, send a 500 error in production
        }
    }
}

?>