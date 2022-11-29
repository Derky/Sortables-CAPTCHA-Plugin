<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha\captcha\tests\feature;

class garbage_collection_test extends base_feature_test
{
	/** @var \phpbb\db\driver\driver_interface */
	public $db;

	/** @var \phpbb\db\tools */
	protected $db_tools;

	/** @var string */
	protected $table_prefix;

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/garbage_collection.xml');
	}

	public function setUp(): void
	{
		parent::setUp();

		global $db, $table_prefix;

		$this->db = $this->new_dbal();
		$this->db_tools = new \phpbb\db\tools\tools($this->db);
		$db = $this->db;

		$this->table_prefix = $table_prefix;
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
