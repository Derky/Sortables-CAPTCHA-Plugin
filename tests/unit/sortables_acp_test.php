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
	/** @var \phpbb\cache\driver\driver_interface|\PHPUnit_Framework_MockObject_MockObject */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface|\PHPUnit_Framework_MockObject_MockObject */
	protected $driver;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user|\PHPUnit_Framework_MockObject_MockObject */
	protected $user;

	/** @var \phpbb\template\template|\PHPUnit_Framework_MockObject_MockObject */
	protected $template;

	/**
	 * Construct sortables class with mocked services
	 *
	 * @param array $input_post_array Variables like submitted with $_POST
	 * @return \derky\sortablescaptcha\captcha\sortables
	 */
	public function get_sortables($input_post_array = array())
	{
		global $phpbb_root_path, $phpEx;

		$request = new \phpbb_mock_request(array(), $input_post_array);

		$this->cache = $this->getMockBuilder('\phpbb\cache\driver\driver_interface')
			->disableOriginalConstructor()
			->getMock();

		$this->config = new \phpbb\config\config(array());

		$this->driver = $this->getMockBuilder('\phpbb\db\driver\driver_interface')
			->disableOriginalConstructor()
			->getMock();

		$this->language = new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx));

		$this->user = $this->getMockBuilder('\phpbb\user')
			->setConstructorArgs(array(
				$this->language,
				'\phpbb\datetime'
			))
			->getMock();

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->disableOriginalConstructor()
			->getMock();

		return new \derky\sortablescaptcha\captcha\sortables(
			$this->driver,
			$this->cache,
			new \phpbb\config\config(array()),
			$this->language,
			new \phpbb\log\dummy(),
			$request,
			$this->template,
			$this->user,
			'phpbb_sortables_questions',
			'phpbb_sortables_answers',
			'phpbb_sortables_confirm');
	}

	public static function acp_input_options_to_array_data()
	{
		return array(
			array(''), array(),
			array('a', array('a')),
			array("a\n\nb", array('a', 'b')),
			array("   a  \n   \n   b ", array('a', 'b')),
			array('0', array('0')),
		);
	}

	/**
	* @dataProvider acp_input_options_to_array_data
	*/
	public function test_acp_input_options_to_array($input_options_string = '', $expected_options_array = array())
	{
		$sortables = $this->get_sortables();

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
		$sortables = $this->get_sortables($input_post_array);

		$this->assertEquals($expected_array, $sortables->acp_get_question_input());
	}
}
