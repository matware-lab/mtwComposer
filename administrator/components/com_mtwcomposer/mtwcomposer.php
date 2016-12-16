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

if (!JFactory::getUser()->authorise('core.manage', 'com_mtwcomposer'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = JControllerLegacy::getInstance('mtwComposer');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
