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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Census\Census;
use Fisharebest\Webtrees\Census\CensusOfCzechRepublic;
use Fisharebest\Webtrees\Census\CensusOfDenmark;
use Fisharebest\Webtrees\Census\CensusOfDeutschland;
use Fisharebest\Webtrees\Census\CensusOfEngland;
use Fisharebest\Webtrees\Census\CensusOfFrance;
use Fisharebest\Webtrees\Census\CensusOfScotland;
use Fisharebest\Webtrees\Census\CensusOfUnitedStates;
use Fisharebest\Webtrees\Census\CensusOfWales;
use Fisharebest\Webtrees\Config;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FontAwesome;
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
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Select2;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Ramsey\Uuid\Uuid;

/**
 * Class FunctionsEdit - common functions for editing
 */
class FunctionsEdit {
	/**
	 * Function edit_language_checkboxes
	 *
	 * @param string $parameter_name
	 * @param array $accepted_languages
	 *
	 * @return string
	 */
	public static function editLanguageCheckboxes($parameter_name, $accepted_languages) {
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
	public static function optionsAccessLevels() {
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
	public static function optionsActiveLanguages() {
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
	public static function optionsCalendarConversions() {
		return ['none' => I18N::translate('No calendar conversion')] + Date::calendarNames();
	}

	/**
	 * A list of contact methods (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	public static function optionsContactMethods() {
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
	public static function optionsHideShow() {
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
	public static function optionsInstalledLanguages() {
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
	public static function numericOptions($integers) {
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
	public static function optionsNoYes() {
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
	public static function optionsRelationships($relationship) {
		$relationships = GedcomCodeRela::getValues();
		// The user is allowed to specify values that aren't in the list.
		if (!array_key_exists($relationship, $relationships)) {
			$relationships[$relationship] = I18N::translate($relationship);
		}

		return $relationships;
	}

	/**
	 * A list of GEDCOM restrictions (e.g. for an edit control).
	 *
	 * @param bool $include_empty
	 *
	 * @return string[]
	 */
	public static function optionsRestrictions($include_empty) {
		$options = [
			'none'         => I18N::translate('Show to visitors'), // Not valid GEDCOM, but very useful
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
	 * A list mail transport options (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	public static function optionsMailTransports() {
		return [
			'internal' => I18N::translate('Use PHP mail to send messages'),
			'external' => I18N::translate('Use SMTP to send messages'),
		];
	}

	/**
	 * A list of temple options (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	public static function optionsTemples() {
		return ['' => I18N::translate('No temple - living ordinance')] + GedcomCodeTemp::templeNames();
	}

	/**
	 * A list of user options (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	public static function optionsUsers() {
		$options = ['' => '-'];

		foreach (User::all() as $user) {
			$options[$user->getUserName()] = $user->getRealName() . ' - ' . $user->getUserName();
		}

		return $options;
	}

	/**
	 * Create a form control to select a family.
	 *
	 * @param Tree        $tree
	 * @param Family|null $family
	 * @param string[]    $attributes
	 *
	 * @return string
	 */
	public static function formControlFamily(Tree $tree, Family $family = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($family !== null) {
			$value   = $family->getXref();
			$options = [$value => view('selects/family', ['family' => $family])];
		}

		return Bootstrap4::select($options, $value, Select2::familyConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a flag.
	 *
	 * @param string   $flag
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	public static function formControlFlag($flag, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($flag !== '') {
			$value   = $flag;
			$options = [$value => Select2::flagValue($flag)];
		}

		return Bootstrap4::select($options, $value, Select2::flagConfig() + $attributes);
	}

	/**
	 * Create a form control to select an individual.
	 *
	 * @param Tree            $tree
	 * @param Individual|null $individual
	 * @param string[]        $attributes
	 *
	 * @return string
	 */
	public static function formControlIndividual(Tree $tree, Individual $individual = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($individual !== null) {
			$value   = $individual->getXref();
			$options = [$value => view('selects/individual', ['individual' => $individual])];
		}

		return Bootstrap4::select($options, $value, Select2::individualConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a media object.
	 *
	 * @param Tree       $tree
	 * @param Media|null $media
	 * @param string[]   $attributes
	 *
	 * @return string
	 */
	public static function formControlMediaObject(Tree $tree, Media $media = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($media !== null) {
			$value   = $media->getXref();
			$options = [$value => view('selects/media', ['media' => $media])];
		}

		return Bootstrap4::select($options, $value, Select2::mediaObjectConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a note.
	 *
	 * @param Tree          $tree
	 * @param Note|null     $note
	 * @param string[]|null $attributes
	 *
	 * @return string
	 */
	public static function formControlNote(Tree $tree, Note $note = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($note !== null) {
			$value   = $note->getXref();
			$options = [$value => view('selects/note', ['note' => $note])];
		}

		return Bootstrap4::select($options, $value, Select2::noteConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a place.
	 *
	 * @param Tree     $tree
	 * @param string   $place
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	public static function formControlPlace(Tree $tree, $place, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($place !== '') {
			$options = [$place => $place];
		}

		return Bootstrap4::select($options, $value, Select2::placeConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a repository.
	 *
	 * @param Tree            $tree
	 * @param Repository|null $repository
	 * @param string[]        $attributes
	 *
	 * @return string
	 */
	public static function formControlRepository(Tree $tree, Repository $repository = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($repository !== null) {
			$value   = $repository->getXref();
			$options = [$value => view('selects/repository', ['repository' => $repository])];
		}

		return Bootstrap4::select($options, $value, Select2::repositoryConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a source.
	 *
	 * @param Tree        $tree
	 * @param Source|null $source
	 * @param string[]    $attributes
	 *
	 * @return string
	 */
	public static function formControlSource(Tree $tree, Source $source = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($source !== null) {
			$value   = $source->getXref();
			$options = [$value => view('selects/source', ['source' => $source])];
		}

		return Bootstrap4::select($options, $value, Select2::sourceConfig($tree) + $attributes);
	}

	/**
	 * Create a form control to select a submitter.
	 *
	 * @param Tree              $tree
	 * @param GedcomRecord|null $submitter
	 * @param string[]          $attributes
	 *
	 * @return string
	 */
	public static function formControlSubmitter(Tree $tree, GedcomRecord $submitter = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($submitter !== null) {
			$value   = $submitter->getXref();
			$options = [$value => view('selects/submitter', ['submitter' => $submitter])];
		}

		return Bootstrap4::select($options, $value, Select2::submitterConfig($tree) + $attributes);
	}

	/**
	 * Remove all links from $gedrec to $xref, and any sub-tags.
	 *
	 * @param string $gedrec
	 * @param string $xref
	 *
	 * @return string
	 */
	public static function removeLinks($gedrec, $xref) {
		$gedrec = preg_replace('/\n1 ' . WT_REGEX_TAG . ' @' . $xref . '@(\n[2-9].*)*/', '', $gedrec);
		$gedrec = preg_replace('/\n2 ' . WT_REGEX_TAG . ' @' . $xref . '@(\n[3-9].*)*/', '', $gedrec);
		$gedrec = preg_replace('/\n3 ' . WT_REGEX_TAG . ' @' . $xref . '@(\n[4-9].*)*/', '', $gedrec);
		$gedrec = preg_replace('/\n4 ' . WT_REGEX_TAG . ' @' . $xref . '@(\n[5-9].*)*/', '', $gedrec);
		$gedrec = preg_replace('/\n5 ' . WT_REGEX_TAG . ' @' . $xref . '@(\n[6-9].*)*/', '', $gedrec);

		return $gedrec;
	}

	/**
	 * Input addon to generate a calendar widget.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public static function inputAddonCalendar($id) {
		return
			'<div class="input-group-append">' .
			'<span class="input-group-text">' .
			FontAwesome::linkIcon('calendar', I18N::translate('Select a date'), ['href' => '#', 'onclick' => 'return calendarWidget("caldiv' . $id . '", "' . $id . '");']) .
			'</span>' .
			'</div>';
	}

	/**
	 * Input addon to select a special characterr using a virtual keyboard
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public static function inputAddonKeyboard($id) {
		return
			'<div class="input-group-append">' .
			'<span class="input-group-text">' .
			FontAwesome::linkIcon('keyboard', I18N::translate('Find a special character'), ['class' => 'wt-osk-trigger', 'href' => '#', 'data-id' => $id]) .
			'</span>' .
			'</div>';
	}

	/**
	 * Input addon to generate a help link.
	 *
	 * @param string $fact
	 *
	 * @return string
	 */
	public static function inputAddonHelp($fact) {
		return
			'<div class="input-group-append">' .
			'<span class="input-group-text">' .
			FunctionsPrint::helpLink($fact) .
			'</span>' .
			'</div>';
	}

	/**
	 * add a new tag input field
	 *
	 * called for each fact to be edited on a form.
	 * Fact level=0 means a new empty form : data are POSTed by name
	 * else data are POSTed using arrays :
	 * glevels[] : tag level
	 *  islink[] : tag is a link
	 *     tag[] : tag name
	 *    text[] : tag value
	 *
	 * @param string $tag fact record to edit (eg 2 DATE xxxxx)
	 * @param string $upperlevel optional upper level tag (eg BIRT)
	 * @param string $label An optional label to echo instead of the default
	 * @param string $extra optional text to display after the input field
	 * @param Individual $person For male/female translations
	 *
	 * @return string
	 */
	public static function addSimpleTag($tag, $upperlevel = '', $label = '', $extra = null, Individual $person = null) {
		global $tags, $xref, $bdm, $action, $WT_TREE;

		// Some form fields need access to previous form fields.
		static $previous_ids = ['SOUR' => '', 'PLAC' => ''];

		preg_match('/^(?:(\d+) (' . WT_REGEX_TAG . ') ?(.*))/', $tag, $match);
		list(, $level, $fact, $value) = $match;

		if ($level === '0') {
			if ($upperlevel) {
				$name = $upperlevel . '_' . $fact;
			} else {
				$name = $fact;
			}
		} else {
			$name = 'text[]';
		}

		if ($level === '0') {
			$id = $fact;
		} else {
			$id = $fact . Uuid::uuid4();
		}
		if ($upperlevel) {
			$id = $upperlevel . '_' . $fact . Uuid::uuid4();
		}

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

		// If using GEDFact-assistant window
		if ($action === 'addnewnote_assisted') {
			// Do not print on GEDFact Assistant window
		} else {
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
			$family = Family::getInstance($xref, $WT_TREE);
			if ($family) {
				$spouse_link = $family->getFirstFact($fact);
				if ($spouse_link) {
					$spouse = $spouse_link->getTarget();
					if ($spouse) {
						$html .= $spouse->getFullName();
					}
				}
			}
		}

		if (in_array($fact, Config::emptyFacts()) && ($value === '' || $value === 'Y' || $value === 'y')) {
			$html .= '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '">';

			if ($fact === 'CENS' && $value === 'Y') {
				$html .= self::censusDateSelector(WT_LOCALE, $xref);

				/** @var CensusAssistantModule $census_assistant */
				$census_assistant = Module::getModuleByName('GEDFact_assistant');
				$record           = Individual::getInstance($xref, $WT_TREE);
				if ($census_assistant !== null && $record instanceof Individual) {
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
			$html .= Bootstrap4::select(GedcomCodeAdop::getValues($person), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'ALIA') {
			$html .= self::formControlIndividual($WT_TREE, Individual::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'ASSO' || $fact === '_ASSO') {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" onclick="createNewRecord(' . $id . ')" title="' . I18N::translate('Create an individual') . '"><i class="fas fa-plus"></i></button></span>' .
				self::formControlIndividual($WT_TREE, Individual::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
			if ($level === '1') {
				$html .= '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this individual, such as a friend or an employer.') . '</p>';
			} else {
				$html .= '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this fact or event, such as a witness or a priest.') . '</p>';
			}
		} elseif ($fact === 'DATE') {
			$html .= '<div class="input-group">';
			$html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" onchange="valid_date(this)" dir="ltr">';
			$html .= self::inputAddonCalendar($id);
			$html .= self::inputAddonHelp('DATE');
			$html .= '</div>';
			$html .= '<div id="caldiv' . $id . '" style="position:absolute;visibility:hidden;background-color:white;z-index:1000"></div>';
			$html .= '<p class="text-muted">' . (new Date($value))->display() . '</p>';
		} elseif ($fact === 'FAMC') {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-family" data-element-id="' . $id . '" title="' . I18N::translate('Create a family') . '"><i class="fas fa-plus"></i></button></span>' .
				self::formControlFamily($WT_TREE, Family::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'LATI') {
			$html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="valid_lati_long(this, \'N\', \'S\')">';
		} elseif ($fact === 'LONG') {
			$html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" oninput="valid_lati_long(this, \'E\', \'W\')">';
		} elseif ($fact === 'NOTE' && $islink) {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn">' .
				'<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#wt-ajax-modal" data-href="' . e(route('create-note-object', ['tree' => $WT_TREE->getName()])) . '" data-select-id="' . $id . '" title="' . I18N::translate('Create a shared note') . '">' .
				'<i class="fas fa-plus"></i><' .
				'/button>' .
				'</span>' .
				self::formControlNote($WT_TREE, Note::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'OBJE') {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-media-object', ['tree' => $WT_TREE->getName()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a media object') . '"><i class="fas fa-plus"></i></button></span>' .
				self::formControlMediaObject($WT_TREE, Media::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'PAGE') {
			$html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '"   data-autocomplete-type="PAGE" data-autocomplete-extra="#' . $previous_ids['SOUR'] . '">';
		} elseif ($fact === 'PEDI') {
			$html .= Bootstrap4::select(GedcomCodePedi::getValues($person), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'PLAC') {
			$html .= '<input ' . Html::attributes([
					'autocomplete'          => 'off',
					'class'                 => 'form-control',
					'id'                    => $id,
					'name'                  => $name,
					'value'                 => $value,
					'type'                  => 'text',
					'data-autocomplete-url' => route('autocomplete-place', [
						'ged'   => $WT_TREE->getName(),
						'query' => 'QUERY',
					]),
			]) . '>';

			/** @TODO - typeaheadjs.css doesn't work in an input-group */
			$html .= '<div class="input-group">';
			$html .= '<div class="input-group-append"><span class="input-group-text">' . FontAwesome::linkIcon('coordinates', I18N::translate('Latitude') . ' / ' . I18N::translate('Longitude'), ['data-toggle' => 'collapse', 'data-target' => '.child_of_' . $id]) . '</span></div>';
			$html .= self::inputAddonHelp('PLAC');
			$html .= '</div>';
			if (Module::getModuleByName('places_assistant')) {
				\PlacesAssistantModule::setup_place_subfields($id);
				\PlacesAssistantModule::print_place_subfields($id);
			}
		} elseif ($fact === 'QUAY') {
			$html .= Bootstrap4::select(GedcomCodeQuay::getValues(), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'RELA') {
			$html .= Bootstrap4::select(FunctionsEdit::optionsRelationships($value), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'REPO') {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-repository', ['tree' => $WT_TREE->getName()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a repository') . '"><i class="fas fa-plus"></i></button></span>' .
				self::formControlRepository($WT_TREE, Repository::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'RESN') {
			$html .= '<div class="input-group">';
			$html .= Bootstrap4::select(FunctionsEdit::optionsRestrictions(true), $value, ['id' => $id, 'name' => $name]);
			$html .= self::inputAddonHelp('RESN');
			$html .= '</span>';
			$html .= '</div>';
		} elseif ($fact === 'SEX') {
			if ($value !== 'M' && $value !== 'F') {
				$value = 'U';
			}
			$html .= Bootstrap4::radioButtons($name, ['M' => I18N::translate('Male'), 'F' => I18N::translate('Female'), 'U' => I18N::translateContext('unknown gender', 'Unknown')], $value, true);
		} elseif ($fact === 'SOUR') {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-source', ['tree' => $WT_TREE->getName()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a source') . '"><i class="fas fa-plus"></i></button></span>' .
				self::formControlSource($WT_TREE, Source::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'STAT') {
			$html .= Bootstrap4::select(GedcomCodeStat::statusNames($upperlevel), $value);
		} elseif ($fact === 'SUBM') {
			$html .=
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-href="' . e(route('create-submitter', ['tree' => $WT_TREE->getName()])) . '" data-target="#wt-ajax-modal" data-select-id="' . $id . '" title="' . I18N::translate('Create a submitter') . '"><i class="fas fa-plus"></i></button></span>' .
				self::formControlSubmitter($WT_TREE, GedcomRecord::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'TEMP') {
			$html .= Bootstrap4::select(FunctionsEdit::optionsTemples(), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'TIME') {
			$html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" pattern="([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?" dir="ltr" placeholder="' . /* I18N: Examples of valid time formats (hours:minutes:seconds) */ I18N::translate('hh:mm or hh:mm:ss') . '">';
		} elseif ($fact === '_WT_USER') {
			$html .= Bootstrap4::select(FunctionsEdit::optionsUsers(), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === '_PRIM') {
			$html .= Bootstrap4::select(['' => '', 'Y' => I18N::translate('always'), 'N' => I18N::translate('never')], $value, ['id' => $id, 'name' => $name]);
			$html .= '<p class="small text-muted">' . I18N::translate('Use this image for charts and on the individual’s page.') . '</p>';
		} elseif ($fact === 'TYPE' && $level === '3') {
			//-- Build the selector for the Media 'TYPE' Fact
			$html .= '<select name="text[]"><option selected value="" ></option>';
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
		if ($fact === 'TYPE' && $level === '2' && $tags[0] === 'MARR') {
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
		} elseif ($fact === 'TYPE' && $level === '0') {
			// NAME TYPE : hide text field and show a selection list
			$html .= Bootstrap4::select(GedcomCodeName::getValues($person), $value, ['id' => $id, 'name' => $name, 'oninput' => 'document.getElementById(\'' . $id . '\').value=this.value"']);
			$html .= '<script>document.getElementById("' . $id . '").style.display="none";</script>';
		}

		// popup links
		switch ($fact) {
			case 'SOUR':
				//-- checkboxes to apply '1 SOUR' to BIRT/MARR/DEAT as '2 SOUR'
				if ($level === '1') {
					$html .= '<br>';
					switch ($WT_TREE->getPreference('PREFER_LEVEL2_SOURCES')) {
						case '2': // records
						$level1_checked = 'checked';
						$level2_checked = '';
						break;
						case '1': // facts
						$level1_checked = '';
						$level2_checked = 'checked';
						break;
						case '0': // none
						default:
						$level1_checked = '';
						$level2_checked = '';
						break;
					}
				if (strpos($bdm, 'B') !== false) {
					$html .= ' <label><input type="checkbox" name="SOUR_INDI" ' . $level1_checked . ' value="1">' . I18N::translate('Individual') . '</label>';
					if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
						foreach ($matches[1] as $match) {
							if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
								$html .= ' <label><input type="checkbox" name="SOUR_' . $match . '" ' . $level2_checked . ' value="1">' . GedcomTag::getLabel($match) . '</label>';
							}
						}
					}
				}
				if (strpos($bdm, 'D') !== false) {
					if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
						foreach ($matches[1] as $match) {
							if (in_array($match, explode('|', WT_EVENTS_DEAT))) {
								$html .= ' <label><input type="checkbox" name="SOUR_' . $match . '"' . $level2_checked . ' value="1">' . GedcomTag::getLabel($match) . '</label>';
							}
						}
					}
				}
				if (strpos($bdm, 'M') !== false) {
					$html .= ' <label><input type="checkbox" name="SOUR_FAM" ' . $level1_checked . ' value="1">' . I18N::translate('Family') . '</label>';
					if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
						foreach ($matches[1] as $match) {
							$html .= ' <label><input type="checkbox" name="SOUR_' . $match . '"' . $level2_checked . ' value="1">' . GedcomTag::getLabel($match) . '</label>';
						}
					}
				}
			}
				break;
		}

		$html .= $extra . '</div></div>';

		return $html;
	}

	/**
	 * Genearate a <select> element, with the dates/places of all known censuses
	 *
	 *
	 * @param string $locale Sort the censuses for this locale
	 * @param string $xref   The individual for whom we are adding a census
	 *
	 * @return string
	 */
	public static function censusDateSelector($locale, $xref) {
		global $controller;

		// Show more likely census details at the top of the list.
		switch ($locale) {
			case 'cs':
				$census_places = [new CensusOfCzechRepublic];
				break;
			case 'en-AU':
			case 'en-GB':
				$census_places = [new CensusOfEngland, new CensusOfWales, new CensusOfScotland];
				break;
			case 'en-US':
				$census_places = [new CensusOfUnitedStates];
				break;
			case 'fr':
			case 'fr-CA':
				$census_places = [new CensusOfFrance];
				break;
			case 'da':
				$census_places = [new CensusOfDenmark];
				break;
			case 'de':
				$census_places = [new CensusOfDeutschland];
				break;
			default:
				$census_places = [];
				break;
		}
		foreach (Census::allCensusPlaces() as $census_place) {
			if (!in_array($census_place, $census_places)) {
				$census_places[] = $census_place;
			}
		}

		$controller->addInlineJavascript('
				function selectCensus(el) {
					var option = $(":selected", el);
					$("input[id^=CENS_DATE]", $(el).closest("form")).val(option.val());
					//$("input[id^=CENS_PLAC]", $(el).closest("form")).val(option.data("place"));
					var place = option.data("place");
					$("select[id^=CENS_PLAC]", $(el).closest("form")).select2().empty().append(new Option(place, place)).val(place).trigger("change");

					$("input.census-class", $(el).closest("form")).val(option.data("census"));
				}
			');

		$options = '<option value="">' . I18N::translate('Census date') . '</option>';

		foreach ($census_places as $census_place) {
			$options .= '<option value=""></option>';
			foreach ($census_place->allCensusDates() as $census) {
				$date            = new Date($census->censusDate());
				$year            = $date->minimumDate()->format('%Y');
				$place_hierarchy = explode(', ', $census->censusPlace());
				$options .= '<option value="' . $census->censusDate() . '" data-place="' . $census->censusPlace() . '" data-census="' . get_class($census) . '">' . $place_hierarchy[0] . ' ' . $year . '</option>';
			}
		}

		return
			'<select id="census-selector" class="form-control" onchange="selectCensus(this)">' . $options . '</select>';
	}

	/**
	 * Prints collapsable fields to add ASSO/RELA, SOUR, OBJE, etc.
	 *
	 * @param string $tag
	 * @param int    $level
	 * @param string $parent_tag
	 */
	public static function printAddLayer($tag, $level = 2, $parent_tag = '') {
		global $WT_TREE;

		switch ($tag) {
			case 'SOUR':
				echo view('cards/add-source-citation', [
					'level'          => $level,
					'full_citations' => $WT_TREE->getPreference('FULL_SOURCES'),
				]);
				break;

			case 'ASSO':
			case 'ASSO2':
				echo view('cards/add-associate', [
					'level' => $level,
				]);
				break;

			case 'NOTE':
				echo view('cards/add-note', [
					'level' => $level,
				]);
				break;

			case 'SHARED_NOTE':
				echo view('cards/add-shared-note', [
					'level'      => $level,
					'parent_tag' => $parent_tag,
				]);
				break;

			case 'OBJE':
				if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($WT_TREE)) {
					echo view('cards/add-media-object', [
						'level' => $level,
					]);
				}
				break;

			case 'RESN':
				echo view('cards/add-restriction', [
					'level' => $level,
				]);
				break;
		}
	}

	/**
	 * Add some empty tags to create a new fact.
	 *
	 * @param string $fact
	 */
	public static function addSimpleTags($fact) {
		global $WT_TREE;

		// For new individuals, these facts default to "Y"
		if ($fact === 'MARR') {
			echo self::addSimpleTag('0 ' . $fact . ' Y');
		} else {
			echo self::addSimpleTag('0 ' . $fact);
		}

		if (!in_array($fact, Config::nonDateFacts())) {
			echo self::addSimpleTag('0 DATE', $fact, GedcomTag::getLabel($fact . ':DATE'));
			echo self::addSimpleTag('0 RELI', $fact, GedcomTag::getLabel($fact . ':RELI'));
		}

		if (!in_array($fact, Config::nonPlaceFacts())) {
			echo self::addSimpleTag('0 PLAC', $fact, GedcomTag::getLabel($fact . ':PLAC'));

			if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
				foreach ($match[1] as $tag) {
					echo self::addSimpleTag('0 ' . $tag, $fact, GedcomTag::getLabel($fact . ':PLAC:' . $tag));
				}
			}
			echo self::addSimpleTag('0 MAP', $fact);
			echo self::addSimpleTag('0 LATI', $fact);
			echo self::addSimpleTag('0 LONG', $fact);
		}
	}

	/**
	 * Assemble the pieces of a newly created record into gedcom
	 *
	 * @return string
	 */
	public static function addNewName() {
		global $WT_TREE;

		$gedrec = "\n1 NAME " . Filter::post('NAME');

		$tags = ['NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX'];

		if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_NAME_FACTS'), $match)) {
			$tags = array_merge($tags, $match[1]);
		}

		// Paternal and Polish and Lithuanian surname traditions can also create a _MARNM
		$SURNAME_TRADITION = $WT_TREE->getPreference('SURNAME_TRADITION');
		if ($SURNAME_TRADITION === 'paternal' || $SURNAME_TRADITION === 'polish' || $SURNAME_TRADITION === 'lithuanian') {
			$tags[] = '_MARNM';
		}

		foreach (array_unique($tags) as $tag) {
			$TAG = Filter::post($tag);
			if ($TAG) {
				$gedrec .= "\n2 {$tag} {$TAG}";
			}
		}

		return $gedrec;
	}

	/**
	 * Create a form to add a sex record.
	 *
	 * @return string
	 */
	public static function addNewSex() {
		switch (Filter::post('SEX', '[MF]', 'U')) {
			case 'M':
				return "\n1 SEX M";
			case 'F':
				return "\n1 SEX F";
			default:
				return "\n1 SEX U";
		}
	}

	/**
	 * Create a form to add a new fact.
	 *
	 * @param string $fact
	 *
	 * @return string
	 */
	public static function addNewFact($fact) {
		global $WT_TREE;

		$FACT = Filter::post($fact);
		$DATE = Filter::post($fact . '_DATE');
		$PLAC = Filter::post($fact . '_PLAC');
		$PLAC = Filter::post($fact . '_RELI');
		if ($DATE || $PLAC || $FACT && $FACT !== 'Y') {
			if ($FACT && $FACT !== 'Y') {
				$gedrec = "\n1 " . $fact . ' ' . $FACT;
			} else {
				$gedrec = "\n1 " . $fact;
			}
			if ($DATE) {
				$gedrec .= "\n2 DATE " . $DATE;
			}
			if ($PLAC) {
				$gedrec .= "\n2 PLAC " . $PLAC;

				if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
					foreach ($match[1] as $tag) {
						$TAG = Filter::post($fact . '_' . $tag);
						if ($TAG) {
							$gedrec .= "\n3 " . $tag . ' ' . $TAG;
						}
					}
				}
				$LATI = Filter::post($fact . '_LATI');
				$LONG = Filter::post($fact . '_LONG');
				if ($LATI || $LONG) {
					$gedrec .= "\n3 MAP\n4 LATI " . $LATI . "\n4 LONG " . $LONG;
				}
			}
			if (Filter::postBool('SOUR_' . $fact)) {
				return self::updateSource($gedrec, 2);
			} else {
				return $gedrec;
			}
		} elseif ($FACT === 'Y') {
			if (Filter::postBool('SOUR_' . $fact)) {
				return self::updateSource("\n1 " . $fact . ' Y', 2);
			} else {
				return "\n1 " . $fact . ' Y';
			}
		} else {
			return '';
		}
	}

	/**
	 * This function splits the $glevels, $tag, $islink, and $text arrays so that the
	 * entries associated with a SOUR record are separate from everything else.
	 *
	 * Input arrays:
	 * - $glevels[] - an array of the gedcom level for each line that was edited
	 * - $tag[] - an array of the tags for each gedcom line that was edited
	 * - $islink[] - an array of 1 or 0 values to indicate when the text is a link element
	 * - $text[] - an array of the text data for each line
	 *
	 * Output arrays:
	 * ** For the SOUR record:
	 * - $glevelsSOUR[] - an array of the gedcom level for each line that was edited
	 * - $tagSOUR[] - an array of the tags for each gedcom line that was edited
	 * - $islinkSOUR[] - an array of 1 or 0 values to indicate when the text is a link element
	 * - $textSOUR[] - an array of the text data for each line
	 * ** For the remaining records:
	 * - $glevelsRest[] - an array of the gedcom level for each line that was edited
	 * - $tagRest[] - an array of the tags for each gedcom line that was edited
	 * - $islinkRest[] - an array of 1 or 0 values to indicate when the text is a link element
	 * - $textRest[] - an array of the text data for each line
	 */
	public static function splitSource() {
		global $glevels, $tag, $islink, $text;
		global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
		global $glevelsRest, $tagRest, $islinkRest, $textRest;

		$glevelsSOUR = [];
		$tagSOUR     = [];
		$islinkSOUR  = [];
		$textSOUR    = [];

		$glevelsRest = [];
		$tagRest     = [];
		$islinkRest  = [];
		$textRest    = [];

		$inSOUR = false;

		for ($i = 0; $i < count($glevels); $i++) {
			if ($inSOUR) {
				if ($levelSOUR < $glevels[$i]) {
					$dest = 'S';
				} else {
					$inSOUR = false;
					$dest   = 'R';
				}
			} else {
				if ($tag[$i] === 'SOUR') {
					$inSOUR    = true;
					$levelSOUR = $glevels[$i];
					$dest      = 'S';
				} else {
					$dest = 'R';
				}
			}
			if ($dest === 'S') {
				$glevelsSOUR[] = $glevels[$i];
				$tagSOUR[]     = $tag[$i];
				$islinkSOUR[]  = $islink[$i];
				$textSOUR[]    = $text[$i];
			} else {
				$glevelsRest[] = $glevels[$i];
				$tagRest[]     = $tag[$i];
				$islinkRest[]  = $islink[$i];
				$textRest[]    = $text[$i];
			}
		}
	}

	/**
	 * Add new GEDCOM lines from the $xxxSOUR interface update arrays, which
	 * were produced by the splitSOUR() function.
	 * See the FunctionsEdit::handle_updatesges() function for details.
	 *
	 * @param string $inputRec
	 * @param string $levelOverride
	 *
	 * @return string
	 */
	public static function updateSource($inputRec, $levelOverride = 'no') {
		global $glevels, $tag, $islink, $text;
		global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;

		if (count($tagSOUR) === 0) {
			return $inputRec; // No update required
		}

		// Save original interface update arrays before replacing them with the xxxSOUR ones
		$glevelsSave = $glevels;
		$tagSave     = $tag;
		$islinkSave  = $islink;
		$textSave    = $text;

		$glevels = $glevelsSOUR;
		$tag     = $tagSOUR;
		$islink  = $islinkSOUR;
		$text    = $textSOUR;

		$myRecord = self::handleUpdates($inputRec, $levelOverride); // Now do the update

		// Restore the original interface update arrays (just in case ...)
		$glevels = $glevelsSave;
		$tag     = $tagSave;
		$islink  = $islinkSave;
		$text    = $textSave;

		return $myRecord;
	}

	/**
	 * Add new GEDCOM lines from the $xxxRest interface update arrays, which
	 * were produced by the splitSOUR() function.
	 * See the FunctionsEdit::handle_updatesges() function for details.
	 *
	 * @param string $inputRec
	 * @param string $levelOverride
	 *
	 * @return string
	 */
	public static function updateRest($inputRec, $levelOverride = 'no') {
		global $glevels, $tag, $islink, $text;
		global $glevelsRest, $tagRest, $islinkRest, $textRest;

		if (count($tagRest) === 0) {
			return $inputRec; // No update required
		}

		// Save original interface update arrays before replacing them with the xxxRest ones
		$glevelsSave = $glevels;
		$tagSave     = $tag;
		$islinkSave  = $islink;
		$textSave    = $text;

		$glevels = $glevelsRest;
		$tag     = $tagRest;
		$islink  = $islinkRest;
		$text    = $textRest;

		$myRecord = self::handleUpdates($inputRec, $levelOverride); // Now do the update

		// Restore the original interface update arrays (just in case ...)
		$glevels = $glevelsSave;
		$tag     = $tagSave;
		$islink  = $islinkSave;
		$text    = $textSave;

		return $myRecord;
	}

	/**
	 * Add new gedcom lines from interface update arrays
	 * The edit_interface and FunctionsEdit::add_simple_tag function produce the following
	 * arrays incoming from the $_POST form
	 * - $glevels[] - an array of the gedcom level for each line that was edited
	 * - $tag[] - an array of the tags for each gedcom line that was edited
	 * - $islink[] - an array of 1 or 0 values to tell whether the text is a link element and should be surrounded by @@
	 * - $text[] - an array of the text data for each line
	 * With these arrays you can recreate the gedcom lines like this
	 * <code>$glevel[0].' '.$tag[0].' '.$text[0]</code>
	 * There will be an index in each of these arrays for each line of the gedcom
	 * fact that is being edited.
	 * If the $text[] array is empty for the given line, then it means that the
	 * user removed that line during editing or that the line is supposed to be
	 * empty (1 DEAT, 1 BIRT) for example. To know if the line should be removed
	 * there is a section of code that looks ahead to the next lines to see if there
	 * are sub lines. For example we don't want to remove the 1 DEAT line if it has
	 * a 2 PLAC or 2 DATE line following it. If there are no sub lines, then the line
	 * can be safely removed.
	 *
	 * @param string $newged the new gedcom record to add the lines to
	 * @param string $levelOverride Override GEDCOM level specified in $glevels[0]
	 *
	 * @return string The updated gedcom record
	 */
	public static function handleUpdates($newged, $levelOverride = 'no') {
		global $glevels, $islink, $tag, $uploaded_files, $text;

		if ($levelOverride === 'no' || count($glevels) === 0) {
			$levelAdjust = 0;
		} else {
			$levelAdjust = $levelOverride - $glevels[0];
		}

		for ($j = 0; $j < count($glevels); $j++) {

			// Look for empty SOUR reference with non-empty sub-records.
			// This can happen when the SOUR entry is deleted but its sub-records
			// were incorrectly left intact.
			// The sub-records should be deleted.
			if ($tag[$j] === 'SOUR' && ($text[$j] === '@@' || $text[$j] === '')) {
				$text[$j] = '';
				$k        = $j + 1;
				while (($k < count($glevels)) && ($glevels[$k] > $glevels[$j])) {
					$text[$k] = '';
					$k++;
				}
			}

			if (trim($text[$j]) !== '') {
				$pass = true;
			} else {
				//-- for facts with empty values they must have sub records
				//-- this section checks if they have subrecords
				$k    = $j + 1;
				$pass = false;
				while (($k < count($glevels)) && ($glevels[$k] > $glevels[$j])) {
					if ($text[$k] !== '') {
						if (($tag[$j] !== 'OBJE') || ($tag[$k] === 'FILE')) {
							$pass = true;
							break;
						}
					}
					if (($tag[$k] === 'FILE') && (count($uploaded_files) > 0)) {
						$filename = array_shift($uploaded_files);
						if (!empty($filename)) {
							$text[$k] = $filename;
							$pass     = true;
							break;
						}
					}
					$k++;
				}
			}

			//-- if the value is not empty or it has sub lines
			//--- then write the line to the gedcom record
			//-- we have to let some emtpy text lines pass through... (DEAT, BIRT, etc)
			if ($pass) {
				$newline = $glevels[$j] + $levelAdjust . ' ' . $tag[$j];
				if ($text[$j] !== '') {
					if ($islink[$j]) {
						$newline .= ' @' . $text[$j] . '@';
					} else {
						$newline .= ' ' . $text[$j];
					}
				}
				$newged .= "\n" . str_replace("\n", "\n" . (1 + substr($newline, 0, 1)) . ' CONT ', $newline);
			}
		}

		return $newged;
	}

	/**
	 * builds the form for adding new facts
	 *
	 * @param string $fact the new fact we are adding
	 */
	public static function createAddForm($fact) {
		global $tags, $WT_TREE;

		$tags = [];

		// handle  MARRiage TYPE
		if (substr($fact, 0, 5) === 'MARR_') {
			$tags[0] = 'MARR';
			echo self::addSimpleTag('1 MARR');
			self::insertMissingSubtags($fact);
		} else {
			$tags[0] = $fact;
			if ($fact === '_UID') {
				$fact .= ' ' . GedcomTag::createUid();
			}
			// These new level 1 tags need to be turned into links
			if (in_array($fact, ['ALIA', 'ASSO'])) {
				$fact .= ' @';
			}
			if (in_array($fact, Config::emptyFacts())) {
				echo self::addSimpleTag('1 ' . $fact . ' Y');
			} else {
				echo self::addSimpleTag('1 ' . $fact);
			}
			self::insertMissingSubtags($tags[0]);
			//-- handle the special SOURce case for level 1 sources [ 1759246 ]
			if ($fact === 'SOUR') {
				echo self::addSimpleTag('2 PAGE');
				echo self::addSimpleTag('3 TEXT');
				if ($WT_TREE->getPreference('FULL_SOURCES')) {
					echo self::addSimpleTag('3 DATE', '', GedcomTag::getLabel('DATA:DATE'));
					echo self::addSimpleTag('2 QUAY');
				}
			}
		}
	}

	/**
	 * Create a form to edit a Fact object.
	 *
	 * @param Fact $fact
	 */
	public static function createEditForm(Fact $fact) {
		global $tags;

		$record = $fact->getParent();

		$tags     = [];
		$gedlines = explode("\n", $fact->getGedcom());

		$linenum = 0;
		$fields  = explode(' ', $gedlines[$linenum]);
		$glevel  = $fields[0];
		$level   = $glevel;

		$type       = $fact->getTag();
		$level0type = $record::RECORD_TYPE;
		$level1type = $type;

		$i           = $linenum;
		$inSource    = false;
		$levelSource = 0;
		$add_date    = true;

		// List of tags we would expect at the next level
		// NB insertMissingSubtags() already takes care of the simple cases
		// where a level 1 tag is missing a level 2 tag. Here we only need to
		// handle the more complicated cases.
		$expected_subtags = [
			'SOUR' => ['PAGE', 'DATA'],
			'DATA' => ['TEXT'],
			'PLAC' => ['MAP'],
			'MAP'  => ['LATI', 'LONG'],
		];
		if ($record->getTree()->getPreference('FULL_SOURCES')) {
			$expected_subtags['SOUR'][] = 'QUAY';
			$expected_subtags['DATA'][] = 'DATE';
		}
		if (GedcomCodeTemp::isTagLDS($level1type)) {
			$expected_subtags['STAT'] = ['DATE'];
		}
		if (in_array($level1type, Config::dateAndTime())) {
			$expected_subtags['DATE'] = ['TIME']; // TIME is NOT a valid 5.5.1 tag
		}
		if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $record->getTree()->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
			$expected_subtags['PLAC'] = array_merge($match[1], $expected_subtags['PLAC']);
		}

		$stack = [];
		// Loop on existing tags :
		while (true) {
			// Keep track of our hierarchy, e.g. 1=>BIRT, 2=>PLAC, 3=>FONE
			$stack[$level] = $type;
			// Merge them together, e.g. BIRT:PLAC:FONE
			$label = implode(':', array_slice($stack, 0, $level));

			$text = '';
			for ($j = 2; $j < count($fields); $j++) {
				if ($j > 2) {
					$text .= ' ';
				}
				$text .= $fields[$j];
			}
			$text = rtrim($text);
			while (($i + 1 < count($gedlines)) && (preg_match('/' . ($level + 1) . ' CONT ?(.*)/', $gedlines[$i + 1], $cmatch) > 0)) {
				$text .= "\n" . $cmatch[1];
				$i++;
			}

			if ($type === 'SOUR') {
				$inSource    = true;
				$levelSource = $level;
			} elseif ($levelSource >= $level) {
				$inSource = false;
			}

			if ($type !== 'CONT') {
				$tags[]    = $type;
				$subrecord = $level . ' ' . $type . ' ' . $text;
				if ($inSource && $type === 'DATE') {
					echo self::addSimpleTag($subrecord, '', GedcomTag::getLabel($label, $record));
				} elseif (!$inSource && $type === 'DATE') {
					echo self::addSimpleTag($subrecord, $level1type, GedcomTag::getLabel($label, $record));
					if ($level === '2') {
						// We already have a date - no need to add one.
						$add_date = false;
					}
				} elseif ($type === 'STAT') {
					echo self::addSimpleTag($subrecord, $level1type, GedcomTag::getLabel($label, $record));
				} else {
					echo self::addSimpleTag($subrecord, $level0type, GedcomTag::getLabel($label, $record));
				}
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
						echo self::addSimpleTag(($level + 1) . ' ' . $subtag, '', GedcomTag::getLabel($label . ':' . $subtag));
						if (!empty($expected_subtags[$subtag])) {
							foreach ($expected_subtags[$subtag] as $subsubtag) {
								echo self::addSimpleTag(($level + 2) . ' ' . $subsubtag, '', GedcomTag::getLabel($label . ':' . $subtag . ':' . $subsubtag));
							}
						}
					}
				}
			}

			$i++;
			if (isset($gedlines[$i])) {
				$fields = explode(' ', $gedlines[$i]);
				$level  = $fields[0];
				if (isset($fields[1])) {
					$type = trim($fields[1]);
				} else {
					$level = 0;
				}
			} else {
				$level = 0;
			}
			if ($level <= $glevel) {
				break;
			}
		}

		if ($level1type !== '_PRIM') {
			self::insertMissingSubtags($level1type, $add_date);
		}
	}

	/**
	 * Populates the global $tags array with any missing sub-tags.
	 *
	 * @param string $level1tag the type of the level 1 gedcom record
	 * @param bool $add_date
	 */
	public static function insertMissingSubtags($level1tag, $add_date = false) {
		global $tags, $WT_TREE;

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
			if (in_array($level1tag, $value) && !in_array($key, $tags)) {
				if ($key === 'TYPE') {
					echo self::addSimpleTag('2 TYPE ' . $type_val, $level1tag);
				} elseif ($level1tag === '_TODO' && $key === 'DATE') {
					echo self::addSimpleTag('2 ' . $key . ' ' . strtoupper(date('d M Y')), $level1tag);
				} elseif ($level1tag === '_TODO' && $key === '_WT_USER') {
					echo self::addSimpleTag('2 ' . $key . ' ' . Auth::user()->getUserName(), $level1tag);
				} elseif ($level1tag === 'NAME' && strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), $key) !== false) {
					echo self::addSimpleTag('2 ' . $key, $level1tag);
				} elseif ($level1tag !== 'NAME') {
					echo self::addSimpleTag('2 ' . $key, $level1tag);
				}
				// Add level 3/4 tags as appropriate
				switch ($key) {
					case 'PLAC':
						if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
							foreach ($match[1] as $tag) {
								echo self::addSimpleTag('3 ' . $tag, '', GedcomTag::getLabel($level1tag . ':PLAC:' . $tag));
							}
						}
						echo self::addSimpleTag('3 MAP');
						echo self::addSimpleTag('4 LATI');
						echo self::addSimpleTag('4 LONG');
						break;
					case 'FILE':
						echo self::addSimpleTag('3 FORM');
						break;
					case 'EVEN':
						echo self::addSimpleTag('3 DATE');
						echo self::addSimpleTag('3 PLAC');
						break;
					case 'STAT':
						if (GedcomCodeTemp::isTagLDS($level1tag)) {
							echo self::addSimpleTag('3 DATE', '', GedcomTag::getLabel('STAT:DATE'));
						}
						break;
					case 'DATE':
						// TIME is NOT a valid 5.5.1 tag
						if (in_array($level1tag, Config::dateAndTime())) {
							echo self::addSimpleTag('3 TIME');
						}
						break;
					case 'HUSB':
					case 'WIFE':
						echo self::addSimpleTag('3 AGE');
						break;
					case 'FAMC':
						if ($level1tag === 'ADOP') {
							echo self::addSimpleTag('3 ADOP BOTH');
						}
						break;
				}
			} elseif ($key === 'DATE' && $add_date) {
				echo self::addSimpleTag('2 DATE', $level1tag, GedcomTag::getLabel($level1tag . ':DATE'));
			}
		}
		// Do something (anything!) with unrecognized custom tags
		if (substr($level1tag, 0, 1) === '_' && $level1tag !== '_UID' && $level1tag !== '_PRIM' && $level1tag !== '_TODO') {
			foreach (['DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE'] as $tag) {
				if (!in_array($tag, $tags)) {
					echo self::addSimpleTag('2 ' . $tag);
					if ($tag === 'PLAC') {
						if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
							foreach ($match[1] as $ptag) {
								echo self::addSimpleTag('3 ' . $ptag, '', GedcomTag::getLabel($level1tag . ':PLAC:' . $ptag));
							}
						}
						echo self::addSimpleTag('3 MAP');
						echo self::addSimpleTag('4 LATI');
						echo self::addSimpleTag('4 LONG');
					}
				}
			}
		}
	}
}
