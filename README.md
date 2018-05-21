# Galaxy Swiss Bourdin

![Galaxy Swiss Bourdin](https://imgur.com/yTMdlhK.png)

Projet Personnel Encadré de deuxième année de BTS SIO

## Installation

Installer les composants: 
Executez la commande ``composer install --no-dev`` dans l'invite de commande à la racine du dossier

**Si en environnement de dev pour générer les tests unitaires, la documentation, executer la commande ``composer install --dev``**

Changer les identifiants de base de données: Dans le fichier ``src\App\Database.php`` changer les valeurs des variables de la classe

## Démarrage

Il faut placer le projet dans un serveur local
Si possible, créer un virtual host (avec WAMP par exemple) qui pointe vers public, sinon decommenter la ligne suivante dans ``/public/index.php`` (ligne 7) :
```
// $router->setBasePath('/GSB/public');
```

Ne mettre en production que la version de "prod", c'est à dire sans les dépendances de dev, pour supprimer les dépendances de dev si elles sont installées, executer la commande ``composer install --no-dev`` avant d'envoyer en production

## Tests unitaires

Lancer la commande suivante : ``phpunit --bootstrap src/Class.php tests/ClassTest`` pour lancer un test sur une classe en particulier (sans détails)

Lancer la commande suivante : ``phpunit --bootstrap src/Class.php --testdox tests/ClassTest`` pour lancer un test sur une classe en particulier (avec détails)

## Documentation

Lien vers la documentation : https://julienravia.github.io/GSB/

Date de la dernière génération de documentation : 21/05/2018

Lancer la mise à jour/regénération de la documentation avec la commande suivante (à la racine du projet) : 
``vendor\bin\apigen generate src --destination docs``

## Composants

* [Twig](https://twig.symfony.com/doc/2.x/) - Moteur de template
* [PHPUnit](https://phpunit.de) - Système de tests unitaires pour PHP
* [date](https://github.com/jenssegers/date) - Librairie pour la gestion des dates
* [AltoRouter](http://altorouter.com) - Système de routeur pour PHP
* [ApiGen](https://github.com/ApiGen/ApiGen) - Génération de la documentation (PHP 7.1)
* [mPDF](https://mpdf.github.io) - Génération d'un document PDF [Github](https://github.com/mpdf/mpdf)

## Avancée

- [x] Tache 1 : Validation de fiche de frais
- [x] Tache 2 : Suivi du paiement des fiches de frais
- [x] Tache 3 : Production de la documentation (dernière version : 23/12/2017)
- [x] Tache 4 : Gestion du refus de certains frais hors forfait
- [x] Tache 5 : Sécurisation des mots de passe stockés. Hashage utilisé : SHA-512
- [x] Tache 6 : Gestion plus fine de l'indeminisation kilométrique
- [x] Tache 7 : Génération d'un état de frais au format PDF
- [x] Tache 8 : Davantage d'écologie dans l'application

## TODO

- [x] Correction de bugs sur la page de validation de fiches de frais (certains visiteurs ou fiches de frais n'apparaissent pas)
- [x] Report d'un frais hors forfait
- [x] Report de plusieurs frais hors forfait
- [x] Correction de plusieurs frairs hors forfait en même temps
- [x] Décompte des frais hors forfait refusés lors de la génération du pdf et du suivi de paiement
- [x] Ajout d'un datepicker pour l'ajout d'élément hors forfait
- [x] Compte avec l'indemnisation kilométrique pour le PDF
- [x] Ajout du montant total au suivi des fiches de frais et calcul avec indemnité kilométrique selon voiture

## Auteurs

* [Guillaume Cauvet](http://www.cauvet-guillaume.fr)
* [Thomas Cianfarani](http://thomascianfarani.fr)
* [Julien Ravia](http://julienravia.fr)
