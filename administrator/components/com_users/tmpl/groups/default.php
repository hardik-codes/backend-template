<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');

$user        = Factory::getUser();
$listOrder   = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction'));

Text::script('COM_USERS_GROUPS_CONFIRM_DELETE', true);

HTMLHelper::_('script', 'com_users/admin-users-groups.min.js', array('version' => 'auto', 'relative' => true));
?>
<form action="<?php echo Route::_('index.php?option=com_users&view=groups'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<?php if (!empty($this->sidebar)) : ?>
            <div id="j-sidebar-container" class="col-md-2">
				<?php echo $this->sidebar; ?>
            </div>
		<?php endif; ?>
        <div class="<?php if (!empty($this->sidebar)) {echo 'col-md-10'; } else { echo 'col-md-12'; } ?>">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => false))); ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-warning">
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="groupList">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_USERS_GROUPS_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
							<tr>
								<td style="width:1%" class="text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</td>
								<th scope="col">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_USERS_HEADING_GROUP_TITLE', 'a.title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" style="width:10%" class="text-center">
									<?php echo Text::_('COM_USERS_DEBUG_PERMISSIONS'); ?>
								</th>
								<th scope="col" style="width:10%" class="text-center">
									<span class="icon-publish hasTooltip" aria-hidden="true" title="<?php echo Text::_('COM_USERS_COUNT_ENABLED_USERS'); ?>"></span>
									<span class="sr-only"><?php echo Text::_('COM_USERS_COUNT_ENABLED_USERS'); ?></span>
								</th>
								<th scope="col" style="width:10%" class="text-center">
									<span class="icon-unpublish hasTooltip" aria-hidden="true" title="<?php echo Text::_('COM_USERS_COUNT_DISABLED_USERS'); ?>"></span>
									<span class="sr-only"><?php echo Text::_('COM_USERS_COUNT_DISABLED_USERS'); ?></span>
								</th>
								<th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $i => $item) :
							$canCreate = $user->authorise('core.create', 'com_users');
							$canEdit   = $user->authorise('core.edit', 'com_users');

							// If this group is super admin and this user is not super admin, $canEdit is false
							if (!$user->authorise('core.admin') && Access::checkGroup($item->id, 'core.admin'))
							{
								$canEdit = false;
							}
							$canChange = $user->authorise('core.edit.state', 'com_users');
						?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="text-center" data-usercount="<?php echo $item->user_count; ?>">
									<?php if ($canEdit) : ?>
										<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
									<?php endif; ?>
								</td>
								<th scope="row">
									<?php echo LayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level + 1)); ?>
									<?php if ($canEdit) : ?>
									<a href="<?php echo Route::_('index.php?option=com_users&task=group.edit&id=' . $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape(addslashes($item->title)); ?>">
										<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span><?php echo $this->escape($item->title); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->title); ?>
									<?php endif; ?>
								</th>
								<td class="text-center btns">
									<a href="<?php echo Route::_('index.php?option=com_users&view=debuggroup&group_id=' . (int) $item->id); ?>">
										<span class="fa fa-list" aria-hidden="true"></span>
										<span class="sr-only"><?php echo Text::_('COM_USERS_DEBUG_PERMISSIONS'); ?></span>
									</a>
								</td>
								<td class="text-center btns">
									<a class="badge <?php echo $item->count_enabled > 0 ? 'badge-success' : 'badge-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_users&view=users&filter[group_id]=' . (int) $item->id . '&filter[state]=0'); ?>">
										<?php echo $item->count_enabled; ?></a>
								</td>
								<td class="text-center btns">
									<a class="badge <?php echo $item->count_disabled > 0 ? 'badge-danger' : 'badge-secondary'; ?>" href="<?php echo Route::_('index.php?option=com_users&view=users&filter[group_id]=' . (int) $item->id . '&filter[state]=1'); ?>">
										<?php echo $item->count_disabled; ?></a>
								</td>
								<td class="d-none d-md-table-cell">
									<?php echo (int) $item->id; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<?php // load the pagination. ?>
					<?php echo $this->pagination->getListFooter(); ?>

				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
