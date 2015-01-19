<?php
/**
*
* @copyright (c) Derky <http://www.derky.nl>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace derky\sortablescaptcha;

class ext extends \phpbb\extension\base
{
	/**
	* Single disable step
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Get config
				$config = $this->container->get('config');

				// Check if sortables currently is the default captcha
				if ($config['captcha_plugin'] === $this->container->get('derky.sortablescaptcha.captcha.sortables')->get_service_name())
				{
					// It's the default captcha, set the default captcha to phpBB's default GD captcha.
					$config->set('captcha_plugin', 'core.captcha.plugins.gd');
				}
				return 'default_captcha_changed';

			break;

			default:

				// Run parent disable step method
				return parent::disable_step($old_state);

			break;
		}
	}
}
