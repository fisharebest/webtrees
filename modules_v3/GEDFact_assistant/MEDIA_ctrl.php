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

/**
 * Defined in edit_interface.php
 *
 * @global Individual $person
 */
global $person;
/**
 * Defined in edit_interface.php
 *
 * @global Controller\PageController $controller
 */
global $controller;

?>
<style>
	/* Outer border around nav elements */
	.outer_nav {
		border: 3px #808080 outset;
	}

	#media-links table.facts_table {
		width: 270px;
	}

	/* top Search box */
	input[type='text'] {
		background: #fff;
		color: #000;
		border: 1px solid #000;
		width: 120px;
	}

	/* "Head" button images */
	.headimg {
		margin-top: -4px;
		border: 0;
	}

	/* Prevents clickable td for Search <td> */
	td #srch a {
		display: inline;
	}
</style>
<div id="media-links">
	<table class="facts_table center">
		<tr>
			<td class="topbottombar">
				<b><?= $controller->getPageTitle() ?></b>
			</td>
		</tr>
		<tr>
			<td>
				<table class="outer_nav">
					<tr>
						<th class="descriptionbox"><?= I18N::translate('Find an individual') ?></th>
					</tr>
					<tr>
						<td id="srch" class="optionbox center">
							<script>
								function findindi() {
									var findInput = document.getElementById('personid');
									var txt = findInput.value;
									if (txt === "") {
										alert("<?= I18N::translate('You must enter a name') ?>");
									} else {
										window.open("module.php?mod=GEDFact_assistant&mod_action=media_find&callback=paste_id&action=filter&type=indi&multiple=&filter=" + txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, height=600, width=450 ").focus();
									}
								}
							</script>
							<input id="personid" type="text" value="">
							<a type="submit" onclick="findindi();">
								<?= I18N::translate('Search') ?>
							</a>
						</td>
					</tr>
					<tr>
						<td>
							<table width="100%" class="fact_table" cellspacing="0" border="0">
								<tr>
									<td colspan=3 class="descriptionbox wrap">
										<i class="headimg vmiddle icon-button_head"></i>
										<?= I18N::translate('View this family') ?>
									</td>
								</tr>
								<?php
								foreach ($person->getChildFamilies() as $family) {
									echo '<tr><th colspan="2">', $family->getFullName(), '</td></tr>';
									print_navigator_family($family, $person);
								}

								foreach ($person->getChildStepFamilies() as $family) {
									echo '<tr><th colspan="2">', $family->getFullName(), '</td></tr>';
									print_navigator_family($family, $person);
								}

								foreach ($person->getSpouseFamilies() as $family) {
									echo '<tr><th colspan="2">', $family->getFullName(), '</td></tr>';
									print_navigator_family($family, $person);
								}
								?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<?php

/**
 * Display family members with clickable links
 *
 * @param Family     $family
 * @param Individual $individual
 */
function print_navigator_family(Family $family, Individual $individual) {
	foreach ($family->getSpouses() as $spouse) {
		?>
		<tr class="fact_value">
			<td class="facts_value" >
				<a href="#" onclick="opener.insertRowToTable('<?= $spouse->getXref() ?>', '<?= Filter::escapeJs($spouse->getFullName()) ?>', '', '', '', '', '', '', '', ''); return false;">
					<?= $spouse === $individual ? '<b>' : '' ?>
					<?= $spouse->getFullName() ?> <?= $spouse->getLifeSpan() ?>
					<?= $spouse === $individual ? '</b>' : '' ?>
				</a>
			</td>
			<td class="facts_value">
				<?php if ($individual !== $spouse): ?>
					<a href="edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=<?= $spouse->getXref() ?>&amp;gedcom=<?= $spouse->getTree()->getNameUrl() ?>">
						<i class="headimg vmiddle icon-button_head"></i>
					</a>
				<?php endif ?>
			</td>
		<tr>
	<?php
	}

	foreach ($family->getChildren() as $child) {
		?>
		<tr>
			<td class="facts_value">
				<a href="#" onclick="opener.insertRowToTable('<?= $child->getXref() ?>', '<?= Filter::escapeJs($child->getFullName()) ?>', '', '', '', '', '', '', '', ''); return false;">
					<?= $child === $individual ? '<b>' : '' ?>
					<?= $child->getFullName() ?> <?= $child->getLifeSpan() ?>
				<?= $child === $individual ? '</b>' : '' ?>
				</a>
			</td>
			<td class="facts_value" >
			<?php if ($individual !== $child): ?>
					<a href="edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=<?= $child->getXref() ?>&amp;gedcom=<?= $child->getTree()->getNameUrl() ?>">
						<i class="headimg vmiddle icon-button_head"></i>
					</a>
				<?php endif ?>
			</td>
		</tr>
	<?php
	}
}
