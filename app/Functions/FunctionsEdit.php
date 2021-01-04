<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\GedcomCode\GedcomCodeLang;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeName;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeQuay;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeRela;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeStat;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeTemp;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteCitation;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompletePlace;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteSurname;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateMediaObjectModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateNoteModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateRepositoryModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSourceModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSubmitterModal;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

use function app;
use function array_key_exists;
use function array_merge;
use function array_slice;
use function count;
use function date;
use function e;
use function explode;
use function implode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function route;
use function str_contains;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;
use function ucfirst;
use function view;

/**
 * Class FunctionsEdit - common functions for editing
 *
 * @deprecated since 2.0.6.  Will be removed in 2.1.0
 */
class FunctionsEdit
{
    /** @var string[] - a list of GEDCOM tags in the edit form. */
    private static $tags = [];

    /**
     * Function edit_language_checkboxes
     *
     * @param string        $parameter_name
     * @param array<string> $languages
     *
     * @return string
     */
    public static function editLanguageCheckboxes($parameter_name, $languages): string
    {
        return view('edit/language-checkboxes', ['languages' => $languages]);
    }

    /**
     * A list of access levels (e.g. for an edit control).
     *
     * @return string[]
     */
    public static function optionsAccessLevels(): array
    {
        return Auth::accessLevelNames();
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
        return app(MessageService::class)->contactMethods();
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
        return Auth::privacyRuleNames();
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

        foreach (app(UserService::class)->all() as $user) {
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
        $localization_service = app(LocalizationService::class);

        $request = app(ServerRequestInterface::class);
        $xref    = $request->getAttribute('xref', '');

        // Some form fields need access to previous form fields.
        static $previous_ids = [
            'SOUR' => '',
            'PLAC' => '',
        ];

        $parts = explode(' ', $tag, 3);
        $level = $parts[0] ?? '';
        $fact  = $parts[1] ?? '';
        $value = $parts[2] ?? '';

        if ($level === '0') {
            // Adding a new fact.
            if ($upperlevel) {
                $name = $upperlevel . '_' . $fact;
            } else {
                $name = $fact;
            }
        } else {
            // Editing an existing fact.
            $name = 'text[]';
        }

        $id = $fact . Uuid::uuid4()->toString();

        $previous_ids[$fact] = $id;

        // field value
        $islink = (bool) preg_match('/^@[^#@][^@]*@$/', $value);
        if ($islink) {
            $value = trim($value, '@');
        }

        if ($fact === 'REPO' || $fact === 'SOUR' || $fact === 'OBJE' || $fact === 'FAMC' || $fact === 'SUBM' || $fact === 'ASSO' || $fact === '_ASSO' || $fact === 'ALIA') {
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
                    $html .= view('help/link', ['topic' => $fact]);
                }
                break;
            case 'ROMN':
            case 'SURN':
            case '_HEB':
                $html .= view('help/link', ['topic' => $fact]);
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
            $family = Registry::familyFactory()->make($xref, $tree);
            if ($family instanceof Family) {
                $spouse_link = $family->facts([$fact])->first();
                if ($spouse_link instanceof Fact) {
                    $spouse = $spouse_link->target();
                    if ($spouse instanceof Individual) {
                        $html .= $spouse->fullName();
                    }
                }
            }
        }

        if (in_array($fact, Config::emptyFacts(), true) && ($value === '' || $value === 'Y' || $value === 'y')) {
            $html .= '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '">';

            $checked = $value === '' ? '' : 'checked';
            $onchange = 'this.previousSibling.value=this.checked ? this.value : &quot;&quot;';
            $html .= '<input type="checkbox" value="Y" ' . $checked . ' onchange="' . $onchange . '">';

            if ($fact === 'CENS' && $value === 'Y') {
                $html .= view('modules/GEDFact_assistant/select-census', [
                    'census_places' => Census::censusPlaces(I18N::languageTag()),
                ]);

                $census_assistant = app(ModuleService::class)->findByInterface(CensusAssistantModule::class)->first();
                $record           = Registry::individualFactory()->make($xref, $tree);

                if ($census_assistant instanceof CensusAssistantModule && $record instanceof Individual) {
                    $html .= $census_assistant->createCensusAssistant($record);
                }
            }
        } elseif ($fact === 'NPFX' || $fact === 'NSFX' || $fact === 'SPFX' || $fact === 'NICK') {
            $html .= '<div class="input-group">';
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="updatewholename()">';
            $html .= view('edit/input-addon-keyboard', ['id' => $id]);
            $html .= '</div>';
        } elseif ($fact === 'GIVN') {
            $html .= '<div class="input-group">';
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="updatewholename()" autofocus>';
            $html .= view('edit/input-addon-keyboard', ['id' => $id]);
            $html .= '</div>';
        } elseif ($fact === 'SURN' || $fact === '_MARNM_SURN') {
            $html .= '<div class="input-group">';
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" autocomplete="off" data-autocomplete-url="' . e(route(AutoCompleteSurname::class, ['tree' => $tree->name()])) . '" oninput="updatewholename()" onblur="updatewholename()">';
            $html .= view('edit/input-addon-keyboard', ['id' => $id]);
            $html .= '</div>';
        } elseif ($fact === 'ADOP') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeAdop::getValues()]);
        } elseif ($fact === 'LANG') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeLang::getValues()]);
        } elseif ($fact === 'ALIA') {
            $html .= '<div class="input-group">';
            $html .= view('components/select-individual', ['id' => $id, 'name' => $name, 'individual' => Registry::individualFactory()->make($value, $tree), 'tree' => $tree]);
            $html .= '</div>';
        } elseif ($fact === 'ASSO' || $fact === '_ASSO') {
            $html .= '<div class="input-group">';
            $html .= view('components/select-individual', ['id' => $id, 'name' => $name, 'individual' => Registry::individualFactory()->make($value, $tree), 'tree' => $tree]);
            $html .= '</div>';
            if ($level === '1') {
                $html .= '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this individual, such as a friend or an employer.') . '</p>';
            } else {
                $html .= '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this fact or event, such as a witness or a priest.') . '</p>';
            }
        } elseif ($fact === 'DATE') {
            // Need to know if the user prefers DMY/MDY/YMD so we can validate dates properly.
            $dmy = '"' . $localization_service->dateFormatToOrder(I18N::dateFormat()) . '"';

            $html .= '<div class="input-group">';
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" onchange="webtrees.reformatDate(this, ' . e($dmy) . ')" dir="ltr">';
            $html .= view('edit/input-addon-calendar', ['id' => $id]);
            $html .= view('edit/input-addon-help', ['fact' => 'DATE']);
            $html .= '</div>';
            $html .= '<div id="caldiv' . $id . '" style="position:absolute;visibility:hidden;background-color:white;z-index:1000"></div>';
            $html .= '<p class="text-muted">' . (new Date($value))->display() . '</p>';
        } elseif ($fact === 'FAMC') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-target="#modal-create-family" data-element-id="' . $id . '" title="' . I18N::translate('Create a family') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-family', ['id' => $id, 'name' => $name, 'family' => Registry::familyFactory()->make($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'LATI') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="webtrees.reformatLatitude(this)">';
        } elseif ($fact === 'LONG') {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="webtrees.reformatLongitude(this)">';
        } elseif ($fact === 'NOTE' && $islink) {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend">' .
                '<button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-target="#wt-ajax-modal" data-href="' . e(route(CreateNoteModal::class, ['tree' => $tree->name()])) . '" data-select-id="' . $id . '" title="' . I18N::translate('Create a shared note') . '">' .
                '' . view('icons/add') . '<' .
                '/button>' .
                '</div>' .
                view('components/select-note', ['id' => $id, 'name' => $name, 'note' => Registry::noteFactory()->make($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'OBJE') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-href="' . e(route(CreateMediaObjectModal::class, ['tree' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a media object') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-media', ['id' => $id, 'name' => $name, 'media' => Registry::mediaFactory()->make($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'PAGE') {
            $html .= '<input ' . Html::attributes([
                    'autocomplete'            => 'off',
                    'class'                   => 'form-control',
                    'id'                      => $id,
                    'name'                    => $name,
                    'value'                   => $value,
                    'type'                    => 'text',
                    'data-autocomplete-url'   => route(AutoCompleteCitation::class, ['tree'  => $tree->name()]),
                    'data-autocomplete-extra' => 'SOUR',
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
                    'data-autocomplete-url' => route(AutoCompletePlace::class, ['tree'  => $tree->name()]),
                ]) . '>';

            $html .= view('edit/input-addon-coordinates', ['id' => $id]);
            $html .= view('edit/input-addon-help', ['fact' => 'PLAC']);
            $html .= '</div>';
        } elseif ($fact === 'QUAY') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => ['' => ''] + GedcomCodeQuay::getValues()]);
        } elseif ($fact === 'RELA') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => self::optionsRelationships($value)]);
        } elseif ($fact === 'REPO') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-href="' . e(route(CreateRepositoryModal::class, ['tree' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a repository') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-repository', ['id' => $id, 'name' => $name, 'repository' => Registry::repositoryFactory()->make($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'RESN') {
            $html .= '<div class="input-group">';
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => self::optionsRestrictions(true)]);
            $html .= view('edit/input-addon-help', ['fact' => 'RESN']);
            $html .= '</span>';
            $html .= '</div>';
        } elseif ($fact === 'SEX') {
            $html .= view('components/radios-inline', ['name' => $name, 'options' => ['M' => I18N::translate('Male'), 'F' => I18N::translate('Female'), 'U' => I18N::translateContext('unknown gender', 'Unknown')], 'selected' => $value]);
        } elseif ($fact === 'SOUR') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-href="' . e(route(CreateSourceModal::class, ['tree' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a source') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-source', ['id' => $id, 'name' => $name, 'source' => Registry::sourceFactory()->make($value, $tree), 'tree' => $tree]) .
                '</div>';
        } elseif ($fact === 'STAT') {
            $html .= view('components/select', ['id' => $id, 'name' => $name, 'selected' => $value, 'options' => GedcomCodeStat::statusNames($upperlevel)]);
        } elseif ($fact === 'SUBM') {
            $html .=
                '<div class="input-group">' .
                '<div class="input-group-prepend"><button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-href="' . e(route(CreateSubmitterModal::class, ['tree' => $tree->name()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a submitter') . '">' . view('icons/add') . '</button></div>' .
                view('components/select-submitter', ['id' => $id, 'name' => $name, 'submitter' => Registry::submitterFactory()->make($value, $tree), 'tree' => $tree]) .
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
        } elseif (($fact !== 'NAME' || $upperlevel === 'REPO' || $upperlevel === 'SUBM' || $upperlevel === 'UNKNOWN') && $fact !== '_MARNM') {
            if ($fact === 'TEXT' || $fact === 'ADDR' || ($fact === 'NOTE' && !$islink)) {
                $html .= '<div class="input-group">';
                $html .= '<textarea class="form-control" id="' . $id . '" name="' . $name . '" rows="5" dir="auto">' . e($value) . '</textarea>';
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

            $marriage_types = [
                '' => '',
                'Civil' => I18N::translate('Civil marriage'),
                'Religious' => I18N::translate('Religious marriage'),
                'Partners' => I18N::translate('Registered partnership'),
            ];

            foreach ($marriage_types as $key => $type_label) {
                $html .= '<option value="' . $key . '" ';
                if (strtolower($key) === strtolower($value)) {
                    $html .= 'selected';
                }
                $html .= '>' . $type_label . '</option>';
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

        if (!in_array($fact, Config::nonDateFacts(), true)) {
            echo self::addSimpleTag($tree, '0 DATE', $fact, GedcomTag::getLabel($fact . ':DATE'));
        }

        if (!in_array($fact, Config::nonPlaceFacts(), true)) {
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
            if (in_array($fact, ['ALIA', 'ASSO'], true)) {
                $fact .= ' @';
            }
            if (in_array($fact, Config::emptyFacts(), true)) {
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

        $level0type = $record->tag();
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

        if ($record->tag() !== 'SOUR') {
            //source citations within other records, i.e. n SOUR / +1 DATA / +2 TEXT
            $expected_subtags['DATA'][] = 'TEXT';
        } //else: source records themselves, i.e. 0 SOUR / 1 DATA don't get a 2 TEXT!

        if ($record->tag() === 'SOUR') {
            //source records themselves, i.e. 0 SOUR / 1 DATA / 2 EVEN get a 3 DATE and a 3 PLAC
            $expected_subtags['EVEN'][] = 'DATE';
            $expected_subtags['EVEN'][] = 'PLAC';
        }

        if ($record->tree()->getPreference('FULL_SOURCES')) {
            $expected_subtags['SOUR'][] = 'QUAY';

            if ($record->tag() !== 'SOUR') {
                //source citations within other records, i.e. n SOUR / +1 DATA / +2 DATE
                $expected_subtags['DATA'][] = 'DATE';
            } //else: source records themselves, i.e. 0 SOUR / 1 DATA don't get a 2 DATE!
        }

        if (GedcomCodeTemp::isTagLDS($level1type)) {
            $expected_subtags['STAT'] = ['DATE'];
        }

        if (in_array($level1type, Config::dateAndTime(), true)) {
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
                echo self::addSimpleTag($tree, $subrecord, '', GedcomTag::getLabel($label));
            } elseif (!$inSource && $type === 'DATE') {
                echo self::addSimpleTag($tree, $subrecord, $level1type, GedcomTag::getLabel($label));
                if ($level === 2) {
                    // We already have a date - no need to add one.
                    $add_date = false;
                }
            } elseif ($type === 'STAT') {
                echo self::addSimpleTag($tree, $subrecord, $level1type, GedcomTag::getLabel($label));
            } else {
                echo self::addSimpleTag($tree, $subrecord, $level0type, GedcomTag::getLabel($label));
            }

            // Get a list of tags present at the next level
            $subtags = [];
            for ($ii = $i + 1; isset($gedlines[$ii]) && preg_match('/^(\d+) (\S+)/', $gedlines[$ii], $mm) && $mm[1] > $level; ++$ii) {
                if ($mm[1] == $level + 1) {
                    $subtags[] = $mm[2];
                }
            }

            // Insert missing tags
            foreach ($expected_subtags[$type] ?? [] as $subtag) {
                if (!in_array($subtag, $subtags, true)) {
                    echo self::addSimpleTag($tree, ($level + 1) . ' ' . $subtag, '', GedcomTag::getLabel($label . ':' . $subtag));
                    foreach ($expected_subtags[$subtag] ?? [] as $subsubtag) {
                        echo self::addSimpleTag($tree, ($level + 2) . ' ' . $subsubtag, '', GedcomTag::getLabel($label . ':' . $subtag . ':' . $subsubtag));
                    }
                }
            }

            $i++;
        }

        if ($level1type !== '_PRIM') {
            //0 SOUR / 1 DATA doesn't get a 2 DATE!
            //0 SOUR / 1 DATA doesn't get a 2 EVEN here either, we rather handle this via cards/add-sour-data-even
            if ($record->tag() !== 'SOUR') {
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
            $type_val  = ucfirst(strtolower(substr($level1tag, 5)));
            $level1tag = 'MARR';
        }

        foreach (Config::levelTwoTags() as $key => $value) {
            if ($key === 'DATE' && in_array($level1tag, Config::nonDateFacts(), true) || $key === 'PLAC' && in_array($level1tag, Config::nonPlaceFacts(), true)) {
                continue;
            }
            if (in_array($level1tag, $value, true) && !in_array($key, self::$tags, true)) {
                if ($key === 'TYPE') {
                    echo self::addSimpleTag($tree, '2 TYPE ' . $type_val, $level1tag);
                } elseif ($level1tag === '_TODO' && $key === 'DATE') {
                    $today = strtoupper(date('d M Y'));
                    echo self::addSimpleTag($tree, '2 ' . $key . ' ' . $today, $level1tag);
                } elseif ($level1tag === '_TODO' && $key === '_WT_USER') {
                    echo self::addSimpleTag($tree, '2 ' . $key . ' ' . Auth::user()->userName(), $level1tag);
                } elseif ($level1tag === 'NAME' && str_contains($tree->getPreference('ADVANCED_NAME_FACTS'), $key)) {
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
                        if (in_array($level1tag, Config::dateAndTime(), true)) {
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
                if (!in_array($tag, self::$tags, true)) {
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
