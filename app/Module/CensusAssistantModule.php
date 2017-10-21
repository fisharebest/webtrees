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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Census\Census;
use Fisharebest\Webtrees\Census\CensusInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\View;

/**
 * Class CensusAssistantModule
 */
class CensusAssistantModule extends AbstractModule {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Census assistant');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Census assistant” module */
			I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		global $WT_TREE;

		switch ($mod_action) {
		case 'census-header':
			header('Content-Type: text/html; charset=utf8');
			$census = Filter::get('census');
			echo $this->censusTableHeader(new $census);
			break;

		case 'census-individual':
			header('Content-Type: text/html; charset=utf8');
			$census     = Filter::get('census');
			$individual = Individual::getInstance(Filter::get('xref'), $WT_TREE);
			$head       = Individual::getInstance(Filter::get('head'), $WT_TREE);
			echo $this->censusTableRow(new $census, $individual, $head);
			break;

		case 'media_find':
			self::mediaFind();
			break;
		case 'media_query_3a':
			self::mediaQuery();
			break;
		default:
			http_response_code(404);
		}
	}

	/**
	 * @param Individual $individual
	 */
	public function createCensusAssistant(Individual $individual) {
		return View::make('modules/census-assistant', [
			'individual' => $individual,
		]);
	}

	/**
	 * @param Individual $individual
	 * @param string     $fact_id
	 * @param string     $newged
	 * @param bool       $keep_chan
	 *
	 * @return string
	 */
	public function updateCensusAssistant(Individual $individual, $fact_id, $newged, $keep_chan) {
		$ca_title       = Filter::post('ca_title');
		$ca_place       = Filter::post('ca_place');
		$ca_citation    = Filter::post('ca_citation');
		$ca_individuals = Filter::postArray('ca_individuals');
		$ca_notes       = Filter::post('ca_notes');
		$ca_census      = Filter::post('ca_census', 'Fisharebest\\\\Webtrees\\\\Census\\\\CensusOf[A-Za-z0-9]+');

		if ($ca_census !== '' && !empty($ca_individuals)) {
			$census = new $ca_census;

			$note_text   = $this->createNoteText($census, $ca_title, $ca_place, $ca_citation, $ca_individuals, $ca_notes);
			$note_gedcom = '0 @new@ NOTE ' . str_replace("\n", "\n1 CONT ", $note_text);
			$note        = $individual->getTree()->createRecord($note_gedcom);

			$newged .= "\n2 NOTE @" . $note->getXref() . '@';

			// Add the census fact to the rest of the household
			foreach (array_keys($ca_individuals) as $xref) {
				if ($xref !== $individual->getXref()) {
					Individual::getInstance($xref, $individual->getTree())
						->updateFact($fact_id, $newged, !$keep_chan);
				}
			}
		}

		return $newged;
	}

	/**
	 * @param CensusInterface $census
	 * @param string          $ca_title
	 * @param string          $ca_place
	 * @param string          $ca_citation
	 * @param string[][]      $ca_individuals
	 * @param string          $ca_notes
	 *
	 * @return string
	 */
	private function createNoteText(CensusInterface $census, $ca_title, $ca_place, $ca_citation, $ca_individuals, $ca_notes) {
		$text = $ca_title . "\n" . $ca_citation . "\n" . $ca_place . "\n\n";

		foreach ($census->columns() as $n => $column) {
			if ($n === 0) {
				$text .= "\n";
			} else {
				$text .= ' | ';
			}
			$text .= $column->abbreviation();
		}

		foreach ($census->columns() as $n => $column) {
			if ($n === 0) {
				$text .= "\n";
			} else {
				$text .= ' | ';
			}
			$text .= '-----';
		}

		foreach ($ca_individuals as $xref => $columns) {
			$text .= "\n" . implode(' | ', $columns);
		}

		return $text . "\n\n" . $ca_notes;
	}

	/**
	 * Find a media object.
	 */
	private static function mediaFind() {
		global $WT_TREE;

		$controller = new SimpleController;
		$filter     = Filter::get('filter');
		$multiple   = Filter::getBool('multiple');

		$controller
			->setPageTitle(I18N::translate('Find an individual'))
			->pageHeader();

		?>
		<script>
			function pasterow(id, name, gend, yob, age, bpl) {
				window.opener.opener.insertRowToTable(id, name, '', gend, '', yob, age, 'Y', '', bpl);
			}

			function pasteid(id, name, thumb) {
				if (thumb) {
					window.opener.paste_id(id, name, thumb);
					<?php if (!$multiple) {
					echo 'window.close();';
				} ?>
				} else {
					// GEDFact_assistant ========================
					if (window.opener.document.getElementById('addlinkQueue')) {
						window.opener.insertRowToTable(id, name);
					}
					window.opener.paste_id(id);
					if (window.opener.pastename) {
						window.opener.pastename(name);
					}
					<?php if (!$multiple) {
					echo 'window.close();';
				} ?>
				}
			}

			function checknames(frm) {
				var button = '';
				if (document.forms[0].subclick) {
					button = document.forms[0].subclick.value;
				}
				if (frm.filter.value.length < 2 && button !== 'all') {
					alert('<?= I18N::translate('Please enter more than one character.') ?>');
					frm.filter.focus();
					return false;
				}
				if (button === 'all') {
					frm.filter.value = '';
				}
				return true;
			}
		</script>

		<?php
		echo '<div>';
		echo '<table class="list_table width90" border="0">';
		echo '<tr><td style="padding: 10px;" class="width90">'; // start column for find text header
		echo $controller->getPageTitle();
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '<br>';
		echo '<button onclick="window.close();">', I18N::translate('close'), '</button>';
		echo '<br>';

		$filter       = trim($filter);
		$filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));
		echo '<table class="tabs_table width90"><tr>';
		$myindilist = FunctionsDb::searchIndividualNames($filter_array, [$WT_TREE]);
		if ($myindilist) {
			echo '<td class="list_value_wrap"><ul>';
			usort($myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			foreach ($myindilist as $indi) {
				$nam = Html::escape($indi->getFullName());
				echo "<li><a href=\"#\" onclick=\"pasterow(
					'" . $indi->getXref() . "' ,
					'" . $nam . "' ,
					'" . $indi->getSex() . "' ,
					'" . $indi->getBirthYear() . "' ,
					'" . (1901 - $indi->getBirthYear()) . "' ,
					'" . $indi->getBirthPlace() . "'); return false;\">
					<b>" . $indi->getFullName() . '</b>&nbsp;&nbsp;&nbsp;';

				$born = I18N::translate('Birth');
				echo '</span><br><span class="list_item">', $born, ' ', $indi->getBirthYear(), '&nbsp;&nbsp;&nbsp;', $indi->getBirthPlace(), '</span></a></li>';
				echo '<hr>';
			}
			echo '</ul></td></tr><tr><td class="list_label">', I18N::translate('Total individuals: %s', count($myindilist)), '</tr></td>';
		} else {
			echo '<td class="list_value_wrap">';
			echo I18N::translate('No results found.');
			echo '</td></tr>';
		}
		echo '</table>';
		echo '</div>';
	}

	/**
	 * Search for a media object.
	 */
	private static function mediaQuery() {
		global $WT_TREE;

		$iid2 = Filter::get('iid', WT_REGEX_XREF);

		$controller = new SimpleController;
		$controller
			->setPageTitle(I18N::translate('Link to an existing media object'))
			->pageHeader();

		$record = GedcomRecord::getInstance($iid2, $WT_TREE);
		if ($record) {
			$headjs = '';
			if ($record instanceof Family) {
				if ($record->getHusband()) {
					$headjs = $record->getHusband()->getXref();
				} elseif ($record->getWife()) {
					$headjs = $record->getWife()->getXref();
				}
			}
			?>
			<script>
				function insertId() {
					if (window.opener.document.getElementById('addlinkQueue')) {
						// alert('Please move this alert window and examine the contents of the pop-up window, then click OK')
						window.opener.insertRowToTable('<?= $record->getXref() ?>', '<?= htmlspecialchars($record->getFullName()) ?>', '<?= $headjs ?>');
						window.close();
					}
				}
			</script>
			<?php
		} else {
			?>
			<script>
				function insertId() {
					window.opener.alert('<?= $iid2 ?> - <?= I18N::translate('Not a valid individual, family, or source ID') ?>');
					window.close();
				}
			</script>
			<?php
		}
		?>
		<script>window.onLoad = insertId();</script>
		<?php
	}

	/**
	 * Generate an HTML row of data for the census header
	 * Add prefix cell (store XREF and drag/drop)
	 * Add suffix cell (delete button)
	 *
	 * @param CensusInterface $census
	 *
	 * @return string
	 */
	public static function censusTableHeader(CensusInterface $census) {
		$html = '';
		foreach ($census->columns() as $column) {
			$html .= '<th class="wt-census-assistant-field" title="' . $column->title() . '">' . $column->abbreviation() . '</th>';
		}

		return '<tr class="wt-census-assistant-row"><th hidden></th>' . $html . '<th></th></tr>';
	}

	/**
	 * Generate an HTML row of data for the census
	 * Add prefix cell (store XREF and drag/drop)
	 * Add suffix cell (delete button)
	 *
	 * @param CensusInterface $census
	 *
	 * @return string
	 */
	public static function censusTableEmptyRow(CensusInterface $census) {
		return '<tr class="wt-census-assistant-row"><td hidden></td>' . str_repeat('<td class="wt-census-assistant-field"><input type="text" class="form-control wt-census-assistant-form-control"></td>', count($census->columns())) . '<td><a class="icon-remove" href="#" title="' . I18N::translate('Remove') . '"></a></td></tr>';
	}

	/**
	 * Generate an HTML row of data for the census
	 * Add prefix cell (store XREF and drag/drop)
	 * Add suffix cell (delete button)
	 *
	 * @param CensusInterface $census
	 * @param Individual      $individual
	 * @param Individual      $head
	 *
	 * @return string
	 */
	public static function censusTableRow(CensusInterface $census, Individual $individual, Individual $head) {
		$html = '';
		foreach ($census->columns() as $column) {
			$html .= '<td class="wt-census-assistant-field"><input class="form-control wt-census-assistant-form-control" type="text" value="' . $column->generate($individual, $head) . '" name="ca_individuals[' . $individual->getXref() . '][]"></td>';
		}

		return '<tr class="wt-census-assistant-row"><td class="wt-census-assistant-field" hidden>' . $individual->getXref() . '</td>' . $html . '<td class="wt-census-assistant-field"><a class="icon-remove" href="#" title="' . I18N::translate('Remove') . '"></a></td></tr>';
	}
}
