<?php

namespace App;

use App\Utils\Date as Date;

/**
 * Classe d'accès aux données
 */
class Database {

    private static $dbh; // Objet dbh
    private $_host = 'localhost';
    private $_database = 'gsb_frais';
    private $_user = 'root';
    private $_password = '';
    private $_port = 3306;
    private static $instance;

    /**
     * Constructeur avec la création de l'instance de base de données
     */
    private function __construct() {
        $user = $this->_user;
        $password = $this->_password;
        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
        $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";

        $dsn = 'mysql:host=' . $this->_host .
                ';dbname=' . $this->_database;
        // Au besoin :
        //';port='      . $this->_port .
        //';connect_timeout=15';
        // Création du pdo
        try {
            Database::$dbh = new \PDO($dsn, $user, $password, $options);
            Database::$dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception('Impossible de se connecter à la base de données');
        }
    }

    /**
     * Méthode statique de récupération de l'instance
     * 
     * @return PDO Instance de base de données
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return On retourne l'identifiant, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp) {
        $requetePrepare = Database::$dbh->prepare(
                'SELECT visiteur.id AS id, visiteur.nom AS nom, '
                . 'visiteur.prenom AS prenom, visiteur.type as type '
                . 'FROM visiteur '
                . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, \PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne les informations d'un visiteur à partir de son identifiant
     *
     * @param String $login Identifiant du visiteur
     *
     * @return On retourne l'identifiant, le nom et le prénom, le type sous la forme d'un tableau associatif
     */
    public function getInfosVisiteurFromId($id)
    {
        $requetePrepare = Database::$dbh->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, visiteur.type as type '
            . 'FROM visiteur '
            . 'WHERE visiteur.id = :id'
        );
        $requetePrepare->bindParam(':id', $id,\PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $requetePrepare = Database::$dbh->prepare(
                'SELECT * FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = Date::EngToFr($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $requetePrepare = Database::$dbh->prepare(
                'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return On retourne l'identifiant, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $requetePrepare = Database::$dbh->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite, '
            . 'fraisforfait.montant as montant '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $requetePrepare = Database::$dbh->prepare(
                'SELECT fraisforfait.id as idfrais '
                . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = Database::$dbh->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, \PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, \PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour la table ligneFraisHorsForfait
     * Met à jour la table ligneFraisHorsForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisHorsForfait($idVisiteur, $mois, $idFrais, $date, $libelle, $montant) {
        $requetePrepare = Database::$dbh->prepare(
                'UPDATE lignefraishorsforfait '
                . 'SET lignefraishorsforfait.date = :date, '
                . 'lignefraishorsforfait.libelle = :libelle, '
                . 'lignefraishorsforfait.montant = :montant '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois '
                . 'AND lignefraishorsforfait.id = :idFrais'
        );
        $requetePrepare->bindParam(':date', $date, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':libelle', $libelle, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':montant', $montant, \PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, \PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $requetePrepare = Database::$dbh->prepare(
                'UPDATE fichefrais '
                . 'SET nbjustificatifs = :unNbJustificatifs '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
                ':unNbJustificatifs', $nbJustificatifs, \PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $boolReturn = false;
        $requetePrepare = Database::$dbh->prepare(
                'SELECT fichefrais.mois FROM fichefrais '
                . 'WHERE fichefrais.mois = :unMois '
                . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $requetePrepare = Database::$dbh->prepare(
                'SELECT MAX(mois) as dernierMois '
                . 'FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = Database::$dbh->prepare(
                'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
                . 'montantvalide,datemodif,idetat) '
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = Database::$dbh->prepare(
                    'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                    . 'idfraisforfait,quantite) '
                    . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
            $requetePrepare->bindParam(
                    ':idFrais', $unIdFrais['idfrais'], \PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
    $idVisiteur, $mois, $libelle, $date, $montant
    ) {
        $dateFr = Date::FrToEng($date);
        $requetePrepare = Database::$dbh->prepare(
                'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, \PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $requetePrepare = Database::$dbh->prepare(
                'DELETE FROM lignefraishorsforfait '
                . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, \PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        if ($idVisiteur == "*") {
            $requetePrepare = Database::$dbh->prepare(
                    'SELECT DISTINCT fichefrais.mois AS mois FROM fichefrais '
                    . 'ORDER BY fichefrais.mois desc'
            );
        } else {
            $requetePrepare = Database::$dbh->prepare(
                    'SELECT fichefrais.mois AS mois FROM fichefrais '
                    . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                    . 'ORDER BY fichefrais.mois desc'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        }
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur ou alors les informations de toutes les fiches de frais des utilisateur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        if ($idVisiteur == "*") {
            $requetePrepare = Database::$dbh->prepare(
                    'SELECT fichefrais.idetat as etat, '
                    . 'fichefrais.datemodif as dateModif,'
                    . 'visiteur.nom as nom,'
                    . 'visiteur.prenom as prenom,'
                    . 'fichefrais.idvisiteur as id,'
                    . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                    . 'fichefrais.montantvalide as montantF, '
                    . 'sum(lignefraishorsforfait.montant) as montantHF, '
                    . 'etat.libelle as libEtat '
                    . 'FROM fichefrais '
                    . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                    . 'INNER JOIN lignefraishorsforfait ON fichefrais.idvisiteur = lignefraishorsforfait.idvisiteur '
                    . 'INNER JOIN visiteur ON fichefrais.idvisiteur = visiteur.id '
                    . 'WHERE fichefrais.mois = :unMois AND lignefraishorsforfait.mois = :unMois '
                    . 'group by fichefrais.idvisiteur'
            );
            $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
            $requetePrepare->execute();
            $lesLignes = array();
            while ($laLigne = $requetePrepare->fetch()) {
                $lesLignes[] = array(
                    'id' => $laLigne['id'],
                    'etat' => $laLigne['etat'],
                    'nom' => $laLigne['nom'], 
                    'prenom' => $laLigne['prenom'], 
                    'dateModif' => $laLigne['dateModif'],
                    'nbJustificatifs' => $laLigne['nbJustificatifs'],
                    'montantF' => $laLigne['montantF'],
                    'montantHF' => $laLigne['montantHF'],
                    'libEtat' => $laLigne['libEtat']
                );
            }
            return $lesLignes;
        } else {
            $requetePrepare = Database::$dbh->prepare(
                        'SELECT fichefrais.idetat as etat, '
                            .'fichefrais.datemodif as dateModif,'
                            .'fichefrais.mois as mois,'
                            . 'visiteur.nom as nom,'
                            . 'visiteur.prenom as prenom,'
                            .'fichefrais.idvisiteur as id,'
                            .'fichefrais.nbjustificatifs as nbJustificatifs, '
                            .'fichefrais.montantvalide as montantF, '
                            .'sum(lignefraishorsforfait.montant) as montantHF, ' 
                            .'etat.libelle as libEtat '
                            .'FROM fichefrais '
                            .'INNER JOIN etat ON fichefrais.idetat = etat.id '
                            . 'INNER JOIN visiteur ON fichefrais.idvisiteur = visiteur.id '
                            .'INNER JOIN lignefraishorsforfait ON fichefrais.idvisiteur = lignefraishorsforfait.idvisiteur '
                            .'WHERE fichefrais.idVisiteur = :unIdVisiteur and lignefraishorsforfait.mois = fichefrais.mois '
                            .'group by fichefrais.mois desc'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
            $requetePrepare->execute();
            $lesLignes = array();
            while ($laLigne = $requetePrepare->fetch()) {
                $lesLignes[] = array(
                    'nom' => $laLigne['nom'], 
                    'prenom' => $laLigne['prenom'],
                    'etat' => $laLigne['etat'],
                    'mois' => $laLigne['mois'],
                    'id' => $laLigne['id'],
                    'dateModif' => $laLigne['dateModif'],
                    'nbJustificatifs' => $laLigne['nbJustificatifs'],
                    'montantF' => $laLigne['montantF'],
                    'montantHF' => $laLigne['montantHF'],
                    'libEtat' => $laLigne['libEtat']
                );
            }
            return $lesLignes;
        }
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return les informations de la fiche de frais
     **/
    public function getInfosUneFicheFrais($idVisiteur, $mois) {
        $requetePrepare = Database::$dbh->prepare(
                    'SELECT fichefrais.idetat as idEtat, '
                    . 'fichefrais.datemodif as dateModif,'
                    . 'fichefrais.nbjustificatifs as nbJustificatifs, '
                    . 'fichefrais.montantvalide as montantValide, '
                    . 'etat.libelle as libEtat '
                    . 'FROM fichefrais '
                    . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
                    . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                    . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $requetePrepare = Database::$dbh->prepare(
                'UPDATE ficheFrais '
                . 'SET idetat = :unEtat, datemodif = now() '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, \PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, \PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function getLesVisiteursAyantFichesFrais() {
        $requetePrepare = Database::$dbh->prepare(
                'SELECT DISTINCT id, nom, prenom '
                . 'FROM visiteur INNER JOIN ficheFrais '
                . 'ON visiteur.id = ficheFrais.idVisiteur '
                . "WHERE type='VISTR'"
                . 'ORDER BY nom, prenom;'
        );
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $id = $lesLignes[$i]['id'];
            $nom = $lesLignes[$i]['nom'];
            $prenom = $lesLignes[$i]['prenom'];
        }
        return $lesLignes;
    }

    /**
     * Récupère la liste des visiteurs
     * 
     * 
    */
    public function getVisiteursList() {
            $requetePrepare = Database::$dbh->prepare(
                    'SELECT nom,'
                    . 'prenom,'
                    . 'id '
                    . 'FROM visiteur '
            );
            $requetePrepare->execute();
            $lesVisiteurs = array();
            while ($laLigne = $requetePrepare->fetch()) {
                $lesVisiteurs[] = array(
                    'id' => $laLigne['id'],
                    'nom' => $laLigne['nom'], 
                    'prenom' => $laLigne['prenom'], 
                );
            }
            return $lesVisiteurs;
    }
}
