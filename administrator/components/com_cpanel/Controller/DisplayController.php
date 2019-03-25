<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Cpanel\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Cpanel Controller
 *
 * @since  1.5
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'cpanel';

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static  A \JControllerLegacy object to support chaining.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		/*
		 * Set the template - this will display cpanel.php
		 * from the selected admin template.
		 */
		$this->input->set('tmpl', 'cpanel');

		return parent::display($cachable, $urlparams);
	}

	public function addModule()
	{
		$position = $this->input->get('position', 'cpanel');
		$function = $this->input->get('function');

		$appendLink = '';

		if ($function)
		{
			$appendLink .= '&function=' . $function;
		}

		if (substr($position, 0, 6) != 'cpanel')
		{
			$position = 'cpanel';
		}

		Factory::getApplication()->setUserState('com_modules.modules.filter.position', $position);
		Factory::getApplication()->setUserState('com_modules.modules.client_id', '1');

		$this->setRedirect(Route::_('index.php?option=com_modules&view=select&tmpl=component&layout=modal' . $appendLink, false));
	}
}
