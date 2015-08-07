<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Listeextensions View
 */
class ExtensionexportiibViewListeextensions extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view.
     * @param   string  $tpl  Template
     * @return  void
     * @since   1.6
     */
    public function display($tpl = null) {
        ExtensionexportiibHelper::addSubmenu('listeextensions');
        $this->sidebar = JHtmlSidebar::render();

        // Get data from the model.
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $extension = JFactory::getApplication()->getUserState('com_extensionexportiib.listeextensions.extension', '');
        if ($extension != '') {
            ?>
            <meta http-equiv="refresh" content="1;url=<?php echo JUri::base() . 'ExtensionExportIIB/' . $extension . ".zip"; ?>">
            <?php
            // Flush the data from the session.
            JFactory::getApplication()->setUserState('com_extensionexportiib.listeextensions.extension', null);
        }
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }


        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = ExtensionexportiibHelper::getActions('com_extensionexportiib');

        // Set the toolbar and number of found items
        $this->addToolBar($this->pagination->total);

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   3.1
     */
    protected function addToolbar($total = null) {
        JToolBarHelper::title(JText::_('COM_EXTENSIONEXPORTIIB_MANAGER') .
                //Reflect number of items in title!
                ($total ? ' <span style="font-size: 0.5em; vertical-align: middle;">(' . $total . ')</span>' : ''), 'generic');

        JHtmlSidebar::setAction('index.php?option=com_extensionexportiib&view=listeextensions');

        JHtmlSidebar::addFilter(
                JText::_('COM_EXTENSIONEXPORTIIB_VALUE_CLIENT_SELECT'), 'filter_client_id', JHtml::_(
                        'select.options', array('0' => 'JSITE', '1' => 'JADMINISTRATOR'), 'value', 'text', $this->state->get('filter.client_id'), true
                )
        );

        JHtmlSidebar::addFilter(
                JText::_('COM_EXTENSIONEXPORTIIB_VALUE_STATE_SELECT'), 'filter_status', JHtml::_(
                        'select.options', array('0' => 'JDISABLED', '1' => 'JENABLED', '2' => 'JPROTECTED', '3' => 'JUNPROTECTED'), 'value', 'text', $this->state->get('filter.status'), true
                )
        );

        JHtmlSidebar::addFilter(
                JText::_('COM_EXTENSIONEXPORTIIB_VALUE_TYPE_SELECT'), 'filter_type', JHtml::_(
                        'select.options', ExtensionexportiibHelper::getExtensionTypes(), 'value', 'text', $this->state->get('filter.type'), true
                )
        );

        JHtmlSidebar::addFilter(
                JText::_('COM_EXTENSIONEXPORTIIB_VALUE_FOLDER_SELECT'), 'filter_group', JHtml::_(
                        'select.options', array_merge(ExtensionexportiibHelper::getExtensionGroupes(), array('*' => JText::_('COM_EXTENSIONEXPORTIIB_VALUE_FOLDER_NONAPPLICABLE'))), 'value', 'text', $this->state->get('filter.group'), true
                )
        );
        $this->sidebar = JHtmlSidebar::render();
    }

    protected function setDocument() {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_EXTENSIONEXPORTIIB_ADMINISTRATION'));
    }

    protected function getSortFields() {
        return array(
            'a.extension_id' => JText::_('COM_EXTENSIONEXPORTIIB_HEADING_EXTENSIONID'),
            'a.name' => JText::_('COM_EXTENSIONEXPORTIIB_HEADING_NAME'),
            'a.type' => JText::_('COM_EXTENSIONEXPORTIIB_HEADING_TYPE'),
            'a.element' => JText::_('COM_EXTENSIONEXPORTIIB_HEADING_ELEMENT')
        );
    }

}
