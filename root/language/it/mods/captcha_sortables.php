<?php
/**
*
* sortables captcha [Italian]
*
* @package language
* @version $Id: captcha_sortables.php 9875 2009-08-13 21:40:23Z Derky $
* @copyright (c) 2009 phpBB Group
* @copyright (c) 2009 Derky - phpBB3styles.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Translated by etms51
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
	'CAPTCHA_SORTABLES'				=> 'Presenta CAPTCHA',
	'CONFIRM_QUESTION_EXPLAIN'		=> 'Si prega di trascinare le opzioni nell\'elenco corretto, per evitare registrazioni automatiche.',
	'CONFIRM_QUESTION_EXPLAIN_NOJS'	=> 'Si prega di selezionare prima le opzioni all\'elenco corretto per evitare le registrazioni automatiche.', // With JavaScript disabled
	'CONFIRM_QUESTION_WRONG'		=> 'Lei non ha correttamente ordinati gli oggetti all\'elenco corretto della domanda di conferma.',

	'QUESTION_ANSWERS'			=> 'Risposte',
	'ANSWERS_EXPLAIN'			=> 'L\'opzione per questa colonna. Si prega di scrivere una opzione per linea.',
	'CONFIRM_QUESTION'			=> 'Domanda',
	'CHANGES_SUBMIT'			=> 'Invia cambiamenti',

	'EDIT_QUESTION'				=> 'Modifica Domanda',
	'QUESTIONS'					=> 'Elenco Domande ordinabili',
	'QUESTIONS_EXPLAIN'			=> 'Qui è possibile aggiungere e modificare le domande per essere risposte sulla registrazione. Per poter usare questo plugin è necessario fornire almeno una domanda  nel forum predefinito della lingua. Le domande devono essere semplici per i target di riferimento. Gli utenti vedranno tutte le opzioni in una colonna e hanno la possibilità di ordinare la colonna corretta. Inoltre, bisogna ricordarsi di cambiare regolarmente le domande.  ',
	'QUESTION_DELETED'			=> 'Domanda cancellata',
	'QUESTION_LANG'				=> 'Lingua',
	'QUESTION_LANG_EXPLAIN'		=> 'La lingua per questa domanda e le sue opzioni sono scritte in.',
	'QUESTION_SORT'				=> 'Elenco predefinito ordinabile',
	'QUESTION_SORT_EXPLAIN'		=> 'In quale colonna devono essere visualizzate le risposte di default.',
	
	'COLUMN_LEFT'				=> 'Colonna di sinistra',
	'COLUMN_RIGHT'				=> 'Colonna di destra',
	'COLUMN_NAME'				=> 'Nome colonna',
	'COLUMN_NAME_LEFT_EXPLAIN'	=> 'Come: Cose che ho bisogno',
	'COLUMN_NAME_RIGHT_EXPLAIN'	=> 'Come: Cose che non ho bisogno',
	
	'DEMO_QUESTION'				=> 'Che cosa è incluso nella zuppa di pomodoro',	
	'DEMO_NAME_LEFT'			=> 'In padella',
	'DEMO_NAME_RIGHT'			=> 'Buttare via',
	'DEMO_OPTION_BANANAS'		=> 'Banane',
	'DEMO_OPTION_TOMATOES'		=> 'Pomodori',
	'DEMO_OPTION_APPLES'		=> 'Mele',
	'DEMO_PREVIEW_ONLY'			=> 'Non è possibile spostare le opzioni in un\' anteprima.',

	'QUESTION_TEXT'				=> 'Domanda',
	'QUESTION_TEXT_EXPLAIN'		=> 'Spiega in che modo le opzioni devono essere ordinati nelle colonne.',

	'SORTABLES_ERROR_MSG'		=> 'Si prega di compilare tutti i campi e inserire almeno una opzione per entrambe le colonne.',
));

?>