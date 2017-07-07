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
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Soundex;

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
        ?>

        <div id="census-assistant-link" hidden>
            <a href="#">
                <?= I18N::translate('Create a shared note using the census assistant') ?>
            </a>
        </div>

        <div id="census-assistant" hidden>
            <input type="hidden" name="ca_census" id="ca-census">
            <div class="form-group">
                <div class="input-group">
                    <label for="census-assistant-title" class="input-group-addon">
                        <?= I18N::translate('Title') ?>
                    </label>
                    <input class="form-control" id="ca-title" name="ca_title" value="">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <div class="input-group">
                        <label for="census-assistant-citation" class="input-group-addon">
                            <?= I18N::translate('Citation') ?>
                        </label>
                        <input class="form-control" id="census-assistant-citation" name="ca_citation">
                    </div>
                </div>

                <div class="form-group col-sm-6">
                    <div class="input-group">
                        <label for="census-assistant-place" class="input-group-addon">
                            <?= I18N::translate('Place') ?>
                        </label>
                        <input class="form-control" id="census-assistant-place" name="ca_place">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><?= I18N::translate('Individuals') ?></span>
                    <?= FunctionsEdit::formControlIndividual($individual, ['id' => 'census-assistant-individual', 'style' => 'width:100%']) ?>
                    <span class="input-group-btn">
						<button type="button" class="btn btn-primary" id="census-assistant-add">
							<?= FontAwesome::semanticIcon('add', I18N::translate('Add')) ?>
						</button>
					</span>
                    <span class="input-group-btn">
						<button type="button" class="btn btn-primary" id="census-assistant-head"
                                title="<?= I18N::translate('Head of household') ?>">
							<?= FontAwesome::semanticIcon('individual', I18N::translate('Head of household')) ?>
						</button>
					</span>
                </div>
            </div>

            <table class="table table-bordered table-small table-responsive wt-census-assistant-table"
                   id="census-assistant-table">
                <thead class="wt-census-assistant-header"></thead>
                <tbody class="wt-census-assistant-body"></tbody>
            </table>

            <div class="form-group">
                <div class="input-group">
                    <label for="census-assistant-notes" class="input-group-addon">
                        <?= I18N::translate('Notes') ?>
                    </label>
                    <input class="form-control" id="census-assistant-notes" name="ca_notes">
                </div>
            </div>
        </div>

        <script>
            // When a census date/place is selected, activate the census-assistant
            function censusAssistantSelect () {
                var censusAssistantLink = document.querySelector('#census-assistant-link');
                var censusAssistant     = document.querySelector('#census-assistant');
                var censusOption        = this.options[this.selectedIndex];
                var census              = censusOption.dataset.census;
                var censusPlace         = censusOption.dataset.place;
                var censusYear          = censusOption.value.substr(-4);

                if (censusOption.value !== '') {
                    censusAssistantLink.removeAttribute('hidden');
                } else {
                    censusAssistantLink.setAttribute('hidden', '');
                }

                censusAssistant.setAttribute('hidden', '');
                document.querySelector('#ca-census').value = census;
                document.querySelector('#ca-title').value  = censusYear + ' ' + censusPlace + ' - <?= I18N::translate('Census transcript') ?> - <?= strip_tags($individual->getFullName()) ?> - <?= I18N::translate('Household') ?>';

                fetch('module.php?mod=GEDFact_assistant&mod_action=census-header&census=' + census)
                    .then(function (response) {
                        return response.text();
                    })
                    .then(function (text) {
                        document.querySelector('#census-assistant-table thead').innerHTML = text;
                        document.querySelector('#census-assistant-table tbody').innerHTML = '';
                    });
            }

            // When the census assistant is activated, show the input fields
            function censusAssistantLink () {
                document.querySelector('#census-selector').setAttribute('hidden', '');
                this.setAttribute('hidden', '');
                document.getElementById('census-assistant').removeAttribute('hidden');
                // Set the current individual as the head of household.
                censusAssistantHead();

                return false;
            }

            // Add the currently selected individual to the census
            function censusAssistantAdd () {
                var censusSelector = document.querySelector('#census-selector');
                var census         = censusSelector.options[censusSelector.selectedIndex].dataset.census;
                var indi_selector  = document.querySelector('#census-assistant-individual');
                var xref           = indi_selector.options[indi_selector.selectedIndex].value;
                var headTd         = document.querySelector('#census-assistant-table td');
                var head           = headTd === null ? xref : headTd.innerHTML;

                fetch('module.php?mod=GEDFact_assistant&mod_action=census-individual&census=' + census + '&xref=' + xref + '&head=' + head, {credentials: 'same-origin'})
                    .then(function (response) {
                        return response.text();
                    })
                    .then(function (text) {
                        document.querySelector('#census-assistant-table tbody').innerHTML += text;
                    });

                return false;
            }

            // Set the currently selected individual as the head of household
            function censusAssistantHead () {
                var censusSelector = document.querySelector('#census-selector');
                var census         = censusSelector.options[censusSelector.selectedIndex].dataset.census;
                var indi_selector  = document.querySelector('#census-assistant-individual');
                var xref           = indi_selector.options[indi_selector.selectedIndex].value;

                fetch('module.php?mod=GEDFact_assistant&mod_action=census-individual&census=' + census + '&xref=' + xref + '&head=' + xref, {credentials: 'same-origin'})
                    .then(function (response) {
                        return response.text();
                    })
                    .then(function (text) {
                        document.querySelector('#census-assistant-table tbody').innerHTML = text;
                    });

                return false;
            }

            document.querySelector('#census-selector').addEventListener('change', censusAssistantSelect);
            document.querySelector('#census-assistant-link').addEventListener('click', censusAssistantLink);
            document.querySelector('#census-assistant-add').addEventListener('click', censusAssistantAdd);
            document.querySelector('#census-assistant-head').addEventListener('click', censusAssistantHead);
        </script>
        <?php
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
        $text = $ca_title . "\n" . $ca_citation . "\n" . $ca_place . "\n\n.start_formatted_area.\n\n";

        foreach ($census->columns() as $n => $column) {
            if ($n > 0) {
                $text .= '|';
            }
            $text .= '.b.' . $column->abbreviation();
        }

        foreach ($ca_individuals as $xref => $columns) {
            $text .= "\n" . implode('|', $columns);
        }

        return $text . "\n.end_formatted_area.\n\n" . $ca_notes;
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
            function pasterow (id, name, gend, yob, age, bpl) {
                window.opener.opener.insertRowToTable(id, name, '', gend, '', yob, age, 'Y', '', bpl);
            }

            function pasteid (id, name, thumb) {
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

            function checknames (frm) {
                if (document.forms[0].subclick) {
                    button = document.forms[0].subclick.value;
                } else {
                    button = '';
                }
                if (frm.filter.value.length < 2 && button !== 'all') {
                    alert('<?= I18N::translate('Please enter more than one character.') ?>');
                    frm.filter.focus();
                    return false;
                }
                if (button == 'all') {
                    frm.filter.value = '';
                }
                return true;
            }
        </script>

        <?php
        echo '<div>';
        echo '<table class="list_table width90" border="0">';
        echo '<tr><td style="padding: 10px;" class="facts_label03 width90">'; // start column for find text header
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
                $nam = Filter::escapeHtml($indi->getFullName());
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
                function insertId () {
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
                function insertId () {
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
     * Convert custom markup into HTML
     *
     * @param Note $note
     *
     * @return string
     */
    public static function formatCensusNote(Note $note) {
        if (preg_match('/(.*)((?:\n.*)*)\n\.start_formatted_area\.\n(.+)\n(.+(?:\n.+)*)\n.end_formatted_area\.((?:\n.*)*)/', $note->getNote(), $match)) {
            // This looks like a census-assistant shared note
            $title     = Filter::escapeHtml($match[1]);
            $preamble  = Filter::escapeHtml($match[2]);
            $header    = Filter::escapeHtml($match[3]);
            $data      = Filter::escapeHtml($match[4]);
            $postamble = Filter::escapeHtml($match[5]);

            // Get the column headers for the census to which this note refers
            // requires the fact place & date to match the specific census
            // censusPlace() (Soundex match) and censusDate() functions
            $fmt_headers = [];
            /** @var GedcomRecord[] $linkedRecords */
            $linkedRecords = array_merge($note->linkedIndividuals('NOTE'), $note->linkedFamilies('NOTE'));
            $firstRecord   = array_shift($linkedRecords);
            if ($firstRecord) {
                $countryCode = '';
                $date        = '';
                foreach ($firstRecord->getFacts('CENS') as $fact) {
                    if (trim($fact->getAttribute('NOTE'), '@') === $note->getXref()) {
                        $date        = $fact->getAttribute('DATE');
                        $place       = explode(',', strip_tags($fact->getPlace()->getFullName()));
                        $countryCode = Soundex::daitchMokotoff(array_pop($place));
                        break;
                    }
                }

                foreach (Census::allCensusPlaces() as $censusPlace) {
                    if (Soundex::compare($countryCode, Soundex::daitchMokotoff($censusPlace->censusPlace()))) {
                        foreach ($censusPlace->allCensusDates() as $census) {
                            if ($census->censusDate() == $date) {
                                foreach ($census->columns() as $column) {
                                    $abbrev = $column->abbreviation();
                                    if ($abbrev) {
                                        $description          = $column->title() ? $column->title() : I18N::translate('Description unavailable');
                                        $fmt_headers[$abbrev] = '<span title="' . $description . '">' . $abbrev . '</span>';
                                    }
                                }
                                break 2;
                            }
                        }
                    }
                }
            }
            // Substitute header labels and format as HTML
            $thead = '<tr><th>' . strtr(str_replace('|', '</th><th>', $header), $fmt_headers) . '</th></tr>';
            $thead = str_replace('.b.', '', $thead);

            // Format data as HTML
            $tbody = '';
            foreach (explode("\n", $data) as $row) {
                $tbody .= '<tr>';
                foreach (explode('|', $row) as $column) {
                    $tbody .= '<td>' . $column . '</td>';
                }
                $tbody .= '</tr>';
            }

            return
                $title . "\n" . // The newline allows the framework to expand the details and turn the first line into a link
                '<div class="markdown">' .
                '<p>' . $preamble . '</p>' .
                '<table>' .
                '<thead>' . $thead . '</thead>' .
                '<tbody>' . $tbody . '</tbody>' .
                '</table>' .
                '<p>' . $postamble . '</p>' .
                '</div>';
        } else {
            // Not a census-assistant shared note - apply default formatting
            return Filter::formatText($note->getNote(), $note->getTree());
        }
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
