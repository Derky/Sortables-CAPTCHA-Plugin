<?php
/**
*
* sortables captcha [Croatian]
*
* @package language
* @version $Id: captcha_sortables.php 9875 2009-08-13 21:40:23Z Derky $
* @copyright (c) 2009 phpBB Group
* @copyright (c) 2009 Derky - phpBB3styles.net
* @copyright (c) 2011 Croatian lang by akarlovic - phpbb.com.hr
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
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
	'CONFIRM_QUESTION_EXPLAIN'		=> 'Molimo povucite opcije u ispravni redosljed, kako bi se izbjegla automatska registracija.',
	'CONFIRM_QUESTION_EXPLAIN_NOJS'	=> 'Molimo odaberite ispravne opcije kako bi se izbjegla automatska registracija.', // With JavaScript disabled
	'CONFIRM_QUESTION_WRONG'		=> 'Vi ste krivo razvrstali stavke iz popisa.',

	'QUESTION_ANSWERS'			=> 'Odgovor',
	'ANSWERS_EXPLAIN'			=> 'Mogucnosti za ovaj stupac. Molimo upisite jednu opciju po retku.',
	'CONFIRM_QUESTION'			=> 'Pitanje',
	'CHANGES_SUBMIT'			=> 'Prihvati izmjene',

	'EDIT_QUESTION'				=> 'Uredi pitanje',
	'QUESTIONS'					=> 'Redosljed Liste Pitanja',
	'QUESTIONS_EXPLAIN'			=> 'Ovdje mozete dodavati i uredjivati pitanja koja koja se pojavljuju kod registracije. Morate osigurati najmanje jedno pitanje u zadanom okviru za koristenje ovog dodatka. Pitanja bi trebala biti jednostavla za ciljanu publiku. Korisnici ce vidjeti sve opcije u jednom stupcu i moraju ih razvrstati u ispravan stupac kao sto ste zadali u administraciji. Osim toga, ne zaboravite povremeno izmjeniti pitanja.',
	'QUESTION_DELETED'			=> 'Pitanje obrisano',
	'QUESTION_LANG'				=> 'Jezik',
	'QUESTION_LANG_EXPLAIN'		=> 'Jezik kojim je pisano pitanje je',
	'QUESTION_SORT'				=> 'Zadana vrsta popisa',
	'QUESTION_SORT_EXPLAIN'		=> 'U kojem stupcu bi trebli biti prikazani odgovori po defaultu.',
	
	'COLUMN_LEFT'				=> 'Ljevi stupac',
	'COLUMN_RIGHT'				=> 'Desni stupac',
	'COLUMN_NAME'				=> 'Ime stupca',
	'COLUMN_NAME_LEFT_EXPLAIN'	=> 'Kao: stvari koje trebam',
	'COLUMN_NAME_RIGHT_EXPLAIN'	=> 'Kao: Stvari koje ne trebate',
	
	'DEMO_QUESTION'				=> 'Sto stavljamo u juhu od rajcice',	
	'DEMO_NAME_LEFT'			=> 'Ostavi',
	'DEMO_NAME_RIGHT'			=> 'Odbaci',
	'DEMO_OPTION_BANANAS'		=> 'Banana',
	'DEMO_OPTION_TOMATOES'		=> 'Rajcica',
	'DEMO_OPTION_APPLES'		=> 'Jabuka',
	'DEMO_PREVIEW_ONLY'			=> 'Ne mozete premjestiti mogucnost primjera. Ovo je samo primjer!',

	'QUESTION_TEXT'				=> 'Pitanje',
	'QUESTION_TEXT_EXPLAIN'		=> 'Objasnite kako otprilike trebaju biti razvrstane rijeci u stupcu.',

	'SORTABLES_ERROR_MSG'		=> 'Molimo ispunite sva polja i unesite barem jednu moguænost za oba stupca.',
));

?>