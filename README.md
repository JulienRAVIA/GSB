# Galaxy Swiss Bourdin

![Galaxy Swiss Bourdin](https://imgur.com/yTMdlhK.png)

Projet Personnel Encadré de deuxième année de BTS SIO

## Installation

Installer les composants: Executez la commande ``composer install`` dans l'invite de commande à la racine du dossier

Changer les identifiants de base de données: Dans le fichier ``src\App\Database.php`` changer les valeurs des variables de la classe

## Démarrage

Il faut placer le projet dans un serveur local
Si possible, créer un virtual host avec pour nom : gsb.ppe (sinon, c'est relou)
Si le nom de virtual host n'est pas gsb.ppe, changer l'url dans le macro url du fichier ``src\Views\macro.twig``

## Tests unitaires

Lancer la commande suivante : ``phpunit --bootstrap src/Class.php tests/ClassTest`` pour lancer un test sur une classe en particulier (sans détails)

Lancer la commande suivante : ``phpunit --bootstrap src/Class.php --testdox tests/ClassTest`` pour lancer un test sur une classe en particulier (avec détails)

## Documentation

Lancer la mise à jour/regénération de la documentation avec la commande suivante (à la racine du projet) : 
``vendor\bin\apigen generate src --destination docs``

## Composants

* [Twig](https://twig.symfony.com/doc/2.x/) - Moteur de template
* [PHPUnit](https://phpunit.de) - Système de tests unitaires pour PHP
* [date](https://github.com/jenssegers/date) - Librairie pour la gestion des dates
* [AltoRouter](http://altorouter.com) - Système de routeur pour PHP
* [ApiGen](https://github.com/ApiGen/ApiGen) - Génération de la documentation (PHP 7.1)
* [mPDF](https://mpdf.github.io) - Génération d'un document PDF [Github](https://github.com/mpdf/mpdf)

## Auteurs

* [Guillaume Cauvet](http://www.cauvet-guillaume.fr)
* [Thomas Cianfarani](http://thomascianfarani.fr)
* [Julien Ravia](http://julienravia.fr)
