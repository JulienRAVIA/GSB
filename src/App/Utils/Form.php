<?php 

namespace App\Utils;

/**
 * Utilitaire pour les formulaires
 */
class Form
{
	/**
	 * On vérifie que la valeur passée soit une valeur numérique	
	 * @param  int  $value    Valeur numérique renseignée
	 * @return int            Valeur numérique renseignée
	 * @return Exception      Exception
	 */
	static function isInt($value) {
		if(is_numeric($value)) {
			return $value;
		} else {
			ErrorLogger::add('La valeur '.$value.' n\'est pas le type de donnée attendu (int)');
			return false;
		}
	}

	/**
	 * On vérifie que la valeur passée soit une chaine de caractères, de plus de x caractères
	 * @param  string  $value  Chaine de caractère renseignée
	 * @param  integer $length Longueur minimale
	 * @return string          Chaine de caractère renseignée
	 * @return Exception       Exception
	 */
	static function isString($value, $length = 5) {
		if(is_string($value)) {
			if(strlen($value) >= $length)  {
				return $value;
			} else {
				ErrorLogger::add('La valeur '.$value.' ne respecte pas la longueur attendue ('.$length.')');
				return false;
			}
		} else {
			ErrorLogger::add('La valeur '.$value.' n\'est pas le type de donnée attendu (string)');
			return false;
		}
	}

	/**
	 * On vérifie que l'adresse email renseignée soit correcte
	 * @param  string  $value Adresse email renseignée
	 * @return string         Adresse email renseignée
	 * @return Exception      Exception
	 */
	static function isMail($value) {
		if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return $value;
		} else {
			ErrorLogger::add('La valeur '.$value.' n\'est pas le type de donnée attendu (email)');
			return false;
		}
	}

	/**
	 * On vérifie que la date attendue soit au bon format
	 * @param  string  $value Date renseignée
	 * @return int        	  Timestamp de la date renseignée
	 * @return exception        Exception
	 */
	static function isDate($value) {
		if (preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $value, $matches)) {
		    if (!checkdate($matches[2], $matches[1], $matches[3])) {
				ErrorLogger::add('La valeur '.$value.' n\'est pas le type de donnée attendu (date). Elle doit-être au format dd/mm/yyyy');
				return false;
		    } else {
        		return $value;
		    }
		} else {
		    ErrorLogger::add('La valeur '.$value.' n\'est pas le type de donnée attendu (date). Elle doit-être au format dd/mm/yyyy');
		    return false;
		}
	}

	/**
	 * On vérifie que les données d'un tableau correspondent
	 * au données attendues, à partir d'un tableau définissant
	 * les champs attendus
	 * @param  array  $array  Données envoyées
	 * @param  array  $fields Champs/clés du tableau attendues
	 * @return boolean         On renvoie true, ou une exception
	 */
	static function isNotEmpty($array, $fields) {
		if(isset($array)) {
			foreach ($fields as $field) {
				if(empty($array[$field])) {
					ErrorLogger::add('Le champ '.$field.' est vide');
					return false;
				}
			}
			if (ErrorLogger::count() == 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Vérification que le mot de passe à plus de 8 caractères, un caractère spécial, un chiffre, et une libxml_set_streams_context(streams_context)
	 * @param  string  $value  Mot de passe à verifier		
	 * @return string 	Mot de passe conforme
	 * @return exception Exception
	 */
	static function isPassword($value) {
		if(!empty($value)) {
			if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/", $value, $matches)) {
				return $value;
			} else {
		    	ErrorLogger::add('Le mot de passe renseigné n\'est pas un mot de passe valide, il doit y avoir 8 caractères, un caractère spécial et un chiffre.');
		    	return false;
			}
		}
	}

	/**
	 * Vérification d'un entier positif
	 * @param  int  $value    Entier à verifier
	 * @return boolean        Si entier positif = true, sinon false
	 */
	static function isStrictInt($value) {
		return preg_match('/[^0-9]/', $value) == 0;
	}
}
