<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_extensionexportiib')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
// require helper file
JLoader::register('ExtensionexportiibHelper', dirname(__FILE__) . '/helpers/extensionexportiib.php');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Extensionexportiib
$controller = JControllerLegacy::getInstance('Extensionexportiib');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();