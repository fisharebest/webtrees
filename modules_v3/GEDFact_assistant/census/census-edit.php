<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

use Fisharebest\Webtrees\Census\CensusInterface;
use Fisharebest\Webtrees\Controller\SimpleController;
use Fisharebest\Webtrees\Module\CensusAssistantModule;

/** @var SimpleController $controller */
global $controller;

/** @var Tree $WT_TREE */
global $WT_TREE;

$xref   = Filter::get('xref', WT_REGEX_XREF);
$census = Filter::get('census');
$head   = Individual::getInstance($xref, $WT_TREE);
check_record_access($head);

$controller->restrictAccess(class_exists($census));

/** @var CensusInterface */
$census = new $census;
$controller->restrictAccess($census instanceof CensusInterface);
$date = new Date($census->censusDate());
$year = $date->minimumDate()->format('%Y');

$headImg = '<i class="icon-button_head"></i>';

$controller
	->setPageTitle(I18N::translate('Create a shared note using the census assistant'))
	->addInlineJavascript(
		'jQuery("head").append(\'<link rel="stylesheet" href="' . WT_STATIC_URL . WT_MODULES_DIR . 'GEDFact_assistant/census/style.css" type="text/css">\');' .
		'jQuery("#tblSample").on("click", ".icon-remove", function() { jQuery(this).closest("tr").remove(); });'
	)
	->pageHeader();

?>

<h2>
	<?php echo $controller->getPageTitle(); ?>
</h2>

<form method="post" action="edit_interface.php" onsubmit="updateCensusText();">
	<input type="hidden" name="action" value="addnoteaction_assisted">
	<input id="pid_array" type="hidden" name="pid_array" value="none">
	<input type="hidden" name="NOTE" id="NOTE">
	<?php echo Filter::getCsrf(); ?>

	<h3>
		<?php echo I18N::translate('Click %s to choose individual as head of family.', $headImg); ?>
	</h3>

	<div class="census-assistant-header optionbox">
		<dl>
			<dt class="label"><?php echo I18N::translate('Head of household'); ?></dt>
			<dd class="field"><?php echo $head->getFullName(); ?></dd>
		</dl>
		<?php echo $head->formatFirstMajorFact(WT_EVENTS_BIRT, 2); ?>
		<?php echo $head->formatFirstMajorFact(WT_EVENTS_DEAT, 2); ?>
	</div>

	<h3>
		<?php echo I18N::translate('Add individuals'); ?>
	</h3>

	<div class="census-assistant-search optionbox">
		<table class="table">
			<tr>
				<td>
					<table class="table fact_table">
						<?php
						foreach ($head->getChildFamilies() as $family) {
							CensusAssistantModule::censusNavigatorFamily($census, $family, $head);
						}

						foreach ($head->getChildStepFamilies() as $family) {
							CensusAssistantModule::censusNavigatorFamily($census, $family, $head);
						}

						foreach ($head->getSpouseFamilies() as $family) {
							CensusAssistantModule::censusNavigatorFamily($census, $family, $head);
						}
						?>
						<tr>
							<td class="optionbox">
							</td>
							<td class="facts_value" colspan="2">
								<input id=personid type="text" size="20">
								<button type="button" onclick="findindi()">
									<label for="personid"><?php echo I18N::translate('Search'); ?></label>
								</button>
							</td>
						</tr>
						<tr>
							<td class="optionbox">
							</td>
							<td class="facts_value" colspan="2">
								<button type="button" onclick="return appendCensusRow('<?php echo Filter::escapeHtml(CensusAssistantModule::censusTableEmptyRow($census)); ?>');">
									<?php echo I18N::translate('Add/insert a blank row'); ?>
								</button>
							</td>
						</tr>
					</table>
			</tr>
		</table>
	</div>

	<h3>
		<?php echo I18N::translate('Edit the details'); ?>
	</h3>

	<div class="census-assistant-input optionbox">
		<table class="table">
			<tbody>
				<tr>
					<th>
						<label for="Titl">
							<?php echo I18N::translate('Title'); ?>
						</label>
					</th>
					<td>
						<input id="Titl" type="text" value="<?php echo $year, ' ', $census->censusPlace(), ' - ', I18N::translate('Census transcript'), ' - ', strip_tags($head->getFullName()), ' - ', I18N::translate('Household'); ?>">
					</td>
				</tr>
				<tr>
					<th>
						<label for="citation">
							<?php echo GedcomTag::getLabel('PAGE'); ?>
						</label>
					</th>
					<td>
						<input id="citation" type="text">
					</td>
				</tr>
				<tr>
					<th>
						<label for="locality">
							<?php echo I18N::translate('Place'); ?>
						</label>
					</th>
					<td>
						<input id="locality" type="text">
					</td>
				</tr>
			</tbody>
		</table>

		<table id="tblSample" class="table table-census-inputs">
			<thead>
			<?php echo CensusAssistantModule::censusTableHeader($census); ?>
			</thead>
			<tbody>
			<?php echo CensusAssistantModule::censusTableRow($census, $head, $head); ?>
			</tbody>
		</table>

		<table class="table">
			<tbody>
				<tr>
					<th>
						<label for="notes">
							<?php echo I18N::translate('Notes'); ?>
						</label>
					</th>
					<td>
						<input id="notes" type="text">
					</td>
				</tr>
			</tbody>
		</table>

	</div>

	<div>
		<button type="submit">
			<?php echo I18N::translate('save'); ?>
		</button>
	</div>
</form>

<script>
	function findindi() {
		var findInput = document.getElementById('personid');
		var txt = findInput.value;
		if (txt === "") {
			alert("<?php echo I18N::translate('You must enter a name'); ?>");
		} else {
			var win02 = window.open(
				"module.php?mod=GEDFact_assistant&mod_action=census_find&callback=paste_id&census=<?php echo Filter::escapeJs(get_class($census)); ?>&action=filter&filter=" + txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, height=400, width=450 ");
			if (window.focus) {
				win02.focus();
			}
		}
	}

	/* Add an HTML row to the table */
	function appendCensusRow(row) {
		jQuery("#tblSample tbody").append(row);

		return false;
	}

	/* Update the census text from the various input fields */
	function updateCensusText() {
		var html        = "";
		var title       = jQuery("#Titl").val();
		var citation    = jQuery("#citation").val();
		var locality    = jQuery("#locality").val();
		var notes       = jQuery("#notes").val();
		var table       = jQuery("#tblSample");
		var max_col_ndx = table.find("thead th").length - 1;
		var line        = "";

		if (title !== "") {
			html += title + "\n";
		}
		if (citation !== "") {
			html += citation + "\n";
		}
		if (locality !== "") {
			html += locality + "\n";
		}

		html += "\n.start_formatted_area.\n";

		table.find("thead th").each(function (n, el) {
			if (n === 0 || n === max_col_ndx) { // Skip prefix & suffix cells
			 return true;
			 }
			line += "|.b." + jQuery(el).html();
		});
		html += line.substr(1) + "\n";

		table.find("tbody tr").each(function(n, el) {
			line = "";
			jQuery("input", jQuery(el)).each(function(n, el) {
				line += "|" + jQuery(el).val();
			});
			html += line.substr(1) + "\n";
		});

		html += ".end_formatted_area.\n";

		if (notes !== "") {
			html += "\n" + notes + "\n";
		}

		jQuery("#NOTE").val(html);

		var pid_array = '';
		table.find("tbody td:first-child").each(function(n, el) {
			if (n > 0) {
				pid_array += ',';
			}
			pid_array += jQuery(el).html();
		});
		jQuery("#pid_array").val(pid_array);

		return false;
	}
</script>
