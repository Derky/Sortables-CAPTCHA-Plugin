<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha\captcha\tests\feature;

class insert_question_test extends base_feature_test
{
	/** @var \phpbb\db\driver\driver_interface */
	public $db;

	/** @var string */
	protected $table_prefix;

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/insert_question_test.xml');
	}

	public function setUp(): void
	{
		parent::setUp();

		global $db, $table_prefix;

		$this->table_prefix = $table_prefix;
		$this->db = $this->new_dbal();
		$db = $this->db;

		$this->table_prefix = $table_prefix;
	}

	public function test_insert_question()
	{
		$request_data = array(
			'question_text' => 'Question 1',
			'lang_iso' => 'en',
			'sort' => '1',
			'name_left' => 'Left',
			'name_right' => 'Right',
			'options_left' => ['Left 1', 'Left 2'],
			'options_right' => ['Right 1'],
		);

		$sortables = $this->get_sortables();

		$question_id = $sortables->acp_insert_question($request_data);

		$sql = 'SELECT *
			FROM ' . $this->table_prefix . 'sortables_questions
			WHERE question_id = ' . (int) $question_id;
		$result = $this->db->sql_query($sql);
		$question = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->assertEquals($request_data['question_text'], $question['question_text']);
	}
}
