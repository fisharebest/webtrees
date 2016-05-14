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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

define('WT_SCRIPT_NAME', 'admin_trees_config.php');

require './includes/session.php';

$controller = new PageController;
$controller->restrictAccess(Auth::isManager($WT_TREE));

$calendars = array('none' => I18N::translate('No calendar conversion')) + Date::calendarNames();

$french_calendar_start    = new Date('22 SEP 1792');
$french_calendar_end      = new Date('31 DEC 1805');
$gregorian_calendar_start = new Date('15 OCT 1582');

$hide_show = array(
	0 => I18N::translate('hide'),
	1 => I18N::translate('show'),
);

$surname_list_styles = array(
	'style1' => /* I18N: Layout option for lists of surnames */ I18N::translate('list'),
	'style2' => /* I18N: Layout option for lists of surnames */ I18N::translate('table'),
	'style3' => /* I18N: Layout option for lists of surnames */ I18N::translate('tag cloud'),
);

$layouts = array(
	0 => /* I18N: page orientation */ I18N::translate('Portrait'),
	1 => /* I18N: page orientation */ I18N::translate('Landscape'),
);

$one_to_nine = array();
for ($n = 1; $n <= 9; ++$n) {
	$one_to_nine[$n] = I18N::number($n);
}

$formats = array(
	''         => /* I18N: None of the other options */ I18N::translate('none'),
	'markdown' => /* I18N: https://en.wikipedia.org/wiki/Markdown */ I18N::translate('markdown'),
);

$source_types = array(
	0 => I18N::translate('none'),
	1 => I18N::translate('facts'),
	2 => I18N::translate('records'),
);

$no_yes = array(
	0 => I18N::translate('no'),
	1 => I18N::translate('yes'),
);

$PRIVACY_CONSTANTS = array(
	'none'         => I18N::translate('Show to visitors'),
	'privacy'      => I18N::translate('Show to members'),
	'confidential' => I18N::translate('Show to managers'),
	'hidden'       => I18N::translate('Hide from everyone'),
);

$privacy = array(
	Auth::PRIV_PRIVATE => I18N::translate('Show to visitors'),
	Auth::PRIV_USER    => I18N::translate('Show to members'),
	Auth::PRIV_NONE    => I18N::translate('Show to managers'),
	Auth::PRIV_HIDE    => I18N::translate('Hide from everyone'),
);

$tags = array_unique(array_merge(
	explode(',', $WT_TREE->getPreference('INDI_FACTS_ADD')), explode(',', $WT_TREE->getPreference('INDI_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('FAM_FACTS_ADD')), explode(',', $WT_TREE->getPreference('FAM_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('NOTE_FACTS_ADD')), explode(',', $WT_TREE->getPreference('NOTE_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('SOUR_FACTS_ADD')), explode(',', $WT_TREE->getPreference('SOUR_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('REPO_FACTS_ADD')), explode(',', $WT_TREE->getPreference('REPO_FACTS_UNIQUE')),
	array('SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN')
));

$all_tags = array();
foreach ($tags as $tag) {
	if ($tag) {
		$all_tags[$tag] = GedcomTag::getLabel($tag);
	}
}

uasort($all_tags, '\Fisharebest\Webtrees\I18N::strcasecmp');

$resns = Database::prepare(
	"SELECT default_resn_id, tag_type, xref, resn" .
	" FROM `##default_resn`" .
	" LEFT JOIN `##name` ON (gedcom_id=n_file AND xref=n_id AND n_num=0)" .
	" WHERE gedcom_id=?" .
	" ORDER BY xref IS NULL, n_sort, xref, tag_type"
)->execute(array($WT_TREE->getTreeId()))->fetchAll();

foreach ($resns as $resn) {
	$resn->record = GedcomRecord::getInstance($resn->xref, $WT_TREE);
	if ($resn->tag_type) {
		$resn->tag_label = GedcomTag::getLabel($resn->tag_type);
	} else {
		$resn->tag_label = '';
	}
}
usort($resns, function (\stdClass $x, \stdClass $y) { return I18N::strcasecmp($x->tag_label, $y->tag_label); });

// We have two fields in one
$CALENDAR_FORMATS = explode('_and_', $WT_TREE->getPreference('CALENDAR_FORMAT') . '_and_');

// Split into separate fields
$relatives_events = explode(',', $WT_TREE->getPreference('SHOW_RELATIVES_EVENTS'));

switch (Filter::post('action')) {
case 'privacy':
	foreach (Filter::postArray('delete', WT_REGEX_INTEGER) as $delete_resn) {
		Database::prepare(
			"DELETE FROM `##default_resn` WHERE default_resn_id=?"
		)->execute(array($delete_resn));
	}

	$xrefs     = Filter::postArray('xref', WT_REGEX_XREF);
	$tag_types = Filter::postArray('tag_type', WT_REGEX_TAG);
	$resns     = Filter::postArray('resn');

	foreach ($xrefs as $n => $xref) {
		$tag_type = $tag_types[$n];
		$resn     = $resns[$n];

		if ($tag_type || $xref) {
			// Delete any existing data
			if ($xref === '') {
				Database::prepare(
					"DELETE FROM `##default_resn` WHERE gedcom_id=? AND tag_type=? AND xref IS NULL"
				)->execute(array($WT_TREE->getTreeId(), $tag_type));
			}
			if ($tag_type === '') {
				Database::prepare(
					"DELETE FROM `##default_resn` WHERE gedcom_id=? AND xref=? AND tag_type IS NULL"
				)->execute(array($WT_TREE->getTreeId(), $xref));
			}
			// Add (or update) the new data
			Database::prepare(
				"REPLACE INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULLIF(?, ''), NULLIF(?, ''), ?)"
			)->execute(array($WT_TREE->getTreeId(), $xref, $tag_type, $resn));
		}
	}

	$WT_TREE->setPreference('HIDE_LIVE_PEOPLE', Filter::postBool('HIDE_LIVE_PEOPLE'));
	$WT_TREE->setPreference('KEEP_ALIVE_YEARS_BIRTH', Filter::post('KEEP_ALIVE_YEARS_BIRTH', WT_REGEX_INTEGER, 0));
	$WT_TREE->setPreference('KEEP_ALIVE_YEARS_DEATH', Filter::post('KEEP_ALIVE_YEARS_DEATH', WT_REGEX_INTEGER, 0));
	$WT_TREE->setPreference('MAX_ALIVE_AGE', Filter::post('MAX_ALIVE_AGE', WT_REGEX_INTEGER, 100));
	$WT_TREE->setPreference('REQUIRE_AUTHENTICATION', Filter::postBool('REQUIRE_AUTHENTICATION'));
	$WT_TREE->setPreference('SHOW_DEAD_PEOPLE', Filter::post('SHOW_DEAD_PEOPLE'));
	$WT_TREE->setPreference('SHOW_LIVING_NAMES', Filter::post('SHOW_LIVING_NAMES'));
	$WT_TREE->setPreference('SHOW_PRIVATE_RELATIONSHIPS', Filter::post('SHOW_PRIVATE_RELATIONSHIPS'));

	FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', $WT_TREE->getTitleHtml()), 'success');
	header('Location: ' . WT_BASE_URL . 'admin_trees_manage.php?ged=' . $WT_TREE->getNameUrl());

	return;

case 'general':
	if (!Filter::checkCsrf()) {
		break;
	}

	// Coming soon
	if (Filter::postBool('all_trees')) {
		FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', $WT_TREE->getTitleHtml()), 'success');
	}
	if (Filter::postBool('new_trees')) {
		FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', $WT_TREE->getTitleHtml()), 'success');
	}

	$WT_TREE->setPreference('ADVANCED_NAME_FACTS', Filter::post('ADVANCED_NAME_FACTS'));
	$WT_TREE->setPreference('ADVANCED_PLAC_FACTS', Filter::post('ADVANCED_PLAC_FACTS'));
	$WT_TREE->setPreference('ALLOW_THEME_DROPDOWN', Filter::postBool('ALLOW_THEME_DROPDOWN'));
	// For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
	// e.g. "gregorian_and_jewish"
	$WT_TREE->setPreference('CALENDAR_FORMAT', implode('_and_', array_unique(array(
		Filter::post('CALENDAR_FORMAT0', 'gregorian|julian|french|jewish|hijri|jalali', 'none'),
		Filter::post('CALENDAR_FORMAT1', 'gregorian|julian|french|jewish|hijri|jalali', 'none'),
	))));
	$WT_TREE->setPreference('CHART_BOX_TAGS', Filter::post('CHART_BOX_TAGS'));
	$WT_TREE->setPreference('COMMON_NAMES_ADD', str_replace(' ', '', Filter::post('COMMON_NAMES_ADD')));
	$WT_TREE->setPreference('COMMON_NAMES_REMOVE', str_replace(' ', '', Filter::post('COMMON_NAMES_REMOVE')));
	$WT_TREE->setPreference('COMMON_NAMES_THRESHOLD', Filter::post('COMMON_NAMES_THRESHOLD', WT_REGEX_INTEGER, 40));
	$WT_TREE->setPreference('CONTACT_USER_ID', Filter::post('CONTACT_USER_ID'));
	$WT_TREE->setPreference('DEFAULT_PEDIGREE_GENERATIONS', Filter::post('DEFAULT_PEDIGREE_GENERATIONS'));
	$WT_TREE->setPreference('EXPAND_NOTES', Filter::postBool('EXPAND_NOTES'));
	$WT_TREE->setPreference('EXPAND_RELATIVES_EVENTS', Filter::postBool('EXPAND_RELATIVES_EVENTS'));
	$WT_TREE->setPreference('EXPAND_SOURCES', Filter::postBool('EXPAND_SOURCES'));
	$WT_TREE->setPreference('FAM_FACTS_ADD', str_replace(' ', '', Filter::post('FAM_FACTS_ADD')));
	$WT_TREE->setPreference('FAM_FACTS_QUICK', str_replace(' ', '', Filter::post('FAM_FACTS_QUICK')));
	$WT_TREE->setPreference('FAM_FACTS_UNIQUE', str_replace(' ', '', Filter::post('FAM_FACTS_UNIQUE')));
	$WT_TREE->setPreference('FAM_ID_PREFIX', Filter::post('FAM_ID_PREFIX'));
	$WT_TREE->setPreference('FULL_SOURCES', Filter::postBool('FULL_SOURCES'));
	$WT_TREE->setPreference('FORMAT_TEXT', Filter::post('FORMAT_TEXT'));
	$WT_TREE->setPreference('GEDCOM_ID_PREFIX', Filter::post('GEDCOM_ID_PREFIX'));
	$WT_TREE->setPreference('GEDCOM_MEDIA_PATH', Filter::post('GEDCOM_MEDIA_PATH'));
	$WT_TREE->setPreference('GENERATE_UIDS', Filter::postBool('GENERATE_UIDS'));
	$WT_TREE->setPreference('GEONAMES_ACCOUNT', Filter::post('GEONAMES_ACCOUNT'));
	$WT_TREE->setPreference('HIDE_GEDCOM_ERRORS', Filter::postBool('HIDE_GEDCOM_ERRORS'));
	$WT_TREE->setPreference('INDI_FACTS_ADD', str_replace(' ', '', Filter::post('INDI_FACTS_ADD')));
	$WT_TREE->setPreference('INDI_FACTS_QUICK', str_replace(' ', '', Filter::post('INDI_FACTS_QUICK')));
	$WT_TREE->setPreference('INDI_FACTS_UNIQUE', str_replace(' ', '', Filter::post('INDI_FACTS_UNIQUE')));
	$WT_TREE->setPreference('LANGUAGE', Filter::post('LANGUAGE'));
	$WT_TREE->setPreference('MAX_DESCENDANCY_GENERATIONS', Filter::post('MAX_DESCENDANCY_GENERATIONS'));
	$WT_TREE->setPreference('MAX_PEDIGREE_GENERATIONS', Filter::post('MAX_PEDIGREE_GENERATIONS'));
	$WT_TREE->setPreference('MEDIA_ID_PREFIX', Filter::post('MEDIA_ID_PREFIX'));
	$WT_TREE->setPreference('MEDIA_UPLOAD', Filter::post('MEDIA_UPLOAD'));
	$WT_TREE->setPreference('META_DESCRIPTION', Filter::post('META_DESCRIPTION'));
	$WT_TREE->setPreference('META_TITLE', Filter::post('META_TITLE'));
	$WT_TREE->setPreference('NOTE_ID_PREFIX', Filter::post('NOTE_ID_PREFIX'));
	$WT_TREE->setPreference('NO_UPDATE_CHAN', Filter::postBool('NO_UPDATE_CHAN'));
	$WT_TREE->setPreference('PEDIGREE_FULL_DETAILS', Filter::postBool('PEDIGREE_FULL_DETAILS'));
	$WT_TREE->setPreference('PEDIGREE_LAYOUT', Filter::postBool('PEDIGREE_LAYOUT'));
	$WT_TREE->setPreference('PEDIGREE_ROOT_ID', Filter::post('PEDIGREE_ROOT_ID', WT_REGEX_XREF));
	$WT_TREE->setPreference('PEDIGREE_SHOW_GENDER', Filter::postBool('PEDIGREE_SHOW_GENDER'));
	$WT_TREE->setPreference('PREFER_LEVEL2_SOURCES', Filter::post('PREFER_LEVEL2_SOURCES'));
	$WT_TREE->setPreference('QUICK_REQUIRED_FACTS', Filter::post('QUICK_REQUIRED_FACTS'));
	$WT_TREE->setPreference('QUICK_REQUIRED_FAMFACTS', Filter::post('QUICK_REQUIRED_FAMFACTS'));
	$WT_TREE->setPreference('REPO_FACTS_ADD', str_replace(' ', '', Filter::post('REPO_FACTS_ADD')));
	$WT_TREE->setPreference('REPO_FACTS_QUICK', str_replace(' ', '', Filter::post('REPO_FACTS_QUICK')));
	$WT_TREE->setPreference('REPO_FACTS_UNIQUE', str_replace(' ', '', Filter::post('REPO_FACTS_UNIQUE')));
	$WT_TREE->setPreference('REPO_ID_PREFIX', Filter::post('REPO_ID_PREFIX'));
	$WT_TREE->setPreference('SAVE_WATERMARK_IMAGE', Filter::postBool('SAVE_WATERMARK_IMAGE'));
	$WT_TREE->setPreference('SAVE_WATERMARK_THUMB', Filter::postBool('SAVE_WATERMARK_THUMB'));
	$WT_TREE->setPreference('SHOW_AGE_DIFF', Filter::postBool('SHOW_AGE_DIFF'));
	$WT_TREE->setPreference('SHOW_COUNTER', Filter::postBool('SHOW_COUNTER'));
	$WT_TREE->setPreference('SHOW_EST_LIST_DATES', Filter::postBool('SHOW_EST_LIST_DATES'));
	$WT_TREE->setPreference('SHOW_FACT_ICONS', Filter::postBool('SHOW_FACT_ICONS'));
	$WT_TREE->setPreference('SHOW_GEDCOM_RECORD', Filter::postBool('SHOW_GEDCOM_RECORD'));
	$WT_TREE->setPreference('SHOW_HIGHLIGHT_IMAGES', Filter::postBool('SHOW_HIGHLIGHT_IMAGES'));
	$WT_TREE->setPreference('SHOW_LAST_CHANGE', Filter::postBool('SHOW_LAST_CHANGE'));
	$WT_TREE->setPreference('SHOW_LDS_AT_GLANCE', Filter::postBool('SHOW_LDS_AT_GLANCE'));
	$WT_TREE->setPreference('SHOW_LEVEL2_NOTES', Filter::postBool('SHOW_LEVEL2_NOTES'));
	$WT_TREE->setPreference('SHOW_MEDIA_DOWNLOAD', Filter::postBool('SHOW_MEDIA_DOWNLOAD'));
	$WT_TREE->setPreference('SHOW_NO_WATERMARK', Filter::post('SHOW_NO_WATERMARK'));
	$WT_TREE->setPreference('SHOW_PARENTS_AGE', Filter::postBool('SHOW_PARENTS_AGE'));
	$WT_TREE->setPreference('SHOW_PEDIGREE_PLACES', Filter::post('SHOW_PEDIGREE_PLACES'));
	$WT_TREE->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', Filter::postBool('SHOW_PEDIGREE_PLACES_SUFFIX'));
	$WT_TREE->setPreference('SHOW_RELATIVES_EVENTS', implode(',', Filter::postArray('SHOW_RELATIVES_EVENTS')));
	$WT_TREE->setPreference('SOURCE_ID_PREFIX', Filter::post('SOURCE_ID_PREFIX'));
	$WT_TREE->setPreference('SOUR_FACTS_ADD', str_replace(' ', '', Filter::post('SOUR_FACTS_ADD')));
	$WT_TREE->setPreference('SOUR_FACTS_QUICK', str_replace(' ', '', Filter::post('SOUR_FACTS_QUICK')));
	$WT_TREE->setPreference('SOUR_FACTS_UNIQUE', str_replace(' ', '', Filter::post('SOUR_FACTS_UNIQUE')));
	$WT_TREE->setPreference('SUBLIST_TRIGGER_I', Filter::post('SUBLIST_TRIGGER_I', WT_REGEX_INTEGER, 200));
	$WT_TREE->setPreference('SURNAME_LIST_STYLE', Filter::post('SURNAME_LIST_STYLE'));
	$WT_TREE->setPreference('SURNAME_TRADITION', Filter::post('SURNAME_TRADITION'));
	$WT_TREE->setPreference('THEME_DIR', Filter::post('THEME_DIR'));
	$WT_TREE->setPreference('THUMBNAIL_WIDTH', Filter::post('THUMBNAIL_WIDTH'));
	$WT_TREE->setPreference('USE_RIN', Filter::postBool('USE_RIN'));
	$WT_TREE->setPreference('USE_SILHOUETTE', Filter::postBool('USE_SILHOUETTE'));
	$WT_TREE->setPreference('WATERMARK_THUMB', Filter::postBool('WATERMARK_THUMB'));
	$WT_TREE->setPreference('WEBMASTER_USER_ID', Filter::post('WEBMASTER_USER_ID'));
	$WT_TREE->setPreference('WEBTREES_EMAIL', Filter::post('WEBTREES_EMAIL'));
	$WT_TREE->setPreference('title', Filter::post('title'));

	// Only accept valid folders for MEDIA_DIRECTORY
	$MEDIA_DIRECTORY = preg_replace('/[\/\\\\]+/', '/', Filter::post('MEDIA_DIRECTORY') . '/');
	if (substr($MEDIA_DIRECTORY, 0, 1) === '/') {
		$MEDIA_DIRECTORY = substr($MEDIA_DIRECTORY, 1);
	}

	if ($MEDIA_DIRECTORY) {
		if (is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
			$WT_TREE->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
		} elseif (File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
			$WT_TREE->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
			FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'info');
		} else {
			FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'danger');
		}
	}

	$gedcom = Filter::post('gedcom');
	if ($gedcom && $gedcom !== $WT_TREE->getName()) {
		try {
			Database::prepare("UPDATE `##gedcom` SET gedcom_name = ? WHERE gedcom_id = ?")->execute(array($gedcom, $WT_TREE->getTreeId()));
			Database::prepare("UPDATE `##site_setting` SET setting_value = ? WHERE setting_name='DEFAULT_GEDCOM' AND setting_value = ?")->execute(array($gedcom, $WT_TREE->getName()));
		} catch (\Exception $ex) {
			// Probably a duplicate name.
		}
	}

	FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', $WT_TREE->getTitleHtml()), 'success');
	header('Location: ' . WT_BASE_URL . 'admin_trees_manage.php');

	return;
}

switch (Filter::get('action')) {
case 'privacy':
	$controller
		->setPageTitle($WT_TREE->getTitleHtml() . ' — ' . I18N::translate('Privacy'))
		->addInlineJavascript('
			jQuery("#default-resn input[type=checkbox]").on("click", function() {
				if ($(this).prop("checked")) {
					jQuery($(this).closest("tr").addClass("text-muted"));
				} else {
					jQuery($(this).closest("tr").removeClass("text-muted"));
				}
			});
			jQuery("#add-resn").on("click", function() {
				jQuery("#default-resn tbody").prepend(jQuery("#new-resn-template").html()); autocomplete();
			});
		');
	break;
case 'general':
	$controller->setPageTitle($WT_TREE->getTitleHtml() . ' — ' . I18N::translate('Preferences'));
	break;
default:
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_ADMIN_JS_URL)
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form class="form-horizontal" method="post">
	<?php echo Filter::getCsrf(); ?>
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">

	<?php if (Filter::get('action') === 'privacy'): ?>

	<input type="hidden" name="action" value="privacy">

	<!-- REQUIRE_AUTHENTICATION -->
	<div class="form-group">
		<div class="control-label col-sm-4">
			<label>
				<?php echo /* I18N: A configuration setting */ I18N::translate('Show the family tree'); ?>
			</label>
			<div class="hidden-xs">
				<span class="label visitors"><i class="fa fa-fw"></i> <?php echo I18N::translate('visitors'); ?></span>
				<span class="label members"><i class="fa fa-fw"></i><?php echo I18N::translate('members'); ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?php echo FunctionsEdit::selectEditControl('REQUIRE_AUTHENTICATION', array('0' => I18N::translate('Show to visitors'), '1' => I18N::translate('Show to members')), null, $WT_TREE->getPreference('REQUIRE_AUTHENTICATION'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Family tree” configuration setting */ I18N::translate('Enabling this option will force all visitors to sign in before they can view any data on the website.'); ?>
			</p>
			<?php if (Site::getPreference('USE_REGISTRATION_MODULE')): ?>
			<p class="small text-muted">
				<?php echo I18N::translate('If visitors can not see the family tree, they will not be able to sign up for an account. You will need to add their account manually.'); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>

	<!-- SHOW_DEAD_PEOPLE -->
	<div class="form-group">
		<div class="control-label col-sm-4">
			<label for="SHOW_DEAD_PEOPLE">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Show dead individuals'); ?>
			</label>
			<div class="hidden-xs">
				<span class="label visitors"><i class="fa fa-fw"></i> <?php echo I18N::translate('visitors'); ?></span>
				<span class="label members"><i class="fa fa-fw"></i><?php echo I18N::translate('members'); ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?php echo FunctionsEdit::selectEditControl('SHOW_DEAD_PEOPLE', array_slice($privacy, 0, 2, true), null, $WT_TREE->getPreference('SHOW_DEAD_PEOPLE'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show dead individuals” configuration setting */ I18N::translate('Set the privacy access level for all dead individuals.'); ?>
			</p>
		</div>
	</div>


	<!-- MAX_ALIVE_AGE -->
	<div class="form-group">
		<label class="control-label col-sm-4" for="MAX_ALIVE_AGE">
			<?php echo I18N::translate('Age at which to assume an individual is dead'); ?>
		</label>
		<div class="col-sm-8">
			<input
				class="form-control"
				id="MAX_ALIVE_AGE"
				maxlength="5"
				name="MAX_ALIVE_AGE"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('MAX_ALIVE_AGE')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Age at which to assume an individual is dead” configuration setting */ I18N::translate('If this individual has any events other than death, burial, or cremation more recent than this number of years, they are considered to be “alive”. Children’s birth dates are considered to be such events for this purpose.'); ?>
			</p>
		</div>
	</div>

	<!-- HIDE_LIVE_PEOPLE -->
	<fieldset class="form-group">
		<div class="control-label col-sm-4">
			<legend>
				<?php echo /* I18N: A configuration setting */ I18N::translate('Show living individuals'); ?>
			</legend>
			<div class="hidden-xs">
				<span class="label visitors"><i class="fa fa-fw"></i> <?php echo I18N::translate('visitors'); ?></span>
				<span class="label members"><i class="fa fa-fw"></i><?php echo I18N::translate('members'); ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?php echo FunctionsEdit::selectEditControl('HIDE_LIVE_PEOPLE', array('0' => I18N::translate('Show to visitors'), '1' => I18N::translate('Show to members')), null, $WT_TREE->getPreference('HIDE_LIVE_PEOPLE'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show living individuals” configuration setting */ I18N::translate('If you show living individuals to visitors, all other privacy restrictions are ignored. Do this only if all the data in your tree is public.'); ?>
			</p>
		</div>
	</fieldset>

		<!-- KEEP_ALIVE_YEARS_BIRTH / KEEP_ALIVE_YEARS_DEATH -->
	<fieldset class="form-group">
		<div class="control-label col-sm-4">
			<legend>
				<?php echo /* I18N: A configuration setting. …who were born in the last XX years or died in the last YY years */ I18N::translate('Extend privacy to dead individuals'); ?>
			</legend>
		</div>
		<div class="col-sm-8">
			<?php
			echo
				/* I18N: Extend privacy to dead individuals who were… */ I18N::translate(
				'born in the last %1$s years or died in the last %2$s years',
				'<input type="text" name="KEEP_ALIVE_YEARS_BIRTH" value="' . $WT_TREE->getPreference('KEEP_ALIVE_YEARS_BIRTH') . '" size="5" maxlength="3">',
				'<input type="text" name="KEEP_ALIVE_YEARS_DEATH" value="' . $WT_TREE->getPreference('KEEP_ALIVE_YEARS_DEATH') . '" size="5" maxlength="3">'
			); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Extend privacy to dead individuals” configuration setting */ I18N::translate('In some countries, privacy laws apply not only to living individuals, but also to those who have died recently. This option will allow you to extend the privacy rules for living individuals to those who were born or died within a specified number of years. Leave these values empty to disable this feature.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_LIVING_NAMES -->
	<div class="form-group">
		<div class="control-label col-sm-4">
			<label for="SHOW_LIVING_NAMES">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Show names of private individuals'); ?>
			</label>
			<div class="hidden-xs">
				<span class="label visitors"><i class="fa fa-fw"></i> <?php echo I18N::translate('visitors'); ?></span>
				<span class="label members"><i class="fa fa-fw"></i><?php echo I18N::translate('members'); ?></span>
				<span class="label managers"><i class="fa fa-fw"></i> <?php echo I18N::translate('managers'); ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?php echo FunctionsEdit::selectEditControl('SHOW_LIVING_NAMES', array_slice($privacy, 0, 3, true), null, $WT_TREE->getPreference('SHOW_LIVING_NAMES'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show names of private individuals” configuration setting */ I18N::translate('This option will show the names (but no other details) of private individuals. Individuals are private if they are still alive or if a privacy restriction has been added to their individual record. To hide a specific name, add a privacy restriction to that name record.'); ?>
			</p>
		</div>
	</div>

	<!-- SHOW_PRIVATE_RELATIONSHIPS -->
	<div class="form-group">
		<div class="control-label col-sm-4">
			<label for="SHOW_PRIVATE_RELATIONSHIPS">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Show private relationships'); ?>
			</label>
			<div class="hidden-xs">
				<span class="label visitors"><i class="fa fa-fw"></i> <?php echo I18N::translate('visitors'); ?></span>
				<span class="label members"><i class="fa fa-fw"></i><?php echo I18N::translate('members'); ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?php echo FunctionsEdit::selectEditControl('SHOW_PRIVATE_RELATIONSHIPS', array('0' => I18N::translate('Hide from everyone'), '1' => I18N::translate('Show to visitors')), null, $WT_TREE->getPreference('SHOW_PRIVATE_RELATIONSHIPS'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show private relationships” configuration setting */ I18N::translate('This option will retain family links in private records. This means that you will see empty “private” boxes on the pedigree chart and on other charts with private individuals.'); ?>
			</p>
		</div>
	</div>
	<h2><?php echo I18N::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag'); ?></h2>

	<script id="new-resn-template" type="text/html">
		<tr>
			<td>
				<input data-autocomplete-type="IFSRO" id="xref" maxlength="20" name="xref[]" type="text">
			</td>
			<td>
				<?php echo FunctionsEdit::selectEditControl('tag_type[]', $all_tags, '', null, null); ?>
			</td>
			<td>
				<?php echo FunctionsEdit::selectEditControl('resn[]', $PRIVACY_CONSTANTS, null, 'privacy', null); ?>
			</td>
			<td>
			</td>
		</tr>
	</script>

	<table class="table table-bordered table-condensed table-hover" id="default-resn">
		<caption class="sr-only">
			<?php echo I18N::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag'); ?>
		</caption>
		<thead>
		<tr>
			<th>
				<?php echo I18N::translate('Record'); ?>
			</th>
			<th>
				<?php echo I18N::translate('Fact or event'); ?>
			</th>
			<th>
				<?php echo I18N::translate('Access level'); ?>
			</th>
			<th>
				<button class="btn btn-primary" id="add-resn" type="button">
					<i class="fa fa-plus"></i>
					<?php echo /* I18N: A button label. Add an item. */ I18N::translate('Add'); ?>
				</button>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($resns as $resn): ?>
			<tr>
				<td>
					<?php if ($resn->record): ?>
					<a href="<?php echo $resn->record->getHtmlUrl(); ?>"><?php echo $resn->record->getFullName(); ?></a>
					<?php elseif ($resn->xref): ?>
					<div class="bg-danger text-danger">
						<?php echo $resn->xref, ' — ', I18N::translate('this record does not exist'); ?>
					</div>
					<?php else: ?>
					<div class="text-muted">
						<?php echo I18N::translate('All records'); ?>
					</div>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($resn->tag_label): ?>
					<?php echo $resn->tag_label; ?>
					<?php else: ?>
					<div class="text-muted">
						<?php echo I18N::translate('All facts and events'); ?>
					</div>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $PRIVACY_CONSTANTS[$resn->resn]; ?>
				</td>
				<td>
					<label for="delete-<?php echo $resn->default_resn_id; ?>">
						<?php echo I18N::translate('Delete'); ?>
						<input id="delete-<?php echo $resn->default_resn_id; ?>" name="delete[]" type="checkbox" value="<?php echo $resn->default_resn_id; ?>">
					</label>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php elseif (Filter::get('action') === 'general'): ?>

	<input type="hidden" name="action" value="general">

	<h3><?php echo I18N::translate('General'); ?></h3>

	<!-- TREE TITLE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="title">
			<?php echo I18N::translate('Family tree title'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				dir="auto"
				id="title"
				maxlength="255"
				name="title"
				required
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('title')); ?>"
				>
		</div>
	</div>

	<!-- TREE URL / FILENAME -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="gedcom">
			<?php echo I18N::translate('URL'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<span class="input-group-addon" dir="ltr">
					<?php echo WT_BASE_URL; ?>?ged=
				</span>
				<input
					class="form-control"
					id="gedcom"
					maxlength="255"
					name="gedcom"
					required
					type="text"
					value="<?php echo $WT_TREE->getNameHtml(); ?>"
					>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: help text for family tree / GEDCOM file names */ I18N::translate('Avoid spaces and punctuation. A family name might be a good choice.'); ?>
			</p>
		</div>
	</div>

	<!-- LANGUAGE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="LANGUAGE">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Language'); ?>
		</label>
		<div class="col-sm-9">
			<select id="LANGUAGE" name="LANGUAGE" class="form-control">
				<?php foreach (I18N::activeLocales() as $active_locale): ?>
					<option value="<?php echo $active_locale->languageTag(); ?>" <?php echo $WT_TREE->getPreference('LANGUAGE') === $active_locale->languageTag() ? 'selected' : ''; ?>>
						<?php echo $active_locale->endonym(); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Language” configuration setting */ I18N::translate('If a visitor to the website has not specified a preferred language in their browser configuration, or they have specified an unsupported language, then this language will be used. Typically, this setting applies to search engines.'); ?>
			</p>
		</div>
	</div>

	<!-- PEDIGREE_ROOT_ID -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="PEDIGREE_ROOT_ID">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Default individual'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					data-autocomplete-type="INDI"
					dir="ltr"
					id="PEDIGREE_ROOT_ID"
					maxlength="20"
					name="PEDIGREE_ROOT_ID"
					type="text"
					value="<?php echo $WT_TREE->getPreference('PEDIGREE_ROOT_ID'); ?>"
				>
				<span class="input-group-addon">
					<?php
					$person = Individual::getInstance($WT_TREE->getPreference('PEDIGREE_ROOT_ID'), $WT_TREE);
					if ($person) {
						echo $person->getFullName(), ' ', $person->getLifeSpan();
					} else {
						echo I18N::translate('Unable to find record with ID');
					}
					?>
				</span>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Default individual” configuration setting */ I18N::translate('This individual will be selected by default when viewing charts and reports.'); ?>
			</p>
		</div>
	</div>

	<!-- CALENDAR_FORMAT -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Calendar conversion'); ?>
			<label class="sr-only" for="CALENDAR_FORMAT0">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Calendar conversion'); ?> 1
			</label>
			<label class="sr-only" for="CALENDAR_FORMAT1">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Calendar conversion'); ?> 2
			</label>
		</legend>
		<div class="col-sm-9">
			<div class=row">
				<div class="col-sm-6" style="padding-left: 0;">
					<?php echo FunctionsEdit::selectEditControl('CALENDAR_FORMAT0', $calendars, null, $CALENDAR_FORMATS[0], 'class="form-control"'); ?>
				</div>
				<div class="col-sm-6" style="padding-right: 0;">
					<?php echo FunctionsEdit::selectEditControl('CALENDAR_FORMAT1', $calendars, null, $CALENDAR_FORMATS[1], 'class="form-control"'); ?>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Calendar conversion” configuration setting */ I18N::translate('Different calendar systems are used in different parts of the world, and many other calendar systems have been used in the past. Where possible, you should enter dates using the calendar in which the event was originally recorded. You can then specify a conversion, to show these dates in a more familiar calendar. If you regularly use two calendars, you can specify two conversions and dates will be converted to both the selected calendars.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Calendar conversion” configuration setting */ I18N::translate('Dates are only converted if they are valid for the calendar. For example, only dates between %1$s and %2$s will be converted to the French calendar and only dates after %3$s will be converted to the Gregorian calendar.', $french_calendar_start->display(false, null, false), $french_calendar_end->display(false, null, false), $gregorian_calendar_start->display(false, null, false)); ?>
			</p>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Calendar conversion” configuration setting */ I18N::translate('In some calendars, days start at midnight. In other calendars, days start at sunset. The conversion process does not take account of the time, so for any event that occurs between sunset and midnight, the conversion between these types of calendar will be one day out.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- USE_RIN -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Use RIN number instead of GEDCOM ID'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('USE_RIN', $no_yes, $WT_TREE->getPreference('USE_RIN'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Use RIN number instead of GEDCOM ID” configuration setting */ I18N::translate('Set to <b>Yes</b> to use the RIN number instead of the GEDCOM ID when asked for individual IDs in configuration files, user settings, and charts. This is useful for genealogy programs that do not consistently export GEDCOMs with the same ID assigned to each individual but always use the same RIN.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- GENERATE_UIDS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Automatically create globally unique IDs'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('GENERATE_UIDS', $no_yes, $WT_TREE->getPreference('GENERATE_UIDS'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Automatically create globally unique IDs” configuration setting */ I18N::translate('<b>GUID</b> in this context is an acronym for “Globally Unique ID”.<br><br>GUIDs are intended to help identify each individual in a manner that is repeatable, so that central organizations such as the Family History Center of the LDS church in Salt Lake City, or even compatible programs running on your own server, can determine whether they are dealing with the same individual no matter where the GEDCOM file originates. The goal of the Family History Center is to have a central repository of genealogy data and expose it through web services. This will enable any program to access the data and update their data within it.<br><br>If you do not intend to share this GEDCOM file with anyone else, you do not need to let webtrees create these GUIDs; however, doing so will do no harm other than increasing the size of your GEDCOM file.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- XXXXX_ID_PREFIX -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('ID settings'); ?>
		</legend>
		<div class="col-sm-9">
			<div class="row">
				<!-- GEDCOM_ID_PREFIX -->
				<div class="col-sm-6 col-md-4">
					<div class="input-group">
						<label class="input-group-addon" for="GEDCOM_ID_PREFIX">
							<?php echo I18N::translate('Individual ID prefix'); ?>
						</label>
						<input
							class="form-control"
							dir="ltr"
							id="GEDCOM_ID_PREFIX"
							maxlength="10"
							name="GEDCOM_ID_PREFIX"
							type="text"
							value="<?php echo $WT_TREE->getPreference('GEDCOM_ID_PREFIX'); ?>"
							>
					</div>
				</div>
				<!-- FAM_ID_PREFIX -->
				<div class="col-sm-6 col-md-4">
					<div class="input-group">
						<label class="input-group-addon" for="FAM_ID_PREFIX">
							<?php echo I18N::translate('Family ID prefix'); ?>
						</label>
						<input
							class="form-control"
							dir="ltr"
							id="FAM_ID_PREFIX"
							maxlength="10"
							name="FAM_ID_PREFIX"
							type="text"
							value="<?php echo $WT_TREE->getPreference('FAM_ID_PREFIX'); ?>"
							>
					</div>
				</div>
				<!-- SOURCE_ID_PREFIX -->
				<div class="col-sm-6 col-md-4">
					<div class="input-group">
						<label class="input-group-addon" for="SOURCE_ID_PREFIX">
							<?php echo I18N::translate('Source ID prefix'); ?>
						</label>
						<input
							class="form-control"
							dir="ltr"
							id="SOURCE_ID_PREFIX"
							maxlength="10"
							name="SOURCE_ID_PREFIX"
							type="text"
							value="<?php echo $WT_TREE->getPreference('SOURCE_ID_PREFIX'); ?>"
							>
					</div>
				</div>
				<!-- REPO_ID_PREFIX -->
				<div class="col-sm-6 col-md-4">
					<div class="input-group">
						<label class="input-group-addon" for="REPO_ID_PREFIX">
							<?php echo I18N::translate('Repository ID prefix'); ?>
						</label>
						<input
							class="form-control"
							dir="ltr"
							id="REPO_ID_PREFIX"
							maxlength="10"
							name="REPO_ID_PREFIX"
							type="text"
							value="<?php echo $WT_TREE->getPreference('REPO_ID_PREFIX'); ?>"
							>
					</div>
				</div>
				<!-- MEDIA_ID_PREFIX -->
				<div class="col-sm-6 col-md-4">
					<div class="input-group">
						<label class="input-group-addon" for="MEDIA_ID_PREFIX">
							<?php echo I18N::translate('Media ID prefix'); ?>
						</label>
						<input
							class="form-control"
							dir="ltr"
							id="MEDIA_ID_PREFIX"
							maxlength="10"
							name="MEDIA_ID_PREFIX"
							type="text"
							value="<?php echo $WT_TREE->getPreference('MEDIA_ID_PREFIX'); ?>"
							>
					</div>
				</div>
				<!-- NOTE_ID_PREFIX -->
				<div class="col-sm-6 col-md-4">
					<div class="input-group">
						<label class="input-group-addon" for="NOTE_ID_PREFIX">
							<?php echo I18N::translate('Note ID prefix'); ?>
						</label>
						<input
							class="form-control"
							dir="ltr"
							id="NOTE_ID_PREFIX"
							maxlength="10"
							name="NOTE_ID_PREFIX"
							type="text"
							value="<?php echo $WT_TREE->getPreference('NOTE_ID_PREFIX'); ?>"
						>
					</div>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “ID settings” configuration setting */ I18N::translate('When new records are created, they are given an internal ID number. You can specify the prefix used for each type of record.'); ?>
			</p>
		</div>
	</fieldset>

	<h3><?php echo I18N::translate('Contact information'); ?></h3>

	<!-- WEBTREES_EMAIL -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="WEBTREES_EMAIL">
			<?php echo /* I18N: A configuration setting */ I18N::translate('webtrees reply address'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="WEBTREES_EMAIL"
				maxlength="255"
				name="WEBTREES_EMAIL"
				required
				type="email"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('WEBTREES_EMAIL')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “webtrees reply address” configuration setting */ I18N::translate('Email address to be used in the “From:” field of emails that webtrees creates automatically.<br><br>webtrees can automatically create emails to notify administrators of changes that need to be reviewed. webtrees also sends notification emails to users who have requested an account.<br><br>Usually, the “From:” field of these automatically created emails is something like <i>From: webtrees-noreply@yoursite</i> to show that no response to the email is required. To guard against spam or other email abuse, some email systems require each message’s “From:” field to reflect a valid email account and will not accept messages that are apparently from account <i>webtrees-noreply</i>.'); ?>
			</p>
		</div>
	</div>

	<!-- CONTACT_USER_ID -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="CONTACT_USER_ID">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Genealogy contact'); ?>
		</label>
		<div class="col-sm-9">
			<select id="CONTACT_USER_ID" name="CONTACT_USER_ID" class="form-control">
				<option value=""></option>
				<?php foreach (User::all() as $user): ?>
					<?php if (Auth::isMember($WT_TREE, $user)): ?>
						<option value="<?php echo $user->getUserId(); ?>" <?php echo $WT_TREE->getPreference('CONTACT_USER_ID') === $user->getUserId() ? 'selected' : ''; ?>>
							<?php echo $user->getRealNameHtml() . ' - ' . Filter::escapeHtml($user->getUserName()); ?>
						</option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Genealogy contact” configuration setting */ I18N::translate('The individual to contact about the genealogy data on this website.'); ?>
			</p>
		</div>
	</div>

	<!-- WEBMASTER_USER_ID -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="WEBMASTER_USER_ID">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Technical help contact'); ?>
		</label>
		<div class="col-sm-9">
			<select id="WEBMASTER_USER_ID" name="WEBMASTER_USER_ID" class="form-control">
				<option value=""></option>
				<?php foreach (User::all() as $user): ?>
					<?php if (Auth::isMember($WT_TREE, $user)): ?>
						<option value="<?php echo $user->getUserId(); ?>" <?php echo $WT_TREE->getPreference('WEBMASTER_USER_ID') === $user->getUserId() ? 'selected' : ''; ?>>
							<?php echo $user->getRealNameHtml() . ' - ' . Filter::escapeHtml($user->getUserName()); ?>
						</option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Technical help contact” configuration setting */ I18N::translate('The individual to be contacted about technical questions or errors encountered on your website.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Website and META tag settings'); ?></h3>

	<!-- META_TITLE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="META_TITLE">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Add to TITLE header tag'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="META_TITLE"
				maxlength="255"
				name="META_TITLE"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('META_TITLE')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Add to TITLE header tag” configuration setting */ I18N::translate('This text will be appended to each page title. It will be shown in the browser’s title bar, bookmarks, etc.'); ?>
			</p>
		</div>
	</div>

	<!-- META_DESCRIPTION -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="META_DESCRIPTION">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Description META tag'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="META_DESCRIPTION"
				maxlength="255"
				name="META_DESCRIPTION"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('META_DESCRIPTION')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Description META tag” configuration setting */ I18N::translate('The value to place in the “meta description” tag in the HTML page header. Leave this field empty to use the name of the family tree.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('User options'); ?></h3>
	<!-- ALLOW_THEME_DROPDOWN -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Theme dropdown selector for theme changes'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('ALLOW_THEME_DROPDOWN', $hide_show, $WT_TREE->getPreference('ALLOW_THEME_DROPDOWN'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Theme dropdown selector for theme changes” configuration setting */ I18N::translate('Gives users the option of selecting their own theme from a menu.<br><br>Even with this option set, the theme currently in effect may not provide for such a menu. To be effective, this option requires the <b>Allow users to select their own theme</b> option to be set as well.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- THEME_DIR -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="THEME_DIR">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Default theme'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('THEME_DIR', Theme::themeNames(), I18N::translate('<default theme>'), $WT_TREE->getPreference('THEME_DIR'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Default theme” configuration setting */ I18N::translate('You can change the appearance of webtrees using “themes”. Each theme has a different style, layout, color scheme, etc.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Media'); ?></h3>
	<h3><?php echo I18N::translate('Media folders'); ?></h3>

	<!-- MEDIA_DIRECTORY -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="MEDIA_DIRECTORY">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Media folder'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<span class="input-group-addon">
					<?php echo WT_DATA_DIR; ?>
				</span>
				<input
					class="form-control"
					dir="ltr"
					id="MEDIA_DIRECTORY"
					maxlength="255"
					name="MEDIA_DIRECTORY"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('MEDIA_DIRECTORY')); ?>"
				>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Media folder” configuration setting */ I18N::translate('This folder will be used to store the media files for this family tree.'); ?>
				<?php echo /* I18N: Help text for the “Media folder” configuration setting */ I18N::translate('If you select a different folder, you must also move any media files from the existing folder to the new one.'); ?>
				<?php echo /* I18N: Help text for the “Media folder” configuration setting */ I18N::translate('If two family trees use the same media folder, then they will be able to share media files. If they use different media folders, then their media files will be kept separate.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Media files'); ?></h3>

	<!-- MEDIA_UPLOAD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="MEDIA_UPLOAD">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Who can upload new media files'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('MEDIA_UPLOAD', array(Auth::PRIV_USER => I18N::translate('Show to members'), Auth::PRIV_NONE => I18N::translate('Show to managers'), Auth::PRIV_HIDE => I18N::translate('Hide from everyone')), null, $WT_TREE->getPreference('MEDIA_UPLOAD'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Who can upload new media files” configuration setting */ I18N::translate('If you are concerned that users might upload inappropriate images, you can restrict media uploads to managers only.'); ?>
			</p>
		</div>
	</div>

	<!-- SHOW_MEDIA_DOWNLOAD -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Show a download link in the media viewer'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_MEDIA_DOWNLOAD', $no_yes, $WT_TREE->getPreference('SHOW_MEDIA_DOWNLOAD'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show a download link in the media viewer” configuration setting */ I18N::translate('The Media Viewer can show a link which, when clicked, will download the media file to the local PC.<br><br>You may want to hide the download link for security reasons.'); ?>
			</p>
		</div>
	</fieldset>

	<h3><?php echo I18N::translate('Thumbnail images'); ?></h3>

	<!-- THUMBNAIL_WIDTH -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="THUMBNAIL_WIDTH">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Width of generated thumbnails'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					id="THUMBNAIL_WIDTH"
					maxlength="4"
					name="THUMBNAIL_WIDTH"
					required
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('THUMBNAIL_WIDTH')); ?>"
					>
				<span class="input-group-addon">
					<?php echo /* Image sizes are measured in pixels */ I18N::translate('pixels'); ?>
				</span>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Width of generated thumbnails” configuration setting */ I18N::translate('This is the width (in pixels) that the program will use when automatically generating thumbnails. The default setting is 100.'); ?>
			</p>
		</div>
	</div>

	<!-- USE_SILHOUETTE -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Use silhouettes'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('USE_SILHOUETTE', $no_yes, $WT_TREE->getPreference('USE_SILHOUETTE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Use silhouettes” configuration setting */ I18N::translate('Use silhouette images when no highlighted image for that individual has been specified. The images used are specific to the gender of the individual in question.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_HIGHLIGHT_IMAGES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo I18N::translate('Show highlight images in individual boxes'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_HIGHLIGHT_IMAGES', $no_yes, $WT_TREE->getPreference('SHOW_HIGHLIGHT_IMAGES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
			</p>
		</div>
	</fieldset>

	<h3><?php echo I18N::translate('Watermarks'); ?></h3>

	<!-- WATERMARK_THUMB -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Add watermarks to thumbnails'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('WATERMARK_THUMB', $no_yes, $WT_TREE->getPreference('WATERMARK_THUMB'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Add watermarks to thumbnails” configuration setting */ I18N::translate('A watermark is text that is added to an image, to discourage others from copying it without permission.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SAVE_WATERMARK_IMAGE -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Store watermarked full size images on server'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SAVE_WATERMARK_IMAGE', $no_yes, $WT_TREE->getPreference('SAVE_WATERMARK_IMAGE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Store watermarked full size images on server” configuration setting */ I18N::translate('Watermarks can be slow to generate for large images. Busy websites may prefer to generate them once and store the watermarked image on the server.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SAVE_WATERMARK_THUMB -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Store watermarked thumbnails on server'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SAVE_WATERMARK_THUMB', $no_yes, $WT_TREE->getPreference('SAVE_WATERMARK_THUMB'), 'class="radio-inline"'); ?>
		</div>
	</fieldset>

	<!-- SHOW_NO_WATERMARK -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SHOW_NO_WATERMARK">
			<?php echo I18N::translate('Images without watermarks'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('SHOW_NO_WATERMARK', $privacy, null, $WT_TREE->getPreference('SHOW_NO_WATERMARK'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Images without watermarks” configuration setting */ I18N::translate('Watermarks are optional and normally shown just to visitors.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Layout'); ?></h3>

	<h3><?php echo I18N::translate('Names'); ?></h3>

	<!-- COMMON_NAMES_THRESHOLD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="COMMON_NAMES_THRESHOLD">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Minimum number of occurrences to be a “common surname”'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="COMMON_NAMES_THRESHOLD"
				maxlength="5"
				name="COMMON_NAMES_THRESHOLD"
				required
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('COMMON_NAMES_THRESHOLD')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Minimum number of occurrences to be a ‘common surname’” configuration setting */ I18N::translate('This is the number of times that a surname must occur before it shows up in the Common Surname list on the “Home page”.'); ?>
			</p>
		</div>
	</div>

	<!-- COMMON_NAMES_ADD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="COMMON_NAMES_ADD">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Names to add to common surnames (comma separated)'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="COMMON_NAMES_ADD"
				maxlength="255"
				name="COMMON_NAMES_ADD"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('COMMON_NAMES_ADD')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Names to add to common surnames (comma separated)” configuration setting */ I18N::translate('If the number of times that a certain surname occurs is lower than the threshold, it will not appear in the list. It can be added here manually. If more than one surname is entered, they must be separated by a comma. <b>Surnames are case-sensitive.</b>'); ?>
			</p>
		</div>
	</div>

	<!-- COMMON_NAMES_REMOVE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="COMMON_NAMES_REMOVE">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Names to remove from common surnames (comma separated)'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="COMMON_NAMES_REMOVE"
				maxlength="255"
				name="COMMON_NAMES_REMOVE"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('COMMON_NAMES_REMOVE')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Names to remove from common surnames (comma separated)” configuration setting */ I18N::translate('If you want to remove a surname from the Common Surname list without increasing the threshold value, you can do that by entering the surname here. If more than one surname is entered, they must be separated by a comma. <b>Surnames are case-sensitive</b>. Surnames entered here will also be removed from the “Top surnames” list on the “Home page”.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Lists'); ?></h3>

	<!-- SURNAME_LIST_STYLE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SURNAME_LIST_STYLE">
			<?php echo I18N::translate('Surname list style'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('SURNAME_LIST_STYLE', $surname_list_styles, null, $WT_TREE->getPreference('SURNAME_LIST_STYLE'), 'class="form-control"'); ?>
			<p class="small text-muted">
			</p>
		</div>
	</div>

	<!-- SUBLIST_TRIGGER_I -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SUBLIST_TRIGGER_I">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Maximum number of surnames on individual list'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="SUBLIST_TRIGGER_I"
				maxlength="5"
				name="SUBLIST_TRIGGER_I"
				required
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('SUBLIST_TRIGGER_I')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Maximum number of surnames on individual list” configuration setting */ I18N::translate('Long lists of individuals with the same surname can be broken into smaller sub-lists according to the first letter of the individual’s given name.<br><br>This option determines when sub-listing of surnames will occur. To disable sub-listing completely, set this option to zero.'); ?>
			</p>
		</div>
	</div>

	<!-- SHOW_EST_LIST_DATES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Estimated dates for birth and death'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_EST_LIST_DATES', $hide_show, $WT_TREE->getPreference('SHOW_EST_LIST_DATES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Estimated dates for birth and death” configuration setting */ I18N::translate('This option controls whether or not to show estimated dates for birth and death instead of leaving blanks on individual lists and charts for individuals whose dates are not known.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_LAST_CHANGE -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
						<?php echo I18N::translate('The date and time of the last update'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_LAST_CHANGE', $hide_show, $WT_TREE->getPreference('SHOW_LAST_CHANGE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
			</p>
		</div>
	</fieldset>

	<h3><?php echo I18N::translate('Charts'); ?></h3>

	<!-- PEDIGREE_LAYOUT -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Default pedigree chart layout'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('PEDIGREE_LAYOUT', $layouts, $WT_TREE->getPreference('PEDIGREE_LAYOUT'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Default pedigree chart layout” tree configuration setting */ I18N::translate('This option indicates whether the pedigree chart should be generated in landscape or portrait mode.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- DEFAULT_PEDIGREE_GENERATIONS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="DEFAULT_PEDIGREE_GENERATIONS">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Default pedigree generations'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="DEFAULT_PEDIGREE_GENERATIONS"
				maxlength="5"
				name="DEFAULT_PEDIGREE_GENERATIONS"
				required
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Default pedigree generations” configuration setting */ I18N::translate('Set the default number of generations to display on descendancy and pedigree charts.'); ?>
			</p>
		</div>
	</div>

	<!-- MAX_PEDIGREE_GENERATIONS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="MAX_PEDIGREE_GENERATIONS">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Maximum pedigree generations'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="MAX_PEDIGREE_GENERATIONS"
				maxlength="5"
				name="MAX_PEDIGREE_GENERATIONS"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Maximum pedigree generations” configuration setting */ I18N::translate('Set the maximum number of generations to display on pedigree charts.'); ?>
			</p>
		</div>
	</div>

	<!-- MAX_DESCENDANCY_GENERATIONS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="MAX_DESCENDANCY_GENERATIONS">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Maximum descendancy generations'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="MAX_DESCENDANCY_GENERATIONS"
				maxlength="5"
				name="MAX_DESCENDANCY_GENERATIONS"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Maximum descendancy generations” configuration setting */ I18N::translate('Set the maximum number of generations to display on descendancy charts.'); ?>
			</p>
		</div>
	</div>

	<!-- PEDIGREE_FULL_DETAILS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Show chart details by default'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('PEDIGREE_FULL_DETAILS', $no_yes, $WT_TREE->getPreference('PEDIGREE_FULL_DETAILS'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show chart details by default” configuration setting */ I18N::translate('This is the initial setting for the “show details” option on the charts.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- PEDIGREE_SHOW_GENDER -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Gender icon on charts'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('PEDIGREE_SHOW_GENDER', $hide_show, $WT_TREE->getPreference('PEDIGREE_SHOW_GENDER'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Gender icon on charts” configuration setting */ I18N::translate('This option controls whether or not to show the individual’s gender icon on charts.<br><br>Since the gender is also indicated by the color of the box, this option doesn’t conceal the gender. The option simply removes some duplicate information from the box.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_PARENTS_AGE -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Age of parents next to child’s birthdate'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_PARENTS_AGE', $hide_show, $WT_TREE->getPreference('SHOW_PARENTS_AGE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Age of parents next to child’s birthdate” configuration setting */ I18N::translate('This option controls whether or not to show age of father and mother next to child’s birthdate on charts.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_LDS_AT_GLANCE -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('LDS ordinance codes in chart boxes'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_LDS_AT_GLANCE', $hide_show, $WT_TREE->getPreference('SHOW_LDS_AT_GLANCE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “LDS ordinance codes in chart boxes” configuration setting. "B", "E", "S" and "P" should not be translated. */ I18N::translate('This is a summary of the <abbr title="The Church of Jesus Christ of Latter-day Saints">LDS</abbr> ordinances for the individual. “B” indicates an LDS baptism. “E” indicates an LDS endowment. “S” indicates an LDS spouse sealing. “P” indicates an LDS child-to-parent sealing.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- CHART_BOX_TAGS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="CHART_BOX_TAGS">
			<?php echo I18N::translate('Other facts to show in charts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="CHART_BOX_TAGS"
					maxlength="255"
					name="CHART_BOX_TAGS"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('CHART_BOX_TAGS')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('CHART_BOX_TAGS', 'INDI');">
						<i class="fa fa-pencil"></i>
						<?php echo /* I18N: A button label */ I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Other facts to show in charts” configuration setting */ I18N::translate('This should be a comma or space separated list of facts, in addition to birth and death, that you want to appear in chart boxes such as the pedigree chart. This list requires you to use fact tags as defined in the GEDCOM 5.5.1 standard. For example, if you wanted the occupation to show up in the box, you would add “OCCU” to this field.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Individual pages'); ?></h3>

	<!-- EXPAND_RELATIVES_EVENTS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Automatically expand list of events of close relatives'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('EXPAND_RELATIVES_EVENTS', $no_yes, $WT_TREE->getPreference('EXPAND_RELATIVES_EVENTS'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Automatically expand list of events of close relatives” configuration setting */ I18N::translate('This option controls whether or not to automatically expand the <i>Events of close relatives</i> list.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_RELATIVES_EVENTS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo I18N::translate('Show the events of close relatives on the individual page'); ?>
		</legend>
		<div class="col-sm-3">
			<div class="checkbox">
				<label for="_BIRT_GCHI">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_BIRT_GCHI" value="_BIRT_GCHI" <?php echo in_array('_BIRT_GCHI', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_BIRT_GCHI'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_BIRT_CHIL">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_BIRT_CHIL" value="_BIRT_CHIL" <?php echo in_array('_BIRT_CHIL', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_BIRT_CHIL'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_BIRT_SIBL">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_BIRT_SIBL" value="_BIRT_SIBL" <?php echo in_array('_BIRT_SIBL', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_BIRT_SIBL'); ?>
				</label>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="checkbox">
				<label for="_MARR_GCHI">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_GCHI" value="_MARR_GCHI" <?php echo in_array('_MARR_GCHI', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_MARR_GCHI'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_MARR_CHIL">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_CHIL" value="_MARR_CHIL" <?php echo in_array('_MARR_CHIL', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_MARR_CHIL'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_MARR_SIBL">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_SIBL" value="_MARR_SIBL" <?php echo in_array('_MARR_SIBL', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_MARR_SIBL'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_MARR_PARE">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_PARE" value="_MARR_PARE" <?php echo in_array('_MARR_PARE', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_MARR_PARE'); ?>
				</label>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="checkbox">
				<label for="_DEAT_GCHI">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_GCHI" value="_DEAT_GCHI" <?php echo in_array('_DEAT_GCHI', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_DEAT_GCHI'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_DEAT_CHIL">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_CHIL" value="_DEAT_CHIL" <?php echo in_array('_DEAT_CHIL', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_DEAT_CHIL'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_DEAT_SIBL">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_SIBL" value="_DEAT_SIBL" <?php echo in_array('_DEAT_SIBL', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_DEAT_SIBL'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_DEAT_PARE">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_PARE" value="_DEAT_PARE" <?php echo in_array('_DEAT_PARE', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_DEAT_PARE'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_DEAT_SPOU">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_SPOU" value="_DEAT_SPOU" <?php echo in_array('_DEAT_SPOU', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_DEAT_SPOU'); ?>
				</label>
			</div>
			<div class="checkbox">
				<label for="_DEAT_GPAR">
					<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_GPAR" value="_DEAT_GPAR" <?php echo in_array('_DEAT_GPAR', $relatives_events) ? 'checked' : ''; ?>>
					<?php echo GedcomTag::getLabel('_DEAT_GPAR'); ?>
				</label>
			</div>
		</div>
	</fieldset>
	<!-- SHOW_FACT_ICONS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */
			I18N::translate('Fact icons'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_FACT_ICONS', $hide_show, $WT_TREE->getPreference('SHOW_FACT_ICONS'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Fact icons” configuration setting */
				I18N::translate('Some themes can display icons on the “Facts and events” tab.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- EXPAND_NOTES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */
			I18N::translate('Automatically expand notes'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('EXPAND_NOTES', $no_yes, $WT_TREE->getPreference('EXPAND_NOTES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Automatically expand notes” configuration setting */
				I18N::translate('This option controls whether or not to automatically display content of a <i>Note</i> record on the Individual page.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- EXPAND_SOURCES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */
			I18N::translate('Automatically expand sources'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('EXPAND_SOURCES', $no_yes, $WT_TREE->getPreference('EXPAND_SOURCES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Automatically expand sources” configuration setting */
				I18N::translate('This option controls whether or not to automatically display content of a <i>Source</i> record on the Individual page.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_LEVEL2_NOTES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */
			I18N::translate('Show all notes and source references on notes and sources tabs'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_LEVEL2_NOTES', $no_yes, $WT_TREE->getPreference('SHOW_LEVEL2_NOTES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show all notes and source references on notes and sources tabs” configuration setting */
				I18N::translate('This option controls whether Notes and Source references that are attached to Facts should be shown on the Notes and Sources tabs of the Individual page.<br><br>Ordinarily, the Notes and Sources tabs show only Notes and Source references that are attached directly to the individual’s database record. These are <i>level 1</i> Notes and Source references.<br><br>The <b>Yes</b> option causes these tabs to also show Notes and Source references that are part of the various Facts in the individual’s database record. These are <i>level 2</i> Notes and Source references because the various Facts are at level 1.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_AGE_DIFF -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */
			I18N::translate('Date differences'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_AGE_DIFF', $hide_show, $WT_TREE->getPreference('SHOW_AGE_DIFF'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Date differences” configuration setting */
				I18N::translate('When this option is selected, webtrees will calculate the age differences between siblings, children, spouses, etc.'); ?>
			</p>
		</div>
	</fieldset>

	<h3><?php echo I18N::translate('Places'); ?></h3>

	<!-- SHOW_PEDIGREE_PLACES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Abbreviate place names'); ?>
			<label class="sr-only" for="SHOW_PEDIGREE_PLACES_SUFFIX">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Abbreviate place names'); ?>
				<?php echo I18N::translate('first'); ?> /<?php echo I18N::translate('last'); ?>
			</label>
			<label class="sr-only" for="SHOW_PEDIGREE_PLACES">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Abbreviate place names'); ?>
			</label>
		</legend>
		<div class="col-sm-9">
			<?php echo /* I18N: The placeholders are edit controls. Show the [first/last] [1/2/3/4/5] parts of a place name */ I18N::translate(
				'Show the %1$s %2$s parts of a place name.',
				FunctionsEdit::selectEditControl('SHOW_PEDIGREE_PLACES_SUFFIX',
					array(
						false => I18N::translateContext('Show the [first/last] [N] parts of a place name.', 'first'),
						true  => I18N::translateContext('Show the [first/last] [N] parts of a place name.', 'last'),
					), null, $WT_TREE->getPreference('SHOW_PEDIGREE_PLACES_SUFFIX')
				),
				FunctionsEdit::selectEditControl('SHOW_PEDIGREE_PLACES', $one_to_nine, null, $WT_TREE->getPreference('SHOW_PEDIGREE_PLACES'))
			); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Abbreviate place names” configuration setting */ I18N::translate('Place names are frequently too long to fit on charts, lists, etc. They can be abbreviated by showing just the first few parts of the name, such as <i>village, county</i>, or the last few part of it, such as <i>region, country</i>.'); ?>
			</p>
		</div>
	</fieldset>

	<h3><?php echo GedcomTag::getLabel('TEXT'); ?></h3>

	<!-- FORMAT_TEXT -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Format text and notes'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('FORMAT_TEXT', $formats, $WT_TREE->getPreference('FORMAT_TEXT'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Format text and notes” configuration setting */ I18N::translate('To ensure compatibility with other genealogy applications, notes, text, and transcripts should be recorded in simple, unformatted text. However, formatting is often desirable to aid presentation, comprehension, etc.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Format text and notes” configuration setting */ I18N::translate('Markdown is a simple system of formatting, used on websites such as Wikipedia. It uses unobtrusive punctuation characters to create headings and sub-headings, bold and italic text, lists, tables, etc.'); ?>
			</p>
		</div>
	</fieldset>

	<h3><?php echo I18N::translate('General'); ?></h3>

	<!-- SHOW_GEDCOM_RECORD -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Allow users to see raw GEDCOM records'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_GEDCOM_RECORD', $no_yes, $WT_TREE->getPreference('SHOW_GEDCOM_RECORD'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Allow users to see raw GEDCOM records” configuration setting */ I18N::translate('Setting this to <b>Yes</b> will place links on individuals, sources, and families to let users bring up another window containing the raw data taken right out of the GEDCOM file.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- HIDE_GEDCOM_ERRORS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('GEDCOM errors'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('HIDE_GEDCOM_ERRORS', $hide_show, $WT_TREE->getPreference('HIDE_GEDCOM_ERRORS'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “GEDCOM errors” configuration setting */ I18N::translate('Many genealogy programs create GEDCOM files with custom tags, and webtrees understands most of them. When unrecognized tags are found, this option lets you choose whether to ignore them or display a warning message.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_COUNTER -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Hit counters'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SHOW_COUNTER', $hide_show, $WT_TREE->getPreference('SHOW_COUNTER'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Hit counters” configuration setting */ I18N::translate('Show hit counters on Portal and Individual pages.'); ?>
			</p>
		</div>
	</fieldset>

	<h3><?php echo /* I18N: Options for editing */ I18N::translate('Edit options'); ?></h3>

	<h3><?php echo I18N::translate('Facts for individual records'); ?></h3>

	<!-- INDI_FACTS_ADD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="INDI_FACTS_ADD">
			<?php echo I18N::translate('All individual facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="INDI_FACTS_ADD"
					maxlength="255"
					name="INDI_FACTS_ADD"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('INDI_FACTS_ADD')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('INDI_FACTS_ADD', 'INDI');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “All individual facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to individuals. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique individual facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- INDI_FACTS_UNIQUE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="INDI_FACTS_UNIQUE">
			<?php echo I18N::translate('Unique individual facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="INDI_FACTS_UNIQUE"
					maxlength="255"
					name="INDI_FACTS_UNIQUE"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('INDI_FACTS_UNIQUE')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('INDI_FACTS_UNIQUE', 'INDI');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Unique individual facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to individuals. For example, if BIRT is in this list, users will not be able to add more than one BIRT record to an individual. Fact names that appear in this list must not also appear in the “All individual facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- QUICK_REQUIRED_FACTS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="QUICK_REQUIRED_FACTS">
			<?php echo I18N::translate('Facts for new individuals'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="QUICK_REQUIRED_FACTS"
					maxlength="255"
					name="QUICK_REQUIRED_FACTS"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('QUICK_REQUIRED_FACTS')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('QUICK_REQUIRED_FACTS', 'INDI');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Facts for new individuals” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new individual. For example, if BIRT is in the list, fields for birth date and birth place will be shown on the form.'); ?>
			</p>
		</div>
	</div>

	<!-- INDI_FACTS_QUICK -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="INDI_FACTS_QUICK">
			<?php echo I18N::translate('Quick individual facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="INDI_FACTS_QUICK"
					maxlength="255"
					name="INDI_FACTS_QUICK"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('INDI_FACTS_QUICK')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('INDI_FACTS_QUICK', 'INDI');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Quick individual facts” configuration setting */ I18N::translate('This is the short list of GEDCOM individual facts that appears next to the full list and can be added with a single click.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Facts for family records'); ?></h3>

	<!-- FAM_FACTS_ADD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="FAM_FACTS_ADD">
			<?php echo I18N::translate('All family facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="FAM_FACTS_ADD"
					maxlength="255"
					name="FAM_FACTS_ADD"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('FAM_FACTS_ADD')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('FAM_FACTS_ADD', 'FAM');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “All family facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to families. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique family facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- FAM_FACTS_UNIQUE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="FAM_FACTS_UNIQUE">
			<?php echo I18N::translate('Unique family facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="FAM_FACTS_UNIQUE"
					maxlength="255"
					name="FAM_FACTS_UNIQUE"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('FAM_FACTS_UNIQUE')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('FAM_FACTS_UNIQUE', 'FAM');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Unique family facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to families. For example, if MARR is in this list, users will not be able to add more than one MARR record to a family. Fact names that appear in this list must not also appear in the “All family facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- QUICK_REQUIRED_FAMFACTS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="QUICK_REQUIRED_FAMFACTS">
			<?php echo I18N::translate('Facts for new families'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="QUICK_REQUIRED_FAMFACTS"
					maxlength="255"
					name="QUICK_REQUIRED_FAMFACTS"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('QUICK_REQUIRED_FAMFACTS', 'FAM');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Facts for new families” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new family. For example, if MARR is in the list, then fields for marriage date and marriage place will be shown on the form.'); ?>
			</p>
		</div>
	</div>

	<!-- FAM_FACTS_QUICK -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="FAM_FACTS_QUICK">
			<?php echo I18N::translate('Quick family facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="FAM_FACTS_QUICK"
					maxlength="255"
					name="FAM_FACTS_QUICK"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('FAM_FACTS_QUICK')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('FAM_FACTS_QUICK', 'FAM');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Quick family facts” configuration setting */ I18N::translate('This is the short list of GEDCOM family facts that appears next to the full list and can be added with a single click.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Facts for source records'); ?></h3>

	<!-- SOUR_FACTS_ADD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SOUR_FACTS_ADD">
			<?php echo I18N::translate('All source facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="SOUR_FACTS_ADD"
					maxlength="255"
					name="SOUR_FACTS_ADD"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('SOUR_FACTS_ADD')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('SOUR_FACTS_ADD', 'SOUR');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “All source facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to sources. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique source facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- SOUR_FACTS_UNIQUE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SOUR_FACTS_UNIQUE">
			<?php echo I18N::translate('Unique source facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="SOUR_FACTS_UNIQUE"
					maxlength="255"
					name="SOUR_FACTS_UNIQUE"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('SOUR_FACTS_UNIQUE')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('SOUR_FACTS_UNIQUE', 'SOUR');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Unique source facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to sources. For example, if TITL is in this list, users will not be able to add more than one TITL record to a source. Fact names that appear in this list must not also appear in the “All source facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- SOUR_FACTS_QUICK -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SOUR_FACTS_QUICK">
			<?php echo I18N::translate('Quick source facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="SOUR_FACTS_QUICK"
					maxlength="255"
					name="SOUR_FACTS_QUICK"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('SOUR_FACTS_QUICK')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('SOUR_FACTS_QUICK', 'SOUR');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Quick source facts” configuration setting */ I18N::translate('This is the short list of GEDCOM source facts that appears next to the full list and can be added with a single click.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Facts for repository records'); ?></h3>

	<!-- REPO_FACTS_ADD -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="REPO_FACTS_ADD">
			<?php echo I18N::translate('All repository facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="REPO_FACTS_ADD"
					maxlength="255"
					name="REPO_FACTS_ADD"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('REPO_FACTS_ADD')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('REPO_FACTS_ADD', 'REPO');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “All repository facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to repositories. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique repository facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- REPO_FACTS_UNIQUE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="REPO_FACTS_UNIQUE">
			<?php echo I18N::translate('Unique repository facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="REPO_FACTS_UNIQUE"
					maxlength="255"
					name="REPO_FACTS_UNIQUE"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('REPO_FACTS_UNIQUE')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('REPO_FACTS_UNIQUE', 'REPO');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Unique repository facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to repositories. For example, if NAME is in this list, users will not be able to add more than one NAME record to a repository. Fact names that appear in this list must not also appear in the “All repository facts” list.'); ?>
			</p>
		</div>
	</div>

	<!-- REPO_FACTS_QUICK -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="REPO_FACTS_QUICK">
			<?php echo I18N::translate('Quick repository facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="REPO_FACTS_QUICK"
					maxlength="255"
					name="REPO_FACTS_QUICK"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('REPO_FACTS_QUICK')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('REPO_FACTS_QUICK', 'REPO');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Quick repository facts” configuration setting */ I18N::translate('This is the short list of GEDCOM repository facts that appears next to the full list and can be added with a single click.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Advanced fact settings'); ?></h3>

	<!-- ADVANCED_NAME_FACTS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="ADVANCED_NAME_FACTS">
			<?php echo I18N::translate('Advanced name facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="ADVANCED_NAME_FACTS"
					maxlength="255"
					name="ADVANCED_NAME_FACTS"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('ADVANCED_NAME_FACTS')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('ADVANCED_NAME_FACTS', 'NAME');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Advanced name facts” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown on the add/edit name form. If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic, or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store names in several different alphabets.'); ?>
			</p>
		</div>
	</div>

	<!-- ADVANCED_PLAC_FACTS -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="ADVANCED_PLAC_FACTS">
			<?php echo I18N::translate('Advanced place name facts'); ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<input
					class="form-control"
					dir="ltr"
					id="ADVANCED_PLAC_FACTS"
					maxlength="255"
					name="ADVANCED_PLAC_FACTS"
					type="text"
					value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('ADVANCED_PLAC_FACTS')); ?>"
					>
				<div class="input-group-btn">
					<a class="btn btn-default" onclick="return findFact('ADVANCED_PLAC_FACTS', 'PLAC');">
						<i class="fa fa-pencil"></i>
						<?php echo I18N::translate('edit'); ?>
					</a>
				</div>
			</div>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Advanced place name facts” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when you add or edit place names. If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic, or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store place names in several different alphabets.'); ?>
			</p>
		</div>
	</div>

	<h3><?php echo I18N::translate('Other settings'); ?></h3>

	<!-- SURNAME_TRADITION -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo I18N::translate('Surname tradition'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('SURNAME_TRADITION', SurnameTradition::allDescriptions(), $WT_TREE->getPreference('SURNAME_TRADITION'), 'class="radio" style="padding-left:20px;font-weight:normal;"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Surname tradition” configuration setting */ I18N::translate('When you add a new family member, a default surname can be provided. This surname will depend on the local tradition.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- FULL_SOURCES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Use full source citations'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('FULL_SOURCES', $no_yes, $WT_TREE->getPreference('FULL_SOURCES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Use full source citations” configuration setting */ I18N::translate('Source citations can include fields to record the quality of the data (primary, secondary, etc.) and the date the event was recorded in the source. If you don’t use these fields, you can disable them when creating new source citations.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- PREFER_LEVEL2_SOURCES -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Source type'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('PREFER_LEVEL2_SOURCES', $source_types, $WT_TREE->getPreference('PREFER_LEVEL2_SOURCES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Source type” configuration setting */ I18N::translate('When adding new close relatives, you can add source citations to the records (individual and family) or to the facts and events (birth, marriage, and death). This option controls whether records or facts will be selected by default.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- GEONAMES_ACCOUNT -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="GEONAMES_ACCOUNT">
			<?php echo I18N::translate('Use the GeoNames database for autocomplete on places'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				dir="ltr"
				id="GEONAMES_ACCOUNT"
				maxlength="255"
				name="GEONAMES_ACCOUNT"
				type="text"
				value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('GEONAMES_ACCOUNT')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Use GeoNames database for autocomplete on places” configuration setting */ I18N::translate('The website www.geonames.org provides a large database of place names. This can be searched when entering new places. To use this feature, you must register for a free account at www.geonames.org and provide the username.'); ?>
			</p>
		</div>
	</div>

	<!-- NO_UPDATE_CHAN -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ I18N::translate('Keep the existing “last change” information'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::radioButtons('NO_UPDATE_CHAN', $no_yes, $WT_TREE->getPreference('NO_UPDATE_CHAN'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Keep the existing ‘last change’ information” configuration setting */ I18N::translate('When a record is edited, the user and timestamp are recorded. Sometimes it is desirable to keep the existing “last change” information, for example when making minor corrections to someone else’s data. This option controls whether this feature is selected by default.'); ?>
			</p>
		</div>
	</fieldset>

	<?php endif; ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-check"></i>
				<?php echo I18N::translate('save'); ?>
			</button>
			<!-- Coming soon
			<div class="checkbox">
				<?php if (count(Tree::getAll()) > 1): ?>
				<label>
					<input type="checkbox" name="all_trees">
					<?php echo /* I18N: Label for checkbox */ I18N::translate('Apply these preferences to all family trees'); ?>
				</label>
				<?php endif; ?>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="new_trees">
					<?php echo /* I18N: Label for checkbox */ I18N::translate('Apply these preferences to new family trees'); ?>
				</label>
			</div>
		</div>
		-->
	</div>
</form>
