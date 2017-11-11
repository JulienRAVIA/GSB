<?php 

namespace App\Utils;

use App\Utils\Form;

/**
 * Utilitaire pour les tableaux
 */
class ArrayUtils
{

	/**
	 * Fonction de copie d'un tableau pour éviter 
	 * la recopie des variables dans les requêtes		
	 * @param  array $array         Tableau à copier et filtrer
	 * @param  array $keysToExclude Clés de tableau à supprimer du tableau
	 * @return array               	Tableau sans les clés que l'on à choisi d'exclure
	 */
    static function copyAndExclude(array $array, array $keysToExclude) {
    	foreach ($keysToExclude as $exclude) {
    		unset($array[$exclude]);
    	}
    	foreach ($array as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * On vérifie que les valeurs dans le tableau 
     * soient uniquement des entiers positifs
     * @param  array  $tabEntiers Tableau à verifier
     * @return boolean            Réponse (true si uniquement entiers positifs, sinon false)
     */
    static function isIntArray($tabEntiers) {
        $boolReturn = true;
        foreach ($tabEntiers as $entier) {
            if(!Form::isStrictInt($entier)) {
                $boolReturn = false;
            }
        }
        return $boolReturn;
    }
}
