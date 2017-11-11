<?php 

namespace App\Utils;

class Date
{

	/**
	 * Conversion d'une date au format français vers une date au format anglais
	 * @param string $maDate Date au format anglais (2017-11-10) à convertir au format français
	 * @return string Date au format français (10/11/2017)
	 */
    public static function EngToFr($maDate)
	{
    	@list($annee, $mois, $jour) = explode('-', $maDate);
    	$date = $jour . '/' . $mois . '/' . $annee;
    	return $date;
	}

	/**
	 * Conversion d'une date au format français vers une date au format anglais
	 * @param string $maDate Date au format français (10/11/2017) à convertir au format anglais
	 * @return string Date au format anglais (2017-11-10)
	 */
	public static function FrToEng($maDate)
	{
	    @list($jour, $mois, $annee) = explode('/', $maDate);
	    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
	}

	/**
	 * Récupèration du mois à partir de l'année (concatené avec l'année en cours)
	 * @param  string $date Date à extraire
	 * @return string       Année suivi du mois (exemple : 201711)
	 */
	public static function getMois($date)
	{
    	@list($jour, $mois, $annee) = explode('/', $date);
    	unset($jour);
    	if (strlen($mois) == 1) {
    	    $mois = '0' . $mois;
    	}
    	return $annee . $mois;
	}

	/**
	 * Vérification que la date ne soit pas dépassée de un an;
	 * @param  string $date Date à verifier
	 * @return boolean      Si date dépassée de un an = true, sinon false;
	 * @todo Vérification que la date passée en paramètre ne soit pas supérieure à la date actuellem
	 */
	public static function outdated($date) {
		$dateActuelle = date('d/m/Y');
    	@list($jour, $mois, $annee) = explode('/', $dateActuelle);
    	$annee--;
    	$anPasse = $annee . $mois . $jour;
    	@list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $date);
    	return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
	}

	/**
	 * On vérifie que la date soit correctement formatée
	 * @param  string $value Date à verifier
	 * @return boolean       True si date correctement formatée, sinon on renvoie une erreur
	 */
	static function check($value) {
		if (preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $value, $matches)) {
			return true;
		} else {
		    ErrorLogger::add('La valeur '.$value.' n\'est pas le type de donnée attendu (date). Elle doit-être au format dd/mm/yyyy');
		}
	}
}
