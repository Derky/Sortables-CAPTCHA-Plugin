<?php
/**
*
* sortables captcha [Slovenian]
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
* Slovenian Translation - Marko K.(max, max-ima)
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
	'CAPTCHA_SORTABLES'				=> 'Razvrsti CAPTCHA',
	'CONFIRM_QUESTION_EXPLAIN'		=> 'Povlecite možnosti na pravilen seznam, da se izognete samodejnim registracijam.',
	'CONFIRM_QUESTION_EXPLAIN_NOJS'	=> 'Prosimo, izberite spodnje možnosti na pravilnem seznamu, da se izognete samodejnim registracijam.', // With JavaScript disabled
	'CONFIRM_QUESTION_WRONG'		=> 'Napačno ste razvrstili elemente na pravilen seznam potrditvenega vprašanja.',

	'QUESTION_ANSWERS'			=> 'Odgovori',
	'ANSWERS_EXPLAIN'			=> 'Možnosti za ta stolpec. Prosimo, napišite eno možnost na vrstico.',
	'CONFIRM_QUESTION'			=> 'Vprašanje',
	'CHANGES_SUBMIT'			=> 'Predložite spremembe',

	'EDIT_QUESTION'				=> 'Uredi vprašanje',
	'QUESTIONS'					=> 'Vprašanja o razvrščenih seznamih',
	'QUESTIONS_EXPLAIN'			=> 'Tukaj lahko dodajate in urejate vprašanja, ki jih boste zastavili ob registraciji. Za uporabo tega vtičnika morate zagotoviti vsaj eno vprašanje v privzetem jeziku plošče. Vprašanja naj bodo enostavna za vašo ciljno publiko. Uporabniki bodo videli vse možnosti v enem stolpcu in jih morali razvrstiti v pravi stolpec. Prav tako ne pozabite redno spreminjati vprašanj.',
	'QUESTION_DELETED'			=> 'Vprašanje izbrisano',
	'QUESTION_LANG'				=> 'Jezik',
	'QUESTION_LANG_EXPLAIN'		=> 'Jezik, v katerem je napisano to vprašanje in njegove možnosti.',
	'QUESTION_SORT'				=> 'Privzeti seznam razvrščanja',
	'QUESTION_SORT_EXPLAIN'		=> 'V katerem stolpcu naj se privzeto prikažejo vsi odgovori.',

	'COLUMN_LEFT'				=> 'Levi stolpec',
	'COLUMN_RIGHT'				=> 'Desni stolpec',
	'COLUMN_NAME'				=> 'Ime stolpca',
	'COLUMN_NAME_LEFT_EXPLAIN'	=> 'Kot: Stvari, ki jih potrebujem',
	'COLUMN_NAME_RIGHT_EXPLAIN'	=> 'Kot: Stvari, ki jih ne potrebujem',

	'DEMO_QUESTION'				=> 'Kaj vključiti v paradižnikovo juho',
	'DEMO_NAME_LEFT'			=> 'V ponvi',
	'DEMO_NAME_RIGHT'			=> 'Vrzi stran',
	'DEMO_OPTION_BANANAS'		=> 'Banane',
	'DEMO_OPTION_TOMATOES'		=> 'Paradižnik',
	'DEMO_OPTION_APPLES'		=> 'Jabolka',
	'DEMO_PREVIEW_ONLY'			=> 'Možnosti v predogledu ne morete premakniti.',

	'QUESTION_TEXT'				=> 'Vprašanje',
	'QUESTION_TEXT_EXPLAIN'		=> 'Pojasnite, kako naj bodo možnosti razvrščene v stolpcih.',

	'SORTABLES_ERROR_MSG'		=> 'Izpolnite vsa polja in vnesite vsaj eno možnost za oba stolpca.',
));
