<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha\captcha\tests\functional;

class acp_captcha_settings_test extends \phpbb_functional_test_case
{
	protected static function setup_extensions()
	{
		return array('derky/sortablescaptcha');
	}

	function test_sortables_shows_in_available_captchas_selectbox() {
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('derky/sortablescaptcha', ['captcha_sortables']);

		$crawler = self::request('GET', 'adm/index.php?i=acp_captcha&mode=visual&sid=' . $this->sid);

		$this->assertContainsLang('CAPTCHA_SORTABLES', $crawler->filter('#captcha_select')->text());
	}
}
