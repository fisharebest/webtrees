<?php
// UI for online updating of the GEDCOM config file.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'admin_trees_config.php');

require './includes/session.php';

$controller=new WT_Controller_Page();
$controller
	->requireManagerLogin()
	->setPageTitle(WT_I18N::translate('Family tree configuration'));

require WT_ROOT.'includes/functions/functions_edit.php';

$PRIVACY_CONSTANTS = array(
	'none'         => WT_I18N::translate('Show to visitors'),
	'privacy'      => WT_I18N::translate('Show to members'),
	'confidential' => WT_I18N::translate('Show to managers'),
	'hidden'       => WT_I18N::translate('Hide from everyone')
);

switch (safe_POST('action')) {
case 'delete':
	if (!WT_Filter::checkCsrf()) {
		break;
	}
	WT_DB::prepare(
		"DELETE FROM `##default_resn` WHERE default_resn_id=?"
	)->execute(array(safe_POST('default_resn_id')));
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
			)->execute(array(WT_GED_ID, safe_POST('tag_type')));
		}
		if (safe_POST('tag_type')=='') {
			WT_DB::prepare(
				"DELETE FROM `##default_resn` WHERE gedcom_id=? AND xref=? AND tag_type IS NULL"
			)->execute(array(WT_GED_ID, safe_POST('xref')));
		}
		WT_DB::prepare(
			"REPLACE INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULLIF(?, ''), NULLIF(?, ''), ?)"
		)->execute(array(WT_GED_ID, safe_POST_xref('xref'), safe_POST('tag_type'), safe_POST('resn')));
	}
	// Reload the page, so that the new privacy restrictions are reflected in the header
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'#privacy');
	exit;
case 'update':
	if (!WT_Filter::checkCsrf()) {
		break;
	}
	set_gedcom_setting(WT_GED_ID, 'ADVANCED_NAME_FACTS',          WT_Filter::post('NEW_ADVANCED_NAME_FACTS'));
	set_gedcom_setting(WT_GED_ID, 'ADVANCED_PLAC_FACTS',          WT_Filter::post('NEW_ADVANCED_PLAC_FACTS'));
	set_gedcom_setting(WT_GED_ID, 'ALLOW_THEME_DROPDOWN',         WT_Filter::postBool('NEW_ALLOW_THEME_DROPDOWN'));
	// For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
	// e.g. "gregorian_and_jewish"
	set_gedcom_setting(WT_GED_ID, 'CALENDAR_FORMAT',              implode('_and_', array_unique(array(
		safe_POST('NEW_CALENDAR_FORMAT0', 'gregorian|julian|french|jewish|hijri|jalali', 'none'),
		safe_POST('NEW_CALENDAR_FORMAT1', 'gregorian|julian|french|jewish|hijri|jalali', 'none')
	))));
	set_gedcom_setting(WT_GED_ID, 'CHART_BOX_TAGS',               safe_POST('NEW_CHART_BOX_TAGS'));
	set_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_ADD',             str_replace(' ', '', safe_POST('NEW_COMMON_NAMES_ADD')));
	set_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_REMOVE',          str_replace(' ', '', safe_POST('NEW_COMMON_NAMES_REMOVE')));
	set_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_THRESHOLD',       safe_POST('NEW_COMMON_NAMES_THRESHOLD', WT_REGEX_INTEGER, 40));
	set_gedcom_setting(WT_GED_ID, 'CONTACT_USER_ID',              safe_POST('NEW_CONTACT_USER_ID'));
	set_gedcom_setting(WT_GED_ID, 'DEFAULT_PEDIGREE_GENERATIONS', safe_POST('NEW_DEFAULT_PEDIGREE_GENERATIONS'));
	set_gedcom_setting(WT_GED_ID, 'EXPAND_NOTES',                 safe_POST_bool('NEW_EXPAND_NOTES'));
	set_gedcom_setting(WT_GED_ID, 'EXPAND_RELATIVES_EVENTS',      safe_POST_bool('NEW_EXPAND_RELATIVES_EVENTS'));
	set_gedcom_setting(WT_GED_ID, 'EXPAND_SOURCES',               safe_POST_bool('NEW_EXPAND_SOURCES'));
	set_gedcom_setting(WT_GED_ID, 'FAM_FACTS_ADD',                str_replace(' ', '', safe_POST('NEW_FAM_FACTS_ADD')));
	set_gedcom_setting(WT_GED_ID, 'FAM_FACTS_QUICK',              str_replace(' ', '', safe_POST('NEW_FAM_FACTS_QUICK')));
	set_gedcom_setting(WT_GED_ID, 'FAM_FACTS_UNIQUE',             str_replace(' ', '', safe_POST('NEW_FAM_FACTS_UNIQUE')));
	set_gedcom_setting(WT_GED_ID, 'FAM_ID_PREFIX',                safe_POST('NEW_FAM_ID_PREFIX'));
	set_gedcom_setting(WT_GED_ID, 'FULL_SOURCES',                 safe_POST_bool('NEW_FULL_SOURCES'));
	set_gedcom_setting(WT_GED_ID, 'GEDCOM_ID_PREFIX',             safe_POST('NEW_GEDCOM_ID_PREFIX'));
	set_gedcom_setting(WT_GED_ID, 'GEDCOM_MEDIA_PATH',            safe_POST('NEW_GEDCOM_MEDIA_PATH'));
	set_gedcom_setting(WT_GED_ID, 'GENERATE_UIDS',                safe_POST_bool('NEW_GENERATE_UIDS'));
	set_gedcom_setting(WT_GED_ID, 'HIDE_GEDCOM_ERRORS',           safe_POST_bool('NEW_HIDE_GEDCOM_ERRORS'));
	set_gedcom_setting(WT_GED_ID, 'HIDE_LIVE_PEOPLE',             safe_POST_bool('NEW_HIDE_LIVE_PEOPLE'));
	set_gedcom_setting(WT_GED_ID, 'GEDCOM_MEDIA_PATH',            safe_POST('GEDCOM_MEDIA_PATH'));
	set_gedcom_setting(WT_GED_ID, 'INDI_FACTS_ADD',               str_replace(' ', '', safe_POST('NEW_INDI_FACTS_ADD')));
	set_gedcom_setting(WT_GED_ID, 'INDI_FACTS_QUICK',             str_replace(' ', '', safe_POST('NEW_INDI_FACTS_QUICK')));
	set_gedcom_setting(WT_GED_ID, 'INDI_FACTS_UNIQUE',            str_replace(' ', '', safe_POST('NEW_INDI_FACTS_UNIQUE')));
	set_gedcom_setting(WT_GED_ID, 'KEEP_ALIVE_YEARS_BIRTH',       safe_POST('KEEP_ALIVE_YEARS_BIRTH', WT_REGEX_INTEGER, 0));
	set_gedcom_setting(WT_GED_ID, 'KEEP_ALIVE_YEARS_DEATH',       safe_POST('KEEP_ALIVE_YEARS_DEATH', WT_REGEX_INTEGER, 0));
	set_gedcom_setting(WT_GED_ID, 'LANGUAGE',                     safe_POST('GEDCOMLANG'));
	set_gedcom_setting(WT_GED_ID, 'MAX_ALIVE_AGE',                safe_POST('MAX_ALIVE_AGE', WT_REGEX_INTEGER, 100));
	set_gedcom_setting(WT_GED_ID, 'MAX_DESCENDANCY_GENERATIONS',  safe_POST('NEW_MAX_DESCENDANCY_GENERATIONS'));
	set_gedcom_setting(WT_GED_ID, 'MAX_PEDIGREE_GENERATIONS',     safe_POST('NEW_MAX_PEDIGREE_GENERATIONS'));
	set_gedcom_setting(WT_GED_ID, 'MEDIA_ID_PREFIX',              safe_POST('NEW_MEDIA_ID_PREFIX'));
	set_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD',                 safe_POST('NEW_MEDIA_UPLOAD'));
	set_gedcom_setting(WT_GED_ID, 'META_DESCRIPTION',             safe_POST('NEW_META_DESCRIPTION'));
	set_gedcom_setting(WT_GED_ID, 'META_TITLE',                   safe_POST('NEW_META_TITLE'));
	set_gedcom_setting(WT_GED_ID, 'NOTE_ID_PREFIX',               safe_POST('NEW_NOTE_ID_PREFIX'));
	set_gedcom_setting(WT_GED_ID, 'NO_UPDATE_CHAN',               safe_POST_bool('NEW_NO_UPDATE_CHAN'));
	set_gedcom_setting(WT_GED_ID, 'PEDIGREE_FULL_DETAILS',        safe_POST_bool('NEW_PEDIGREE_FULL_DETAILS'));
	set_gedcom_setting(WT_GED_ID, 'PEDIGREE_LAYOUT',              safe_POST_bool('NEW_PEDIGREE_LAYOUT'));
	set_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID',             safe_POST_xref('NEW_PEDIGREE_ROOT_ID'));
	set_gedcom_setting(WT_GED_ID, 'PEDIGREE_SHOW_GENDER',         safe_POST_bool('NEW_PEDIGREE_SHOW_GENDER'));
	set_gedcom_setting(WT_GED_ID, 'PREFER_LEVEL2_SOURCES',        safe_POST('NEW_PREFER_LEVEL2_SOURCES'));
	set_gedcom_setting(WT_GED_ID, 'QUICK_REQUIRED_FACTS',         safe_POST('NEW_QUICK_REQUIRED_FACTS'));
	set_gedcom_setting(WT_GED_ID, 'QUICK_REQUIRED_FAMFACTS',      safe_POST('NEW_QUICK_REQUIRED_FAMFACTS'));
	set_gedcom_setting(WT_GED_ID, 'REPO_FACTS_ADD',               str_replace(' ', '', safe_POST('NEW_REPO_FACTS_ADD')));
	set_gedcom_setting(WT_GED_ID, 'REPO_FACTS_QUICK',             str_replace(' ', '', safe_POST('NEW_REPO_FACTS_QUICK')));
	set_gedcom_setting(WT_GED_ID, 'REPO_FACTS_UNIQUE',            str_replace(' ', '', safe_POST('NEW_REPO_FACTS_UNIQUE')));
	set_gedcom_setting(WT_GED_ID, 'REPO_ID_PREFIX',               safe_POST('NEW_REPO_ID_PREFIX'));
	set_gedcom_setting(WT_GED_ID, 'REQUIRE_AUTHENTICATION',       safe_POST_bool('NEW_REQUIRE_AUTHENTICATION'));
	set_gedcom_setting(WT_GED_ID, 'SAVE_WATERMARK_IMAGE',         safe_POST_bool('NEW_SAVE_WATERMARK_IMAGE'));
	set_gedcom_setting(WT_GED_ID, 'SAVE_WATERMARK_THUMB',         safe_POST_bool('NEW_SAVE_WATERMARK_THUMB'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_AGE_DIFF',                safe_POST_bool('NEW_SHOW_AGE_DIFF'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_COUNTER',                 safe_POST_bool('NEW_SHOW_COUNTER'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_DEAD_PEOPLE',             safe_POST('SHOW_DEAD_PEOPLE'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_EST_LIST_DATES',          safe_POST_bool('NEW_SHOW_EST_LIST_DATES'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_FACT_ICONS',              safe_POST_bool('NEW_SHOW_FACT_ICONS'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_GEDCOM_RECORD',           safe_POST_bool('NEW_SHOW_GEDCOM_RECORD'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_HIGHLIGHT_IMAGES',        safe_POST_bool('NEW_SHOW_HIGHLIGHT_IMAGES'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_LAST_CHANGE',             safe_POST_bool('NEW_SHOW_LAST_CHANGE'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_LDS_AT_GLANCE',           safe_POST_bool('NEW_SHOW_LDS_AT_GLANCE'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_LEVEL2_NOTES',            safe_POST_bool('NEW_SHOW_LEVEL2_NOTES'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_LIVING_NAMES',            safe_POST('SHOW_LIVING_NAMES'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_MEDIA_DOWNLOAD',          safe_POST_bool('NEW_SHOW_MEDIA_DOWNLOAD'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_NO_WATERMARK',            safe_POST('NEW_SHOW_NO_WATERMARK'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_PARENTS_AGE',             safe_POST_bool('NEW_SHOW_PARENTS_AGE'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_PEDIGREE_PLACES',         safe_POST('NEW_SHOW_PEDIGREE_PLACES'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_PEDIGREE_PLACES_SUFFIX',  safe_POST_bool('NEW_SHOW_PEDIGREE_PLACES_SUFFIX'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_PRIVATE_RELATIONSHIPS',   safe_POST('SHOW_PRIVATE_RELATIONSHIPS'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_RELATIVES_EVENTS',        safe_POST('NEW_SHOW_RELATIVES_EVENTS'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_STATS',                   safe_POST_bool('NEW_SHOW_STATS'));
	set_gedcom_setting(WT_GED_ID, 'SOURCE_ID_PREFIX',             safe_POST('NEW_SOURCE_ID_PREFIX'));
	set_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_ADD',               str_replace(' ', '', safe_POST('NEW_SOUR_FACTS_ADD')));
	set_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_QUICK',             str_replace(' ', '', safe_POST('NEW_SOUR_FACTS_QUICK')));
	set_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_UNIQUE',            str_replace(' ', '', safe_POST('NEW_SOUR_FACTS_UNIQUE')));
	set_gedcom_setting(WT_GED_ID, 'SUBLIST_TRIGGER_I',            safe_POST('NEW_SUBLIST_TRIGGER_I', WT_REGEX_INTEGER, 200));
	set_gedcom_setting(WT_GED_ID, 'SURNAME_LIST_STYLE',           safe_POST('NEW_SURNAME_LIST_STYLE'));
	set_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION',            safe_POST('NEW_SURNAME_TRADITION'));
	set_gedcom_setting(WT_GED_ID, 'THEME_DIR',                    safe_POST('NEW_THEME_DIR'));
	set_gedcom_setting(WT_GED_ID, 'THUMBNAIL_WIDTH',              safe_POST('NEW_THUMBNAIL_WIDTH'));
	set_gedcom_setting(WT_GED_ID, 'USE_GEONAMES',                 safe_POST_bool('NEW_USE_GEONAMES'));
	set_gedcom_setting(WT_GED_ID, 'USE_RIN',                      safe_POST_bool('NEW_USE_RIN'));
	set_gedcom_setting(WT_GED_ID, 'USE_SILHOUETTE',               safe_POST_bool('NEW_USE_SILHOUETTE'));
	set_gedcom_setting(WT_GED_ID, 'WATERMARK_THUMB',              safe_POST_bool('NEW_WATERMARK_THUMB'));
	set_gedcom_setting(WT_GED_ID, 'WEBMASTER_USER_ID',            safe_POST('NEW_WEBMASTER_USER_ID'));
	set_gedcom_setting(WT_GED_ID, 'WEBTREES_EMAIL',               safe_POST('NEW_WEBTREES_EMAIL'));
	set_gedcom_setting(WT_GED_ID, 'WORD_WRAPPED_NOTES',           safe_POST_bool('NEW_WORD_WRAPPED_NOTES'));
	if (safe_POST('gedcom_title', WT_REGEX_UNSAFE)) {
		set_gedcom_setting(WT_GED_ID, 'title',                        safe_POST('gedcom_title', WT_REGEX_UNSAFE));
	}

	// Only accept valid folders for NEW_MEDIA_DIRECTORY
	$NEW_MEDIA_DIRECTORY = preg_replace('/[\/\\\\]+/', '/', safe_POST('NEW_MEDIA_DIRECTORY') . '/');
	if (substr($NEW_MEDIA_DIRECTORY, 0, 1) == '/') {
		$NEW_MEDIA_DIRECTORY = substr($NEW_MEDIA_DIRECTORY, 1);
	}

	if ($NEW_MEDIA_DIRECTORY) {
		if (is_dir(WT_DATA_DIR . $NEW_MEDIA_DIRECTORY)) {
			set_gedcom_setting(WT_GED_ID, 'MEDIA_DIRECTORY', $NEW_MEDIA_DIRECTORY);
		} elseif (@mkdir(WT_DATA_DIR . $NEW_MEDIA_DIRECTORY, 0755, true)) {
			set_gedcom_setting(WT_GED_ID, 'MEDIA_DIRECTORY', $NEW_MEDIA_DIRECTORY);
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was created.', WT_DATA_DIR . $NEW_MEDIA_DIRECTORY));
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', WT_DATA_DIR . $NEW_MEDIA_DIRECTORY));
		}
	}

	// Reload the page, so that the settings take effect immediately.	
	Zend_Session::writeClose();
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
	exit;
}

$controller
	->pageHeader()
	->addInlineJavascript('jQuery("#tabs").tabs(); jQuery("#tabs").css("display", "inline");')
	->addInlineJavascript('var pastefield; function paste_id(value) { pastefield.value=value; }');

if (count(WT_Tree::getAll())==1) { //Removed because it doesn't work here for multiple GEDCOMs. Can be reinstated when fixed (https://bugs.launchpad.net/webtrees/+bug/613235)
	$controller->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js');
}

?>
<form enctype="multipart/form-data" method="post" id="configform" name="configform" action="<?php echo WT_SCRIPT_NAME; ?>">
	<?php echo WT_Filter::getCsrf(); ?>
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="ged" value="<?php echo htmlspecialchars(WT_GEDCOM); ?>">

	<div id="tabs">
		<ul>
			<li><a href="#file-options"><span><?php echo WT_I18N::translate('General'); ?></span></a></li>
			<li><a href="#privacy"><span><?php echo WT_I18N::translate('Privacy'); ?></span></a></li>
			<li><a href="#config-media"><span><?php echo WT_I18N::translate('Media'); ?></span></a></li>
			<li><a href="#layout-options"><span><?php echo WT_I18N::translate('Layout'); ?></span></a></li>
			<li><a href="#hide-show"><span><?php echo WT_I18N::translate('Hide &amp; Show'); ?></span></a></li>
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
						<input type="text" name="gedcom_title" dir="ltr" value="<?php echo htmlspecialchars(get_gedcom_setting(WT_GED_ID, 'title')); ?>" size="40" maxlength="255">
					</td>
				</tr>
				<tr>
					<td><?php echo WT_I18N::translate('Language'), help_link('LANGUAGE'); ?></td>
					<td><?php echo edit_field_language('GEDCOMLANG', $LANGUAGE); ?></td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default individual'), help_link('default_individual'); ?>
					</td>
					<td class="wrap">
						<input type="text" name="NEW_PEDIGREE_ROOT_ID" id="NEW_PEDIGREE_ROOT_ID" value="<?php echo get_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID'); ?>" size="5" maxlength="20">
						<?php
							echo print_findindi_link('NEW_PEDIGREE_ROOT_ID');
							$person=WT_Person::getInstance(get_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID'));
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
						<select id="NEW_CALENDAR_FORMAT0" name="NEW_CALENDAR_FORMAT0">
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
	
					<select id="NEW_CALENDAR_FORMAT1" name="NEW_CALENDAR_FORMAT1">
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
						<?php echo edit_field_yes_no('NEW_USE_RIN', get_gedcom_setting(WT_GED_ID, 'USE_RIN')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically create globally unique IDs'), help_link('GENERATE_GUID'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_GENERATE_UIDS', get_gedcom_setting(WT_GED_ID, 'GENERATE_UIDS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Add spaces where notes were wrapped'), help_link('WORD_WRAPPED_NOTES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_WORD_WRAPPED_NOTES', get_gedcom_setting(WT_GED_ID, 'WORD_WRAPPED_NOTES')); ?>
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
						<input type="text" name="NEW_GEDCOM_ID_PREFIX" dir="ltr" value="<?php echo $GEDCOM_ID_PREFIX; ?>" size="5" maxlength="20">
					</td>
					<td>
						<?php echo WT_I18N::translate('Family ID prefix'), help_link('FAM_ID_PREFIX'); ?>
					</td>
					<td>
						<input type="text" name="NEW_FAM_ID_PREFIX" dir="ltr" value="<?php echo $FAM_ID_PREFIX; ?>" size="5" maxlength="20">
					</td>
					<td>
						<?php echo WT_I18N::translate('Source ID prefix'), help_link('SOURCE_ID_PREFIX'); ?>
					</td>
					<td>
						<input type="text" name="NEW_SOURCE_ID_PREFIX" dir="ltr" value="<?php echo $SOURCE_ID_PREFIX; ?>" size="5" maxlength="20">
					</td>
				</tr>
				<tr>
					<td><?php echo WT_I18N::translate('Repository ID prefix'), help_link('REPO_ID_PREFIX'); ?></td>
					<td><input type="text" name="NEW_REPO_ID_PREFIX" dir="ltr" value="<?php echo $REPO_ID_PREFIX; ?>" size="5" maxlength="20">
					</td>
					<td><?php echo WT_I18N::translate('Media ID prefix'), help_link('MEDIA_ID_PREFIX'); ?></td>
					<td><input type="text" name="NEW_MEDIA_ID_PREFIX" dir="ltr" value="<?php echo $MEDIA_ID_PREFIX; ?>" size="5" maxlength="20">
					</td>
					<td><?php echo WT_I18N::translate('Note ID prefix'), help_link('NOTE_ID_PREFIX'); ?></td>
					<td><input type="text" name="NEW_NOTE_ID_PREFIX" dir="ltr" value="<?php echo $NOTE_ID_PREFIX; ?>" size="5" maxlength="20">
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th colspan="2"><?php echo WT_I18N::translate('Contact Information'); ?></th>
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
					<td><input type="text" name="NEW_WEBTREES_EMAIL" value="<?php echo $WEBTREES_EMAIL; ?>" size="50" maxlength="255" dir="ltr"></td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Genealogy contact'), help_link('CONTACT_USER_ID'); ?>
					</td>
					<td><select name="NEW_CONTACT_USER_ID">
					<?php
						$CONTACT_USER_ID=get_gedcom_setting(WT_GED_ID, 'CONTACT_USER_ID');
						foreach (get_all_users() as $user_id=>$user_name) {
							if (get_user_setting($user_id, 'verified_by_admin')) {
								echo "<option value=\"".$user_id."\"";
								if ($CONTACT_USER_ID==$user_id) echo " selected=\"selected\"";
								echo ">".getUserFullName($user_id)." - ".$user_name."</option>";
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
					<td><select name="NEW_WEBMASTER_USER_ID">
					<?php
						$WEBMASTER_USER_ID=get_gedcom_setting(WT_GED_ID, 'WEBMASTER_USER_ID');
						foreach (get_all_users() as $user_id=>$user_name) {
							if (userIsAdmin($user_id)) {
								echo "<option value=\"".$user_id."\"";
								if ($WEBMASTER_USER_ID==$user_id) echo " selected=\"selected\"";
								echo ">".getUserFullName($user_id)." - ".$user_name."</option>";
							}
						}
					?>
					</select>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th colspan="2"><?php echo WT_I18N::translate('Web Site and META Tag Settings'); ?></th>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Add to TITLE header tag'), help_link('META_TITLE'); ?>
					</td>
					<td>
						<input type="text" dir="ltr" name="NEW_META_TITLE" value="<?php echo htmlspecialchars(get_gedcom_setting(WT_GED_ID, 'META_TITLE')); ?>" size="40" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Description META tag'), help_link('META_DESCRIPTION'); ?>
					</td>
					<td>
						<input type="text" dir="ltr" name="NEW_META_DESCRIPTION" value="<?php echo get_gedcom_setting(WT_GED_ID, 'META_DESCRIPTION'); ?>" size="40" maxlength="255">
						<br>
						<?php echo WT_I18N::translate('Leave this field empty to use the title of the currently active database.'); ?>
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
						<?php echo radio_buttons('NEW_ALLOW_THEME_DROPDOWN', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), get_gedcom_setting(WT_GED_ID, 'ALLOW_THEME_DROPDOWN')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default Theme'), help_link('THEME'); ?>
					</td>
					<td>
						<select name="NEW_THEME_DIR">
							<?php
								echo '<option value="">', htmlspecialchars(WT_I18N::translate('<default theme>')), '</option>';
								$current_themedir=get_gedcom_setting(WT_GED_ID, 'THEME_DIR');
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
						<?php echo edit_field_yes_no('NEW_REQUIRE_AUTHENTICATION', get_gedcom_setting(WT_GED_ID, 'REQUIRE_AUTHENTICATION')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Privacy options'), help_link('HIDE_LIVE_PEOPLE'); ?>
					</td>
					<td>
						<?php  echo radio_buttons('NEW_HIDE_LIVE_PEOPLE', array(false=>WT_I18N::translate('disable'), true=>WT_I18N::translate('enable')), $HIDE_LIVE_PEOPLE, ''); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show dead people'), help_link('SHOW_DEAD_PEOPLE'); ?>
					</td>
					<td>
						<?php echo edit_field_access_level("SHOW_DEAD_PEOPLE", get_gedcom_setting(WT_GED_ID, 'SHOW_DEAD_PEOPLE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php /* I18N: ... [who were] born in the last XX years or died in the last YY years */ echo WT_I18N::translate('Extend privacy to dead people'), help_link('KEEP_ALIVE'); ?>
					</td>
					<td>
						<?php
						echo
							/* I18N: Extend privacy to dead people [who were] ... */ WT_I18N::translate(
								'born in the last %1$s years or died in the last %2$s years',
								'<input type="text" name="KEEP_ALIVE_YEARS_BIRTH" value="'.get_gedcom_setting(WT_GED_ID, 'KEEP_ALIVE_YEARS_BIRTH').'" size="5" maxlength="3">',
								'<input type="text" name="KEEP_ALIVE_YEARS_DEATH" value="'.get_gedcom_setting(WT_GED_ID, 'KEEP_ALIVE_YEARS_DEATH').'" size="5" maxlength="3">'
							); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Names of private individuals'), help_link('SHOW_LIVING_NAMES'); ?>
					</td>
					<td>
						<?php echo edit_field_access_level("SHOW_LIVING_NAMES", get_gedcom_setting(WT_GED_ID, 'SHOW_LIVING_NAMES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show private relationships'), help_link('SHOW_PRIVATE_RELATIONSHIPS'); ?>
					</td>
					<td>
						<?php  echo edit_field_yes_no('SHOW_PRIVATE_RELATIONSHIPS', get_gedcom_setting(WT_GED_ID, 'SHOW_PRIVATE_RELATIONSHIPS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Age at which to assume a person is dead'), help_link('MAX_ALIVE_AGE'); ?>
					</td>
					<td>
						<input type="text" name="MAX_ALIVE_AGE" value="<?php echo get_gedcom_setting(WT_GED_ID, 'MAX_ALIVE_AGE'); ?>" size="5" maxlength="3">
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
			explode(',', get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_ADD')), explode(',', get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_UNIQUE')),
			explode(',', get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_ADD' )), explode(',', get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_UNIQUE' )),
			explode(',', get_gedcom_setting(WT_GED_ID, 'NOTE_FACTS_ADD')), explode(',', get_gedcom_setting(WT_GED_ID, 'NOTE_FACTS_UNIQUE')),
			explode(',', get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_ADD')), explode(',', get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_UNIQUE')),
			explode(',', get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_ADD')), explode(',', get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_UNIQUE')),
			array('SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN')
		));
	
		foreach ($tags as $tag) {
			if ($tag) {
				$all_tags[$tag]=WT_Gedcom_Tag::getLabel($tag);
			}
		}
	
		uasort($all_tags, 'utf8_strcasecmp');
	
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
						<?php echo WT_DATA_DIR; ?><input type="text" name="NEW_MEDIA_DIRECTORY" value="<?php echo $MEDIA_DIRECTORY; ?>" dir="ltr" size="15" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo /* I18N: A media path (e.g. c:\aaa\bbb\ccc\ddd.jpeg) in a GEDCOM file */ WT_I18N::translate('GEDCOM media path'), help_link('GEDCOM_MEDIA_PATH'); ?>
					</td>
					<td>
						<input type="text" name="NEW_GEDCOM_MEDIA_PATH" value="<?php echo $GEDCOM_MEDIA_PATH; ?>" dir="ltr" size="30" maxlength="255">
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
						<?php echo select_edit_control('NEW_MEDIA_UPLOAD', array(WT_PRIV_USER=>WT_I18N::translate('Show to members'),
	 WT_PRIV_NONE=>WT_I18N::translate('Show to managers'), WT_PRIV_HIDE=>WT_I18N::translate('Hide from everyone')), null, get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show download link in media viewer'), help_link('SHOW_MEDIA_DOWNLOAD'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_SHOW_MEDIA_DOWNLOAD', get_gedcom_setting(WT_GED_ID, 'SHOW_MEDIA_DOWNLOAD')); ?>
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
						<input type="text" name="NEW_THUMBNAIL_WIDTH" value="<?php echo $THUMBNAIL_WIDTH; ?>" size="5" maxlength="4">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Use silhouettes'), help_link('USE_SILHOUETTE'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_USE_SILHOUETTE', get_gedcom_setting(WT_GED_ID, 'USE_SILHOUETTE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show highlight images in people boxes'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_SHOW_HIGHLIGHT_IMAGES', get_gedcom_setting(WT_GED_ID, 'SHOW_HIGHLIGHT_IMAGES')); ?>
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
						<?php echo edit_field_yes_no('NEW_WATERMARK_THUMB', get_gedcom_setting(WT_GED_ID, 'WATERMARK_THUMB')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Store watermarked full size images on server?'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_SAVE_WATERMARK_IMAGE', get_gedcom_setting(WT_GED_ID, 'SAVE_WATERMARK_IMAGE')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Store watermarked thumbnails on server?'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_SAVE_WATERMARK_THUMB', get_gedcom_setting(WT_GED_ID, 'SAVE_WATERMARK_THUMB')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Images without watermarks'); ?>
					</td>
					<td>
						<?php echo edit_field_access_level("NEW_SHOW_NO_WATERMARK", $SHOW_NO_WATERMARK); ?>
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
						<?php echo WT_I18N::translate('Min. no. of occurrences to be a "common surname"'), help_link('COMMON_NAMES_THRESHOLD'); ?>
					</td>
					<td>
						<input type="text" name="NEW_COMMON_NAMES_THRESHOLD" value="<?php echo get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_THRESHOLD'); ?>" size="5" maxlength="5">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Names to add to common surnames (comma separated)'), help_link('COMMON_NAMES_ADD'); ?>
					</td>
					<td>
						<input type="text" name="NEW_COMMON_NAMES_ADD" dir="ltr" value="<?php echo get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_ADD'); ?>" size="50" maxlength="255">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Names to remove from common surnames (comma separated)'), help_link('COMMON_NAMES_REMOVE'); ?>
					</td>
					<td>
						<input type="text" name="NEW_COMMON_NAMES_REMOVE" dir="ltr" value="<?php echo get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_REMOVE'); ?>" size="50" maxlength="255">
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
						<select name="NEW_SURNAME_LIST_STYLE">
							<option value="style1" <?php if ($SURNAME_LIST_STYLE=="style1") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('list'); ?></option>
							<option value="style2" <?php if ($SURNAME_LIST_STYLE=="style2") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('table'); ?></option>
							<option value="style3" <?php if ($SURNAME_LIST_STYLE=="style3") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('tag cloud'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Maximum number of surnames on individual list'), help_link('SUBLIST_TRIGGER_I'); ?>
					</td>
					<td>
						<input type="text" name="NEW_SUBLIST_TRIGGER_I" value="<?php echo get_gedcom_setting(WT_GED_ID, 'SUBLIST_TRIGGER_I'); ?>" size="5" maxlength="5">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Estimated dates for birth and death'), help_link('SHOW_EST_LIST_DATES'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_EST_LIST_DATES', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), get_gedcom_setting(WT_GED_ID, 'SHOW_EST_LIST_DATES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('The date and time of the last update'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_LAST_CHANGE', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $SHOW_LAST_CHANGE); ?>
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
						<select name="NEW_PEDIGREE_LAYOUT">
							<option value="yes" <?php if ($PEDIGREE_LAYOUT) echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Landscape'); ?></option>
							<option value="no" <?php if (!$PEDIGREE_LAYOUT) echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Portrait'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Default pedigree generations'), help_link('DEFAULT_PEDIGREE_GENERATIONS'); ?>
					</td>
					<td>
						<input type="text" name="NEW_DEFAULT_PEDIGREE_GENERATIONS" value="<?php echo $DEFAULT_PEDIGREE_GENERATIONS; ?>" size="5" maxlength="3">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Maximum pedigree generations'), help_link('MAX_PEDIGREE_GENERATIONS'); ?>
					</td>
					<td>
						<input type="text" name="NEW_MAX_PEDIGREE_GENERATIONS" value="<?php echo $MAX_PEDIGREE_GENERATIONS; ?>" size="5" maxlength="3">
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Maximum descendancy generations'), help_link('MAX_DESCENDANCY_GENERATIONS'); ?>
					</td>
					<td>
						<input type="text" name="NEW_MAX_DESCENDANCY_GENERATIONS" value="<?php echo $MAX_DESCENDANCY_GENERATIONS; ?>" size="5" maxlength="3">
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
						<?php echo edit_field_yes_no('NEW_EXPAND_RELATIVES_EVENTS', get_gedcom_setting(WT_GED_ID, 'EXPAND_RELATIVES_EVENTS')); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php echo WT_I18N::translate('Show events of close relatives on individual page'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="NEW_SHOW_RELATIVES_EVENTS" value="<?php echo $SHOW_RELATIVES_EVENTS; ?>">
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
										if (strstr($SHOW_RELATIVES_EVENTS, $col)) {
											echo " checked=\"checked\"";
										}
										echo " onchange=\"var old=document.configform.NEW_SHOW_RELATIVES_EVENTS.value; if (this.checked) old+=','+this.value; else old=old.replace(/".$col."/g,''); old=old.replace(/[,]+/gi,','); old=old.replace(/^[,]/gi,''); old=old.replace(/[,]$/gi,''); document.configform.NEW_SHOW_RELATIVES_EVENTS.value=old\"> ";
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
							select_edit_control('NEW_SHOW_PEDIGREE_PLACES_SUFFIX',
								array(
									false=>WT_I18N::translate_c('Show the [first/last] [N] parts of a place name.', 'first'),
									true =>WT_I18N::translate_c('Show the [first/last] [N] parts of a place name.', 'last')
								),
								null,
								get_gedcom_setting(WT_GED_ID, 'SHOW_PEDIGREE_PLACES_SUFFIX')
							),
							select_edit_control('NEW_SHOW_PEDIGREE_PLACES',
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
								get_gedcom_setting(WT_GED_ID, 'SHOW_PEDIGREE_PLACES')
							)
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
						<?php echo WT_I18N::translate('Abbreviate chart labels'), help_link('ABBREVIATE_CHART_LABELS'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_ABBREVIATE_CHART_LABELS', get_gedcom_setting(WT_GED_ID, 'ABBREVIATE_CHART_LABELS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show chart details by default'), help_link('PEDIGREE_FULL_DETAILS'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_PEDIGREE_FULL_DETAILS', get_gedcom_setting(WT_GED_ID, 'PEDIGREE_FULL_DETAILS')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Gender icon on charts'), help_link('PEDIGREE_SHOW_GENDER'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_PEDIGREE_SHOW_GENDER', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $PEDIGREE_SHOW_GENDER); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Age of parents next to child\'s birthdate'), help_link('SHOW_PARENTS_AGE'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_PARENTS_AGE', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $SHOW_PARENTS_AGE); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('LDS ordinance codes in chart boxes'), help_link('SHOW_LDS_AT_GLANCE'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_LDS_AT_GLANCE', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $SHOW_LDS_AT_GLANCE); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Other facts to show in charts'), help_link('CHART_BOX_TAGS'); ?>
					</td>
					<td>
						<input type="text" id="NEW_CHART_BOX_TAGS" name="NEW_CHART_BOX_TAGS" value="<?php echo $CHART_BOX_TAGS; ?>" dir="ltr" size="50" maxlength="255"><?php echo print_findfact_link('NEW_CHART_BOX_TAGS'); ?>
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
						<?php echo radio_buttons('NEW_SHOW_FACT_ICONS', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $SHOW_FACT_ICONS); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically expand notes'), help_link('EXPAND_NOTES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_EXPAND_NOTES', get_gedcom_setting(WT_GED_ID, 'EXPAND_NOTES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Automatically expand sources'), help_link('EXPAND_SOURCES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_EXPAND_SOURCES', get_gedcom_setting(WT_GED_ID, 'EXPAND_SOURCES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Show all notes and source references on notes and sources tabs'), help_link('SHOW_LEVEL2_NOTES'); ?>
					</td>
					<td>
						<?php echo edit_field_yes_no('NEW_SHOW_LEVEL2_NOTES', get_gedcom_setting(WT_GED_ID, 'SHOW_LEVEL2_NOTES')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Date differences'), help_link('SHOW_AGE_DIFF'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_AGE_DIFF', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $SHOW_AGE_DIFF); ?>
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
						<?php echo edit_field_yes_no('NEW_SHOW_GEDCOM_RECORD', get_gedcom_setting(WT_GED_ID, 'SHOW_GEDCOM_RECORD')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('GEDCOM errors'), help_link('HIDE_GEDCOM_ERRORS'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_HIDE_GEDCOM_ERRORS', array(true=>WT_I18N::translate('hide'), false=>WT_I18N::translate('show')), $HIDE_GEDCOM_ERRORS); /* Note: name of object is reverse of description */ ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Hit counters'), help_link('SHOW_COUNTER'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_COUNTER', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), $SHOW_COUNTER); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo WT_I18N::translate('Execution statistics'), help_link('SHOW_STATS'); ?>
					</td>
					<td>
						<?php echo radio_buttons('NEW_SHOW_STATS', array(false=>WT_I18N::translate('hide'), true=>WT_I18N::translate('show')), get_gedcom_setting(WT_GED_ID, 'SHOW_STATS')); ?>
					</td>
				</tr>
			</table>
		</div>
		<!-- EDIT -->
		<div id="edit-options">
			<table>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for Individual records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All facts'), help_link('INDI_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="NEW_INDI_FACTS_ADD" name="NEW_INDI_FACTS_ADD" value="<?php echo get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_INDI_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique facts'), help_link('INDI_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="NEW_INDI_FACTS_UNIQUE" name="NEW_INDI_FACTS_UNIQUE" value="<?php echo get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_INDI_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('New entry facts'), help_link('QUICK_REQUIRED_FACTS'); ?>
				</td>
				<td>
					<input type="text" id="NEW_QUICK_REQUIRED_FACTS" name="NEW_QUICK_REQUIRED_FACTS" value="<?php echo $QUICK_REQUIRED_FACTS; ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_QUICK_REQUIRED_FACTS'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick facts'), help_link('INDI_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="NEW_INDI_FACTS_QUICK" name="NEW_INDI_FACTS_QUICK" value="<?php echo get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_INDI_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for Family records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All facts'), help_link('FAM_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="NEW_FAM_FACTS_ADD" name="NEW_FAM_FACTS_ADD" value="<?php echo get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_FAM_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique facts'), help_link('FAM_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="NEW_FAM_FACTS_UNIQUE" name="NEW_FAM_FACTS_UNIQUE" value="<?php echo get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_FAM_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('New entry facts'), help_link('QUICK_REQUIRED_FAMFACTS'); ?>
				</td>
				<td>
					<input type="text" id="NEW_QUICK_REQUIRED_FAMFACTS" name="NEW_QUICK_REQUIRED_FAMFACTS" value="<?php echo $QUICK_REQUIRED_FAMFACTS; ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_QUICK_REQUIRED_FAMFACTS'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick facts'), help_link('FAM_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="NEW_FAM_FACTS_QUICK" name="NEW_FAM_FACTS_QUICK" value="<?php echo get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_FAM_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for Source records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All facts'), help_link('SOUR_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="NEW_SOUR_FACTS_ADD" name="NEW_SOUR_FACTS_ADD" value="<?php echo get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_SOUR_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique facts'), help_link('SOUR_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="NEW_SOUR_FACTS_UNIQUE" name="NEW_SOUR_FACTS_UNIQUE" value="<?php echo get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_SOUR_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick facts'), help_link('SOUR_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="NEW_SOUR_FACTS_QUICK" name="NEW_SOUR_FACTS_QUICK" value="<?php echo get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_SOUR_FACTS_QUICK'); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo WT_I18N::translate('Facts for Repository records'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('All facts'), help_link('REPO_FACTS_ADD'); ?>
				</td>
				<td>
					<input type="text" id="NEW_REPO_FACTS_ADD" name="NEW_REPO_FACTS_ADD" value="<?php echo get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_ADD'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_REPO_FACTS_ADD'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Unique facts'), help_link('REPO_FACTS_UNIQUE'); ?>
				</td>
				<td>
					<input type="text" id="NEW_REPO_FACTS_UNIQUE" name="NEW_REPO_FACTS_UNIQUE" value="<?php echo get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_UNIQUE'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_REPO_FACTS_UNIQUE'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Quick facts'), help_link('REPO_FACTS_QUICK'); ?>
				</td>
				<td>
					<input type="text" id="NEW_REPO_FACTS_QUICK" name="NEW_REPO_FACTS_QUICK" value="<?php echo get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_QUICK'); ?>" size="60" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_REPO_FACTS_QUICK'); ?>
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
					<input type="text" id="NEW_ADVANCED_NAME_FACTS" name="NEW_ADVANCED_NAME_FACTS" value="<?php echo $ADVANCED_NAME_FACTS; ?>" size="40" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_ADVANCED_NAME_FACTS'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Advanced place name facts'), help_link('ADVANCED_PLAC_FACTS'); ?>
				</td>
				<td>
					<input type="text" id="NEW_ADVANCED_PLAC_FACTS" name="NEW_ADVANCED_PLAC_FACTS" value="<?php echo $ADVANCED_PLAC_FACTS; ?>" size="40" maxlength="255" dir="ltr"><?php echo print_findfact_link('NEW_ADVANCED_PLAC_FACTS'); ?>
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
					<?php echo select_edit_control('NEW_SURNAME_TRADITION', array('paternal'=>WT_I18N::translate_c('Surname tradition', 'paternal'), 'patrilineal'=>WT_I18N::translate('patrilineal'), 'matrilineal'=>WT_I18N::translate('matrilineal'), 'spanish'=>WT_I18N::translate_c('Surname tradition', 'Spanish'), 'portuguese'=>WT_I18N::translate_c('Surname tradition', 'Portuguese'), 'icelandic'=>WT_I18N::translate_c('Surname tradition', 'Icelandic'), 'polish'=>WT_I18N::translate_c('Surname tradition', 'Polish'), 'lithuanian'=>WT_I18N::translate_c('Surname tradition', 'Lithuanian'), 'none'=>WT_I18N::translate_c('Surname tradition', 'none')), null, get_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Use full source citations'), help_link('FULL_SOURCES'); ?>
				</td>
				<td>
					<?php echo edit_field_yes_no('NEW_FULL_SOURCES', get_gedcom_setting(WT_GED_ID, 'FULL_SOURCES')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Source type'), help_link('PREFER_LEVEL2_SOURCES'); ?>
				</td>
				<td>
					<?php echo select_edit_control('NEW_PREFER_LEVEL2_SOURCES', array(0=>WT_I18N::translate('none'), 1=>WT_I18N::translate('facts'), 2=>WT_I18N::translate('records')), null, get_gedcom_setting(WT_GED_ID, 'PREFER_LEVEL2_SOURCES')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Use GeoNames database for autocomplete on places'), help_link('USE_GEONAMES'); ?>
				</td>
				<td>
					<?php echo edit_field_yes_no('NEW_USE_GEONAMES', get_gedcom_setting(WT_GED_ID, 'USE_GEONAMES')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN'); ?>
				</td>
				<td>
					<?php echo edit_field_yes_no('NEW_NO_UPDATE_CHAN', get_gedcom_setting(WT_GED_ID, 'NO_UPDATE_CHAN')); ?>
				</td>
			</tr>
			</table>
		</div>
	</div>
	<p>
		<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
	</p>
</form>
