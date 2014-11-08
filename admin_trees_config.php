<?php
// UI for online updating of the GEDCOM config file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\User;

define('WT_SCRIPT_NAME', 'admin_trees_config.php');

require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isManager())
	->setPageTitle(WT_I18N::translate('Family tree configuration'));

$PRIVACY_CONSTANTS = array(
	'none'         => WT_I18N::translate('Show to visitors'),
	'privacy'      => WT_I18N::translate('Show to members'),
	'confidential' => WT_I18N::translate('Show to managers'),
	'hidden'       => WT_I18N::translate('Hide from everyone')
);

switch (WT_Filter::post('action')) {
case 'delete':
	if (!WT_Filter::checkCsrf()) {
		break;
	}
	WT_DB::prepare(
		"DELETE FROM `##default_resn` WHERE default_resn_id=?"
	)->execute(array(WT_Filter::post('default_resn_id')));
	// Reload the page, so that the new privacy restrictions are reflected in the header
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'#privacy');
	exit;
case 'add':
	if (!WT_Filter::checkCsrf()) {
		break;
	}
	if ((WT_Filter::post('xref') || WT_Filter::post('tag_type')) && WT_Filter::post('resn')) {
		if (WT_Filter::post('xref')=='') {
			WT_DB::prepare(
				"DELETE FROM `##default_resn` WHERE gedcom_id=? AND tag_type=? AND xref IS NULL"
			)->execute(array(WT_GED_ID, WT_Filter::post('tag_type')));
		}
		if (WT_Filter::post('tag_type')=='') {
			WT_DB::prepare(
				"DELETE FROM `##default_resn` WHERE gedcom_id=? AND xref=? AND tag_type IS NULL"
			)->execute(array(WT_GED_ID, WT_Filter::post('xref')));
		}
		WT_DB::prepare(
			"REPLACE INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULLIF(?, ''), NULLIF(?, ''), ?)"
		)->execute(array(WT_GED_ID, WT_Filter::post('xref', WT_REGEX_XREF), WT_Filter::post('tag_type'), WT_Filter::post('resn')));
	}
	// Reload the page, so that the new privacy restrictions are reflected in the header
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'#privacy');
	exit;
case 'update':
	if (!WT_Filter::checkCsrf()) {
		break;
	}
	$WT_TREE->setPreference('ADVANCED_NAME_FACTS',          WT_Filter::post('ADVANCED_NAME_FACTS'));
	$WT_TREE->setPreference('ADVANCED_PLAC_FACTS',          WT_Filter::post('ADVANCED_PLAC_FACTS'));
	$WT_TREE->setPreference('ALLOW_THEME_DROPDOWN',         WT_Filter::postBool('ALLOW_THEME_DROPDOWN'));
	// For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
	// e.g. "gregorian_and_jewish"
	$WT_TREE->setPreference('CALENDAR_FORMAT',              implode('_and_', array_unique(array(
		WT_Filter::post('CALENDAR_FORMAT0', 'gregorian|julian|french|jewish|hijri|jalali', 'none'),
		WT_Filter::post('CALENDAR_FORMAT1', 'gregorian|julian|french|jewish|hijri|jalali', 'none')
	))));
	$WT_TREE->setPreference('CHART_BOX_TAGS',               WT_Filter::post('CHART_BOX_TAGS'));
	$WT_TREE->setPreference('COMMON_NAMES_ADD',             str_replace(' ', '', WT_Filter::post('COMMON_NAMES_ADD')));
	$WT_TREE->setPreference('COMMON_NAMES_REMOVE',          str_replace(' ', '', WT_Filter::post('COMMON_NAMES_REMOVE')));
	$WT_TREE->setPreference('COMMON_NAMES_THRESHOLD',       WT_Filter::post('COMMON_NAMES_THRESHOLD', WT_REGEX_INTEGER, 40));
	$WT_TREE->setPreference('CONTACT_USER_ID',              WT_Filter::post('CONTACT_USER_ID'));
	$WT_TREE->setPreference('DEFAULT_PEDIGREE_GENERATIONS', WT_Filter::post('DEFAULT_PEDIGREE_GENERATIONS'));
	$WT_TREE->setPreference('EXPAND_NOTES',                 WT_Filter::postBool('EXPAND_NOTES'));
	$WT_TREE->setPreference('EXPAND_RELATIVES_EVENTS',      WT_Filter::postBool('EXPAND_RELATIVES_EVENTS'));
	$WT_TREE->setPreference('EXPAND_SOURCES',               WT_Filter::postBool('EXPAND_SOURCES'));
	$WT_TREE->setPreference('FAM_FACTS_ADD',                str_replace(' ', '', WT_Filter::post('FAM_FACTS_ADD')));
	$WT_TREE->setPreference('FAM_FACTS_QUICK',              str_replace(' ', '', WT_Filter::post('FAM_FACTS_QUICK')));
	$WT_TREE->setPreference('FAM_FACTS_UNIQUE',             str_replace(' ', '', WT_Filter::post('FAM_FACTS_UNIQUE')));
	$WT_TREE->setPreference('FAM_ID_PREFIX',                WT_Filter::post('FAM_ID_PREFIX'));
	$WT_TREE->setPreference('FULL_SOURCES',                 WT_Filter::postBool('FULL_SOURCES'));
	$WT_TREE->setPreference('FORMAT_TEXT',                  WT_Filter::post('FORMAT_TEXT'));
	$WT_TREE->setPreference('GEDCOM_ID_PREFIX',             WT_Filter::post('GEDCOM_ID_PREFIX'));
	$WT_TREE->setPreference('GEDCOM_MEDIA_PATH',            WT_Filter::post('GEDCOM_MEDIA_PATH'));
	$WT_TREE->setPreference('GENERATE_UIDS',                WT_Filter::postBool('GENERATE_UIDS'));
	$WT_TREE->setPreference('GEONAMES_ACCOUNT',             WT_Filter::post('GEONAMES_ACCOUNT'));
	$WT_TREE->setPreference('HIDE_GEDCOM_ERRORS',           WT_Filter::postBool('HIDE_GEDCOM_ERRORS'));
	$WT_TREE->setPreference('HIDE_LIVE_PEOPLE',             WT_Filter::postBool('HIDE_LIVE_PEOPLE'));
	$WT_TREE->setPreference('INDI_FACTS_ADD',               str_replace(' ', '', WT_Filter::post('INDI_FACTS_ADD')));
	$WT_TREE->setPreference('INDI_FACTS_QUICK',             str_replace(' ', '', WT_Filter::post('INDI_FACTS_QUICK')));
	$WT_TREE->setPreference('INDI_FACTS_UNIQUE',            str_replace(' ', '', WT_Filter::post('INDI_FACTS_UNIQUE')));
	$WT_TREE->setPreference('KEEP_ALIVE_YEARS_BIRTH',       WT_Filter::post('KEEP_ALIVE_YEARS_BIRTH', WT_REGEX_INTEGER, 0));
	$WT_TREE->setPreference('KEEP_ALIVE_YEARS_DEATH',       WT_Filter::post('KEEP_ALIVE_YEARS_DEATH', WT_REGEX_INTEGER, 0));
	$WT_TREE->setPreference('LANGUAGE',                     WT_Filter::post('GEDCOMLANG'));
	$WT_TREE->setPreference('MAX_ALIVE_AGE',                WT_Filter::post('MAX_ALIVE_AGE', WT_REGEX_INTEGER, 100));
	$WT_TREE->setPreference('MAX_DESCENDANCY_GENERATIONS',  WT_Filter::post('MAX_DESCENDANCY_GENERATIONS'));
	$WT_TREE->setPreference('MAX_PEDIGREE_GENERATIONS',     WT_Filter::post('MAX_PEDIGREE_GENERATIONS'));
	$WT_TREE->setPreference('MEDIA_ID_PREFIX',              WT_Filter::post('MEDIA_ID_PREFIX'));
	$WT_TREE->setPreference('MEDIA_UPLOAD',                 WT_Filter::post('MEDIA_UPLOAD'));
	$WT_TREE->setPreference('META_DESCRIPTION',             WT_Filter::post('META_DESCRIPTION'));
	$WT_TREE->setPreference('META_TITLE',                   WT_Filter::post('META_TITLE'));
	$WT_TREE->setPreference('NOTE_ID_PREFIX',               WT_Filter::post('NOTE_ID_PREFIX'));
	$WT_TREE->setPreference('NO_UPDATE_CHAN',               WT_Filter::postBool('NO_UPDATE_CHAN'));
	$WT_TREE->setPreference('PEDIGREE_FULL_DETAILS',        WT_Filter::postBool('PEDIGREE_FULL_DETAILS'));
	$WT_TREE->setPreference('PEDIGREE_LAYOUT',              WT_Filter::postBool('PEDIGREE_LAYOUT'));
	$WT_TREE->setPreference('PEDIGREE_ROOT_ID',             WT_Filter::post('PEDIGREE_ROOT_ID', WT_REGEX_XREF));
	$WT_TREE->setPreference('PEDIGREE_SHOW_GENDER',         WT_Filter::postBool('PEDIGREE_SHOW_GENDER'));
	$WT_TREE->setPreference('PREFER_LEVEL2_SOURCES',        WT_Filter::post('PREFER_LEVEL2_SOURCES'));
	$WT_TREE->setPreference('QUICK_REQUIRED_FACTS',         WT_Filter::post('QUICK_REQUIRED_FACTS'));
	$WT_TREE->setPreference('QUICK_REQUIRED_FAMFACTS',      WT_Filter::post('QUICK_REQUIRED_FAMFACTS'));
	$WT_TREE->setPreference('REPO_FACTS_ADD',               str_replace(' ', '', WT_Filter::post('REPO_FACTS_ADD')));
	$WT_TREE->setPreference('REPO_FACTS_QUICK',             str_replace(' ', '', WT_Filter::post('REPO_FACTS_QUICK')));
	$WT_TREE->setPreference('REPO_FACTS_UNIQUE',            str_replace(' ', '', WT_Filter::post('REPO_FACTS_UNIQUE')));
	$WT_TREE->setPreference('REPO_ID_PREFIX',               WT_Filter::post('REPO_ID_PREFIX'));
	$WT_TREE->setPreference('REQUIRE_AUTHENTICATION',       WT_Filter::postBool('REQUIRE_AUTHENTICATION'));
	$WT_TREE->setPreference('SAVE_WATERMARK_IMAGE',         WT_Filter::postBool('SAVE_WATERMARK_IMAGE'));
	$WT_TREE->setPreference('SAVE_WATERMARK_THUMB',         WT_Filter::postBool('SAVE_WATERMARK_THUMB'));
	$WT_TREE->setPreference('SHOW_AGE_DIFF',                WT_Filter::postBool('SHOW_AGE_DIFF'));
	$WT_TREE->setPreference('SHOW_COUNTER',                 WT_Filter::postBool('SHOW_COUNTER'));
	$WT_TREE->setPreference('SHOW_DEAD_PEOPLE',             WT_Filter::post('SHOW_DEAD_PEOPLE'));
	$WT_TREE->setPreference('SHOW_EST_LIST_DATES',          WT_Filter::postBool('SHOW_EST_LIST_DATES'));
	$WT_TREE->setPreference('SHOW_FACT_ICONS',              WT_Filter::postBool('SHOW_FACT_ICONS'));
	$WT_TREE->setPreference('SHOW_GEDCOM_RECORD',           WT_Filter::postBool('SHOW_GEDCOM_RECORD'));
	$WT_TREE->setPreference('SHOW_HIGHLIGHT_IMAGES',        WT_Filter::postBool('SHOW_HIGHLIGHT_IMAGES'));
	$WT_TREE->setPreference('SHOW_LAST_CHANGE',             WT_Filter::postBool('SHOW_LAST_CHANGE'));
	$WT_TREE->setPreference('SHOW_LDS_AT_GLANCE',           WT_Filter::postBool('SHOW_LDS_AT_GLANCE'));
	$WT_TREE->setPreference('SHOW_LEVEL2_NOTES',            WT_Filter::postBool('SHOW_LEVEL2_NOTES'));
	$WT_TREE->setPreference('SHOW_LIVING_NAMES',            WT_Filter::post('SHOW_LIVING_NAMES'));
	$WT_TREE->setPreference('SHOW_MEDIA_DOWNLOAD',          WT_Filter::postBool('SHOW_MEDIA_DOWNLOAD'));
	$WT_TREE->setPreference('SHOW_NO_WATERMARK',            WT_Filter::post('SHOW_NO_WATERMARK'));
	$WT_TREE->setPreference('SHOW_PARENTS_AGE',             WT_Filter::postBool('SHOW_PARENTS_AGE'));
	$WT_TREE->setPreference('SHOW_PEDIGREE_PLACES',         WT_Filter::post('SHOW_PEDIGREE_PLACES'));
	$WT_TREE->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX',  WT_Filter::postBool('SHOW_PEDIGREE_PLACES_SUFFIX'));
	$WT_TREE->setPreference('SHOW_PRIVATE_RELATIONSHIPS',   WT_Filter::post('SHOW_PRIVATE_RELATIONSHIPS'));
	$WT_TREE->setPreference('SHOW_RELATIVES_EVENTS',        WT_Filter::post('SHOW_RELATIVES_EVENTS'));
	$WT_TREE->setPreference('SHOW_STATS',                   WT_Filter::postBool('SHOW_STATS'));
	$WT_TREE->setPreference('SOURCE_ID_PREFIX',             WT_Filter::post('SOURCE_ID_PREFIX'));
	$WT_TREE->setPreference('SOUR_FACTS_ADD',               str_replace(' ', '', WT_Filter::post('SOUR_FACTS_ADD')));
	$WT_TREE->setPreference('SOUR_FACTS_QUICK',             str_replace(' ', '', WT_Filter::post('SOUR_FACTS_QUICK')));
	$WT_TREE->setPreference('SOUR_FACTS_UNIQUE',            str_replace(' ', '', WT_Filter::post('SOUR_FACTS_UNIQUE')));
	$WT_TREE->setPreference('SUBLIST_TRIGGER_I',            WT_Filter::post('SUBLIST_TRIGGER_I', WT_REGEX_INTEGER, 200));
	$WT_TREE->setPreference('SURNAME_LIST_STYLE',           WT_Filter::post('SURNAME_LIST_STYLE'));
	$WT_TREE->setPreference('SURNAME_TRADITION',            WT_Filter::post('SURNAME_TRADITION'));
	$WT_TREE->setPreference('THEME_DIR',                    WT_Filter::post('THEME_DIR'));
	$WT_TREE->setPreference('THUMBNAIL_WIDTH',              WT_Filter::post('THUMBNAIL_WIDTH'));
	$WT_TREE->setPreference('USE_RIN',                      WT_Filter::postBool('USE_RIN'));
	$WT_TREE->setPreference('USE_SILHOUETTE',               WT_Filter::postBool('USE_SILHOUETTE'));
	$WT_TREE->setPreference('WATERMARK_THUMB',              WT_Filter::postBool('WATERMARK_THUMB'));
	$WT_TREE->setPreference('WEBMASTER_USER_ID',            WT_Filter::post('WEBMASTER_USER_ID'));
	$WT_TREE->setPreference('WEBTREES_EMAIL',               WT_Filter::post('WEBTREES_EMAIL'));
	$WT_TREE->setPreference('WORD_WRAPPED_NOTES',           WT_Filter::postBool('WORD_WRAPPED_NOTES'));
	if (WT_Filter::post('gedcom_title')) {
		$WT_TREE->setPreference('title',                        WT_Filter::post('gedcom_title'));
	}
	// Only accept valid folders for MEDIA_DIRECTORY
	$MEDIA_DIRECTORY = preg_replace('/[\/\\\\]+/', '/', WT_Filter::post('MEDIA_DIRECTORY') . '/');
	if (substr($MEDIA_DIRECTORY, 0, 1) == '/') {
		$MEDIA_DIRECTORY = substr($MEDIA_DIRECTORY, 1);
	}

	if ($MEDIA_DIRECTORY) {
		if (is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
			$WT_TREE->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
		} elseif (WT_File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
			$WT_TREE->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was created.', WT_DATA_DIR . $MEDIA_DIRECTORY));
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', WT_DATA_DIR . $MEDIA_DIRECTORY));
		}
	}

	$gedcom = WT_Filter::post('gedcom');
	if ($gedcom && $gedcom != WT_GEDCOM) {
		try {
			WT_DB::prepare("UPDATE `##gedcom` SET gedcom_name = ? WHERE gedcom_id = ?")->execute(array($gedcom, WT_GED_ID));
			WT_DB::prepare("UPDATE `##site_setting` SET setting_value = ? WHERE setting_name='DEFAULT_GEDCOM' AND setting_value = ?")->execute(array($gedcom, WT_GEDCOM));
		} catch (Exception $ex) {
			// Probably a duplicate name.
			$gedcom = WT_GEDCOM;
		}
	}

	// Reload the page, so that the settings take effect immediately.
	Zend_Session::writeClose();
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?ged=' . $gedcom);
	exit;
}

$controller
	->pageHeader()
	->addInlineJavascript('jQuery("#tabs").tabs(); jQuery("#tabs").css("display", "inline");')
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');


?>
<form enctype="multipart/form-data" method="post" id="configform" name="configform" action="<?php echo WT_SCRIPT_NAME; ?>">
	<?php echo WT_Filter::getCsrf(); ?>
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">

	<div id="tabs">
		<ul>
			<li><a href="#file-options"><span><?php echo WT_I18N::translate('General'); ?></span></a></li>
			<li><a href="#privacy"><span><?php echo WT_I18N::translate('Privacy'); ?></span></a></li>
			<li><a href="#config-media"><span><?php echo WT_I18N::translate('Media'); ?></span></a></li>
			<li><a href="#layout-options"><span><?php echo WT_I18N::translate('Layout'); ?></span></a></li>
			<li><a href="#hide-show"><span><?php echo WT_I18N::translate('Hide &amp; show'); ?></span></a></li>
			<li><a href="#edit-options"><span><?php echo WT_I18N::translate('Edit options'); ?></span></a></li>
		</ul>
		<!-- GENERAL -->
		<div id="file-options">
			<table>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Family tree title'); ?>
					</td>
					<td>
						<input type="text" name="gedcom_title" dir="ltr" value="<?php echo WT_Filter::escapeHtml($WT_TREE->getPreference('title')); ?>" size="50" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('URL'); ?>
					</td>
					<td>
						<?php echo WT_SERVER_NAME, WT_SCRIPT_PATH ?>index.php?ged=<input type="text" name="gedcom" dir="ltr" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>" size="20" maxlength="255">
					</td>
				</tr>
				<tr>
					<td><?php echo WT_I18N::translate('Language'), help_link('LANGUAGE'); ?></td>
					<td><?php echo edit_field_language('GEDCOMLANG', $WT_TREE->getPreference('LANGUAGE')); ?></td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default individual'), help_link('default_individual'); ?>
					</td>
					<td class="wrap">
						<input data-autocomplete-type="INDI" type="text" name="PEDIGREE_ROOT_ID" id="PEDIGREE_ROOT_ID" value="<?php echo $WT_TREE->getPreference('PEDIGREE_ROOT_ID'); ?>" size="5" maxlength="20">
						<?php
							echo print_findindi_link('PEDIGREE_ROOT_ID');
							$person=WT_Individual::getInstance($WT_TREE->getPreference('PEDIGREE_ROOT_ID'));
							if ($person) {
								echo ' <span class="list_item">', $person->getFullName(), ' ', $person->format_first_major_fact(WT_EVENTS_BIRT, 1), '</span>';
							} else {
								echo ' <span class="error">', WT_I18N::translate('Unable to find record with ID'), '</span>';
							}
						?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Calendar conversion'), help_link('CALENDAR_FORMAT'); ?>
					</td>
					<td>
						<select id="CALENDAR_FORMAT0" name="CALENDAR_FORMAT0">
						<?php
						$CALENDAR_FORMATS=explode('_and_', $CALENDAR_FORMAT);
						if (count($CALENDAR_FORMATS)==1) {
							$CALENDAR_FORMATS[]='none';
						}
						foreach (array(
							'none'     =>WT_I18N::translate('No calendar conversion'),
							'gregorian'=>WT_Date_Gregorian::calendarName(),
							'julian'   =>WT_Date_Julian::calendarName(),
							'french'   =>WT_Date_French::calendarName(),
							'jewish'   =>WT_Date_Jewish::calendarName(),
							'hijri'    =>WT_Date_Hijri::calendarName(),
							'jalali'   =>WT_Date_Jalali::calendarName(),
						) as $cal=>$name) {
							echo '<option value="', $cal, '"';
							if ($CALENDAR_FORMATS[0]==$cal) {
								echo ' selected="selected"';
							}
							echo '>', $name, '</option>';
						}
						?>
					</select>

					<select id="CALENDAR_FORMAT1" name="CALENDAR_FORMAT1">
						<?php
						foreach (array(
							'none'     =>WT_I18N::translate('No calendar conversion'),
							'gregorian'=>WT_Date_Gregorian::calendarName(),
							'julian'   =>WT_Date_Julian::calendarName(),
							'french'   =>WT_Date_French::calendarName(),
							'jewish'   =>WT_Date_Jewish::calendarName(),
							'hijri'    =>WT_Date_Hijri::calendarName(),
							'jalali'   =>WT_Date_Jalali::calendarName(),
						) as $cal=>$name) {
							echo '<option value="', $cal, '"';
							if ($CALENDAR_FORMATS[1]==$cal) {
								echo ' selected="selected"';
							}
							echo '>', $name, '</option>';
						}
						?>
					</select></td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Use RIN number instead of GEDCOM ID'), help_link('USE_RIN'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('USE_RIN', $WT_TREE->getPreference('USE_RIN')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically create globally unique IDs'), help_link('GENERATE_GUID'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('GENERATE_UIDS', $WT_TREE->getPreference('GENERATE_UIDS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Add spaces where notes were wrapped'), help_link('WORD_WRAPPED_NOTES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('WORD_WRAPPED_NOTES', $WT_TREE->getPreference('WORD_WRAPPED_NOTES')); ?>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th colspan="6"><?php echo WT_I18N::translate('ID settings'); ?></th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Individual ID prefix'), help_link('GEDCOM_ID_PREFIX'); ?>
					</td>
					<td>
						<input type="text" name="GEDCOM_ID_PREFIX" dir="ltr" value="<?php echo $WT_TREE->getPreference('GEDCOM_ID_PREFIX'); ?>" size="5" maxlength="20">
					</td>
					<td>
						<?php echo WT_I18N::translate('Family ID prefix'), help_link('FAM_ID_PREFIX'); ?>
					</td>
					<td>
						<input type="text" name="FAM_ID_PREFIX" dir="ltr" value="<?php echo $WT_TREE->getPreference('FAM_ID_PREFIX'); ?>" size="5" maxlength="20">
					</td>
					<td>
						<?php echo WT_I18N::translate('Source ID prefix'), help_link('SOURCE_ID_PREFIX'); ?>
					</td>
					<td>
						<input type="text" name="SOURCE_ID_PREFIX" dir="ltr" value="<?php echo $WT_TREE->getPreference('SOURCE_ID_PREFIX'); ?>" size="5" maxlength="20">
					</td>
				</tr>
				<tr>
					<td><?php echo WT_I18N::translate('Repository ID prefix'), help_link('REPO_ID_PREFIX'); ?></td>
					<td><input type="text" name="REPO_ID_PREFIX" dir="ltr" value="<?php echo $WT_TREE->getPreference('REPO_ID_PREFIX'); ?>" size="5" maxlength="20">
					</td>
					<td><?php echo WT_I18N::translate('Media ID prefix'), help_link('MEDIA_ID_PREFIX'); ?></td>
					<td><input type="text" name="MEDIA_ID_PREFIX" dir="ltr" value="<?php echo $WT_TREE->getPreference('MEDIA_ID_PREFIX'); ?>" size="5" maxlength="20">
					</td>
					<td><?php echo WT_I18N::translate('Note ID prefix'), help_link('NOTE_ID_PREFIX'); ?></td>
					<td><input type="text" name="NOTE_ID_PREFIX" dir="ltr" value="<?php echo $WT_TREE->getPreference('NOTE_ID_PREFIX'); ?>" size="5" maxlength="20">
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th colspan="2"><?php echo WT_I18N::translate('Contact information'); ?></th>
				</tr>
				<tr>
					<?php
					if (empty($WEBTREES_EMAIL)) {
						$WEBTREES_EMAIL = "webtrees-noreply@".preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
					}
					?>
					<td>
						<?php echo WT_I18N::translate('webtrees reply address'), help_link('WEBTREES_EMAIL'); ?>
					</td>
					<td><input type="text" name="WEBTREES_EMAIL" value="<?php echo $WEBTREES_EMAIL; ?>" size="50" maxlength="255" dir="ltr"></td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Genealogy contact'), help_link('CONTACT_USER_ID'); ?>
					</td>
					<td><select name="CONTACT_USER_ID">
					<?php
						$CONTACT_USER_ID = $WT_TREE->getPreference('CONTACT_USER_ID');
						foreach (User::all() as $user) {
							if ($user->getPreference('verified_by_admin')) {
								echo "<option value=\"" . $user->getUserId() . "\"";
								if ($CONTACT_USER_ID == $user->getUserId()) {
									echo " selected=\"selected\"";
								}
								echo '>' . WT_Filter::escapeHtml($user->getRealName()) . ' - ' . WT_Filter::escapeHtml($user->getUserName()) . '</option>';
							}
						}
					?>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Technical help contact'), help_link('WEBMASTER_USER_ID'); ?>
					</td>
					<td><select name="WEBMASTER_USER_ID">
					<?php
						$WEBMASTER_USER_ID = $WT_TREE->getPreference('WEBMASTER_USER_ID');
						foreach (User::allAdmins() as $user) {
							echo '<option value="' . $user->getUserId() . '"';
							if ($WEBMASTER_USER_ID == $user->getUserId()) {
								echo ' selected="selected"';
							}
							echo '>' . WT_Filter::escapeHtml($user->getRealName()) . ' - ' . WT_Filter::escapeHtml($user->getUserName()) . '</option>';
						}
					?>
					</select>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th colspan="2"><?php echo WT_I18N::translate('Web site and META tag settings'); ?></th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Add to TITLE header tag'), help_link('META_TITLE'); ?>
					</td>
					<td>
						<input type="text" dir="ltr" name="META_TITLE" value="<?php echo WT_Filter::escapeHtml($WT_TREE->getPreference('META_TITLE')); ?>" size="40" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Description META tag'), help_link('META_DESCRIPTION'); ?>
					</td>
					<td>
						<input type="text" dir="ltr" name="META_DESCRIPTION" value="<?php echo $WT_TREE->getPreference('META_DESCRIPTION'); ?>" size="40" maxlength="255">
						<br>
						<?php echo WT_I18N::translate('Leave this field empty to use the name of the family tree.'); ?>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('User options'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Theme dropdown selector for theme changes'), help_link('ALLOW_THEME_DROPDOWN'); ?>
					</td>
					<td>
						<?php echo radio_buttons('ALLOW_THEME_DROPDOWN', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('ALLOW_THEME_DROPDOWN')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default theme'), help_link('THEME'); ?>
					</td>
					<td>
						<select name="THEME_DIR">
							<?php
								echo '<option value="">', WT_Filter::escapeHtml(WT_I18N::translate('<default theme>')), '</option>';
								$current_themedir=$WT_TREE->getPreference('THEME_DIR');
								foreach (get_theme_names() as $themename=>$themedir) {
									echo '<option value="', $themedir, '"';
									if ($themedir==$current_themedir) {
										echo ' selected="selected"';
									}
									echo '>', $themename, '</option>';
								}
							?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<!-- PRIVACY OPTIONS -->
		<div id="privacy">
			<table>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Require visitor authentication'), help_link('REQUIRE_AUTHENTICATION'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('REQUIRE_AUTHENTICATION', $WT_TREE->getPreference('REQUIRE_AUTHENTICATION')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Privacy options'), help_link('HIDE_LIVE_PEOPLE'); ?>
					</td>
					<td>
						<?php  echo radio_buttons('HIDE_LIVE_PEOPLE', array(false=>WT_I18N::translate('disable'), true=>WT_I18N::translate('enable')), $WT_TREE->getPreference('HIDE_LIVE_PEOPLE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show dead individuals'), help_link('SHOW_DEAD_PEOPLE'); ?>
					</td>
					<td>
						<?php echo edit_field_access_level("SHOW_DEAD_PEOPLE", $WT_TREE->getPreference('SHOW_DEAD_PEOPLE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php /* I18N: ... [who were] born in the last XX years or died in the last YY years */ echo WT_I18N::translate('Extend privacy to dead individuals'), help_link('KEEP_ALIVE'); ?>
					</td>
					<td>
						<?php
						echo
							/* I18N: Extend privacy to dead people [who were] ... */ WT_I18N::translate(
								'born in the last %1$s years or died in the last %2$s years',
								'<input type="text" name="KEEP_ALIVE_YEARS_BIRTH" value="'.$WT_TREE->getPreference('KEEP_ALIVE_YEARS_BIRTH').'" size="5" maxlength="3">',
								'<input type="text" name="KEEP_ALIVE_YEARS_DEATH" value="'.$WT_TREE->getPreference('KEEP_ALIVE_YEARS_DEATH').'" size="5" maxlength="3">'
							); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Names of private individuals'), help_link('SHOW_LIVING_NAMES'); ?>
					</td>
					<td>
						<?php echo edit_field_access_level("SHOW_LIVING_NAMES", $WT_TREE->getPreference('SHOW_LIVING_NAMES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show private relationships'), help_link('SHOW_PRIVATE_RELATIONSHIPS'); ?>
					</td>
					<td>
						<?php  echo edit_field_yes_no('SHOW_PRIVATE_RELATIONSHIPS', $WT_TREE->getPreference('SHOW_PRIVATE_RELATIONSHIPS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Age at which to assume an individual is dead'), help_link('MAX_ALIVE_AGE'); ?>
					</td>
					<td>
						<input type="text" name="MAX_ALIVE_AGE" value="<?php echo $WT_TREE->getPreference('MAX_ALIVE_AGE'); ?>" size="5" maxlength="3">
					</td>
				</tr>
			</table>
			<br>
			<table>
				<tr>
					<th colspan="4">
						<?php echo WT_I18N::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag'); ?>
					</th>
				</tr>
		<?php

		$all_tags=array();
		$tags=array_unique(array_merge(
			explode(',', $WT_TREE->getPreference('INDI_FACTS_ADD')), explode(',', $WT_TREE->getPreference('INDI_FACTS_UNIQUE')),
			explode(',', $WT_TREE->getPreference('FAM_FACTS_ADD' )), explode(',', $WT_TREE->getPreference('FAM_FACTS_UNIQUE' )),
			explode(',', $WT_TREE->getPreference('NOTE_FACTS_ADD')), explode(',', $WT_TREE->getPreference('NOTE_FACTS_UNIQUE')),
			explode(',', $WT_TREE->getPreference('SOUR_FACTS_ADD')), explode(',', $WT_TREE->getPreference('SOUR_FACTS_UNIQUE')),
			explode(',', $WT_TREE->getPreference('REPO_FACTS_ADD')), explode(',', $WT_TREE->getPreference('REPO_FACTS_UNIQUE')),
			array('SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN')
		));

		foreach ($tags as $tag) {
			if ($tag) {
				$all_tags[$tag]=WT_Gedcom_Tag::getLabel($tag);
			}
		}

		uasort($all_tags, array('WT_I18N', 'strcasecmp'));

		echo '<tr><td>';
		echo '<input type="text" class="pedigree_form" name="xref" id="xref" size="6" maxlength="20">';
		echo ' ', print_findindi_link('xref');
		echo ' ', print_findfamily_link('xref');
		echo ' ', print_findsource_link('xref');
		echo ' ', print_findrepository_link('xref');
		echo ' ', print_findnote_link('xref');
		echo ' ', print_findmedia_link('xref', '1media');
		echo '</td><td>';
		echo select_edit_control('tag_type', $all_tags, '', null, null);
		echo '</td><td>';
		echo select_edit_control('resn', $PRIVACY_CONSTANTS, null, 'privacy', null);
		echo '</td><td>';
		echo '<input type="button" value="', WT_I18N::translate('Add'), '" onClick="document.configform.elements[\'action\'].value=\'add\';document.configform.submit();">';
		echo '<input type="hidden" name="default_resn_id" value="">'; // value set by JS
		echo '</td></tr>';
		$rows=WT_DB::prepare(
			"SELECT default_resn_id, tag_type, xref, resn".
			" FROM `##default_resn`".
			" LEFT JOIN `##name` ON (gedcom_id=n_file AND xref=n_id AND n_num=0)".
			" WHERE gedcom_id=?".
			" ORDER BY xref IS NULL, n_sort, xref, tag_type"
		)->execute(array(WT_GED_ID))->fetchAll();
		foreach ($rows as $row) {
			echo '<tr><td>';
			if ($row->xref) {
				$record=WT_GedcomRecord::getInstance($row->xref);
				if ($record) {
					echo '<a href="', $record->getHtmlUrl(), '">', $record->getFullName(), '</a>';
				} else {
					echo WT_I18N::translate('this record does not exist');
				}
			} else {
				echo '&nbsp;';
			}
			echo '</td><td>';
			if ($row->tag_type) {
				// I18N: e.g. Marriage (MARR)
				echo WT_Gedcom_Tag::getLabel($row->tag_type);
			} else {
				echo '&nbsp;';
			}
			echo '</td><td>';
			echo $PRIVACY_CONSTANTS[$row->resn];
			echo '</td><td>';
			echo '<input type="button" value="', WT_I18N::translate('Delete'), '" onClick="document.configform.elements[\'action\'].value=\'delete\';document.configform.elements[\'default_resn_id\'].value=\''.$row->default_resn_id.'\';document.configform.submit();">';
			echo '</td></tr>';
		}
		echo '</table>';
		?>
		</div>
		<!--  MULTIMEDIA -->
		<div id="config-media">
			<table>
				<tr>
					<th colspan="2"><?php echo WT_I18N::translate('Media folders'); ?></th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Media folder'), help_link('MEDIA_DIRECTORY'); ?>
					</td>
					<td>
						<?php echo WT_DATA_DIR; ?><input type="text" name="MEDIA_DIRECTORY" value="<?php echo $WT_TREE->getPreference('MEDIA_DIRECTORY'); ?>" dir="ltr" size="15" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo /* I18N: A media path (e.g. c:\aaa\bbb\ccc\ddd.jpeg) in a GEDCOM file */ WT_I18N::translate('GEDCOM media path'), help_link('GEDCOM_MEDIA_PATH'); ?>
					</td>
					<td>
						<input type="text" name="GEDCOM_MEDIA_PATH" value="<?php echo $WT_TREE->getPreference('GEDCOM_MEDIA_PATH'); ?>" dir="ltr" size="30" maxlength="255">
					</td>
				</tr>
				<tr>
					<th colspan="2"><?php echo WT_I18N::translate('Media files'); ?></th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Who can upload new media files?'), help_link('MEDIA_UPLOAD'); ?>
					</td>
					<td>
						<?php echo select_edit_control('MEDIA_UPLOAD', array(WT_PRIV_USER=>WT_I18N::translate('Show to members'),
	 WT_PRIV_NONE=>WT_I18N::translate('Show to managers'), WT_PRIV_HIDE=>WT_I18N::translate('Hide from everyone')), null, $WT_TREE->getPreference('MEDIA_UPLOAD')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show download link in media viewer'), help_link('SHOW_MEDIA_DOWNLOAD'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('SHOW_MEDIA_DOWNLOAD', $WT_TREE->getPreference('SHOW_MEDIA_DOWNLOAD')); ?>
					</td>
				</tr>
				<tr>
					<th colspan="2"><?php echo /* I18N: Small versions of images */ WT_I18N::translate('Thumbnail images'); ?></th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Width of generated thumbnails'), help_link('THUMBNAIL_WIDTH'); ?>
					</td>
					<td>
						<input type="text" name="THUMBNAIL_WIDTH" value="<?php echo $WT_TREE->getPreference('THUMBNAIL_WIDTH'); ?>" size="5" maxlength="4">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Use silhouettes'), help_link('USE_SILHOUETTE'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('USE_SILHOUETTE', $WT_TREE->getPreference('USE_SILHOUETTE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show highlight images in individual boxes'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('SHOW_HIGHLIGHT_IMAGES', $WT_TREE->getPreference('SHOW_HIGHLIGHT_IMAGES')); ?>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo /* I18N: Copyright messages, added to images */ WT_I18N::translate('Watermarks'), help_link('Watermarks'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Add watermarks to thumbnails?'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('WATERMARK_THUMB', $WT_TREE->getPreference('WATERMARK_THUMB')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Store watermarked full size images on server?'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('SAVE_WATERMARK_IMAGE', $WT_TREE->getPreference('SAVE_WATERMARK_IMAGE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Store watermarked thumbnails on server?'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('SAVE_WATERMARK_THUMB', $WT_TREE->getPreference('SAVE_WATERMARK_THUMB')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Images without watermarks'); ?>
					</td>
					<td>
						<?php echo edit_field_access_level("SHOW_NO_WATERMARK", $WT_TREE->getPreference('SHOW_NO_WATERMARK')); ?>
					</td>
				</tr>
			</table>
		</div>
		<!-- LAYOUT -->
		<div id="layout-options">
			<table>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Names'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Min. no. of occurrences to be a “common surname”'), help_link('COMMON_NAMES_THRESHOLD'); ?>
					</td>
					<td>
						<input type="text" name="COMMON_NAMES_THRESHOLD" value="<?php echo $WT_TREE->getPreference('COMMON_NAMES_THRESHOLD'); ?>" size="5" maxlength="5">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Names to add to common surnames (comma separated)'), help_link('COMMON_NAMES_ADD'); ?>
					</td>
					<td>
						<input type="text" name="COMMON_NAMES_ADD" dir="ltr" value="<?php echo $WT_TREE->getPreference('COMMON_NAMES_ADD'); ?>" size="50" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Names to remove from common surnames (comma separated)'), help_link('COMMON_NAMES_REMOVE'); ?>
					</td>
					<td>
						<input type="text" name="COMMON_NAMES_REMOVE" dir="ltr" value="<?php echo $WT_TREE->getPreference('COMMON_NAMES_REMOVE'); ?>" size="50" maxlength="255">
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Lists'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Surname list style'); ?>
					</td>
					<td>
						<select name="SURNAME_LIST_STYLE">
							<option value="style1" <?php if ($WT_TREE->getPreference('SURNAME_LIST_STYLE') == 'style1') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('list'); ?></option>
							<option value="style2" <?php if ($WT_TREE->getPreference('SURNAME_LIST_STYLE') === 'style2') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('table'); ?></option>
							<option value="style3" <?php if ($WT_TREE->getPreference('SURNAME_LIST_STYLE') === 'style3') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('tag cloud'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Maximum number of surnames on individual list'), help_link('SUBLIST_TRIGGER_I'); ?>
					</td>
					<td>
						<input type="text" name="SUBLIST_TRIGGER_I" value="<?php echo $WT_TREE->getPreference('SUBLIST_TRIGGER_I'); ?>" size="5" maxlength="5">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Estimated dates for birth and death'), help_link('SHOW_EST_LIST_DATES'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_EST_LIST_DATES', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_EST_LIST_DATES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('The date and time of the last update'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_LAST_CHANGE', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_LAST_CHANGE')); ?>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Charts'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default pedigree chart layout'), help_link('PEDIGREE_LAYOUT'); ?>
					</td>
					<td>
						<select name="PEDIGREE_LAYOUT">
							<option value="yes" <?php if ($WT_TREE->getPreference('PEDIGREE_LAYOUT')) echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Landscape'); ?></option>
							<option value="no" <?php if (!$WT_TREE->getPreference('PEDIGREE_LAYOUT')) echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Portrait'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default pedigree generations'), help_link('DEFAULT_PEDIGREE_GENERATIONS'); ?>
					</td>
					<td>
						<input type="text" name="DEFAULT_PEDIGREE_GENERATIONS" value="<?php echo $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS'); ?>" size="5" maxlength="3">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Maximum pedigree generations'), help_link('MAX_PEDIGREE_GENERATIONS'); ?>
					</td>
					<td>
						<input type="text" name="MAX_PEDIGREE_GENERATIONS" value="<?php echo $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS'); ?>" size="5" maxlength="3">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Maximum descendancy generations'), help_link('MAX_DESCENDANCY_GENERATIONS'); ?>
					</td>
					<td>
						<input type="text" name="MAX_DESCENDANCY_GENERATIONS" value="<?php echo $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS'); ?>" size="5" maxlength="3">
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Individual pages'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically expand list of events of close relatives'), help_link('EXPAND_RELATIVES_EVENTS'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('EXPAND_RELATIVES_EVENTS', $WT_TREE->getPreference('EXPAND_RELATIVES_EVENTS')); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php echo WT_I18N::translate('Show events of close relatives on individual page'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="SHOW_RELATIVES_EVENTS" value="<?php echo $WT_TREE->getPreference('SHOW_RELATIVES_EVENTS'); ?>">
						<table id="relatives">
							<?php
							$rel_events=array(
								array('_BIRT_GCHI', '_MARR_GCHI', '_DEAT_GCHI'),
								array('_BIRT_CHIL', '_MARR_CHIL', '_DEAT_CHIL'),
								array('_BIRT_SIBL', '_MARR_SIBL', '_DEAT_SIBL'),
								array(null,         null,         '_DEAT_SPOU'),
								array(null,         '_MARR_PARE', '_DEAT_PARE'),
								array(null,         null,         '_DEAT_GPAR'),
							);
							foreach ($rel_events as $row) {
								echo '<tr>';
								foreach ($row as $col) {
									echo '<td>';
									if (is_null($col)) {
										echo '&nbsp;';
									} else {
										echo "<input type=\"checkbox\" name=\"SHOW_RELATIVES_EVENTS_checkbox\" value=\"".$col."\"";
										if (strstr($WT_TREE->getPreference('SHOW_RELATIVES_EVENTS'), $col)) {
											echo " checked=\"checked\"";
										}
										echo " onchange=\"var old=document.configform.SHOW_RELATIVES_EVENTS.value; if (this.checked) old+=','+this.value; else old=old.replace(/".$col."/g,''); old=old.replace(/[,]+/gi,','); old=old.replace(/^[,]/gi,''); old=old.replace(/[,]$/gi,''); document.configform.SHOW_RELATIVES_EVENTS.value=old\"> ";
										echo WT_Gedcom_Tag::getLabel($col);
									}
									echo '</td>';
								}
								echo '</tr>';
							}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Places'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Abbreviate place names'), help_link('SHOW_PEDIGREE_PLACES'); ?>
					</td>
					<td>
						<?php
						echo /* I18N: The placeholders are edit controls.  Show the [first/last] [1/2/3/4/5] parts of a place name */ WT_I18N::translate(
							'Show the %1$s %2$s parts of a place name.',
							select_edit_control('SHOW_PEDIGREE_PLACES_SUFFIX',
								array(
									false=>WT_I18N::translate_c('Show the [first/last] [N] parts of a place name.', 'first'),
									true =>WT_I18N::translate_c('Show the [first/last] [N] parts of a place name.', 'last')
								),
								null,
								$WT_TREE->getPreference('SHOW_PEDIGREE_PLACES_SUFFIX')
							),
							select_edit_control('SHOW_PEDIGREE_PLACES',
								array(
									1=>WT_I18N::number(1),
									2=>WT_I18N::number(2),
									3=>WT_I18N::number(3),
									4=>WT_I18N::number(4),
									5=>WT_I18N::number(5),
									6=>WT_I18N::number(6),
									7=>WT_I18N::number(7),
									8=>WT_I18N::number(8),
									9=>WT_I18N::number(9),
								),
								null,
								$WT_TREE->getPreference('SHOW_PEDIGREE_PLACES')
							)
						);
						?>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_Gedcom_Tag::getLabel('TEXT'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Format text and notes'), help_link('FORMAT_TEXT'); ?>
					</td>
					<td>
						<?php
						echo select_edit_control('FORMAT_TEXT',
								array(
									''         => WT_I18N::translate('none'),
									'markdown' => /* I18N: https://en.wikipedia.org/wiki/Markdown */ WT_I18N::translate('markdown')
								),
								null,
								$WT_TREE->getPreference('FORMAT_TEXT')
							);
						?>
					</td>
				</tr>
			</table>
		</div>
		<!-- HIDE & SHOW -->
		<div id="hide-show">
			<table>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Charts'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show chart details by default'), help_link('PEDIGREE_FULL_DETAILS'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('PEDIGREE_FULL_DETAILS', $WT_TREE->getPreference('PEDIGREE_FULL_DETAILS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Gender icon on charts'), help_link('PEDIGREE_SHOW_GENDER'); ?>
					</td>
					<td>
						<?php echo radio_buttons('PEDIGREE_SHOW_GENDER', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('PEDIGREE_SHOW_GENDER')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Age of parents next to child’s birthdate'), help_link('SHOW_PARENTS_AGE'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_PARENTS_AGE', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_PARENTS_AGE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('LDS ordinance codes in chart boxes'), help_link('SHOW_LDS_AT_GLANCE'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_LDS_AT_GLANCE', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_LDS_AT_GLANCE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Other facts to show in charts'), help_link('CHART_BOX_TAGS'); ?>
					</td>
					<td>
						<input type="text" id="CHART_BOX_TAGS" name="CHART_BOX_TAGS" value="<?php echo $WT_TREE->getPreference('CHART_BOX_TAGS'); ?>" dir="ltr" size="50" maxlength="255"><?php echo print_findfact_link('CHART_BOX_TAGS'); ?>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('Individual pages'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Fact icons'), help_link('SHOW_FACT_ICONS'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_FACT_ICONS', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_FACT_ICONS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically expand notes'), help_link('EXPAND_NOTES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('EXPAND_NOTES', $WT_TREE->getPreference('EXPAND_NOTES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically expand sources'), help_link('EXPAND_SOURCES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('EXPAND_SOURCES', $WT_TREE->getPreference('EXPAND_SOURCES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show all notes and source references on notes and sources tabs'), help_link('SHOW_LEVEL2_NOTES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('SHOW_LEVEL2_NOTES', $WT_TREE->getPreference('SHOW_LEVEL2_NOTES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Date differences'), help_link('SHOW_AGE_DIFF'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_AGE_DIFF', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_AGE_DIFF')); ?>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('General'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Allow users to see raw GEDCOM records'), help_link('SHOW_GEDCOM_RECORD'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('SHOW_GEDCOM_RECORD', $WT_TREE->getPreference('SHOW_GEDCOM_RECORD')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('GEDCOM errors'), help_link('HIDE_GEDCOM_ERRORS'); ?>
					</td>
					<td>
						<?php echo radio_buttons('HIDE_GEDCOM_ERRORS', array(true=>WT_I18N::translate('hide'), false=>WT_I18N::translate('show')), $WT_TREE->getPreference('HIDE_GEDCOM_ERRORS')); /* Note: name of object is reverse of description */ ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Hit counters'), help_link('SHOW_COUNTER'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_COUNTER', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_COUNTER')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Execution statistics'), help_link('SHOW_STATS'); ?>
					</td>
					<td>
						<?php echo radio_buttons('SHOW_STATS', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $WT_TREE->getPreference('SHOW_STATS')); ?>
					</td>
				</tr>
			</table>
		</div>
		<!-- EDIT -->
		<div id="edit-options">
			<table>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for individual records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All individual facts'), help_link('INDI_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="INDI_FACTS_ADD" name="INDI_FACTS_ADD" value="<?php echo $WT_TREE->getPreference('INDI_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('INDI_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique individual facts'), help_link('INDI_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="INDI_FACTS_UNIQUE" name="INDI_FACTS_UNIQUE" value="<?php echo $WT_TREE->getPreference('INDI_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('INDI_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Facts for new individuals'), help_link('QUICK_REQUIRED_FACTS'); ?>
				</td>
				<td>
					<input type="text" id="QUICK_REQUIRED_FACTS" name="QUICK_REQUIRED_FACTS" value="<?php echo $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('QUICK_REQUIRED_FACTS'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick individual facts'), help_link('INDI_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="INDI_FACTS_QUICK" name="INDI_FACTS_QUICK" value="<?php echo $WT_TREE->getPreference('INDI_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('INDI_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for family records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All family facts'), help_link('FAM_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="FAM_FACTS_ADD" name="FAM_FACTS_ADD" value="<?php echo $WT_TREE->getPreference('FAM_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('FAM_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique family facts'), help_link('FAM_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="FAM_FACTS_UNIQUE" name="FAM_FACTS_UNIQUE" value="<?php echo $WT_TREE->getPreference('FAM_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('FAM_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Facts for new families'), help_link('QUICK_REQUIRED_FAMFACTS'); ?>
				</td>
				<td>
					<input type="text" id="QUICK_REQUIRED_FAMFACTS" name="QUICK_REQUIRED_FAMFACTS" value="<?php echo $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('QUICK_REQUIRED_FAMFACTS'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick family facts'), help_link('FAM_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="FAM_FACTS_QUICK" name="FAM_FACTS_QUICK" value="<?php echo $WT_TREE->getPreference('FAM_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('FAM_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for source records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All source facts'), help_link('SOUR_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="SOUR_FACTS_ADD" name="SOUR_FACTS_ADD" value="<?php echo $WT_TREE->getPreference('SOUR_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('SOUR_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique source facts'), help_link('SOUR_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="SOUR_FACTS_UNIQUE" name="SOUR_FACTS_UNIQUE" value="<?php echo $WT_TREE->getPreference('SOUR_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('SOUR_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick source facts'), help_link('SOUR_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="SOUR_FACTS_QUICK" name="SOUR_FACTS_QUICK" value="<?php echo $WT_TREE->getPreference('SOUR_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('SOUR_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for repository records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All repository facts'), help_link('REPO_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="REPO_FACTS_ADD" name="REPO_FACTS_ADD" value="<?php echo $WT_TREE->getPreference('REPO_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('REPO_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique repository facts'), help_link('REPO_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="REPO_FACTS_UNIQUE" name="REPO_FACTS_UNIQUE" value="<?php echo $WT_TREE->getPreference('REPO_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('REPO_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick repository facts'), help_link('REPO_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="REPO_FACTS_QUICK" name="REPO_FACTS_QUICK" value="<?php echo $WT_TREE->getPreference('REPO_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('REPO_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Advanced fact settings'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Advanced name facts'), help_link('ADVANCED_NAME_FACTS'); ?>
				</td>
				<td>
					<input type="text" id="ADVANCED_NAME_FACTS" name="ADVANCED_NAME_FACTS" value="<?php echo $WT_TREE->getPreference('ADVANCED_NAME_FACTS'); ?>" size="40" maxlength="255" dir="ltr"><?php echo print_findfact_link('ADVANCED_NAME_FACTS'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Advanced place name facts'), help_link('ADVANCED_PLAC_FACTS'); ?>
				</td>
				<td>
					<input type="text" id="ADVANCED_PLAC_FACTS" name="ADVANCED_PLAC_FACTS" value="<?php echo $WT_TREE->getPreference('ADVANCED_PLAC_FACTS'); ?>" size="40" maxlength="255" dir="ltr"><?php echo print_findfact_link('ADVANCED_PLAC_FACTS'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Other settings'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Surname tradition'), help_link('SURNAME_TRADITION'); ?>
				</td>
				<td>
					<?php echo select_edit_control('SURNAME_TRADITION', array('paternal'=>WT_I18N::translate_c('Surname tradition', 'paternal'), 'patrilineal'=>WT_I18N::translate('patrilineal'), 'matrilineal'=>WT_I18N::translate('matrilineal'), 'spanish'=>WT_I18N::translate_c('Surname tradition', 'Spanish'), 'portuguese'=>WT_I18N::translate_c('Surname tradition', 'Portuguese'), 'icelandic'=>WT_I18N::translate_c('Surname tradition', 'Icelandic'), 'polish'=>WT_I18N::translate_c('Surname tradition', 'Polish'), 'lithuanian'=>WT_I18N::translate_c('Surname tradition', 'Lithuanian'), 'none'=>WT_I18N::translate_c('Surname tradition', 'none')), null, $WT_TREE->getPreference('SURNAME_TRADITION')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Use full source citations'), help_link('FULL_SOURCES'); ?>
				</td>
				<td>
					<?php echo edit_field_yes_no('FULL_SOURCES', $WT_TREE->getPreference('FULL_SOURCES')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Source type'), help_link('PREFER_LEVEL2_SOURCES'); ?>
				</td>
				<td>
					<?php echo select_edit_control('PREFER_LEVEL2_SOURCES', array(0=>WT_I18N::translate('none'), 1=>WT_I18N::translate('facts'), 2=>WT_I18N::translate('records')), null, $WT_TREE->getPreference('PREFER_LEVEL2_SOURCES')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo /* I18N: GeoNames is the www.geonames.org website */ WT_I18N::translate('Use the GeoNames database for autocomplete on places'), help_link('GEONAMES_ACCOUNT'); ?>
				</td>
				<td>
					<input type="text" id="GEONAMES_ACCOUNT" name="GEONAMES_ACCOUNT" value="<?php echo WT_Filter::escapeHtml($WT_TREE->getPreference('GEONAMES_ACCOUNT')); ?>" size="40" maxlength="255" dir="ltr" placeholder="<?php echo WT_I18N::translate('Username'); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN'); ?>
				</td>
				<td>
					<?php echo edit_field_yes_no('NO_UPDATE_CHAN', $WT_TREE->getPreference('NO_UPDATE_CHAN')); ?>
				</td>
			</tr>
			</table>
		</div>
	</div>
	<p>
		<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
	</p>
</form>
