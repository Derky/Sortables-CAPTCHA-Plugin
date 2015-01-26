<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha\captcha\tests\unit;

class sortables_acp_test extends \phpbb_test_case
{

	public static function acp_input_options_to_array_data()
	{
		return array(
			array('a', array('a')),
			array("a\n\nb", array('a', 'b')),
			array("   a  \n   \n   b ", array('a', 'b')),
			array('0', array('0')),
		);
	}

	/**
	* @dataProvider acp_input_options_to_array_data
	*/
	public function test_acp_input_options_to_array($input_options_string, $expected_options_array)
	{
		$sortables = new \derky\sortablescaptcha\captcha\sortables(
			$this->getMock('phpbb\db\driver\driver_interface'),
			$this->getMock('\phpbb\cache\driver\driver_interface'),
			new \phpbb\config\config(array()),
			new \phpbb\log\null(),
			new \phpbb_mock_request(),
			$this->getMock('\phpbb\template\template'),
			new \phpbb\user('\phpbb\datetime'),
			'phpbb_sortables_questions',
			'phpbb_sortables_answers',
			'phpbb_sortables_confirm');

		$this->assertEquals($expected_options_array, $sortables->acp_input_options_to_array($input_options_string));
	}

	public static function acp_get_question_input_data()
	{
		return array(
			// Test empty question
			array(array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => '', 'options_right' => ''),
				  array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => array(), 'options_right' => array())),

			// Test normal question
			array(array('question_text' => 'What do we need for tomato soup?', 'sort' => '1', 'lang_iso' => 'en', 'name_left' => 'In the pan', 'name_right' => 'Throw away', 'options_left' => "Tomatoes", 'options_right' => "Bananas\nApples"),
				  array('question_text' => 'What do we need for tomato soup?', 'sort' => '1', 'lang_iso' => 'en', 'name_left' => 'In the pan', 'name_right' => 'Throw away', 'options_left' => array('Tomatoes'), 'options_right' => array('Bananas', 'Apples'))),

			// Test automatic removal of empty line between 2 valid options
			array(array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => "A\n\nB", 'options_right' => "C\n\nD"),
				  array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => array('A', 'B'), 'options_right' => array('C', 'D'))),

			// Test automatic removal of blank line with some spaces between 2 valid options
			array(array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => "E\n    \nF", 'options_right' => "G\n    \nH"),
				  array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => array('E', 'F'), 'options_right' => array('G', 'H'))),

			// Test the valid option zero: "0" (this option should not be removed, this test makes sure that empty() is not used)
			array(array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => "0", 'options_right' => "1"),
				  array('question_text' => '', 'sort' => '', 'lang_iso' => '', 'name_left' => '', 'name_right' => '', 'options_left' => array('0'), 'options_right' => array('1'))),
		);
	}

	/**
	* @dataProvider acp_get_question_input_data
	*/
	public function test_acp_get_question_input($input_post_array, $expected_array)
	{
		$request = new \phpbb_mock_request(array(), $input_post_array);

		$sortables = new \derky\sortablescaptcha\captcha\sortables(
			$this->getMock('phpbb\db\driver\driver_interface'),
			$this->getMock('\phpbb\cache\driver\driver_interface'),
			new \phpbb\config\config(array()),
			new \phpbb\log\null(),
			$request,
			$this->getMock('\phpbb\template\template'),
			new \phpbb\user('\phpbb\datetime'),
			'phpbb_sortables_questions',
			'phpbb_sortables_answers',
			'phpbb_sortables_confirm');

		$this->assertEquals($expected_array, $sortables->acp_get_question_input());
	}

}