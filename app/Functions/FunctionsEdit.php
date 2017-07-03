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
use Rhumsaa\Uuid\Uuid;

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
			$html .= '<div class="checkbox">';
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
	 * A list of registration rules (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	public static function optionsRegistrationRules() {
		return [
			0 => I18N::translate('No predefined text'),
			1 => I18N::translate('Predefined text that states all users can request a user account'),
			2 => I18N::translate('Predefined text that states admin will decide on each request for a user account'),
			3 => I18N::translate('Predefined text that states only family members can request a user account'),
			4 => I18N::translate('Choose user defined welcome text typed below'),
		];
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
	 * A list SSL modes (e.g. for an edit control).
	 *
	 * @return string[]
	 */
	public static function optionsSslModes() {
		return [
			'none'                                                                        => I18N::translate('none'),
			/* I18N: Secure Sockets Layer - a secure communications protocol*/ 'ssl'      => I18N::translate('ssl'),
			/* I18N: Transport Layer Security - a secure communications protocol */ 'tls' => I18N::translate('tls'),
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
	 * @param Family|null $family
	 * @param string[]    $attributes
	 *
	 * @return string
	 */
	public static function formControlFamily(Family $family = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($family !== null) {
			$value   = $family->getXref();
			$options = [$value => Select2::familyValue($family)];
		}

		return Bootstrap4::select($options, $value, Select2::familyConfig() + $attributes);
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
	 * @param Individual|null $individual
	 * @param string[]        $attributes
	 *
	 * @return string
	 */
	public static function formControlIndividual(Individual $individual = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($individual !== null) {
			$value   = $individual->getXref();
			$options = [$value => Select2::individualValue($individual)];
		}

		return Bootstrap4::select($options, $value, Select2::individualConfig() + $attributes);
	}

	/**
	 * Create a form control to select a media object.
	 *
	 * @param Media|null $media
	 * @param string[]   $attributes
	 *
	 * @return string
	 */
	public static function formControlMediaObject(Media $media = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($media !== null) {
			$value   = $media->getXref();
			$options = [$value => Select2::mediaObjectValue($media)];
		}

		return Bootstrap4::select($options, $value, Select2::mediaObjectConfig() + $attributes);
	}

	/**
	 * Create a form control to select a note.
	 *
	 * @param Note|null     $note
	 * @param string[]|null $attributes
	 *
	 * @return string
	 */
	public static function formControlNote(Note $note = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($note !== null) {
			$value   = $note->getXref();
			$options = [$value => Select2::noteValue($note)];
		}

		return Bootstrap4::select($options, $value, Select2::noteConfig() + $attributes);
	}

	/**
	 * Create a form control to select a place.
	 *
	 * @param string   $place
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	public static function formControlPlace($place, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($place !== '') {
			$options = [$place => $place];
		}

		return Bootstrap4::select($options, $value, Select2::placeConfig() + $attributes);
	}

	/**
	 * Create a form control to select a repository.
	 *
	 * @param Repository|null $repository
	 * @param string[]        $attributes
	 *
	 * @return string
	 */
	public static function formControlRepository(Repository $repository = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($repository !== null) {
			$value   = $repository->getXref();
			$options = [$value => Select2::repositoryValue($repository)];
		}

		return Bootstrap4::select($options, $value, Select2::repositoryConfig() + $attributes);
	}

	/**
	 * Create a form control to select a source.
	 *
	 * @param Source|null $source
	 * @param string[]    $attributes
	 *
	 * @return string
	 */
	public static function formControlSource(Source $source = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($source !== null) {
			$value   = $source->getXref();
			$options = [$value => Select2::sourceValue($source)];
		}

		return Bootstrap4::select($options, $value, Select2::sourceConfig() + $attributes);
	}

	/**
	 * Create a form control to select a submitter.
	 *
	 * @param GedcomRecord|null $submitter
	 * @param string[]          $attributes
	 *
	 * @return string
	 */
	public static function formControlSubmitter(GedcomRecord $submitter = null, array $attributes = []) {
		$value   = '';
		$options = ['' => ''];

		if ($submitter !== null) {
			$value   = $submitter->getXref();
			$options = [$value => Select2::submitterValue($submitter)];
		}

		return Bootstrap4::select($options, $value, Select2::submitterConfig() + $attributes);
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
			'<span class="input-group-addon">' .
			FontAwesome::linkIcon('calendar', I18N::translate('Select a date'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return calendarWidget("caldiv' . $id . '", "' . $id . '");']) .
			'</span>';
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
			'<span class="input-group-addon">' .
			FontAwesome::linkIcon('keyboard', I18N::translate('Find a special character'), ['class' => 'btn btn-link wt-osk-trigger', 'href' => '#', 'data-id' => $id]) .
			'</span>';
	}

	/**
	 * Input addon to generate a help link.
	 *
	 * @param string $fact
	 *
	 * @return string
	 */
	public static function inputAddonHelp($fact) {
		return '<span class="input-group-addon">' . FunctionsPrint::helpLink($fact) . '</span>';
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
			$value = trim(substr($tag, strlen($fact) + 3), ' @\r');
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
				$row_class .= ' hidden-xs-up';
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

		echo '<div class="' . $row_class . '">';
		echo '<label class="col-sm-3 col-form-label" for="' . $id . '">';

		// tag name
		if ($label) {
			echo $label;
		} elseif ($upperlevel) {
			echo GedcomTag::getLabel($upperlevel . ':' . $fact);
		} else {
			echo GedcomTag::getLabel($fact);
		}

		// If using GEDFact-assistant window
		if ($action === 'addnewnote_assisted') {
			// Do not print on GEDFact Assistant window
		} else {
			// Not all facts have help text.
			switch ($fact) {
			case 'NAME':
				if ($upperlevel !== 'REPO' && $upperlevel !== 'UNKNOWN') {
					echo FunctionsPrint::helpLink($fact);
				}
				break;
			case 'ROMN':
			case 'SURN':
			case '_HEB':
				echo FunctionsPrint::helpLink($fact);
				break;
			}
		}
		// tag level
		if ($level !== '0') {
			echo '<input type="hidden" name="glevels[]" value="', $level, '">';
			echo '<input type="hidden" name="islink[]" value="', $islink, '">';
			echo '<input type="hidden" name="tag[]" value="', $fact, '">';
		}
		echo '</label>';

		// value
		echo '<div class="col-sm-9">';

		// Show names for spouses in MARR/HUSB/AGE and MARR/WIFE/AGE
		if ($fact === 'HUSB' || $fact === 'WIFE') {
			$family = Family::getInstance($xref, $WT_TREE);
			if ($family) {
				$spouse_link = $family->getFirstFact($fact);
				if ($spouse_link) {
					$spouse = $spouse_link->getTarget();
					if ($spouse) {
						echo $spouse->getFullName();
					}
				}
			}
		}

		if (in_array($fact, Config::emptyFacts()) && ($value === '' || $value === 'Y' || $value === 'y')) {
			echo '<input type="hidden" id="', $id, '" name="', $name, '" value="', $value, '">';

			if ($fact === 'CENS' && $value === 'Y') {
				echo self::censusDateSelector(WT_LOCALE, $xref);

				/** @var CensusAssistantModule $census_assistant */
				$census_assistant = Module::getModuleByName('GEDFact_assistant');
				$record = Individual::getInstance($xref, $WT_TREE);
				if ($census_assistant !== null && $record instanceof Individual) {
					$census_assistant->createCensusAssistant($record);
				}
			}
		} elseif ($fact === 'NPFX' || $fact === 'NSFX' || $fact === 'SPFX' || $fact === 'NICK') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" oninput="updatewholename()">';
		} elseif ($fact === 'GIVN') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" data-autocomplete-type="GIVN" oninput="updatewholename()" autofocus>';
		} elseif ($fact === 'SURN' || $fact === '_MARNM_SURN') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" data-autocomplete-type="SURN" oninput="updatewholename()">';
		} elseif ($fact === 'ADOP') {
			echo Bootstrap4::select(GedcomCodeAdop::getValues($person), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'ALIA') {
			echo self::formControlIndividual(Individual::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'ASSO' || $fact === '_ASSO') {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" onclick="createNewRecord(' . $id . ')" title="' . I18N::translate('Create an individual') . '"><i class="fa fa-plus"></i></button></span>' .
				self::formControlIndividual(Individual::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
			if ($level === '1') {
				echo '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this individual, such as a friend or an employer.') . '</p>';
			} else {
				echo '<p class="small text-muted">' . I18N::translate('An associate is another individual who was involved with this fact or event, such as a witness or a priest.') . '</p>';
			}
		} elseif ($fact === 'DATE') {
			echo '<div class="input-group">';
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" oninput="valid_date(this)">';
			echo self::inputAddonCalendar($id);
			echo self::inputAddonHelp('DATE');
			echo '</div>';
			echo '<div id="caldiv' . $id . '" style="position:absolute;visibility:hidden;background-color:white;z-index:1000"></div>';
			echo 	'<p class="text-muted">' . (new Date($value))->display() . '</p>';
		} elseif ($fact === 'FAMC') {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-family" data-element-id="' . $id . '" title="' . I18N::translate('Create a family') . '"><i class="fa fa-plus"></i></button></span>' .
				self::formControlFamily(Family::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'LATI') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" oninput="valid_lati_long(this, \'N\', \'S\')">';
		} elseif ($fact === 'LONG') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" oninput="valid_lati_long(this, \'E\', \'W\')">';
		} elseif ($fact === 'NOTE' && $islink) {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-note-object" data-element-id="' . $id . '" title="' . I18N::translate('Create a shared note') . '"><i class="fa fa-plus"></i></button></span>' .
				self::formControlNote(Note::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'OBJE') {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-media-object" data-element-id="' . $id . '" title="' . I18N::translate('Create a media object') . '"><i class="fa fa-plus"></i></button></span>' .
				self::formControlMediaObject(Media::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'PAGE') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '"   data-autocomplete-type="PAGE" data-autocomplete-extra="#' . $previous_ids['SOUR'] . '">';
		} elseif ($fact === 'PEDI') {
			echo Bootstrap4::select(GedcomCodePedi::getValues($person), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'PLAC') {
			echo '<div class="input-group">';
			echo self::formControlPlace($value, ['id' => $id, 'name' => $name]);
			echo '<span class="input-group-addon">' . FontAwesome::linkIcon('coordinates', I18N::translate('Latitude') . ' / ' . I18N::translate('Longitude'), ['data-toggle' => 'collapse', 'data-target' => '.child_of_' . $id]) . '</span>';
			echo self::inputAddonHelp('PLAC');
			echo '</div>';
			if (Module::getModuleByName('places_assistant')) {
				\PlacesAssistantModule::setup_place_subfields($id);
				\PlacesAssistantModule::print_place_subfields($id);
			}
		} elseif ($fact === 'QUAY') {
			echo Bootstrap4::select(GedcomCodeQuay::getValues(), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'RELA') {
			echo Bootstrap4::select(FunctionsEdit::optionsRelationships($value), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'REPO') {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-repository" data-element-id="' . $id . '" title="' . I18N::translate('Create a repository') . '"><i class="fa fa-plus"></i></button></span>' . self::formControlRepository(Individual::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'RESN') {
			echo '<div class="input-group">';
			echo Bootstrap4::select(FunctionsEdit::optionsRestrictions(true), $value, ['id' => $id, 'name' => $name]);
			echo self::inputAddonHelp('RESN');
			echo '</span>';
			echo '</div>';
		} elseif ($fact === 'SEX') {
			echo Bootstrap4::radioButtons($name, ['M' => I18N::translate('Male'), 'F' => I18N::translate('Female'), 'U' => I18N::translateContext('unknown gender', 'Unknown')], $value, true);
		} elseif ($fact === 'SOUR') {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-source" data-element-id="' . $id . '" title="' . I18N::translate('Create a source') . '"><i class="fa fa-plus"></i></button></span>' .
				self::formControlSource(Source::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'STAT') {
			echo Bootstrap4::select(GedcomCodeStat::statusNames($upperlevel), $value);
		} elseif ($fact === 'SUBM') {
			echo
				'<div class="input-group">' .
				'<span class="input-group-btn"><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modal-create-submitter" data-element-id="' . $id . '" title="' . I18N::translate('Create a submitter') . '"><i class="fa fa-plus"></i></button></span>' .
				self::formControlSubmitter(GedcomRecord::getInstance($value, $WT_TREE), ['id' => $id, 'name' => $name]) .
				'</div>';
		} elseif ($fact === 'TEMP') {
			echo Bootstrap4::select(FunctionsEdit::optionsTemples(), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === 'TIME') {
			echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '" pattern="([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?" dir="ltr" placeholder="' . /* I18N: Examples of valid time formats (hours:minutes:seconds) */ I18N::translate('hh:mm or hh:mm:ss') . '">';
		} elseif ($fact === '_WT_USER') {
			echo Bootstrap4::select(FunctionsEdit::optionsUsers(), $value, ['id' => $id, 'name' => $name]);
		} elseif ($fact === '_PRIM') {
			echo Bootstrap4::select(['' => '', 'Y' => I18N::translate('always'), 'N' => I18N::translate('never')], $value, ['id' => $id, 'name' => $name]);
			echo '<p class="small text-muted">', I18N::translate('Use this image for charts and on the individual’s page.'), '</p>';
		} elseif ($fact === 'TYPE' && $level === '3') {
			//-- Build the selector for the Media 'TYPE' Fact
			echo '<select name="text[]"><option selected value="" ></option>';
			$selectedValue = strtolower($value);
			if (!array_key_exists($selectedValue, GedcomTag::getFileFormTypes())) {
				echo '<option selected value="', Filter::escapeHtml($value), '" >', Filter::escapeHtml($value), '</option>';
			}
			foreach (GedcomTag::getFileFormTypes() as $typeName => $typeValue) {
				echo '<option value="', $typeName, '" ';
				if ($selectedValue === $typeName) {
					echo 'selected';
				}
				echo '>', $typeValue, '</option>';
			}
			echo '</select>';
		} elseif (($fact !== 'NAME' || $upperlevel === 'REPO' || $upperlevel === 'UNKNOWN') && $fact !== '_MARNM') {
			if ($fact === 'TEXT' || $fact === 'ADDR' || ($fact === 'NOTE' && !$islink)) {
				echo '<div class="input-group">';
				echo '<textarea class="form-control" id="', $id, '" name="', $name, '" dir="auto">', Filter::escapeHtml($value), '</textarea>';
				echo self::inputAddonKeyboard($id);
				echo '</div>';
			} else {
				// If using GEDFact-assistant window
				echo '<input class="form-control" type="text" id="', $id, '" name="', $name, '" value="', Filter::escapeHtml($value), '">';
			}
		} else {
			// Populated in javascript from sub-tags
			echo '<input type="hidden" id="', $id, '" name="', $name, '" oninput="updateTextName(\'', $id, '\')" value="', Filter::escapeHtml($value), '" class="', $fact, '">';
			echo '<span id="', $id, '_display" dir="auto">', Filter::escapeHtml($value), '</span>';
			echo ' <a href="#edit_name" onclick="convertHidden(\'', $id, '\'); return false" class="icon-edit_indi" title="' . I18N::translate('Edit the name') . '"></a>';
		}
		// MARRiage TYPE : hide text field and show a selection list
		if ($fact === 'TYPE' && $level === '2' && $tags[0] === 'MARR') {
			echo '<script>';
			echo 'document.getElementById(\'', $id, '\').style.display=\'none\'';
			echo '</script>';
			echo '<select id="', $id, '_sel" oninput="document.getElementById(\'', $id, '\').value=this.value" >';
			foreach (['Unknown', 'Civil', 'Religious', 'Partners'] as $key) {
				if ($key === 'Unknown') {
					echo '<option value="" ';
				} else {
					echo '<option value="', $key, '" ';
				}
				$a = strtolower($key);
				$b = strtolower($value);
				if ($b !== '' && strpos($a, $b) !== false || strpos($b, $a) !== false) {
					echo 'selected';
				}
				echo '>', GedcomTag::getLabel('MARR_' . strtoupper($key)), '</option>';
			}
			echo '</select>';
		} elseif ($fact === 'TYPE' && $level === 0) {
			// NAME TYPE : hide text field and show a selection list
			echo Bootstrap4::select(GedcomCodeName::getValues($person), $value, ['id' => $id, 'name' => $name, 'oninput' => 'document.getElementById(\'' . $id . '\').value=this.value"']);
			echo '<script>document.getElementById("', $id, '").style.display="none";</script>';
		}

		// popup links
		switch ($fact) {
		case 'SOUR':
			//-- checkboxes to apply '1 SOUR' to BIRT/MARR/DEAT as '2 SOUR'
			if ($level === '1') {
				echo '<br>';
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
						echo ' <label><input type="checkbox" name="SOUR_INDI" ', $level1_checked, ' value="1">', I18N::translate('Individual'), '</label>';
						if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
							foreach ($matches[1] as $match) {
								if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
									echo ' <label><input type="checkbox" name="SOUR_', $match, '" ', $level2_checked, ' value="1">', GedcomTag::getLabel($match), '</label>';
								}
							}
						}
					}
					if (strpos($bdm, 'D') !== false) {
						if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
							foreach ($matches[1] as $match) {
								if (in_array($match, explode('|', WT_EVENTS_DEAT))) {
									echo ' <label><input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="1">', GedcomTag::getLabel($match), '</label>';
								}
							}
						}
					}
					if (strpos($bdm, 'M') !== false) {
						echo ' <label><input type="checkbox" name="SOUR_FAM" ', $level1_checked, ' value="1">', I18N::translate('Family'), '</label>';
						if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
							foreach ($matches[1] as $match) {
								echo ' <label><input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="1">', GedcomTag::getLabel($match), '</label>';
							}
						}
					}
				}
				break;
		}

		echo '<div id="' . $id . '_description">';

		// pastable values
		if ($fact === 'FORM' && $upperlevel === 'OBJE') {
			FunctionsPrint::printAutoPasteLink($id, Config::fileFormats());
		}
		echo '</div>', $extra, '</div></div>';

		return $id;
	}

	/**
	 * Genearate a <select> element, with the dates/places of all known censuses
	 *
	 *
	 * @param string $locale - Sort the censuses for this locale
	 * @param string $xref   - The individual for whom we are adding a census
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
			echo '<h3>', I18N::translate('Add a source citation'), '</h3>';
			self::addSimpleTag($level . ' SOUR @');
			self::addSimpleTag(($level + 1) . ' PAGE');
			self::addSimpleTag(($level + 1) . ' DATA');
			self::addSimpleTag(($level + 2) . ' TEXT');
			if ($WT_TREE->getPreference('FULL_SOURCES')) {
				self::addSimpleTag(($level + 2) . ' DATE', '', GedcomTag::getLabel('DATA:DATE'));
				self::addSimpleTag(($level + 1) . ' QUAY');
			}
			self::addSimpleTag(($level + 1) . ' OBJE');
			self::addSimpleTag(($level + 1) . ' SHARED_NOTE');
			break;

		case 'ASSO':
		case 'ASSO2':
			echo '<h3>', I18N::translate('Add an associate'), '</h3>';
			self::addSimpleTag($level . ' _ASSO @');
			self::addSimpleTag(($level + 1) . ' RELA');
			self::addSimpleTag(($level + 1) . ' NOTE');
			self::addSimpleTag(($level + 1) . ' SHARED_NOTE');
			break;

		case 'NOTE':
			echo '<h3>', I18N::translate('Add a note'), '</h3>';
			self::addSimpleTag($level . ' NOTE');
			break;

		case 'SHARED_NOTE':
			echo '<h3>', I18N::translate('Add a shared note'), '</h3>';
			self::addSimpleTag($level . ' SHARED_NOTE', $parent_tag);
			break;

		case 'OBJE':
			if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($WT_TREE)) {
				echo '<h3>', I18N::translate('Add a media object'), '</h3>';
				self::addSimpleTag($level . ' OBJE');
			}
			break;

		case 'RESN':
			echo '<h3>', I18N::translate('Add a restriction'), '</h3>';
			self::addSimpleTag($level . ' RESN');
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
			self::addSimpleTag('0 ' . $fact . ' Y');
		} else {
			self::addSimpleTag('0 ' . $fact);
		}

		if (!in_array($fact, Config::nonDateFacts())) {
			self::addSimpleTag('0 DATE', $fact, GedcomTag::getLabel($fact . ':DATE'));
		}

		if (!in_array($fact, Config::nonPlaceFacts())) {
			self::addSimpleTag('0 PLAC', $fact, GedcomTag::getLabel($fact . ':PLAC'));

			if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
				foreach ($match[1] as $tag) {
					self::addSimpleTag('0 ' . $tag, $fact, GedcomTag::getLabel($fact . ':PLAC:' . $tag));
				}
			}
			self::addSimpleTag('0 MAP', $fact);
			self::addSimpleTag('0 LATI', $fact);
			self::addSimpleTag('0 LONG', $fact);
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
			self::addSimpleTag('1 MARR');
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
				self::addSimpleTag('1 ' . $fact . ' Y');
			} else {
				self::addSimpleTag('1 ' . $fact);
			}
			self::insertMissingSubtags($tags[0]);
			//-- handle the special SOURce case for level 1 sources [ 1759246 ]
			if ($fact === 'SOUR') {
				self::addSimpleTag('2 PAGE');
				self::addSimpleTag('3 TEXT');
				if ($WT_TREE->getPreference('FULL_SOURCES')) {
					self::addSimpleTag('3 DATE', '', GedcomTag::getLabel('DATA:DATE'));
					self::addSimpleTag('2 QUAY');
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
					self::addSimpleTag($subrecord, '', GedcomTag::getLabel($label, $record));
				} elseif (!$inSource && $type === 'DATE') {
					self::addSimpleTag($subrecord, $level1type, GedcomTag::getLabel($label, $record));
					if ($level === '2') {
						// We already have a date - no need to add one.
						$add_date = false;
					}
				} elseif ($type === 'STAT') {
					self::addSimpleTag($subrecord, $level1type, GedcomTag::getLabel($label, $record));
				} else {
					self::addSimpleTag($subrecord, $level0type, GedcomTag::getLabel($label, $record));
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
						self::addSimpleTag(($level + 1) . ' ' . $subtag, '', GedcomTag::getLabel($label . ':' . $subtag));
						if (!empty($expected_subtags[$subtag])) {
							foreach ($expected_subtags[$subtag] as $subsubtag) {
								self::addSimpleTag(($level + 2) . ' ' . $subsubtag, '', GedcomTag::getLabel($label . ':' . $subtag . ':' . $subsubtag));
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
					self::addSimpleTag('2 TYPE ' . $type_val, $level1tag);
				} elseif ($level1tag === '_TODO' && $key === 'DATE') {
					self::addSimpleTag('2 ' . $key . ' ' . strtoupper(date('d M Y')), $level1tag);
				} elseif ($level1tag === '_TODO' && $key === '_WT_USER') {
					self::addSimpleTag('2 ' . $key . ' ' . Auth::user()->getUserName(), $level1tag);
				} elseif ($level1tag === 'TITL' && strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), $key) !== false) {
					self::addSimpleTag('2 ' . $key, $level1tag);
				} elseif ($level1tag === 'NAME' && strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), $key) !== false) {
					self::addSimpleTag('2 ' . $key, $level1tag);
				} elseif ($level1tag !== 'TITL' && $level1tag !== 'NAME') {
					self::addSimpleTag('2 ' . $key, $level1tag);
				}
				// Add level 3/4 tags as appropriate
				switch ($key) {
				case 'PLAC':
					if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
						foreach ($match[1] as $tag) {
							self::addSimpleTag('3 ' . $tag, '', GedcomTag::getLabel($level1tag . ':PLAC:' . $tag));
						}
					}
					self::addSimpleTag('3 MAP');
					self::addSimpleTag('4 LATI');
					self::addSimpleTag('4 LONG');
					break;
				case 'FILE':
					self::addSimpleTag('3 FORM');
					break;
				case 'EVEN':
					self::addSimpleTag('3 DATE');
					self::addSimpleTag('3 PLAC');
					break;
				case 'STAT':
					if (GedcomCodeTemp::isTagLDS($level1tag)) {
						self::addSimpleTag('3 DATE', '', GedcomTag::getLabel('STAT:DATE'));
					}
					break;
				case 'DATE':
					// TIME is NOT a valid 5.5.1 tag
					if (in_array($level1tag, Config::dateAndTime())) {
						self::addSimpleTag('3 TIME');
					}
					break;
				case 'HUSB':
				case 'WIFE':
					self::addSimpleTag('3 AGE');
					break;
				case 'FAMC':
					if ($level1tag === 'ADOP') {
						self::addSimpleTag('3 ADOP BOTH');
					}
					break;
				}
			} elseif ($key === 'DATE' && $add_date) {
				self::addSimpleTag('2 DATE', $level1tag, GedcomTag::getLabel($level1tag . ':DATE'));
			}
		}
		// Do something (anything!) with unrecognized custom tags
		if (substr($level1tag, 0, 1) === '_' && $level1tag !== '_UID' && $level1tag !== '_PRIM' && $level1tag !== '_TODO') {
			foreach (['DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE'] as $tag) {
				if (!in_array($tag, $tags)) {
					self::addSimpleTag('2 ' . $tag);
					if ($tag === 'PLAC') {
						if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
							foreach ($match[1] as $ptag) {
								self::addSimpleTag('3 ' . $ptag, '', GedcomTag::getLabel($level1tag . ':PLAC:' . $ptag));
							}
						}
						self::addSimpleTag('3 MAP');
						self::addSimpleTag('4 LATI');
						self::addSimpleTag('4 LONG');
					}
				}
			}
		}
	}

	/**
	 * Simple forms to create the essential fields of new records.
	 *
	 * @param Tree $tree
	 *
	 * @return string
	 */
	public static function createRecordFormModals(Tree $tree) {
		?>

		<!-- Form to create a new family -->
		<div class="modal wt-modal-create-record" id="modal-create-family">
			<form id="form-create-family"><!-- This form is posted using jQuery -->
				<?= Filter::getCsrf() ?>
				<input type="hidden" name="action" value="create-family">
				<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?= I18N::translate('Create a family from existing individuals') ?></h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label class="col-form-label" for="husband">
									<?= I18N::translate('Husband') ?>
								</label>
								<?= self::formControlIndividual(null, ['id' => 'husband', 'name' => 'husband']) ?>
							</div>
							<div class="form-group">
								<label class="col-form-label" for="wife">
									<?= I18N::translate('Wife') ?>
								</label>
								<?= self::formControlIndividual(null, ['id' => 'wife', 'name' => 'wife']) ?>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('save') ?>
								<?= I18N::translate('save') ?>
							</button>
							<button type="button" class="btn btn-text" data-dismiss="modal">
								<?= FontAwesome::decorativeIcon('cancel') ?>
								<?= I18N::translate('cancel') ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>

		<!-- Form to create a new media object -->
		<div class="modal wt-modal-create-record" id="modal-create-media-object">
			<form id="form-create-media-object"><!-- This form is posted using jQuery -->
				<?= Filter::getCsrf() ?>
				<input type="hidden" name="action" value="create-media-object">
				<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?= I18N::translate('Create a media object') ?></h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="file">
									<?= I18N::translate('Media file to upload') ?>
								</label>
								<div class="col-sm-10">
									<input type="file" class="form-control" id="file" name="file">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="type">
									<?= I18N::translate('Filename on server') ?>
								</label>
								<div class="col-sm-10">
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="auto" value="0" checked>
											<span class="input-group">
												<input class="form-control" type="text" placeholder="<?= I18N::translate('Folder name on server') ?>">
												<span class="input-group-addon">/</span>
												<input class="form-control" type="text" placeholder="<?= I18N::translate('Same as uploaded file') ?>">
											</span>
										</label>
									</div>
									<p class="small text-muted">
										<?= I18N::translate('If you have a large number of media files, you can organize them into folders and subfolders.') ?>
									</p>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="auto" value="1">
											<?= I18N::translate('Create a unique filename') ?>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="title">
									<?= I18N::translate('Title') ?>
								</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="title" id="title">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="type">
									<?= I18N::translate('Media type') ?>
								</label>
								<div class="col-sm-10">
									<?= Bootstrap4::select(['' => ''] + GedcomTag::getFileFormTypes(), '') ?>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="note">
									<?= I18N::translate('Note') ?>
								</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="note" name="note"></textarea>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('save') ?>
								<?= I18N::translate('save') ?>
							</button>
							<button type="button" class="btn btn-text" data-dismiss="modal">
								<?= FontAwesome::decorativeIcon('cancel') ?>
								<?= I18N::translate('cancel') ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>

		<!-- Form to create a new note object -->
		<div class="modal wt-modal-create-record" id="modal-create-note-object">
			<form id="form-create-note-object"><!-- This form is posted using jQuery -->
				<?= Filter::getCsrf() ?>
				<input type="hidden" name="action" value="create-note-object">
				<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?= I18N::translate('Create a shared note') ?></h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label class="col-form-label" for="note">
									<?= GedcomTag::getLabel('NOTE') ?>
								</label>
								<textarea class="form-control" id="note" name="note"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('save') ?>
								<?= I18N::translate('save') ?>
							</button>
							<button type="button" class="btn btn-text" data-dismiss="modal">
								<?= FontAwesome::decorativeIcon('cancel') ?>
								<?= I18N::translate('cancel') ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>

		<!-- Form to create a new repository -->
		<div class="modal wt-modal-create-record" id="modal-create-repository">
			<form id="form-create-repository"><!-- This form is posted using jQuery -->
				<?= Filter::getCsrf() ?>
				<input type="hidden" name="action" value="create-repository">
				<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?= I18N::translate('Create a repository') ?></h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label class="col-form-label" for="repository-name">
									<?= GedcomTag::getLabel('REPO:NAME') ?>
								</label>
								<input class="form-control" type="text" id="repository-name" name="repository_name" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('save') ?>
								<?= I18N::translate('save') ?>
							</button>
							<button type="button" class="btn btn-text" data-dismiss="modal">
								<?= FontAwesome::decorativeIcon('cancel') ?>
								<?= I18N::translate('cancel') ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>

		<!-- Form to create a new source -->
		<div class="modal wt-modal-create-record" id="modal-create-source">
			<form id="form-create-source"><!-- This form is posted using jQuery -->
				<?= Filter::getCsrf() ?>
				<input type="hidden" name="action" value="create-source">
				<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?= I18N::translate('Create a source') ?></h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="source-title">
									<?= I18N::translate('Title') ?>
								</label>
								<div class="col-sm-10">
									<input class="form-control" type="text" id="source-title" name="TITL" required>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="source-abbreviation">
									<?= I18N::translate('Abbreviation') ?>
								</label>
								<div class="col-sm-10">
									<input class="form-control" type="text" id="source-abbreviation" name="ABBR">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="source-author">
									<?= I18N::translate('Author') ?>
								</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="source-author" name="AUTH">
								</div>
								<label class="col-form-label col-sm-2" for="source-publication">
									<?= I18N::translate('Publication') ?>
								</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="source-publication" name="PUBL">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="source-repository">
									<?= I18N::translate('Repository') ?>
								</label>
								<div class="col-sm-4">
									<?= self::formControlRepository(null, ['id' => 'source-repository', 'name' => 'REPO']) ?>
								</div>
								<label class="col-form-label col-sm-2" for="source-call-number">
									<?= I18N::translate('Call number') ?>
								</label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="source-call-number" name="CALN">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2" for="source-text">
									<?= I18N::translate('Text') ?>
								</label>
								<div class="col-sm-10">
									<textarea class="form-control" rows="2" id="source-text" name="TEXT"></textarea>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('save') ?>
								<?= I18N::translate('save') ?>
							</button>
							<button type="button" class="btn btn-text" data-dismiss="modal">
								<?= FontAwesome::decorativeIcon('cancel') ?>
								<?= I18N::translate('cancel') ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>

		<!-- Form to create a new submitter -->
		<div class="modal wt-modal-create-record" id="modal-create-submitter">
			<form id="form-create-submitter"><!-- This form is posted using jQuery -->
				<?= Filter::getCsrf() ?>
				<input type="hidden" name="action" value="create-submitter">
				<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?= I18N::translate('Create a submitter') ?></h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label class="col-form-label" for="submitter-name">
									<?= GedcomTag::getLabel('SUBM:NAME') ?>
								</label>
								<input class="form-control" type="text" id="submitter-name" name="submitter_name" required>
							</div>
							<div class="form-group">
								<label class="col-form-label" for="submitter-address">
									<?= GedcomTag::getLabel('SUBM:ADDR') ?>
								</label>
								<input class="form-control" type="text" id="submitter-address" name="submitter_address">
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('save') ?>
								<?= I18N::translate('save') ?>
							</button>
							<button type="button" class="btn btn-text" data-dismiss="modal">
								<?= FontAwesome::decorativeIcon('cancel') ?>
								<?= I18N::translate('cancel') ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>

		<!-- On screen keyboard -->
		<div class="card wt-osk">
			<div class="card-header">
				<div class="card-title">
					<button type="button" class="btn btn-primary">&times;</button>

					<button type="button" class="btn btn-secondary wt-osk-pin-button" data-toggle="button" aria-pressed="false"><?= FontAwesome::semanticIcon('pin', I18N::translate('Keep open')) ?></button>

					<button type="button" class="btn btn-secondary wt-osk-shift-button" data-toggle="button" aria-pressed="false">a &harr; A</button>

					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-secondary active" dir="ltr">
							<input type="radio" class="wt-osk-script-button" checked autocomplete="off" data-script="latn"> Abcd
						</label>
						<label class="btn btn-secondary" dir="ltr">
							<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="cyrl"> &Acy;&bcy;&gcy;&dcy;
						</label>
						<label class="btn btn-secondary" dir="ltr">
							<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="grek"> &Alpha;&beta;&gamma;&delta;
						</label>
						<label class="btn btn-secondary" dir="rtl">
							<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="arab"> &#x627;&#x628;&#x629;&#x62a;
						</label>
						<label class="btn btn-secondary" dir="rtl">
							<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="hebr"> &#x5d0;&#x5d1;&#x5d2;&#x5d3;
						</label>
					</div>
				</div>
			</div>
			<div class="card-block wt-osk-keys">
				<!-- Quotation marks -->
				<div class="wt-osk-group">
					<span class="wt-osk-key">&lsquo;<sup class="wt-osk-key-shift">&ldquo;</sup></span>
					<span class="wt-osk-key">&rsquo;<sup class="wt-osk-key-shift">&rdquo;</sup></span>
					<span class="wt-osk-key">&lsaquo;<sup class="wt-osk-key-shift">&ldquo;</sup></span>
					<span class="wt-osk-key">&rsaquo;<sup class="wt-osk-key-shift">&raquo;</sup></span>
					<span class="wt-osk-key">&sbquo;<sup class="wt-osk-key-shift">&bdquo;</sup></span>
					<span class="wt-osk-key">&prime;<sup class="wt-osk-key-shift">&Prime;</sup></span>
				</div>
				<!-- Symbols and punctuation -->
				<div class="wt-osk-group">
					<span class="wt-osk-key">&copy;</span>
					<span class="wt-osk-key">&deg;</span>
					<span class="wt-osk-key">&hellip;</span>
					<span class="wt-osk-key">&middot;<sup class="wt-osk-key-shift">&bullet;</sup></span>
					<span class="wt-osk-key">&ndash;<sup class="wt-osk-key-shift">&mdash;</sup></span>
					<span class="wt-osk-key">&dagger;<sup class="wt-osk-key-shift">&ddagger;</sup></span>
					<span class="wt-osk-key">&sect;<sup class="wt-osk-key-shift">&para;</sup></span>
					<span class="wt-osk-key">&iquest;<sup class="wt-osk-key-shift">&iexcl;</sup></span>
				</div>
				<!-- Letter A with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&agrave;<sup class="wt-osk-key-shift">&Agrave;</sup></span>
					<span class="wt-osk-key">&aacute;<sup class="wt-osk-key-shift">&Aacute;</sup></span>
					<span class="wt-osk-key">&acirc;<sup class="wt-osk-key-shift">&Acirc;</sup></span>
					<span class="wt-osk-key">&atilde;<sup class="wt-osk-key-shift">&Atilde;</sup></span>
					<span class="wt-osk-key">&aring;<sup class="wt-osk-key-shift">&Aring;</sup></span>
					<span class="wt-osk-key">&aogon;<sup class="wt-osk-key-shift">&Aogon;</sup></span>
					<span class="wt-osk-key">&aelig;<sup class="wt-osk-key-shift">&AElig;</sup></span>
					<span class="wt-osk-key">&ordf;</span>
				</div>
				<!-- Letter C with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&ccedil;<sup class="wt-osk-key-shift">&Ccedil;</sup></span>
					<span class="wt-osk-key">&ccaron;<sup class="wt-osk-key-shift">&Ccaron;</sup></span>
				</div>
				<!-- Letter D with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&Dcaron;<sup class="wt-osk-key-shift">&Dcaron;</sup></span>
				</div>
				<!-- Letter E with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&egrave;<sup class="wt-osk-key-shift">&Egrave;</sup></span>
					<span class="wt-osk-key">&eacute;<sup class="wt-osk-key-shift">&Eacute;</sup></span>
					<span class="wt-osk-key">&ecirc;<sup class="wt-osk-key-shift">&Ecirc;</sup></span>
					<span class="wt-osk-key">&euml;<sup class="wt-osk-key-shift">&Euml;</sup></span>
					<span class="wt-osk-key">&eogon;<sup class="wt-osk-key-shift">&Eogon;</sup></span>
				</div>
				<!-- Letter G with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&gbreve;<sup class="wt-osk-key-shift">&Gbreve;</sup></span>
				</div>
				<!-- Letter I with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&igrave;<sup class="wt-osk-key-shift">&Igrave;</sup></span>
					<span class="wt-osk-key">&iacute;<sup class="wt-osk-key-shift">&Iacute;</sup></span>
					<span class="wt-osk-key">&icirc;<sup class="wt-osk-key-shift">&Icirc;</sup></span>
					<span class="wt-osk-key">&iuml;<sup class="wt-osk-key-shift">&Iuml;</sup></span>
					<span class="wt-osk-key">&iogon;<sup class="wt-osk-key-shift">&Iogon;</sup></span>
					<span class="wt-osk-key">&inodot;<sup class="wt-osk-key-shift">&Idot;</sup></span>
					<span class="wt-osk-key">&ijlig;<sup class="wt-osk-key-shift">&IJlig;</sup></span>
				</div>
				<!-- Letter L with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&lcaron;<sup class="wt-osk-key-shift">&Lcaron;</sup></span>
					<span class="wt-osk-key">&lacute;<sup class="wt-osk-key-shift">&Lacute;</sup></span>
					<span class="wt-osk-key">&lstrok;<sup class="wt-osk-key-shift">&Lstrok;</sup></span>
				</div>
				<!-- Letter N with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&napos;</span>
					<span class="wt-osk-key">&ntilde;<sup class="wt-osk-key-shift">&Ntilde;</sup></span>
					<span class="wt-osk-key">&ncaron;<sup class="wt-osk-key-shift">&Ncaron;</sup></span>
				</div>
				<!-- Letter O with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&ograve;<sup class="wt-osk-key-shift">&Ograve;</sup></span>
					<span class="wt-osk-key">&oacute;<sup class="wt-osk-key-shift">&Oacute;</sup></span>
					<span class="wt-osk-key">&ocirc;<sup class="wt-osk-key-shift">&Ocirc;</sup></span>
					<span class="wt-osk-key">&otilde;<sup class="wt-osk-key-shift">&Otilde;</sup></span>
					<span class="wt-osk-key">&ouml;<sup class="wt-osk-key-shift">&Ouml;</sup></span>
					<span class="wt-osk-key">&oslash;<sup class="wt-osk-key-shift">&Oslash;</sup></span>
					<span class="wt-osk-key">&oelig;<sup class="wt-osk-key-shift">&OElig;</sup></span>
					<span class="wt-osk-key">&ordm;</span>
				</div>
				<!-- Letter T with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&tcaron;<sup class="wt-osk-key-shift">&Tcaron;</sup></span>
				</div>
				<!-- Letter R with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&racute;<sup class="wt-osk-key-shift">&Racute;</sup></span>
					<span class="wt-osk-key">&rcaron;<sup class="wt-osk-key-shift">&Rcaron;</sup></span>
				</div>
				<!-- Letter S with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&scaron;<sup class="wt-osk-key-shift">&Scaron;</sup></span>
					<span class="wt-osk-key">&scedil;<sup class="wt-osk-key-shift">&Scedil;</sup></span>
					<span class="wt-osk-key">&#x17F;</sup></span>
				</div>
				<!-- Letter U with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&ugrave;<sup class="wt-osk-key-shift">&Ugrave;</sup></span>
					<span class="wt-osk-key">&uacute;<sup class="wt-osk-key-shift">&Uacute;</sup></span>
					<span class="wt-osk-key">&ucirc;<sup class="wt-osk-key-shift">&Ucirc;</sup></span>
					<span class="wt-osk-key">&utilde;<sup class="wt-osk-key-shift">&Utilde;</sup></span>
					<span class="wt-osk-key">&umacr;<sup class="wt-osk-key-shift">&Umacr;</sup></span>
					<span class="wt-osk-key">&uogon;<sup class="wt-osk-key-shift">&Uogon;</sup></span>
				</div>
				<!-- Letter Y with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&yacute;<sup class="wt-osk-key-shift">&Yacute;</sup></span>
				</div>
				<!-- Letter Z with diacritic -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&zdot;<sup class="wt-osk-key-shift">&Zdot;</sup></span>
					<span class="wt-osk-key">&zcaron;<sup class="wt-osk-key-shift">&Zcaron;</sup></span>
				</div>
				<!-- Esszet, Eth and Thorn -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
					<span class="wt-osk-key">&szlig;<sup class="wt-osk-key-shift">&#7838;</sup></span>
					<span class="wt-osk-key">&eth;<sup class="wt-osk-key-shift">&ETH;</sup></span>
					<span class="wt-osk-key">&thorn;<sup class="wt-osk-key-shift">&THORN;</sup></span>
				</div>
				<!-- Extra Cyrillic characters -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-cyrl" dir="ltr" hidden>
					<span class="wt-osk-key">&iocy;<sup class="wt-osk-key-shift">&IOcy;</sup></span>
					<span class="wt-osk-key">&djcy;<sup class="wt-osk-key-shift">&DJcy;</sup></span>
					<span class="wt-osk-key">&gjcy;<sup class="wt-osk-key-shift">&GJcy;</sup></span>
					<span class="wt-osk-key">&jukcy;<sup class="wt-osk-key-shift">&Jukcy;</sup></span>
					<span class="wt-osk-key">&dscy;<sup class="wt-osk-key-shift">&DScy;</sup></span>
					<span class="wt-osk-key">&iukcy;<sup class="wt-osk-key-shift">&Iukcy;</sup></span>
					<span class="wt-osk-key">&yicy;<sup class="wt-osk-key-shift">&YIcy;</sup></span>
					<span class="wt-osk-key">&jsercy;<sup class="wt-osk-key-shift">&Jsercy;</sup></span>
					<span class="wt-osk-key">&ljcy;<sup class="wt-osk-key-shift">&LJcy;</sup></span>
					<span class="wt-osk-key">&njcy;<sup class="wt-osk-key-shift">&NJcy;</sup></span>
					<span class="wt-osk-key">&tshcy;<sup class="wt-osk-key-shift">&TSHcy;</sup></span>
					<span class="wt-osk-key">&kjcy;<sup class="wt-osk-key-shift">&KJcy;</sup></span>
					<span class="wt-osk-key">&ubrcy;<sup class="wt-osk-key-shift">&Ubrcy;</sup></span>
					<span class="wt-osk-key">&dzcy;<sup class="wt-osk-key-shift">&DZcy;</sup></span>
				</div>
				<!-- Cyrillic alphabet -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-cyrl" dir="ltr" hidden>
					<span class="wt-osk-key">&acy;<sup class="wt-osk-key-shift">&Acy;</sup></span>
					<span class="wt-osk-key">&bcy;<sup class="wt-osk-key-shift">&Bcy;</sup></span>
					<span class="wt-osk-key">&gcy;<sup class="wt-osk-key-shift">&Gcy;</sup></span>
					<span class="wt-osk-key">&dcy;<sup class="wt-osk-key-shift">&Dcy;</sup></span>
					<span class="wt-osk-key">&iecy;<sup class="wt-osk-key-shift">&IEcy;</sup></span>
					<span class="wt-osk-key">&zhcy;<sup class="wt-osk-key-shift">&ZHcy;</sup></span>
					<span class="wt-osk-key">&zcy;<sup class="wt-osk-key-shift">&Zcy;</sup></span>
					<span class="wt-osk-key">&icy;<sup class="wt-osk-key-shift">&Icy;</sup></span>
					<span class="wt-osk-key">&jcy;<sup class="wt-osk-key-shift">&Jcy;</sup></span>
					<span class="wt-osk-key">&kcy;<sup class="wt-osk-key-shift">&Kcy;</sup></span>
					<span class="wt-osk-key">&lcy;<sup class="wt-osk-key-shift">&Lcy;</sup></span>
					<span class="wt-osk-key">&mcy;<sup class="wt-osk-key-shift">&Mcy;</sup></span>
					<span class="wt-osk-key">&ncy;<sup class="wt-osk-key-shift">&Ncy;</sup></span>
					<span class="wt-osk-key">&ocy;<sup class="wt-osk-key-shift">&Ocy;</sup></span>
					<span class="wt-osk-key">&pcy;<sup class="wt-osk-key-shift">&Pcy;</sup></span>
					<span class="wt-osk-key">&scy;<sup class="wt-osk-key-shift">&Scy;</sup></span>
					<span class="wt-osk-key">&tcy;<sup class="wt-osk-key-shift">&Tcy;</sup></span>
					<span class="wt-osk-key">&ucy;<sup class="wt-osk-key-shift">&Ucy;</sup></span>
					<span class="wt-osk-key">&ucy;<sup class="wt-osk-key-shift">&Ucy;</sup></span>
					<span class="wt-osk-key">&fcy;<sup class="wt-osk-key-shift">&Fcy;</sup></span>
					<span class="wt-osk-key">&khcy;<sup class="wt-osk-key-shift">&KHcy;</sup></span>
					<span class="wt-osk-key">&tscy;<sup class="wt-osk-key-shift">&TScy;</sup></span>
					<span class="wt-osk-key">&chcy;<sup class="wt-osk-key-shift">&CHcy;</sup></span>
					<span class="wt-osk-key">&shcy;<sup class="wt-osk-key-shift">&SHcy;</sup></span>
					<span class="wt-osk-key">&shchcy;<sup class="wt-osk-key-shift">&SHCHcy;</sup></span>
					<span class="wt-osk-key">&hardcy;<sup class="wt-osk-key-shift">&HARDcy;</sup></span>
					<span class="wt-osk-key">&ycy;<sup class="wt-osk-key-shift">&Ycy;</sup></span>
					<span class="wt-osk-key">&softcy;<sup class="wt-osk-key-shift">&SOFTcy;</sup></span>
					<span class="wt-osk-key">&ecy;<sup class="wt-osk-key-shift">&Ecy;</sup></span>
					<span class="wt-osk-key">&yucy;<sup class="wt-osk-key-shift">&YUcy;</sup></span>
					<span class="wt-osk-key">&yacy;<sup class="wt-osk-key-shift">&YAcy;</sup></span>
				</div>
				<!-- Greek alphabet -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-grek" dir="ltr" hidden>
					<span class="wt-osk-key">&alpha;<sup class="wt-osk-key-shift">&Alpha;</sup></span>
					<span class="wt-osk-key">&beta;<sup class="wt-osk-key-shift">&Beta;</sup></span>
					<span class="wt-osk-key">&gamma;<sup class="wt-osk-key-shift">&Gamma;</sup></span>
					<span class="wt-osk-key">&delta;<sup class="wt-osk-key-shift">&Delta;</sup></span>
					<span class="wt-osk-key">&epsilon;<sup class="wt-osk-key-shift">&Epsilon;</sup></span>
					<span class="wt-osk-key">&zeta;<sup class="wt-osk-key-shift">&Zeta;</sup></span>
					<span class="wt-osk-key">&eta;<sup class="wt-osk-key-shift">&eta;</sup></span>
					<span class="wt-osk-key">&theta;<sup class="wt-osk-key-shift">&Theta;</sup></span>
					<span class="wt-osk-key">&iota;<sup class="wt-osk-key-shift">&Iota;</sup></span>
					<span class="wt-osk-key">&kappa;<sup class="wt-osk-key-shift">&Kappa;</sup></span>
					<span class="wt-osk-key">&lambda;<sup class="wt-osk-key-shift">&Lambda;</sup></span>
					<span class="wt-osk-key">&mu;<sup class="wt-osk-key-shift">&Mu;</sup></span>
					<span class="wt-osk-key">&nu;<sup class="wt-osk-key-shift">&Nu;</sup></span>
					<span class="wt-osk-key">&xi;<sup class="wt-osk-key-shift">&Xi;</sup></span>
					<span class="wt-osk-key">&omicron;<sup class="wt-osk-key-shift">&Omicron;</sup></span>
					<span class="wt-osk-key">&pi;<sup class="wt-osk-key-shift">&Pi;</sup></span>
					<span class="wt-osk-key">&rho;<sup class="wt-osk-key-shift">&Rho;</sup></span>
					<span class="wt-osk-key">&sigma;<sup class="wt-osk-key-shift">&Sigma;</sup></span>
					<span class="wt-osk-key">&tau;<sup class="wt-osk-key-shift">&Tau;</sup></span>
					<span class="wt-osk-key">&upsilon;<sup class="wt-osk-key-shift">&Upsilon;</sup></span>
					<span class="wt-osk-key">&phi;<sup class="wt-osk-key-shift">&Phi;</sup></span>
					<span class="wt-osk-key">&chi;<sup class="wt-osk-key-shift">&chi;</sup></span>
					<span class="wt-osk-key">&psi;<sup class="wt-osk-key-shift">&Psi;</sup></span>
					<span class="wt-osk-key">&omega;<sup class="wt-osk-key-shift">&Omega;</sup></span>
				</div>
				<!-- Arabic alphabet -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-arab" dir="rtl" hidden>
					<span class="wt-osk-key">ا</span>
					<span class="wt-osk-key">ب</span>
					<span class="wt-osk-key">ت</span>
					<span class="wt-osk-key">ثج</span>
					<span class="wt-osk-key">ح</span>
					<span class="wt-osk-key">خ</span>
					<span class="wt-osk-key">د</span>
					<span class="wt-osk-key">ذ</span>
					<span class="wt-osk-key">ر</span>
					<span class="wt-osk-key">ز</span>
					<span class="wt-osk-key">س</span>
					<span class="wt-osk-key">ش</span>
					<span class="wt-osk-key">ص</span>
					<span class="wt-osk-key">ض</span>
					<span class="wt-osk-key">ط</span>
					<span class="wt-osk-key">ظ</span>
					<span class="wt-osk-key">ع</span>
					<span class="wt-osk-key">غ</span>
					<span class="wt-osk-key">ف</span>
					<span class="wt-osk-key">ق</span>
					<span class="wt-osk-key">ك</span>
					<span class="wt-osk-key">ل</span>
					<span class="wt-osk-key">من</span>
					<span class="wt-osk-key">ه</span>
					<span class="wt-osk-key">و</span>
					<span class="wt-osk-key">ي</span>
					<span class="wt-osk-key">آ</span>
					<span class="wt-osk-key">ة</span>
					<span class="wt-osk-key">ى</span>
					<span class="wt-osk-key">ی</span>
				</div>
				<!-- Hebrew alphabet -->
				<div class="wt-osk-group wt-osk-script wt-osk-script-hebr" dir="rtl" hidden>
					<span class="wt-osk-key">&#x5d0;</span>
					<span class="wt-osk-key">&#x5d1;</span>
					<span class="wt-osk-key">&#x5d2;</span>
					<span class="wt-osk-key">&#x5d3;</span>
					<span class="wt-osk-key">&#x5d4;</span>
					<span class="wt-osk-key">&#x5d5;</span>
					<span class="wt-osk-key">&#x5d6;</span>
					<span class="wt-osk-key">&#x5d7;</span>
					<span class="wt-osk-key">&#x5d8;</span>
					<span class="wt-osk-key">&#x5d9;</span>
					<span class="wt-osk-key">&#x5da;</span>
					<span class="wt-osk-key">&#x5db;</span>
					<span class="wt-osk-key">&#x5dc;</span>
					<span class="wt-osk-key">&#x5dd;</span>
					<span class="wt-osk-key">&#x5de;</span>
					<span class="wt-osk-key">&#x5df;</span>
					<span class="wt-osk-key">&#x5e0;</span>
					<span class="wt-osk-key">&#x5e1;</span>
					<span class="wt-osk-key">&#x5e2;</span>
					<span class="wt-osk-key">&#x5e3;</span>
					<span class="wt-osk-key">&#x5e4;</span>
					<span class="wt-osk-key">&#x5e5;</span>
					<span class="wt-osk-key">&#x5e6;</span>
					<span class="wt-osk-key">&#x5e7;</span>
					<span class="wt-osk-key">&#x5e8;</span>
					<span class="wt-osk-key">&#x5e9;</span>
					<span class="wt-osk-key">&#x5ea;</span>
					<span class="wt-osk-key">&#x5f0;</span>
					<span class="wt-osk-key">&#x5f1;</span>
					<span class="wt-osk-key">&#x5f2;</span>
					<span class="wt-osk-key">&#x5f3;</span>
					<span class="wt-osk-key">&#x5f4;</span>
				</div>
			</div>
		</div>
		<?php
	}
}
