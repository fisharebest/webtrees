<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\FamilyController;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'family.php');
require './includes/session.php';

$record = Family::getInstance(Filter::get('famid', WT_REGEX_XREF), $WT_TREE);
$controller = new FamilyController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This family has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This family has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This family has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This family has been edited.  The changes need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	}
} elseif ($controller->record && $controller->record->getTree()->getPreference('SHOW_PRIVATE_RELATIONSHIPS')) {
	$controller->pageHeader();
	// Continue - to display the children/parents/grandparents.
	// We'll check for showing the details again later
} else {
	http_response_code(404);
	$controller->pageHeader();
	echo '<p class="ui-state-error">', I18N::translate('This family does not exist or you do not have permission to view it.'), '</p>';

	return;
}

?>
<div id="family-page">
<h2><?php echo $controller->record->getFullName(); ?></h2>

<table id="family-table" align="center" width="95%">
	<tr valign="top">
		<td valign="top" style="width: <?php echo Theme::theme()->parameter('chart-box-x') + 30; ?>px;"><!--//List of children//-->
			<?php FunctionsCharts::printFamilyChildren($controller->record); ?>
		</td>
		<td> <!--//parents pedigree chart and Family Details//-->
			<table width="100%">
				<tr>
					<td class="subheaders" valign="top"><?php echo I18N::translate('Parents'); ?></td>
					<td class="subheaders" valign="top"><?php echo I18N::translate('Grandparents'); ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						FunctionsCharts::printFamilyParents($controller->record);
						if (Auth::isEditor($controller->record->getTree())) {
							$husb = $controller->record->getHusband();
							if (!$husb) {
								echo '<a href="#" onclick="return add_spouse_to_family(\'', $controller->record->getXref(), '\', \'HUSB\');">', I18N::translate('Add a new father'), '</a><br>';
							}
							$wife = $controller->record->getWife();
							if (!$wife) {
								echo '<a href="#" onclick="return add_spouse_to_family(\'', $controller->record->getXref(), '\', \'WIFE\');">', I18N::translate('Add a new mother'), '</a><br>';
							}
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<span class="subheaders"><?php echo I18N::translate('Family group information'); ?></span>
						<?php
							if ($controller->record->canShow()) {
								echo '<table class="facts_table">';
								$controller->printFamilyFacts();
								echo '</table>';
							} else {
								echo '<p class="ui-state-highlight">', I18N::translate('The details of this family are private.'), '</p>';
							}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div> <!-- Close <div id="family-page"> -->
