<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha\captcha\tests\feature;

class garbage_collection_test extends \phpbb_database_test_case
{
	/** @var \phpbb\db\tools */
	protected $db_tools;

	/** @var string */
	protected $table_prefix;

	/** @var \phpbb\db\driver\driver_interface */
	private $db;

	static protected function setup_extensions()
	{
		return array('derky/sortablescaptcha');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/garbage_collection.xml');
	}

	public function setUp()
	{
		parent::setUp();

		global $table_prefix;

		$this->table_prefix = $table_prefix;
		$this->db = $this->new_dbal();
		$this->db_tools = new \phpbb\db\tools($this->db);
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

	public function test_sortables_confirm_table_exists()
	{
		$this->assertTrue($this->db_tools->sql_table_exists($this->table_prefix . 'sortables_confirm'), 'Asserting that table "' . $this->table_prefix . 'sortables_confirm" exist');
	}

	public function test_sortables_garbage_collection()
	{
		// Garbage collection should delete 2 from the 3 rows because related sessions no longer exists
		$expected_confirm_rows = 1;
		$sortables = $this->get_sortables();
		$sortables->garbage_collect();

		$sql = 'SELECT COUNT(confirm_id) AS count
			FROM ' . $this->table_prefix . 'sortables_confirm';
		$this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('count');

		$this->assertEquals($expected_confirm_rows, $count);
	}
}
