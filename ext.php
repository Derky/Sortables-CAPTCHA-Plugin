<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace derky\sortablescaptcha;

/**
* @ignore
*/

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
