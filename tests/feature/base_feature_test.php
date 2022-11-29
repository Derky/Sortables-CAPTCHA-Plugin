<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha\captcha\tests\feature;

abstract class base_feature_test extends \phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('derky/sortablescaptcha');
	}

	/**
	 * Construct sortables class with mocked services except database driver
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
			$this->db,
			$this->cache,
			new \phpbb\config\config(array()),
			new \phpbb\log\dummy(),
			$request,
			$this->template,
			$this->user,
			'phpbb_sortables_questions',
			'phpbb_sortables_answers',
			'phpbb_sortables_confirm');
	}
}
