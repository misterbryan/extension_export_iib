<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_extensionexportiib
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/extension.php';

/**
 * Extensionexportiib Listeextensions Model
 *
 * @since  1.6
 */
class ExtensionexportiibModelListeextensions extends ExtensionexportiibModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$clientId = $this->getUserStateFromRequest($this->context . '.filter.client_id', 'filter_client_id', '');
		$this->setState('filter.client_id', $clientId);

		$status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '');
		$this->setState('filter.status', $status);
                
		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
		$this->setState('filter.type', $categoryId);

		$group = $this->getUserStateFromRequest($this->context . '.filter.group', 'filter_group', '');
		$this->setState('filter.group', $group);

		$this->setState('message', $app->getUserState('com_extensionexportiib.message'));
		$this->setState('extension_message', $app->getUserState('com_extensionexportiib.extension_message'));
		$app->setUserState('com_extensionexportiib.message', '');
		$app->setUserState('com_extensionexportiib.extension_message', '');

		parent::populateState('name', 'asc');
	}

	/**
	 * Method to get the database query.
	 *
	 * @return  JDatabaseQuery  the database query
	 *
	 * @since   3.1
	 */
	protected function getListQuery()
	{
		$status = $this->getState('filter.status');
		$type = $this->getState('filter.type');
		$client = $this->getState('filter.client_id');
		$group = $this->getState('filter.group');
		$query = JFactory::getDbo()->getQuery(true)
			->select('*')
			->select('2*protected+(1-protected)*enabled as status')
			->from('#__extensions')
			->where("state = 0",'AND')
			->where("type != 'library'",'AND')
			->where("type != 'file'",'AND')
			->where("type != 'package'",'AND')
			->where("type != 'language'");

		if ($status != '')
		{
			if ($status == '2')
			{
				$query->where('protected = 1');
			}
			elseif ($status == '3')
			{
				$query->where('protected = 0');
			}
			else
			{
				$query->where('protected = 0')
					->where('enabled=' . (int) $status);
			}
		}

		if ($type)
		{
			$query->where('type=' . $this->_db->quote($type));
		}

		if ($client != '')
		{
			$query->where('client_id=' . (int) $client);
		}

		if ($group != '' && in_array($type, array('plugin', 'library', '')))
		{
			$query->where('folder=' . $this->_db->quote($group == '*' ? '' : $group));
		}

		// Filter by search in id
		$search = $this->getState('filter.search');

		if (!empty($search) && stripos($search, 'id:') === 0)
		{
			$query->where('extension_id = ' . (int) substr($search, 3));
		}

		return $query;
	}
	
    /*
     * Get attribute from database
     */

    public function getAttributes($extension_id) {
        // Create a new query object.		
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        // Select some fields from the table
        $query
                ->select("element, type, client_id, folder")
                ->from('#__extensions')
                ->where("extension_id = $extension_id");
        $db->setQuery($query);
        $row = $db->loadObject();
        return $row;
    }


}
