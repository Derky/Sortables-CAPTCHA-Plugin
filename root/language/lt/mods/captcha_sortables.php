<?php
/**
*
* sortables captcha [Lithuanian]
*
* @package language
* @version $Id: captcha_sortables.php 9875 2009-08-13 21:40:23Z Derky $
* @copyright (c) 2009 phpBB Group
* @copyright (c) 2009 Derky - phpBB3styles.net
* @copyright (c) 2012 Lithuanian lang by Isverte djsaras skype:djsaras1989
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'CAPTCHA_SORTABLES'				=> 'Sortables CAPTCHA',
	'CONFIRM_QUESTION_EXPLAIN'		=> 'Prašome nutempti teisingus atsakymus prie teisingų sąrašo.Tai padės apsisaugoti nuo automatinės botų registracijos.',
	'CONFIRM_QUESTION_EXPLAIN_NOJS'	=> 'Prašome pasirinkti teisingus atsakymus.', // With JavaScript disabled
	'CONFIRM_QUESTION_WRONG'		=> 'Jūsų pažymėti atsakymai į pateikta klausimą yra neteisingi.',

	'QUESTION_ANSWERS'			=> 'Atsakymai',
	'ANSWERS_EXPLAIN'			=> 'Variantai šiai lentelei. Prašome rašyti vieną variantą vienoje eilutėje.',
	'CONFIRM_QUESTION'			=> 'Klausimas',
	'CHANGES_SUBMIT'			=> 'Patvirtinti pakeitimus',

	'EDIT_QUESTION'				=> 'Redaguoti klausimą',
	'QUESTIONS'					=> 'Klausimų sąrašas',
	'QUESTIONS_EXPLAIN'			=> 'Čia jūs galite tvarkyti klausimus kurie bus pateikiami registracijos metu. Turi būti sukurtas bent vienas klausimas ,kad šis priedas veiktų sėkmingai. Klausimait ūrėtų būti kuo paprastesni. Vartotojai matys sąrašą su atsakymais ir teisingus atsakymus tūrės perkelti į teisingų atsakymų lentelę. Bėje prisiminkite,klausimus reikia keisti kiek galima dažniau.',
	'QUESTION_DELETED'			=> 'Klausimas ištrintas',
	'QUESTION_LANG'				=> 'Kalba',
	'QUESTION_LANG_EXPLAIN'		=> 'Klausimas ir jo atsakymai yra sudaryti kalba:.',
	'QUESTION_SORT'				=> 'Standartinis sąrašas',
	'QUESTION_SORT_EXPLAIN'		=> 'Kurioje pusėje atsakymai tūrėtų būti rodomi.',

	'COLUMN_LEFT'				=> 'Kairėje pusėje',
	'COLUMN_RIGHT'				=> 'Dešinėje pusėje',
	'COLUMN_NAME'				=> 'Laukelio pavadinimas',
	'COLUMN_NAME_LEFT_EXPLAIN'	=> 'Pvz.: dalykai kūrių man reikia',
	'COLUMN_NAME_RIGHT_EXPLAIN'	=> 'Pvz.: Dalykai kūrių man nereikia',
	
	'DEMO_QUESTION'				=> 'Ko reikia kad išvirtum pamidorų sriubą',	
	'DEMO_NAME_LEFT'			=> 'Puode',
	'DEMO_NAME_RIGHT'			=> 'Išmesti lauk',
	'DEMO_OPTION_BANANAS'		=> 'Bananai',
	'DEMO_OPTION_TOMATOES'		=> 'Pomidorai',
	'DEMO_OPTION_APPLES'		=> 'Obuoliai',
	'DEMO_PREVIEW_ONLY'			=> 'Peržiūros režime jūs negalite perkelti atsakymų.',

	'QUESTION_TEXT'				=> 'Klausimas',
	'QUESTION_TEXT_EXPLAIN'		=> 'Parašykite klausimą pagal kurį žmonės orientuotusi pateikdami atsakymą.',

	'SORTABLES_ERROR_MSG'		=> 'Prašome užpildyti visus laukelius ir įrašyti bent po vieną atsakymą kiekviename langelyje.',
));

?>