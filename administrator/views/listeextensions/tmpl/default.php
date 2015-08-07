<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_extensionexportiib
 *
 * @copyright   Copyright (C) 2005 - 2015 Ibrini. All rights reserved.
 * @author      Ibrini (http://ibrini.com)
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<div id="extensionexportiib-listeextensions" class="clearfix">
    <form action="<?php echo JRoute::_('index.php?option=com_extensionexportiib&view=listeextensions'); ?>" method="post" name="adminForm" id="adminForm">

        <?php if (!empty($this->sidebar)) : ?>
            <div id="j-sidebar-container" class="span2">
                <?php echo $this->sidebar; ?>
            </div>
            <div id="j-main-container" class="span10">
            <?php else : ?>
                <div id="j-main-container">
                <?php endif; ?>



                <!-- Begin Filters -->
                <div id="filter-bar" class="btn-toolbar">
                    <div class="btn-group pull-right hidden-phone">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                    <div class="filter-search btn-group pull-left">
                        <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_EXTENSIONEXPORTIIB_FILTER_LABEL'); ?>" />
                    </div>
                    <div class="btn-group pull-left">
                        <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                        <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value = '';
                                this.form.submit();"><i class="icon-remove"></i></button>
                    </div>
                </div>
                <div class="clearfix"> </div>

                <!-- Begin Content -->
                <?php if (count($this->items)) : ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo JText::_('COM_EXTENSIONEXPORTIIB_HEADING_EXPORT'); ?>
                                </th>
                                <th class="nowrap">
                                    <?php echo JHtml::_('grid.sort', 'COM_EXTENSIONEXPORTIIB_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
                                </th>
                                <th class="center">
                                    <?php echo JHtml::_('grid.sort', 'COM_EXTENSIONEXPORTIIB_HEADING_TYPE', 'type', $listDirn, $listOrder); ?>
                                </th>
                                <th class="hidden-phone">
                                    <?php echo JHtml::_('grid.sort', 'COM_EXTENSIONEXPORTIIB_HEADING_CLIENT', 'client_id', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%" class="center">
                                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'status', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%" class="center">
                                    <?php echo JText::_('JVERSION'); ?>
                                </th>
                                <th width="10%" class="center hidden-phone">
                                    <?php echo JText::_('JDATE'); ?>
                                </th>
                                <th width="15%" class="center hidden-phone">
                                    <?php echo JText::_('JAUTHOR'); ?>
                                </th>
                                <th width="10" class="hidden-phone">
                                    <?php echo JHtml::_('grid.sort', 'COM_EXTENSIONEXPORTIIB_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tfoot><tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center">
                                        <a class="btn btn-micro hasTooltip" href="<?php echo JRoute::_('index.php?option=com_extensionexportiib&task=listeextensions.export&id=' . $item->extension_id); ?>" title="<?php echo JText::_('COM_EXTENSIONEXPORTIIB_HEADING_EXPORT'); ?>">
                                            <span class="icon-share"></span>
                                        </a>
                                     </td>
                                    <td>
                                        <label for="cb<?php echo $i; ?>">
                                            <span class="bold hasTooltip" title="<?php echo JHtml::tooltipText($item->name, $item->description, 0); ?>"><?php echo $item->name; ?></span>
                                        </label>
                                    </td>
                                    <td class="center">
                                        <?php echo JText::_($item->type); ?>
                                    </td>
                                    <td class="center hidden-phone">
                                        <?php echo $item->client; ?>
                                    </td>
                                    <td class="center">
                                        <?php if ($item->status == 2) : ?>
                                            <a class="btn btn-micro hasTooltip" title="Extension protégée">
                                                <i class="icon-lock"></i>   
                                            </a>
                                        <?php elseif ($item->status == 1) : ?>
                                            <a class="btn btn-micro hasTooltip" title="Extension activée">
                                                <i class="icon-publish"></i>   
                                            </a>
                                        <?php else : ?>
                                            <a class="btn btn-micro hasTooltip" title="Extension désactivée">
                                                <i class="icon-unpublish"></i>   
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <?php echo @$item->version != '' ? $item->version : '&#160;'; ?>
                                    </td>
                                    <td class="center hidden-phone">
                                        <?php echo @$item->creationDate != '' ? $item->creationDate : '&#160;'; ?>
                                    </td>
                                    <td class="center hidden-phone">
                                        <span class="editlinktip hasTooltip" title="<?php echo JHtml::tooltipText(JText::_('COM_EXTENSIONEXPORTIIB_AUTHOR_INFORMATION'), $item->author_info, 0); ?>">
                                            <?php echo @$item->author != '' ? $item->author : '&#160;'; ?>
                                        </span>
                                    </td>
                                    <td class="hidden-phone">
                                        <?php echo $item->extension_id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
    </form>
</div>
