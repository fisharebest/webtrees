<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\IndividualController;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$pid    = Filter::get('pid', WT_REGEX_XREF);
$record = Individual::getInstance($pid, $WT_TREE);
if (!$record) {
	$record = Individual::getInstance(FunctionsDb::findRin($pid), $WT_TREE);
}
$controller = new IndividualController($record);

if ($controller->record && $controller->record->canShow()) {
	if (Filter::get('action') == 'ajax') {
		$controller->ajaxRequest();

		return;
	}
	// Generate the sidebar content *before* we display the page header,
	// as the clippings cart needs to have write access to the session.
	$sidebar_html = $controller->getSideBarContent();

	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This individual has been deleted. You should review the deletion and then %1$s or %2$s it.',
				'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
				'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This individual has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This individual has been edited. You should review the changes and then %1$s or %2$s them.',
				'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
				'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This individual has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	}
	$controller->pageHeader();
} elseif ($controller->record && $controller->record->canShowName()) {
	// Just show the name.
	$controller->pageHeader();
	echo '<h2>', $controller->record->getFullName(), '</h2>';
	echo '<p>', I18N::translate('The details of this individual are private.'), '</p>';

	return;
} else {
	FlashMessages::addMessage(I18N::translate('This individual does not exist or you do not have permission to view it.'), 'danger');
	http_response_code(404);
	$controller->pageHeader();

	return;
}

// If this individual is linked to a user account, show the link
$user_link = '';
if (Auth::isAdmin()) {
	$user = User::findByGenealogyRecord($controller->record);
	if ($user) {
		$user_link =  ' —  <a href="admin_users.php?filter=' . Filter::escapeHtml($user->getUserName()) . '">' . Filter::escapeHtml($user->getUserName()) . '</a>';
	};
}

// What is (was) the age of the individual
$bdate = $controller->record->getBirthDate();
$ddate = $controller->record->getDeathDate();
if ($bdate->isOK() && !$controller->record->isDead()) {
	// If living display age
	$age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, new Date(strtoupper(date('d M Y'))))) . ')';
} elseif ($bdate->isOK() && $ddate->isOK()) {
	// If dead, show age at death
	$age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, $ddate)) . ')';
} else {
	$age = '';
}

// Allow tabs to insert Javascript, etc.
// TODO: there's probably a cleaner way to do this.
foreach ($controller->getTabs() as $tab) {
	echo $tab->getPreLoadContent();
}

$controller->addInlineJavascript('
// If the URL contains a fragment, then activate the corresponding tab.
// Use a prefix on the fragment, to prevent scrolling to the element.
var target = window.location.hash.replace("tab-", "");
var tab = $("#individual-tabs .nav-link[href=\'" + target + "\']");
// If not, then activate the first tab.
if (tab.length === 0) {
	tab = $("#individual-tabs .nav-link:first");
}
tab.tab("show");
');

?>

<h2>
	<?= $controller->record->getFullName() ?><?= $user_link ?>, <?= $controller->record->getLifeSpan() ?> <?= $age ?>
</h2>

<div class="row">
	<div class="col-sm-8">
		<div class="row">
			<!-- Main image -->
			<div class="col-sm-3">
				<?= $controller->record->displayImage() ?>
			</div>

			<!-- Names -->
			<div class="col-sm-9" id="individual-names" role="tablist">
				<?php foreach ($controller->record->getFacts('NAME') as $n => $name_fact): ?>
				<?= $controller->formatNameRecord($n, $name_fact) ?>
				<?php endforeach ?>
				<?php foreach ($controller->record->getFacts('SEX') as $n => $sex_fact): ?>
				<?= $controller->formatSexRecord($sex_fact) ?>
				<?php endforeach ?>

				<?php if ($controller->record->canEdit()): ?>
				<div class="card">
					<div class="card-header" role="tab" id="name-header-add">
						<div class="card-title mb-0">
							<a href="edit_interface.php?action=addname&amp;xref=<?= $controller->record->getXref() ?>&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>">
								<?= I18N::translate('Add a name') ?>
							</a>
						</div>
					</div>
				</div>
				<?php endif ?>
			</div>
		</div>

		<div id="individual-tabs">
			<ul class="nav nav-tabs">
				<?php foreach ($controller->getTabs() as $tab): ?>
					<li class="nav-item">
						<a class="nav-link<?= $tab->isGrayedOut() ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" data-href="<?= $controller->record->getHtmlUrl(), '&amp;action=ajax&amp;module=', $tab->getName() ?>" href="#<?= $tab->getName() ?>">
							<?= $tab->getTitle() ?>
						</a>
					</li>
					<?php endforeach ?>
			</ul>
			<div class="tab-content">
				<?php	foreach ($controller->getTabs() as $tab): ?>
					<div id="<?= $tab->getName() ?>" class="tab-pane fade wt-ajax-load" role="tabpanel"><?php if (!$tab->canLoadAjax()): ?>
						<?= $tab->getTabContent() ?>
					<?php endif ?></div>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<?= $sidebar_html ?>
	</div>
</div>
