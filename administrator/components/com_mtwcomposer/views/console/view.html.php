<?php
/**
* mtwComposer
*
* @version $Id$
* @package Matware
* @subpackage mtwComposer
* @copyright Copyright 2004 - 2016 Matias Aguirre. All rights reserved.
* @license GNU General Public License version 2 or later.
* @author Matias Aguirre <maguirre@matware.com.ar>
* @link http://www.matware.com.ar
*/

defined('_JEXEC') or die;

/**
 * mtwComposer Console view
 *
 * @since  1.0
 */
class mtwComposerViewConsole extends JViewLegacy
{
	/**
	 * Renders the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function display($tpl = null)
	{
		// Get data from the model.
		$this->state = $this->get('State');

		// Set the toolbar information.
		JToolbarHelper::title(JText::_('COM_MTWCOMPOSER_OVERVIEW'), 'loop install');

		// Add toolbar buttons.
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_mtwcomposer') || $user->authorise('core.options', 'com_mtwcomposer'))
		{
			JToolbarHelper::preferences('com_mtwcomposer');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_JOOMLA_UPDATE');

		// Render the view.
		parent::display($tpl);
	}
}
