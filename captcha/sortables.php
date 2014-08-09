<?php
/**
*
* @package VC
* @version $Id: phpbb_captcha_sortables_plugin.php 2009-09-03 Derky $
* extending from: phpbb_captcha_qa_plugin.php 10484 2010-02-08 16:43:39Z bantu $
* @copyright (c) 2006, 2008 phpBB Group
* @copyright (c) 2009 Derky - phpBB3styles.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace derky\sortablescaptcha\captcha;

/**
* Sortables captcha with extending of the QA captcha class.
*
* @package VC
*/
class sortables extends \phpbb\captcha\plugins\qa
{
	var $confirm_id;
	var $options_left;  // $answer in captcha_qa
	var $options_right; //
	var $question_ids;
	var $answer_ids = false;
	var $question_text;
	var $question_lang;
	var $question_sort;
	var $attempts = 0;
	var $type;
	// dirty trick: 0 is false, but can still encode that the captcha is not yet validated
	var $solved = 0;
	
	/**
	* @var \phpbb\db\driver\driver_interface
	 */
	protected $db;
	
	/**
	* @var \phpbb\cache\service
	*/
	protected $cache;
	
	/**
	* @var \phpbb\config\config
	*/
	protected $config;
	
	/**
	* @var \phpbb\template\template
	*/
	protected $template;
	
	/**
	* @var \phpbb\user
	*/
	protected $user;
	
	/**
	 * 
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\cache\service $cache
	 * @param \phpbb\config\config $config
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user $user
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->db = $db;
		$this->cache = $cache;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
		
		// This global will be removed when all database specific changes are moved to migrations.
		global $table_prefix;
		
		/**
		* Hack for phpBB 3.0.9 table/index name limitations
		* Add a backwards_compatibility boolean to $this->config
		* Due to static API calls, this has to be defined here
		*/
		if (!isset($this->config['sortables_bc']))
		{
			$this->db_tool = new \phpbb\db\tools($this->db);

			// Find out if we need backwards compatibility
			($this->db_tool->sql_table_exists($table_prefix . 'captcha_sortables_questions')) ? set_config('sortables_bc', 1) : set_config('sortables_bc', 0);
		}

		// Use the backwards compatible table names? (longer then 30 digits and already created on a phpBB 3.0.8 installation or lower)
		if ($this->config['sortables_bc'])
		{
			define('CAPTCHA_SORTABLES_QUESTIONS_TABLE',	$table_prefix . 'captcha_sortables_questions');
			define('CAPTCHA_SORTABLES_ANSWERS_TABLE',	$table_prefix . 'captcha_sortables_answers');
			define('CAPTCHA_SORTABLES_CONFIRM_TABLE',	$table_prefix . 'captcha_sortables_confirm');
		}
		else // The new shorted table names
		{
			define('CAPTCHA_SORTABLES_QUESTIONS_TABLE',	$table_prefix . 'sortables_questions');
			define('CAPTCHA_SORTABLES_ANSWERS_TABLE',	$table_prefix . 'sortables_answers');
			define('CAPTCHA_SORTABLES_CONFIRM_TABLE',	$table_prefix . 'sortables_confirm');
		}
	}

	/**
	* @param int $type  as per the CAPTCHA API docs, the type
	*/
	function init($type)
	{
		// load our language file 
		$this->user->add_lang_ext('derky/sortablescaptcha', 'captcha_sortables');
		
		// read input
		$this->confirm_id = request_var('sortables_confirm_id', '');

		$this->type = (int) $type;
		$this->question_lang = $this->user->lang_name;
		
		// we need all defined questions - shouldn't be too many, so we can just grab them
		// try the user's lang first
		$sql = 'SELECT question_id 
				FROM ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . "
				WHERE lang_iso = '" . $this->db->sql_escape($this->user->lang_name) . "'";
		$result = $this->db->sql_query($sql, 3600);
		
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->question_ids[$row['question_id']] = $row['question_id'];
		}
		$this->db->sql_freeresult($result);
		
		// fallback to the board default lang
		if (!sizeof($this->question_ids))
		{
			$this->question_lang = $this->config['default_lang'];
			
			$sql = 'SELECT question_id 
					FROM ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . "
					WHERE lang_iso = '" . $this->db->sql_escape($this->config['default_lang']) . "'"; 
			$result = $this->db->sql_query($sql, 7200);
			
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->question_ids[$row['question_id']] = $row['question_id'];
			}
			$this->db->sql_freeresult($result);
		}

		// okay, if there is a confirm_id, we try to load that confirm's state. If not, we try to find one
		if (!$this->load_answer() && (!$this->load_confirm_id() || !$this->load_answer()))
		{
			// we have no valid confirm ID, better get ready to ask something
			$this->select_question();
		}
	}
	
	/**
	*  API function
	*/
	static public function get_instance()
	{
		$instance = new phpbb_captcha_sortables_plugin();

		return $instance;
	}

	/**
	* See if the captcha has created its tables.
	*/
	public function is_installed()
	{
		$this->db_tool = new \phpbb\db\tools($this->db);

		return $this->db_tool->sql_table_exists(CAPTCHA_SORTABLES_QUESTIONS_TABLE);
	}
	
	/**
	*  API function - for the captcha to be available, it must have installed itself and there has to be at least one question in the board's default lang
	*/
	public function is_available()
	{
		// load language file for pretty display in the ACP dropdown
		$this->user->add_lang_ext('derky/sortablescaptcha', 'captcha_sortables');

		if (!self::is_installed())
		{
			return false;
		}

		$sql = 'SELECT COUNT(question_id) AS question_count 
			FROM ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . " 
			WHERE lang_iso = '" . $this->db->sql_escape($this->config['default_lang']) . "'"; 
		$result = $this->db->sql_query($sql);
		$question_count = $this->db->sql_fetchfield('question_count');
		$this->db->sql_freeresult($result);

		return ((bool) $question_count);
	}
	
	/**
	*  API function
	*/
	function has_config()
	{
		return true;
	}

	/**
	*  API function
	*/
	static public function get_name()
	{
		return 'CAPTCHA_SORTABLES';
	}

	/**
	*  API function
	*/
	function get_service_name()
	{
		return 'derky.sortablescaptcha.captcha.sortables';
	}

	/**
	*  API function - send the question to the template
	*/
	function get_template()
	{		
		if ($this->is_solved())
		{
			return false;
		}
		else
		{
			$this->template->assign_vars(array(
				'SORTABLES_CONFIRM_QUESTION'	=> $this->question_text,
				'SORTABLES_CONFIRM_ID'			=> $this->confirm_id,
				'SORTABLES_NAME_LEFT'			=> $this->name_left,
				'SORTABLES_NAME_RIGHT'			=> $this->name_right,
				'SORTABLES_DEFAULT_SORT'		=> (!$this->question_sort) ? 'LEFT' : 'RIGHT', // 0 = left, 1 = right
				'S_CONFIRM_CODE'				=> true,
				'S_TYPE'						=> $this->type,
				
				// Set version numbers here, so jQuery updates don't require a template refresh anymore
				'SORTABLES_JQUERYUI_VERSION'	=> '1.9.2',
			));

			return '@derky_sortablescaptcha/captcha_sortables.html';
		}
	}

	/**
	*  API function - we just display a mockup so that the captcha doesn't need to be installed
	*/
	function get_demo_template()
	{
		return '@derky_sortablescaptcha/captcha_sortables_acp_demo.html';
	}

	/**
	*  API function
	*/
	function get_hidden_fields()
	{
		$hidden_fields = array();

		// this is required - otherwise we would forget about the captcha being already solved
		if ($this->solved)
		{
			$hidden_fields['sortables_options_left'] = $this->options_left;
			$hidden_fields['sortables_options_right'] = $this->options_right;
		}
		$hidden_fields['sortables_confirm_id'] = $this->confirm_id;
		return $hidden_fields;
	}

	/**
	*  API function, just the same from captcha_qa but with other table names
	*/
	function garbage_collect($type = 0)
	{
		$sql = 'SELECT c.confirm_id
			FROM ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . ' c
			LEFT JOIN ' . SESSIONS_TABLE . ' s 
				ON (c.session_id = s.session_id)
			WHERE s.session_id IS NULL' .
				((empty($type)) ? '' : ' AND c.confirm_type = ' . (int) $type);
		$result = $this->db->sql_query($sql);

		if ($row = $this->db->sql_fetchrow($result))
		{
			$sql_in = array();
			
			do
			{
				$sql_in[] = (string) $row['confirm_id'];
			}
			while ($row = $this->db->sql_fetchrow($result));

			if (sizeof($sql_in))
			{
				$sql = 'DELETE FROM ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . '
					WHERE ' . $this->db->sql_in_set('confirm_id', $sql_in);
				$this->db->sql_query($sql);
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	*  API function - we don't drop the tables here, as that would cause the loss of all entered questions.
	*/
	function uninstall()
	{
		$this->garbage_collect(0);
	}

	/**
	*  API function - create the tables needed for sortables captcha
	*/
	function install()
	{
		$this->db_tool = new \phpbb\db\tools($this->db);
		$tables = array(CAPTCHA_SORTABLES_QUESTIONS_TABLE, CAPTCHA_SORTABLES_ANSWERS_TABLE, CAPTCHA_SORTABLES_CONFIRM_TABLE);
		
		$schemas = array(
				CAPTCHA_SORTABLES_QUESTIONS_TABLE		=> array (
								'COLUMNS' => array(
									'question_id'	=> array('UINT', Null, 'auto_increment'),
									'sort'			=> array('BOOL', 0),
									'lang_id'		=> array('UINT', 0),
									'lang_iso'		=> array('VCHAR:30', ''),
									'question_text'	=> array('TEXT_UNI', ''),
									'name_left'		=> array('STEXT_UNI', 0), // Column names
									'name_right'	=> array('STEXT_UNI', 0),
								),
								'PRIMARY_KEY'		=> 'question_id',
								'KEYS'				=> array(
									'iso'			=> array('INDEX', 'lang_iso'),
								),
				),
				CAPTCHA_SORTABLES_ANSWERS_TABLE		=> array (
								'COLUMNS' => array(
									'answer_id'		=> array('UINT', Null, 'auto_increment'),
									'question_id'	=> array('UINT', 0),
									'answer_sort'	=> array('BOOL', 0),
									'answer_text'	=> array('STEXT_UNI', ''),
								),
								'PRIMARY_KEY'		=> 'answer_id',
								'KEYS'				=> array(
									'qid'				=> array('INDEX', 'question_id'),
									'asort'				=> array('INDEX', 'answer_sort'),
								),
				),
				CAPTCHA_SORTABLES_CONFIRM_TABLE		=> array (
								'COLUMNS' => array(
									'session_id'	=> array('CHAR:32', ''),
									'confirm_id'	=> array('CHAR:32', ''),
									'lang_iso'		=> array('VCHAR:30', ''),
									'question_id'	=> array('UINT', 0),
									'attempts'		=> array('UINT', 0),
									'confirm_type'	=> array('USINT', 0),
								),
								'KEYS'				=> array(
									'sid'				=> array('INDEX', 'session_id'),
									'lookup'			=> array('INDEX', array('confirm_id', 'session_id', 'lang_iso')),
								),
								'PRIMARY_KEY'		=> 'confirm_id',
				),
		);
		
		foreach ($schemas as $table => $schema)
		{
			if (!$this->db_tool->sql_table_exists($table))
			{
				$this->db_tool->sql_create_table($table, $schema);
			}
		}
	}


	/**
	*  API function - see what has to be done to validate
	*/
	function validate()
	{
		$error = '';
		
		if (!sizeof($this->question_ids))
		{
			return false;
		}
		
		if (!$this->confirm_id)
		{
			$error = $this->user->lang['CONFIRM_QUESTION_WRONG'];
		}
		else
		{
			if ($this->check_answer())
			{
				// $this->delete_code(); commented out to allow posting.php to repeat the question
				$this->solved = true;
			}
			else
			{
				$error = $this->user->lang['CONFIRM_QUESTION_WRONG'];
			}
		}

		if (strlen($error))
		{
			// okay, incorrect answer. Let's ask a new question.
			$this->new_attempt();
			$this->solved = false;
			
			return $error;
		}
		else
		{
			return false;
		}
	}

	/**
	*  Select a question
	*/
	function select_question()
	{
		if (!sizeof($this->question_ids))
		{
			return false;
		}
		$this->confirm_id = md5(unique_id($this->user->ip));
		$this->question = (int) array_rand($this->question_ids);
		
		$sql = 'INSERT INTO ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . ' ' . $this->db->sql_build_array('INSERT', array(
			'confirm_id'	=> (string) $this->confirm_id,
			'session_id'	=> (string) $this->user->session_id,
			'lang_iso'		=> (string) $this->question_lang,
			'confirm_type'	=> (int) $this->type,
			'question_id'	=> (int) $this->question,
		));
		$this->db->sql_query($sql);
		
		$this->load_answer();
	}

	/**
	* New Question, if desired.
	*/
	function reselect_question()
	{
		if (!sizeof($this->question_ids))
		{
			return false;
		}

		$this->question = (int) array_rand($this->question_ids);
		$this->solved = 0;

		$sql = 'UPDATE ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . ' 
			SET question_id = ' . (int) $this->question . "
			WHERE confirm_id = '" . $this->db->sql_escape($this->confirm_id) . "' 
				AND session_id = '" . $this->db->sql_escape($this->user->session_id) . "'";
		$this->db->sql_query($sql);
		
		$this->load_answer();
	}

	/**
	* Wrong answer, so we increase the attempts and use a different question.
	*/
	function new_attempt()
	{
		// yah, I would prefer a stronger rand, but this should work
		$this->question = (int) array_rand($this->question_ids);
		$this->solved = 0;

		$sql = 'UPDATE ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . ' 
			SET question_id = ' . (int) $this->question . ",
				attempts = attempts + 1
			WHERE confirm_id = '" . $this->db->sql_escape($this->confirm_id) . "' 
				AND session_id = '" . $this->db->sql_escape($this->user->session_id) . "'";
		$this->db->sql_query($sql);

		$this->load_answer();
	}
	

	/**
	* See if there is already an entry for the current session.
	*/
	function load_confirm_id()
	{
		$sql = 'SELECT confirm_id
			FROM ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . " 
			WHERE 
				session_id = '" . $this->db->sql_escape($this->user->session_id) . "'
				AND lang_iso = '" . $this->db->sql_escape($this->question_lang) . "'
				AND confirm_type = " . $this->type;
		$result = $this->db->sql_query_limit($sql, 1);
		$confirm_id = $this->db->sql_fetchfield('confirm_id');
		$this->db->sql_freeresult($result);

		if ($confirm_id)
		{
			$this->confirm_id = $confirm_id;
			return true;
		}
		return false;
	}

	/**
	* Look up everything we need and populate the instance variables.
	*/
	function load_answer()
	{
		if (!strlen($this->confirm_id) || !sizeof($this->question_ids))
		{
			return false;
		}

		$sql = 'SELECT con.question_id, attempts, question_text, sort, name_left, name_right
			FROM ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . ' con, ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . " qes 
			WHERE con.question_id = qes.question_id
				AND confirm_id = '" . $this->db->sql_escape($this->confirm_id) . "'
				AND session_id = '" . $this->db->sql_escape($this->user->session_id) . "'
				AND qes.lang_iso = '" . $this->db->sql_escape($this->question_lang) . "'
				AND confirm_type = " . $this->type;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		if ($row)
		{
			$this->question = $row['question_id'];
			$this->attempts = $row['attempts'];
			$this->question_sort = $row['sort'];
			$this->question_text = $row['question_text'];
			$this->name_left = $row['name_left'];
			$this->name_right = $row['name_right'];
			
			// Let's load the answers
			$sql = 'SELECT answer_id, answer_text
				FROM ' . CAPTCHA_SORTABLES_ANSWERS_TABLE . "
				WHERE question_id = '" . (int) $this->question . "' 
				ORDER BY " . $this->sql_random();
			$result = $this->db->sql_query($sql);
			
			$this->template->destroy_block_vars('options'); // It's running twice, only grab the lastest see topic 1732385
			$this->total_options = 0;
			while ($row = $this->db->sql_fetchrow($result))
			{ 
				$this->template->assign_block_vars('options', array(
					'ID'		=> $row['answer_id'],
					'TEXT'		=> $row['answer_text'],
				));			
				$this->total_options++;
			}
			$this->db->sql_freeresult($result);
			return true;
			
		}
		
		return false;
	}

	/**
	*  The actual validation
	*/
	function check_answer()
	{
		// Well how did the user sorted it
		$options_left = request_var('sortables_options_left', array(0));
		$options_right = request_var('sortables_options_right', array(0));
		
		// Make sure the didn't submitted more options then it should (like trying everything... left/right: options ^ 2 )
		if ($this->total_options === sizeof($options_left) + sizeof($options_right))
		{
			// Let's count how many options the user sorted correctly		
			$sql = 'SELECT COUNT(*) AS total 
							FROM ' . CAPTCHA_SORTABLES_ANSWERS_TABLE . '
							WHERE question_id = ' . (int) $this->question . '
									AND ((answer_sort = 0 AND ' . $this->db->sql_in_set('answer_id', $options_left, false, true) . ')
									OR (answer_sort = 1 AND ' . $this->db->sql_in_set('answer_id', $options_right, false, true) .'))';
			$result = $this->db->sql_query($sql);
			$total_options_good = (int) $this->db->sql_fetchfield('total');
			
			// Now compare that amount with the total amount of options for this question
			if ($this->total_options === $total_options_good)
			{
				$this->solved = true;
				// Remember this for the hidden fields
				$this->options_left = $options_left;
				$this->options_right = $options_right;
			}
			$this->db->sql_freeresult($result);
		}
		
		return $this->solved;
	}

	/**
	*  API function - clean the entry
	*/
	function delete_code()
	{
		$sql = 'DELETE FROM ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . "
				WHERE confirm_id = '" . $this->db->sql_escape($confirm_id) . "'
					AND session_id = '" . $this->db->sql_escape($this->user->session_id) . "'
					AND confirm_type = " . $this->type;
		$this->db->sql_query($sql);
	}

	/**
	*  API function 
	*/
	function get_attempt_count()
	{
		return $this->attempts;
	}

	/**
	*  API function 
	*/
	function reset()
	{
		$sql = 'DELETE FROM ' . CAPTCHA_SORTABLES_CONFIRM_TABLE . "
				WHERE session_id = '" . $this->db->sql_escape($this->user->session_id) . "'
					AND confirm_type = " . (int) $this->type;
		$this->db->sql_query($sql);

		// we leave the class usable by generating a new question
		$this->select_question();
	}

	/**
	*  API function 
	*/
	function is_solved()
	{
		if (request_var('qa_answer', false) && $this->solved === 0)
		{
			$this->validate();
		}
		return (bool) $this->solved;
	}
	
	
	/**
	*  API function - The ACP backend, this marks the end of the easy methods
	*/
	function acp_page($id, &$module)
	{
		$this->user->add_lang('acp/board');
		$this->user->add_lang_ext('derky/sortablescaptcha', 'captcha_sortables');

		if (!$this->is_installed())
		{
			$this->install();
		}
		$module->tpl_name = '@derky_sortablescaptcha/captcha_sortables_acp';
		$module->page_title = 'ACP_VC_SETTINGS';
		$form_key = 'acp_captcha';
		add_form_key($form_key);

		$submit = request_var('submit', false);
		$question_id = request_var('question_id', 0);
		$action = request_var('action', '');
		
		// we have two pages, so users might want to navigate from one to the other
		$list_url = $module->u_action . "&amp;configure=1&amp;select_captcha=" . $this->get_service_name();
		
		$this->template->assign_vars(array(
				'U_ACTION'		=> $module->u_action,
				'QUESTION_ID'	=> $question_id ,
				'CLASS'			=> $this->get_service_name(),
		));

		// show the list?
		if (!$question_id && $action != 'add')
		{
			$this->acp_question_list($module);
		}
		else if ($question_id && $action == 'delete')
		{
			if ($this->get_service_name() !== $this->config['captcha_plugin'] || !$this->acp_is_last($question_id))
			{
				if (confirm_box(true))
				{
					$this->acp_delete_question($question_id);
					trigger_error($this->user->lang['QUESTION_DELETED'] . adm_back_link($list_url));
				}
				else
				{
					confirm_box(false, $this->user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
						'question_id'		=> $question_id,
						'action'			=> $action,
						'configure'			=> 1,
						'select_captcha'	=> $this->get_service_name(),
						))
					);
				}
			}
			else
			{
				trigger_error($this->user->lang['QA_LAST_QUESTION'] . adm_back_link($list_url), E_USER_WARNING);
			}
		}
		else
		{
			// okay, show the editor
			$error = false;
			$input_question = request_var('question_text', '', true);
			$input_name_left = request_var('name_left', '', true);
			$input_name_right = request_var('name_right', '', true);
			$input_options_left = request_var('options_left', '', true);
			$input_options_right = request_var('options_right', '', true);
			$input_lang = request_var('lang_iso', '');
			$input_sort = request_var('sort', false);
			$langs = $this->get_languages();
			foreach ($langs as $lang => $entry)
			{
				$this->template->assign_block_vars('langs', array(
					'ISO' => $lang,
					'NAME' => $entry['name'],
				));
			}
			
			$this->template->assign_vars(array(
				'U_LIST'		=> $list_url,
			));
			if ($question_id)
			{
				if ($question = $this->acp_get_question_data($question_id))
				{
					$options_left = (isset($input_options_left[$lang])) ? $input_options_left[$lang] : implode("\n", $question['options_left']);
					$options_right = (isset($input_options_right[$lang])) ? $input_options_right[$lang] : implode("\n", $question['options_right']);
					$this->template->assign_vars(array(
						'QUESTION_TEXT'		=> ($input_question) ? $input_question : $question['question_text'],
						'LANG_ISO'			=> ($input_lang) ? $input_lang : $question['lang_iso'],
						'SORT'				=> (isset($_REQUEST['sort'])) ? $input_sort : $question['sort'],
						'NAME_LEFT'			=> ($input_name_left) ? $input_name_left : $question['name_left'],
						'NAME_RIGHT'		=> ($input_name_right) ? $input_name_right : $question['name_right'],
						'OPTIONS_LEFT'		=> $options_left,
						'OPTIONS_RIGHT'		=> $options_right,
					));
				}
				else
				{
					trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($list_url));
				}
			}
			else
			{
			
				$this->template->assign_vars(array(
						'QUESTION_TEXT'		=> $input_question,
						'LANG_ISO'			=> $input_lang,
						'SORT'				=> $input_sort,
						'NAME_LEFT'			=> $input_name_left,
						'NAME_RIGHT'		=> $input_name_right,
						'OPTIONS_LEFT'		=> $input_options_left,
						'OPTIONS_RIGHT'		=> $input_options_right,
				));
			}
			
			if ($submit && check_form_key($form_key))
			{
				$data = $this->acp_get_question_input();
				if (!$this->validate_input($data))
				{
					$this->template->assign_vars(array(
						'S_ERROR'			=> true,
					));
				}
				else
				{
					if ($question_id)
					{
						$this->acp_update_question($data, $question_id);
					}
					else
					{
						$this->acp_add_question($data);
					}
					
					add_log('admin', 'LOG_CONFIG_VISUAL');
					trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($list_url));
				}
			}
			else if ($submit)
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($list_url), E_USER_WARNING);
			}
		}
	}
	

	/**
	*  This handles the list overview
	*/
	function acp_question_list(&$module)
	{
		$sql = 'SELECT * 
				FROM ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE;
		$result = $this->db->sql_query($sql);
		$this->template->assign_vars(array(
			'S_LIST'			=> true,
		));

		while ($row = $this->db->sql_fetchrow($result))
		{
			$url = $module->u_action . "&amp;question_id={$row['question_id']}&amp;configure=1&amp;select_captcha=" . $this->get_service_name() . '&amp;';
			
			$this->template->assign_block_vars('questions', array(
				'QUESTION_TEXT'		=> $row['question_text'],
				'QUESTION_ID'		=> $row['question_id'],
				'QUESTION_LANG'		=> $row['lang_iso'],
				'U_DELETE'			=> "{$url}action=delete",
				'U_EDIT'			=> "{$url}action=edit",
			));
		}
		$this->db->sql_freeresult($result);
	}

	/**
	*  Grab a question and bring it into a format the editor understands
	*/
	function acp_get_question_data($question_id)
	{
		if ($question_id)
		{
			$sql = 'SELECT * 
				FROM ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . ' 
				WHERE question_id = ' . $question_id;
			$result = $this->db->sql_query($sql);
			$question = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			
			if (!$question)
			{
				return false;
			}

			$question['answers'] = array();
			$question['options_left'] = array();
			$question['options_right'] = array();
	
			$sql = 'SELECT * 
				FROM ' . CAPTCHA_SORTABLES_ANSWERS_TABLE . ' 
				WHERE question_id = ' . $question_id;
			$result = $this->db->sql_query($sql);
			
			while ($row = $this->db->sql_fetchrow($result))
			{
				if (!$row['answer_sort']) // 0 = left column, 1 = right column
				{
					$question['options_left'][] = $row['answer_text'];
				}
				else
				{
					$question['options_right'][] = $row['answer_text'];
				}
			}
			$this->db->sql_freeresult($result);
			return $question;
		}
		
	}
	
	
	/**
	*  Grab a question from input and bring it into a format the editor understands
	*/
	function acp_get_question_input()
	{
		$question = array(
			'question_text'	=> request_var('question_text', '', true),
			'sort'			=> request_var('sort', false),
			'lang_iso'		=> request_var('lang_iso', ''),
			'name_left'		=> request_var('name_left', '', true),
			'name_right'	=> request_var('name_right', '', true),
			'options_left'	=> explode("\n", request_var('options_left', '', true)),
			'options_right'	=> explode("\n", request_var('options_right', '', true)),
		);
		
		return $question;
	}

	/**
	*  Update a question.
	* param mixed $data : an array as created from acp_get_question_input or acp_get_question_data
	*/
	function acp_update_question($data, $question_id)
	{
		// easier to delete all answers than to figure out which to update
		$sql = 'DELETE FROM ' . CAPTCHA_SORTABLES_ANSWERS_TABLE . " WHERE question_id = $question_id";
		$this->db->sql_query($sql);
		
		$langs = $this->get_languages();
		$question_ary = $data;
		$question_ary['lang_id'] = $langs[$question_ary['lang_iso']]['id'];
		unset($question_ary['options_left']);
		unset($question_ary['options_right']);
		
		$sql = "UPDATE " . CAPTCHA_SORTABLES_QUESTIONS_TABLE . ' 
			SET ' . $this->db->sql_build_array('UPDATE', $question_ary) . "
			WHERE question_id = $question_id";
		$this->db->sql_query($sql);
		
		$this->acp_insert_answers($data, $question_id);
		
		$this->cache->destroy('sql', CAPTCHA_SORTABLES_QUESTIONS_TABLE);
	}
	
	/**
	*  Insert a question.
	* param mixed $data : an array as created from acp_get_question_input or acp_get_question_data
	*/
	function acp_add_question($data)
	{
		$langs = $this->get_languages();
		$question_ary = $data;
		
		$question_ary['lang_id'] = $langs[$data['lang_iso']]['id'];
		unset($question_ary['options_left']);
		unset($question_ary['options_right']);
		
		$sql = 'INSERT INTO ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . $this->db->sql_build_array('INSERT', $question_ary);
		$this->db->sql_query($sql);
		
		$question_id = $this->db->sql_nextid();
		
		$this->acp_insert_answers($data, $question_id);
		
		$this->cache->destroy('sql', CAPTCHA_SORTABLES_QUESTIONS_TABLE);
	}
	
	/**
	*  Insert the answers.
	* param mixed $data : an array as created from acp_get_question_input or acp_get_question_data
	*/
	function acp_insert_answers($data, $question_id)
	{
		foreach ($data['options_left'] as $answer)
		{
			$answer_ary = array(
				'answer_id'		=> $this->acp_gen_random_answer_id(),
				'question_id'	=> $question_id,
				'answer_sort'	=> 0,
				'answer_text'	=> $answer,
			);
			$sql = 'INSERT INTO ' . CAPTCHA_SORTABLES_ANSWERS_TABLE . $this->db->sql_build_array('INSERT', $answer_ary);
			$this->db->sql_query($sql);
		}
		foreach ($data['options_right'] as $answer)
		{
			$answer_ary = array(
				'answer_id'		=> $this->acp_gen_random_answer_id(),
				'question_id'	=> $question_id,
				'answer_sort'	=> 1,
				'answer_text'	=> $answer,
			);
			$sql = 'INSERT INTO ' . CAPTCHA_SORTABLES_ANSWERS_TABLE . $this->db->sql_build_array('INSERT', $answer_ary);
			$this->db->sql_query($sql);
		}
		
		$this->cache->destroy('sql', CAPTCHA_SORTABLES_ANSWERS_TABLE);
	}
	

	/**
	*  Delete a question.
	*/
	function acp_delete_question($question_id)
	{
		$tables = array(CAPTCHA_SORTABLES_QUESTIONS_TABLE, CAPTCHA_SORTABLES_ANSWERS_TABLE);
		foreach ($tables as $table)
		{
			$sql = "DELETE FROM $table 
				WHERE question_id = $question_id";
			$this->db->sql_query($sql);
		}
		
		$this->cache->destroy('sql', $tables);
	}
	
	/**
	*  Check if the entered data can be inserted/used
	* param mixed $data : an array as created from acp_get_question_input or acp_get_question_data
	*/
	function validate_input($question_data)
	{
		$langs = $this->get_languages();
		
		if (!isset($question_data['lang_iso']) ||
			!isset($question_data['question_text']) ||
			!isset($question_data['sort']) ||
			!isset($question_data['name_left']) ||
			!isset($question_data['name_right']) ||
			!isset($question_data['options_left']) ||
			!isset($question_data['options_right']))
		{
			return false;
		}
		
		if (!isset($langs[$question_data['lang_iso']]) ||
			!$question_data['question_text'] ||
			!sizeof($question_data['options_left']) || 
			!sizeof($question_data['options_right']))
		{
			return false;
		}
		
		return true;
	}	

	/**
	*  See if there is a question other than the one we have
	*/
	function acp_is_last($question_id)
	{
		if ($question_id)
		{
			$sql = 'SELECT question_id
				FROM ' . CAPTCHA_SORTABLES_QUESTIONS_TABLE . "
				WHERE lang_iso = '" . $this->db->sql_escape($this->config['default_lang']) . "'
					AND  question_id <> " .  (int) $question_id;
			$result = $this->db->sql_query_limit($sql, 1);
			$question = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$question)
			{
				return true;
			}
			return false;
		}
	}
	
	/**
	*	Get all answer_ids (used to check if a random answer_id is not already used)
	*/
	function acp_get_answer_ids()
	{
		// If it's ready set, then stop here
		if ($this->answer_ids)
		{
			return $this->answer_ids;
		}
		
		// Get all answer ids
		$sql = 'SELECT answer_id 
				FROM ' . CAPTCHA_SORTABLES_ANSWERS_TABLE;
		$result = $this->db->sql_query($sql);
		
		// Fill it up
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->answer_ids[] = $row['answer_id'];
		}
		$this->db->sql_freeresult($result);
				
		// When the answers table is empty, add 0 to prevent problems
		if (empty($this->answer_ids))
		{
			$this->answer_ids[] = 0;
		}
		
		return $this->answer_ids;
	}
	
	/**
	*	Generate an unique answer_id
	*/
	function acp_gen_random_answer_id()
	{
		// Get the already used ids
		$answer_ids = $this->acp_get_answer_ids();
		
		// Randomise
		$random = mt_rand(1, 100000);
		
		// If already used, repeat this function recursively
		if (in_array($random, $answer_ids))
		{
			return $this->acp_gen_random_answer_id();
		}
		
		return $random;
	}
	
	/**
	*	Get the random statement for this database layer
	*/
	function sql_random()
	{
		$statement = '';
		
		switch ($this->db->get_sql_layer())
		{
			case 'firebird':
			case 'oracle':
			case 'postgres':
			case 'sqlite':
				$statement = 'RANDOM()';
			break;
			
			case 'mssql':
			case 'mssqlnative':
			case 'mysql_40':
			case 'mysql_41':
			case 'mysqli':
			default:
				$statement = 'RAND()';
			break;
		}
		return $statement;
	}
}

?>