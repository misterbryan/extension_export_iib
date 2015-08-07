<?php

// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * General Controller of Extensionexportiib component
 */
class ExtensionexportiibController extends JControllerLegacy {
    
 function display( $cachable = false, $urlparams = false )
	{
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view','listeextensions'));

		parent::display($cachable);
	}
   
}
