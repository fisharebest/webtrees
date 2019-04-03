<?php
/**
 * webtrees: online genealogy
 * Copy§right (C) 2017 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Census\Census;
use Fisharebest\Webtrees\Config;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeAdop;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeName;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeQuay;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeRela;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeStat;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeTemp;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class FunctionsEdit - common functions for editing
 */
class FunctionsEdit
{
    /** @var string[] - a list of GEDCOM tags in the edit form. */
    private static $tags = [];

    /**
     * Function edit_language_checkboxes
     *
     * @param string $parameter_name
     * @param array  $accepted_languages
     *
     * @return string
     */
    public static function editLanguageCheckboxes($parameter_name, $accepted_languages): string
    {
        $html = '';
        foreach (I18N::activeLocales() as $locale) {
            $html .= '<div class="form-check">';
            $html .= '<label title="' . $locale->languageTag() . '">';
            $html .= '<input type="checkbox" name="' . $parameter_name . '[]" value="' . $locale->languageTag() . '"';
            $html .= in_array($locale->languageTag(), $accepted_languages) ? ' checked>' : '>';
            $html .= $locale->endonym();
            $html .= '</label>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * A list of access levels (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsAccessLevels(): array
    {
        return [
            Auth::PRIV_PRIVATE => I18N::translate('Show to visitors'),
            Auth::PRIV_USER    => I18N::translate('Show to members'),
            Auth::PRIV_NONE    => I18N::translate('Show to managers'),
            Auth::PRIV_HIDE    => I18N::translate('Hide from everyone'),
        ];
    }

    /**
     * A list of active languages (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsActiveLanguages(): array
    {
        $languages = [];
        foreach (I18N::activeLocales() as $locale) {
            $languages[$locale->languageTag()] = $locale->endonym();
        }

        return $languages;
    }

    /**
     * A list of calendar conversions (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsCalendarConversions(): array
    {
        return ['none' => I18N::translate('No calendar conversion')] + Date::calendarNames();
    }

    /**
     * A list of contact methods (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsContactMethods(): array
    {
        return [
            'messaging'  => I18N::translate('Internal messaging'),
            'messaging2' => I18N::translate('Internal messaging with emails'),
            'messaging3' => I18N::translate('webtrees sends emails with no storage'),
            'mailto'     => I18N::translate('Mailto link'),
            'none'       => I18N::translate('No contact'),
        ];
    }

    /**
     * A list of hide/show options (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsHideShow(): array
    {
        return [
            '0' => I18N::translate('no'),
            '1' => I18N::translate('yes'),
        ];
    }

    /**
     * A list of installed languages (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsInstalledLanguages(): array
    {
        $languages = [];
        foreach (I18N::installedLocales() as $locale) {
            $languages[$locale->languageTag()] = $locale->endonym();
        }

        return $languages;
    }

    /**
     * A list of integers (e.g. for an edit control).
     *
     * @param int[] $integers
     *
     * @return string[]
     */
    public static function numericOptions($integers): array
    {
        $array = [];
        foreach ($integers as $integer) {
            if ($integer === -1) {
                $array[$integer] = I18N::translate('All');
            } else {
                $array[$integer] = I18N::number($integer);
            }
        }

        return $array;
    }

    /**
     * A list of no/yes options (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsNoYes(): array
    {
        return [
            '0' => I18N::translate('no'),
            '1' => I18N::translate('yes'),
        ];
    }

    /**
     * A list of GEDCOM relationships (e.g. for an edit control).
     *
     * @param string $relationship
     *
     * @return string[]
     */
    public static function optionsRelationships($relationship): array
    {
        $relationships = GedcomCodeRela::getValues();
        // The user is allowed to specify values that aren't in the list.
        if (!array_key_exists($relationship, $relationships)) {
            $relationships[$relationship] = I18N::translate($relationship);
        }

        return $relationships;
    }

    /**
     * A list of GEDCOM restrictions for inline data.
     *
     * @param bool $include_empty
     *
     * @return string[]
     */
    public static function optionsRestrictions($include_empty): array
    {
        $options = [
            'none'         => I18N::translate('Show to visitors'),
            'privacy'      => I18N::translate('Show to members'),
            'confidential' => I18N::translate('Show to managers'),
            'locked'       => I18N::translate('Only managers can edit'),
        ];

        if ($include_empty) {
            $options = ['' => ''] + $options;
        }

        return $options;
    }

    /**
     * A list of GEDCOM restrictions for privacy rules.
     *
     * @return string[]
     */
    public static function optionsRestrictionsRule(): array
    {
        $options = [
            'none'         => I18N::translate('Show to visitors'),
            'privacy'      => I18N::translate('Show to members'),
            'confidential' => I18N::translate('Show to managers'),
            'hidden'       => I18N::translate('Hide from everyone'),
        ];

        return $options;
    }

    /**
     * A list of temple options (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsTemples(): array
    {
        return ['' => I18N::translate('No temple - living ordinance')] + GedcomCodeTemp::templeNames();
    }

    /**
     * A list of user options (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsUsers(): array
    {
        $options = ['' => '-'];

        foreach ((new UserService())->all() as $user) {
            $options[$user->userName()] = $user->realName() . ' - ' . $user->userName();
        }

        return $options;
    }

    /**
     * add a new tag input field
     * called for each fact to be edited on a form.
     * Fact level=0 means a new empty form : data are POSTed by name
     * else data are POSTed using arrays :
     * glevels[] : tag level
     *  islink[] : tag is a link
     *     tag[] : tag name
     *    text[] : tag value
     *
     * @param Tree   $tree
     * @param string $tag        fact record to edit (eg 2 DATE xxxxx)
     * @param string $upperlevel optional upper level tag (eg BIRT)
     * @param string $label      An optional label to echo instead of the default
     *
     * @return string
     */
    public static function addSimpleTag(Tree $tree, $tag, $upperlevel = '', $label = ''): string
    {
        $request = app(ServerRequestInterface::class);
        $xref    = $request->get('xref', '');

        // Some form fields need access to previous form fields.
        static $previous_ids = [
            'SOUR' => '',
            'PLAC' => '',
        ];

        preg_match('/^(?:(\d+) (' . Gedcom::REGEX_TAG . ') ?(.*))/', $tag, $match);
        [, $level, $fact, $value] = $match;

        if ($level === '0') {
            if ($upperlevel) {
                $name = $upperlevel . '_' . $fact;
            } else {
                $name = $fact;
            }
        } else {
            $name = 'text[]';
        }

        $id = $fact . Uuid::uuid4()->toString();

        $previous_ids[$fact] = $id;

        // field value
        $islink = (substr($value, 0, 1) === '@' && substr($value, 0, 2) !== '@#');
        if ($islink) {
            $value = trim($value, '@');
        } else {
            $value = (string) substr($tag, strlen($fact) + 3);
        }
        if ($fact === 'REPO' || $fact === 'SOUR' || $fact === 'OBJE' || $fact === 'FAMC') {
            $islink = true;
        }

        if ($fact === 'SHARED_NOTE_EDIT' || $fact === 'SHARED_NOTE') {
            $islink = true;
            $fact   = 'NOTE';
        }

        $row_class = 'form-group row';
        switch ($fact) {
            case 'DATA':
            case 'MAP':
                // These GEDCOM tags should have no data, just child tags.
                if ($value === '') {
                    $row_class .= ' d-none';
                }
                break;
            case 'LATI':
            case 'LONG':
                // Indicate that this row is a child of a previous row, so we can expand/collapse them.
                $row_class .= ' child_of_' . $previous_ids['PLAC'];
                if ($value === '') {
                    $row_class .= ' collapse';
                }
                break;
        }

        $html = '';
        $html .= '<div class="' . $row_class . '">';
        $html .= '<label class="col-sm-3 col-form-label" for="' . $id . '">';

        // tag name
        if ($label) {
            $html .= $label;
        } elseif ($upperlevel) {
            $html .= GedcomTag::getLabel($upperlevel . ':' . $fact);
        } else {
            $html .= GedcomTag::getLabel($fact);
        }

        // Not all facts have help text.
        switch ($fact) {
            case 'NAME':
                if ($upperlevel !== 'REPO' && $upperlevel !== 'UNKNOWN') {
                    $html .= FunctionsPrint::helpLink($fact);
                }
                break;
            case 'ROMN':
            case 'SURN':
            case '_HEB':
                $html .= FunctionsPrint::helpLink($fact);
                break;
        }

        // tag level
        if ($level !== '0') {
            $html .= '<input type="hidden" name="glevels[]" value="' . $level . '">';
            $html .= '<input type="hidden" name="islink[]" value="' . $islink . '">';
            $html .= '<input type="hidden" name="tag[]" value="' . $fact . '">';
        }
        $html .= '</label>';

        // value
        $html .= '<div class="col-sm-9">';

        // Show names for spouses in MARR/HUSB/AGE and MARR/WIFE/AGE
        if ($fact === 'HUSB' || $fact === 'WIFE') {
            $family = Family::getInstance($xref, $tree);
            if ($family) {
                $spouse_link = $family->facts([$fact])->first();
                if ($spouse_link) {
                    $spouse = $spouse_link->target();
                    if ($spouse instanceof Individual) {
                        $html .= $spouse->fullName();
                    }
                }
            }
        }

        if (in_array($fact, Config::emptyFacts()) && ($value === '' || $value === 'Y' || $value === 'y')) {
            $html .= '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '">';

            if ($fact === 'CENS' && $value === 'Y') {
                $html .= view('modules/GEDFact_assistant/select-census', [
                    'census_places' => Census::censusPlaces(WT_LOCALE),
                ]);

                $census_assistant = app(ModuleService::class)->findByInterface(CensusAssistantModule::class)->first();
                $record           = Individual::getInstance($xref, $tree);

                if ($census_assistant instanceof CensusAssistantModule && $record instanceof Individual) {
                    $html .= $census_assistant->createCensusAssistant($record);
                }
            }
        } elseif ($fact === 'NPFX' || $fact === 'NSFX' || $fact === 'SPFX' || $fact === 'NICK') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="updatewholename()">';
        } elseif ($fact === 'GIVN') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" data-autocomplete-type="GIVN" oninput="updatewholename()" autofocus>';
        } elseif ($fact === 'SURN' || $fact === '_MARNM_SURN') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" data-autocomplete-type="SURN" oninput="updatewholename()">';
        } elseif ($fact === 'ADOP') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeAdop::getValues()]);
        } elseif ($fact === 'ALIA') {
            $html .= view('components/select-individual', ['id' => $id, 'name' => $name, 'individual' => Individual::getInstance($value, $tree), 'tree' => $tree]);
        } elseif ($fact === 'ASSO' || $fact === '_ASSO') {
            $html .= view('components/select-individual', ['id' => $id, 'name' => $name, 'individual' => Individual::getInstance($value, $tree), 'tree' => $tree]);
            if ($level === '1') {
                $html .= '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this individual, such as a friend or an employer.') . '</p>';
            } else {
                $html .= '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this fact or event, such as a witness or a priest.') . '</p>';
            }
        } elseif ($fact === 'DATE') {
            // Need to know if the user prefers DMY/MDY/YMD so we can validate dates properly.
            $dmy = '"' . preg_replace('/[^DMY]/', '', str_replace([
                    'j',
                    'F',
                ], [
                    'D',
                    'M',
                ], I18N::dateFormat())) . '"';

            $html .= '<div class="input-group">';
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" onchange="valid_date(this, ' . e($dmy) . ')" dir="ltr">';
            $html .= view('edit/input-addon-calendar', ['id' => $id]);
            $html .= view('edit/input-addon-help', ['fact' => 'DATE']);
            $html .= '</div>';
            $html .= '<div id="caldiv' . $id . '" style="position:absolute;visibility:hidden;background-color:white;z-index:1000"></div>';
            $html .= '<p class="text-muted">' . (new Date($value))->display() . '</p>';
        } elseif ($fact === 'FAMC') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-family" data-element-id="' . $id . '" title="' . I18N::translate('Create a family') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-family', ['id' => $id, 'name' => $name, 'family' => Family::getInstance($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'LATI') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="valid_lati_long(this, \'N\', \'S\')">';
        } elseif ($fact === 'LONG') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="valid_lati_long(this, \'E\', \'W\')">';
        } elseif ($fact === 'NOTE' && $islink) {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend">' .
                '<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#wt-ajax-modal" data-href="' . e(route('create-note-object', ['ged' => $tree->name()])) . '" data-select-id="' . $id . '" title="' . I18N::translate('Create a shared note') . '">' .
                '' . view('icons/add') . '<' .
                '/button>' .
                '</div>' .
                view('components/select-note', ['id' => $id, 'name' => $name, 'note' => Note::getInstance($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'OBJE') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-media-object', ['ged' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a media object') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-media', ['id' => $id, 'name' => $name, 'media' => Media::getInstance($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'PAGE') {
            $html .= '<input ' . Html::attributes([
                    'autocomplete'            => 'off',
                    'class'                   => 'form-control',
                    'id'                      => $id,
                    'name'                    => $name,
                    'value'                   => $value,
                    'type'                    => 'text',
                    'data-autocomplete-url'   => route('autocomplete-page', [
                        'ged'   => $tree->name(),
                        'query' => 'QUERY',
                    ]),
                    'data-autocomplete-extra' => '#' . $previous_ids['SOUR'],
                ]) . '>';
        } elseif ($fact === 'PEDI') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodePedi::getValues()]);
        } elseif ($fact === 'PLAC') {
            $html .= '<div class="input-group">';
            $html .= '<input ' . Html::attributes([
                    'autocomplete'          => 'off',
                    'class'                 => 'form-control',
                    'id'                    => $id,
                    'name'                  => $name,
                    'value'                 => $value,
                    'type'                  => 'text',
                    'data-autocomplete-url' => route('autocomplete-place', [
                        'ged'   => $tree->name(),
                        'query' => 'QUERY',
                    ]),
                ]) . '>';

            /** @TODO - typeaheadjs.css doesn't work in an input-group */
            $html .= view('edit/input-addon-coordinates', ['id' => $id]);
            $html .= view('edit/input-addon-help', ['fact' => 'PLAC']);
            $html .= '</div>';
        } elseif ($fact === 'QUAY') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeQuay::getValues()]);
        } elseif ($fact === 'RELA') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => self::optionsRelationships($value)]);
        } elseif ($fact === 'REPO') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-repository', ['ged' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a repository') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-repository', ['id' => $id, 'name' => $name, 'repository' => Repository::getInstance($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'RESN') {
            $html .= '<div class="input-group">';
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => self::optionsRestrictions(true)]);
            $html .= view('edit/input-addon-help', ['fact' => 'RESN']);
            $html .= '</span>';
            $html .= '</div>';
        } elseif ($fact === 'SEX') {
            if ($value !== 'M' && $value !== 'F') {
                $value = 'U';
            }
            $html .= view('components/radios-inline', ['name' => $name, 'options' => ['M' => I18N::translate('Male'), 'F' => I18N::translate('Female'), 'U' => I18N::translateContext('unknown gender', 'Unknown')], 'selected' => $value]);
        } elseif ($fact === 'SOUR') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-source', ['ged' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a source') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-source', ['id' => $id, 'name' => $name, 'source' => Source::getInstance($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'STAT') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeStat::statusNames($upperlevel)]);
        } elseif ($fact === 'SUBM') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-submitter', ['ged' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a submitter') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-submitter', ['id' => $id, 'name' => $name, 'submitter' => GedcomRecord::getInstance($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'TEMP') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => self::optionsTemples()]);
        } elseif ($fact === 'TIME') {
            /* I18N: Examples of valid time formats (hours:minutes:seconds) */
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" pattern="([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?" dir="ltr" placeholder="' . I18N::translate('hh:mm or hh:mm:ss') . '">';
        } elseif ($fact === '_WT_USER') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => self::optionsUsers()]);
        } elseif ($fact === '_PRIM') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => ['' => '', 'Y' => I18N::translate('always'), 'N' => I18N::translate('never')]]);
            $html .= '<p class="small text-muted">' . I18N::translate('Use this image for charts and on the individual’s page.') . '</p>';
        } elseif ($fact === 'TYPE' && $level === '0') {
            // Level 0 TYPE fields are only used for NAME records
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeName::getValues()]);
        } elseif ($fact === 'TYPE' && $level === '3') {
            //-- Build the selector for the Media 'TYPE' Fact
            $html          .= '<select name="text[]"><option selected value="" ></option>';
            $selectedValue = strtolower($value);
            if (!array_key_exists($selectedValue, GedcomTag::getFileFormTypes())) {
                $html .= '<option selected value="' . e($value) . '" >' . e($value) . '</option>';
            }
            foreach (['' => ''] + GedcomTag::getFileFormTypes() + [] as $typeName => $typeValue) {
                $html .= '<option value="' . $typeName . '" ';
                if ($selectedValue === $typeName) {
                    $html .= 'selected';
                }
                $html .= '>' . $typeValue . '</option>';
            }
            $html .= '</select>';
        } elseif (($fact !== 'NAME' || $upperlevel === 'REPO' || $upperlevel === 'UNKNOWN') && $fact !== '_MARNM') {
            if ($fact === 'TEXT' || $fact === 'ADDR' || ($fact === 'NOTE' && !$islink)) {
                $html .= '<div class="input-group">';
                $html .= '<textarea class="form-control" id="' . $id . '" name="' . $name . '" dir="auto">' . e($value) . '</textarea>';
                $html .= '</div>';
            } else {
                // If using GEDFact-assistant window
                $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '">';
            }
        } else {
            // Populated in javascript from sub-tags
            $html .= '<input type="hidden" id="' . $id . '" name="' . $name . '" oninput="updateTextName(\'' . $id . '\')" value="' . e($value) . '" class="' . $fact . '">';
            $html .= '<span id="' . $id . '_display" dir="auto">' . e($value) . '</span>';
            $html .= ' <a href="#edit_name" onclick="convertHidden(\'' . $id . '\'); return false" class="icon-edit_indi" title="' . I18N::translate('Edit the name') . '"></a>';
        }
        // MARRiage TYPE : hide text field and show a selection list
        if ($fact === 'TYPE' && $level === '2' && self::$tags[0] === 'MARR') {
            $html .= '<script>';
            $html .= 'document.getElementById(\'' . $id . '\').style.display=\'none\'';
            $html .= '</script>';
            $html .= '<select id="' . $id . '_sel" oninput="document.getElementById(\'' . $id . '\').value=this.value" >';
            foreach (['Unknown', 'Civil', 'Religious', 'Partners'] as $key) {
                if ($key === 'Unknown') {
                    $html .= '<option value="" ';
                } else {
                    $html .= '<option value="' . $key . '" ';
                }
                $a = strtolower($key);
                $b = strtolower($value);
                if ($b !== '' && strpos($a, $b) !== false || strpos($b, $a) !== false) {
                    $html .= 'selected';
                }
                $html .= '>' . GedcomTag::getLabel('MARR_' . strtoupper($key)) . '</option>';
            }
            $html .= '</select>';
        }

        $html .= '</div></div>';

        return $html;
    }

    /**
     * Add some empty tags to create a new fact.
     *
     * @param Tree   $tree
     * @param string $fact
     *
     * @return void
     */
    public static function addSimpleTags(Tree $tree, $fact): void
    {
        // For new individuals, these facts default to "Y"
        if ($fact === 'MARR') {
            echo self::addSimpleTag($tree, '0 ' . $fact . ' Y');
        } else {
            echo self::addSimpleTag($tree, '0 ' . $fact);
        }

        if (!in_array($fact, Config::nonDateFacts())) {
            echo self::addSimpleTag($tree, '0 DATE', $fact, GedcomTag::getLabel($fact . ':DATE'));
            echo self::addSimpleTag($tree, '0 RELI', $fact, GedcomTag::getLabel($fact . ':RELI'));
        }

        if (!in_array($fact, Config::nonPlaceFacts())) {
            echo self::addSimpleTag($tree, '0 PLAC', $fact, GedcomTag::getLabel($fact . ':PLAC'));

            if (preg_match_all('/(' . Gedcom::REGEX_TAG . ')/', $tree->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
                foreach ($match[1] as $tag) {
                    echo self::addSimpleTag($tree, '0 ' . $tag, $fact, GedcomTag::getLabel($fact . ':PLAC:' . $tag));
                }
            }
            echo self::addSimpleTag($tree, '0 MAP', $fact);
            echo self::addSimpleTag($tree, '0 LATI', $fact);
            echo self::addSimpleTag($tree, '0 LONG', $fact);
        }
    }

    /**
     * builds the form for adding new facts
     *
     * @param Tree   $tree
     * @param string $fact the new fact we are adding
     *
     * @return void
     */
    public static function createAddForm(Tree $tree, $fact): void
    {
        self::$tags = [];

        // handle  MARRiage TYPE
        if (substr($fact, 0, 5) === 'MARR_') {
            self::$tags[0] = 'MARR';
            echo self::addSimpleTag($tree, '1 MARR');
            self::insertMissingSubtags($tree, $fact);
        } else {
            self::$tags[0] = $fact;
            if ($fact === '_UID') {
                $fact .= ' ' . GedcomTag::createUid();
            }
            // These new level 1 tags need to be turned into links
            if (in_array($fact, [
                'ALIA',
                'ASSO',
            ])) {
                $fact .= ' @';
            }
            if (in_array($fact, Config::emptyFacts())) {
                echo self::addSimpleTag($tree, '1 ' . $fact . ' Y');
            } else {
                echo self::addSimpleTag($tree, '1 ' . $fact);
            }
            self::insertMissingSubtags($tree, self::$tags[0]);
            //-- handle the special SOURce case for level 1 sources [ 1759246 ]
            if ($fact === 'SOUR') {
                echo self::addSimpleTag($tree, '2 PAGE');
                echo self::addSimpleTag($tree, '2 DATA');
                echo self::addSimpleTag($tree, '3 TEXT');
                if ($tree->getPreference('FULL_SOURCES')) {
                    echo self::addSimpleTag($tree, '3 DATE', '', GedcomTag::getLabel('DATA:DATE'));
                    echo self::addSimpleTag($tree, '2 QUAY');
                }
            }
        }
    }

    /**
     * Create a form to edit a Fact object.
     *
     * @param Fact $fact
     *
     * @return void
     */
    public static function createEditForm(Fact $fact): void
    {
        $record = $fact->record();
        $tree   = $record->tree();

        self::$tags = [];

        $level0type = $record::RECORD_TYPE;
        $level1type = $fact->getTag();

        // List of tags we would expect at the next level
        // NB insertMissingSubtags() already takes care of the simple cases
        // where a level 1 tag is missing a level 2 tag. Here we only need to
        // handle the more complicated cases.
        $expected_subtags = [
            'SOUR' => [
                'PAGE',
                'DATA',
            ],
            'PLAC' => ['MAP'],
            'MAP'  => [
                'LATI',
                'LONG',
            ],
        ];
        
        if ($record::RECORD_TYPE !== 'SOUR') {
            //source citations within other records, i.e. n SOUR / +1 DATA / +2 TEXT
            $expected_subtags['DATA'][] = 'TEXT';
        } //else: source records themselves, i.e. 0 SOUR / 1 DATA don't get a 2 TEXT!

        if ($record::RECORD_TYPE === 'SOUR') {
            //source records themselves, i.e. 0 SOUR / 1 DATA / 2 EVEN get a 3 DATE and a 3 PLAC
            $expected_subtags['EVEN'][] = 'DATE';
            $expected_subtags['EVEN'][] = 'PLAC';
        }
        
        if ($record->tree()->getPreference('FULL_SOURCES')) {
            $expected_subtags['SOUR'][] = 'QUAY';
            
            if ($record::RECORD_TYPE !== 'SOUR') {
                //source citations within other records, i.e. n SOUR / +1 DATA / +2 DATE
                $expected_subtags['DATA'][] = 'DATE';
            } //else: source records themselves, i.e. 0 SOUR / 1 DATA don't get a 2 DATE!
        }

        if (GedcomCodeTemp::isTagLDS($level1type)) {
            $expected_subtags['STAT'] = ['DATE'];
        }

        if (in_array($level1type, Config::dateAndTime())) {
            // TIME is NOT a valid 5.5.1 tag
            $expected_subtags['DATE'] = ['TIME'];
        }

        if (preg_match_all('/(' . Gedcom::REGEX_TAG . ')/', $record->tree()->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
            $expected_subtags['PLAC'] = array_merge($match[1], $expected_subtags['PLAC']);
        }

        $stack       = [];
        $gedlines    = explode("\n", $fact->gedcom());
        $count       = count($gedlines);
        $i           = 0;
        $inSource    = false;
        $levelSource = 0;
        $add_date    = true;

        // Loop on existing tags :
        while ($i < $count) {
            $fields = explode(' ', $gedlines[$i], 3);
            $level  = (int) $fields[0];
            $type   = $fields[1] ?? '';
            $text   = $fields[2] ?? '';

            // Keep track of our hierarchy, e.g. 1=>BIRT, 2=>PLAC, 3=>FONE
            $stack[$level] = $type;
            // Merge them together, e.g. BIRT:PLAC:FONE
            $label = implode(':', array_slice($stack, 0, $level));

            // Merge text from continuation lines
            while ($i + 1 < $count && preg_match('/^' . ($level + 1) . ' CONT ?(.*)/', $gedlines[$i + 1], $cmatch) > 0) {
                $text .= "\n" . $cmatch[1];
                $i++;
            }

            if ($type === 'SOUR') {
                $inSource    = true;
                $levelSource = $level;
            } elseif ($levelSource >= $level) {
                $inSource = false;
            }

            self::$tags[] = $type;
            $subrecord    = $level . ' ' . $type . ' ' . $text;

            // Dates need different labels, depending on whether they are inside sources.
            if ($inSource && $type === 'DATE') {
                echo self::addSimpleTag($tree, $subrecord, '', GedcomTag::getLabel($label, $record));
            } elseif (!$inSource && $type === 'DATE') {
                echo self::addSimpleTag($tree, $subrecord, $level1type, GedcomTag::getLabel($label, $record));
                if ($level === 2) {
                    // We already have a date - no need to add one.
                    $add_date = false;
                }
            } elseif ($type === 'STAT') {
                echo self::addSimpleTag($tree, $subrecord, $level1type, GedcomTag::getLabel($label, $record));
            } else {
                echo self::addSimpleTag($tree, $subrecord, $level0type, GedcomTag::getLabel($label, $record));
            }

            // Get a list of tags present at the next level
            $subtags = [];
            for ($ii = $i + 1; isset($gedlines[$ii]) && preg_match('/^(\d+) (\S+)/', $gedlines[$ii], $mm) && $mm[1] > $level; ++$ii) {
                if ($mm[1] == $level + 1) {
                    $subtags[] = $mm[2];
                }
            }

            // Insert missing tags
            if (!empty($expected_subtags[$type])) {
                foreach ($expected_subtags[$type] as $subtag) {
                    if (!in_array($subtag, $subtags)) {
                        echo self::addSimpleTag($tree, ($level + 1) . ' ' . $subtag, '', GedcomTag::getLabel($label . ':' . $subtag));
                        if (!empty($expected_subtags[$subtag])) {
                            foreach ($expected_subtags[$subtag] as $subsubtag) {
                                echo self::addSimpleTag($tree, ($level + 2) . ' ' . $subsubtag, '', GedcomTag::getLabel($label . ':' . $subtag . ':' . $subsubtag));
                            }
                        }
                    }
                }
            }

            $i++;
        }

        if ($level1type !== '_PRIM') {
            //0 SOUR / 1 DATA doesn't get a 2 DATE!
            //0 SOUR / 1 DATA doesn't get a 2 EVEN here either, we rather handle this via cards/add-sour-data-even
            if ($record::RECORD_TYPE !== 'SOUR') {
                self::insertMissingSubtags($tree, $level1type, $add_date);
            }
        }
    }

    /**
     * Populates the global $tags array with any missing sub-tags.
     *
     * @param Tree   $tree
     * @param string $level1tag the type of the level 1 gedcom record
     * @param bool   $add_date
     *
     * @return void
     */
    public static function insertMissingSubtags(Tree $tree, $level1tag, $add_date = false): void
    {
        // handle  MARRiage TYPE
        $type_val = '';
        if (substr($level1tag, 0, 5) === 'MARR_') {
            $type_val  = substr($level1tag, 5);
            $level1tag = 'MARR';
        }

        foreach (Config::levelTwoTags() as $key => $value) {
            if ($key === 'DATE' && in_array($level1tag, Config::nonDateFacts()) || $key === 'PLAC' && in_array($level1tag, Config::nonPlaceFacts())) {
                continue;
            }
            if (in_array($level1tag, $value) && !in_array($key, self::$tags)) {
                if ($key === 'TYPE') {
                    echo self::addSimpleTag($tree, '2 TYPE ' . $type_val, $level1tag);
                } elseif ($level1tag === '_TODO' && $key === 'DATE') {
                    echo self::addSimpleTag($tree, '2 ' . $key . ' ' . strtoupper(date('d M Y')), $level1tag);
                } elseif ($level1tag === '_TODO' && $key === '_WT_USER') {
                    echo self::addSimpleTag($tree, '2 ' . $key . ' ' . Auth::user()->userName(), $level1tag);
                } elseif ($level1tag === 'NAME' && strstr($tree->getPreference('ADVANCED_NAME_FACTS'), $key) !== false) {
                    echo self::addSimpleTag($tree, '2 ' . $key, $level1tag);
                } elseif ($level1tag !== 'NAME') {
                    echo self::addSimpleTag($tree, '2 ' . $key, $level1tag);
                }
                // Add level 3/4 tags as appropriate
                switch ($key) {
                    case 'PLAC':
                        if (preg_match_all('/(' . Gedcom::REGEX_TAG . ')/', $tree->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
                            foreach ($match[1] as $tag) {
                                echo self::addSimpleTag($tree, '3 ' . $tag, '', GedcomTag::getLabel($level1tag . ':PLAC:' . $tag));
                            }
                        }
                        echo self::addSimpleTag($tree, '3 MAP');
                        echo self::addSimpleTag($tree, '4 LATI');
                        echo self::addSimpleTag($tree, '4 LONG');
                        break;
                    case 'EVEN':
                        echo self::addSimpleTag($tree, '3 DATE');
                        echo self::addSimpleTag($tree, '3 PLAC');
                        break;
                    case 'STAT':
                        if (GedcomCodeTemp::isTagLDS($level1tag)) {
                            echo self::addSimpleTag($tree, '3 DATE', '', GedcomTag::getLabel('STAT:DATE'));
                        }
                        break;
                    case 'DATE':
                        // TIME is NOT a valid 5.5.1 tag
                        if (in_array($level1tag, Config::dateAndTime())) {
                            echo self::addSimpleTag($tree, '3 TIME');
                        }
                        break;
                    case 'HUSB':
                    case 'WIFE':
                        echo self::addSimpleTag($tree, '3 AGE');
                        break;
                    case 'FAMC':
                        if ($level1tag === 'ADOP') {
                            echo self::addSimpleTag($tree, '3 ADOP BOTH');
                        }
                        break;
                }
            } elseif ($key === 'DATE' && $add_date) {
                echo self::addSimpleTag($tree, '2 DATE', $level1tag, GedcomTag::getLabel($level1tag . ':DATE'));
            }
        }
        // Do something (anything!) with unrecognized custom tags
        if (substr($level1tag, 0, 1) === '_' && $level1tag !== '_UID' && $level1tag !== '_PRIM' && $level1tag !== '_TODO') {
            foreach (['DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE'] as $tag) {
                if (!in_array($tag, self::$tags)) {
                    echo self::addSimpleTag($tree, '2 ' . $tag);
                    if ($tag === 'PLAC') {
                        if (preg_match_all('/(' . Gedcom::REGEX_TAG . ')/', $tree->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
                            foreach ($match[1] as $ptag) {
                                echo self::addSimpleTag($tree, '3 ' . $ptag, '', GedcomTag::getLabel($level1tag . ':PLAC:' . $ptag));
                            }
                        }
                        echo self::addSimpleTag($tree, '3 MAP');
                        echo self::addSimpleTag($tree, '4 LATI');
                        echo self::addSimpleTag($tree, '4 LONG');
                    }
                }
            }
        }
    }
}
