<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller->restrictAccess(Auth::isManager($WT_TREE));

$french_calendar_start    = new Date('22 SEP 1792');
$french_calendar_end      = new Date('31 DEC 1805');
$gregorian_calendar_start = new Date('15 OCT 1582');

$surname_list_styles = [
	'style1' => /* I18N: Layout option for lists of names */ I18N::translate('list'),
	'style2' => /* I18N: Layout option for lists of names */ I18N::translate('table'),
	'style3' => /* I18N: Layout option for lists of names */ I18N::translate('tag cloud'),
];

$layouts = [
	0 => /* I18N: page orientation */ I18N::translate('Portrait'),
	1 => /* I18N: page orientation */ I18N::translate('Landscape'),
];

$formats = [
	''         => /* I18N: None of the other options */ I18N::translate('none'),
	'markdown' => /* I18N: https://en.wikipedia.org/wiki/Markdown */ I18N::translate('markdown'),
];

$source_types = [
	0 => I18N::translate('none'),
	1 => I18N::translate('facts'),
	2 => I18N::translate('records'),
];

$THEME_OPTIONS = ['' => I18N::translate('<default theme>')] + Theme::themeNames();

$PRIVACY_CONSTANTS = [
	'none'         => I18N::translate('Show to visitors'),
	'privacy'      => I18N::translate('Show to members'),
	'confidential' => I18N::translate('Show to managers'),
	'hidden'       => I18N::translate('Hide from everyone'),
];

$tags = array_unique(array_merge(
	explode(',', $WT_TREE->getPreference('INDI_FACTS_ADD')), explode(',', $WT_TREE->getPreference('INDI_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('FAM_FACTS_ADD')), explode(',', $WT_TREE->getPreference('FAM_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('NOTE_FACTS_ADD')), explode(',', $WT_TREE->getPreference('NOTE_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('SOUR_FACTS_ADD')), explode(',', $WT_TREE->getPreference('SOUR_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('REPO_FACTS_ADD')), explode(',', $WT_TREE->getPreference('REPO_FACTS_UNIQUE')),
	['SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN']
));

$all_tags = [];
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
)->execute([$WT_TREE->getTreeId()])->fetchAll();

foreach ($resns as $resn) {
	$resn->record = GedcomRecord::getInstance($resn->xref, $WT_TREE);
	if ($resn->tag_type) {
		$resn->tag_label = GedcomTag::getLabel($resn->tag_type);
	} else {
		$resn->tag_label = '';
	}
}
usort($resns, function (\stdClass $x, \stdClass $y) {
	return I18N::strcasecmp($x->tag_label, $y->tag_label);
});

// We have two fields in one
$CALENDAR_FORMATS = explode('_and_', $WT_TREE->getPreference('CALENDAR_FORMAT') . '_and_');

// Split into separate fields
$relatives_events = explode(',', $WT_TREE->getPreference('SHOW_RELATIVES_EVENTS'));

switch (Filter::post('action')) {
	case 'general':
		if (!Filter::checkCsrf()) {
			break;
		}

		// Coming soon
		if (Filter::postBool('all_trees')) {
			FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.'), 'success');
		}
		if (Filter::postBool('new_trees')) {
			FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.'), 'success');
		}

		$WT_TREE->setPreference('ADVANCED_NAME_FACTS', implode(',', Filter::postArray('ADVANCED_NAME_FACTS')));
		$WT_TREE->setPreference('ADVANCED_PLAC_FACTS', implode(',', Filter::postArray('ADVANCED_PLAC_FACTS')));
		$WT_TREE->setPreference('ALLOW_THEME_DROPDOWN', Filter::postBool('ALLOW_THEME_DROPDOWN'));
		// For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
		// e.g. "gregorian_and_jewish"
		$WT_TREE->setPreference('CALENDAR_FORMAT', implode('_and_', array_unique([
			Filter::post('CALENDAR_FORMAT0', 'gregorian|julian|french|jewish|hijri|jalali', 'none'),
			Filter::post('CALENDAR_FORMAT1', 'gregorian|julian|french|jewish|hijri|jalali', 'none'),
		])));
		$WT_TREE->setPreference('CHART_BOX_TAGS', Filter::post('CHART_BOX_TAGS'));
		$WT_TREE->setPreference('CONTACT_USER_ID', Filter::post('CONTACT_USER_ID'));
		$WT_TREE->setPreference('DEFAULT_PEDIGREE_GENERATIONS', Filter::post('DEFAULT_PEDIGREE_GENERATIONS'));
		$WT_TREE->setPreference('EXPAND_NOTES', Filter::postBool('EXPAND_NOTES'));
		$WT_TREE->setPreference('EXPAND_SOURCES', Filter::postBool('EXPAND_SOURCES'));
		$WT_TREE->setPreference('FAM_FACTS_ADD', implode(',', Filter::postArray('FAM_FACTS_ADD')));
		$WT_TREE->setPreference('FAM_FACTS_QUICK', implode(',', Filter::postArray('FAM_FACTS_QUICK')));
		$WT_TREE->setPreference('FAM_FACTS_UNIQUE', implode(',', Filter::postArray('FAM_FACTS_UNIQUE')));
		$WT_TREE->setPreference('FULL_SOURCES', Filter::postBool('FULL_SOURCES'));
		$WT_TREE->setPreference('FORMAT_TEXT', Filter::post('FORMAT_TEXT'));
		$WT_TREE->setPreference('GEDCOM_MEDIA_PATH', Filter::post('GEDCOM_MEDIA_PATH'));
		$WT_TREE->setPreference('GENERATE_UIDS', Filter::postBool('GENERATE_UIDS'));
		$WT_TREE->setPreference('GEONAMES_ACCOUNT', Filter::post('GEONAMES_ACCOUNT'));
		$WT_TREE->setPreference('HIDE_GEDCOM_ERRORS', Filter::postBool('HIDE_GEDCOM_ERRORS'));
		$WT_TREE->setPreference('INDI_FACTS_ADD', implode(',', Filter::postArray('INDI_FACTS_ADD')));
		$WT_TREE->setPreference('INDI_FACTS_QUICK', implode(',', Filter::postArray('INDI_FACTS_QUICK')));
		$WT_TREE->setPreference('INDI_FACTS_UNIQUE', implode(',', Filter::postArray('INDI_FACTS_UNIQUE')));
		$WT_TREE->setPreference('LANGUAGE', Filter::post('LANGUAGE'));
		$WT_TREE->setPreference('MAX_DESCENDANCY_GENERATIONS', Filter::post('MAX_DESCENDANCY_GENERATIONS'));
		$WT_TREE->setPreference('MAX_PEDIGREE_GENERATIONS', Filter::post('MAX_PEDIGREE_GENERATIONS'));
		$WT_TREE->setPreference('MEDIA_UPLOAD', Filter::post('MEDIA_UPLOAD'));
		$WT_TREE->setPreference('META_DESCRIPTION', Filter::post('META_DESCRIPTION'));
		$WT_TREE->setPreference('META_TITLE', Filter::post('META_TITLE'));
		$WT_TREE->setPreference('NO_UPDATE_CHAN', Filter::postBool('NO_UPDATE_CHAN'));
		$WT_TREE->setPreference('PEDIGREE_LAYOUT', Filter::postBool('PEDIGREE_LAYOUT'));
		$WT_TREE->setPreference('PEDIGREE_ROOT_ID', Filter::post('PEDIGREE_ROOT_ID', WT_REGEX_XREF));
		$WT_TREE->setPreference('PEDIGREE_SHOW_GENDER', Filter::postBool('PEDIGREE_SHOW_GENDER'));
		$WT_TREE->setPreference('PREFER_LEVEL2_SOURCES', Filter::post('PREFER_LEVEL2_SOURCES'));
		$WT_TREE->setPreference('QUICK_REQUIRED_FACTS', implode(',', Filter::postArray('QUICK_REQUIRED_FACTS')));
		$WT_TREE->setPreference('QUICK_REQUIRED_FAMFACTS', implode(',', Filter::postArray('QUICK_REQUIRED_FAMFACTS')));
		$WT_TREE->setPreference('REPO_FACTS_ADD', implode(',', Filter::postArray('REPO_FACTS_ADD')));
		$WT_TREE->setPreference('REPO_FACTS_QUICK', implode(',', Filter::postArray('REPO_FACTS_QUICK')));
		$WT_TREE->setPreference('REPO_FACTS_UNIQUE', implode(',', Filter::postArray('REPO_FACTS_UNIQUE')));
		$WT_TREE->setPreference('SHOW_COUNTER', Filter::postBool('SHOW_COUNTER'));
		$WT_TREE->setPreference('SHOW_EST_LIST_DATES', Filter::postBool('SHOW_EST_LIST_DATES'));
		$WT_TREE->setPreference('SHOW_FACT_ICONS', Filter::postBool('SHOW_FACT_ICONS'));
		$WT_TREE->setPreference('SHOW_GEDCOM_RECORD', Filter::postBool('SHOW_GEDCOM_RECORD'));
		$WT_TREE->setPreference('SHOW_HIGHLIGHT_IMAGES', Filter::postBool('SHOW_HIGHLIGHT_IMAGES'));
		$WT_TREE->setPreference('SHOW_LAST_CHANGE', Filter::postBool('SHOW_LAST_CHANGE'));
		$WT_TREE->setPreference('SHOW_LDS_AT_GLANCE', Filter::postBool('SHOW_LDS_AT_GLANCE'));
		$WT_TREE->setPreference('SHOW_MEDIA_DOWNLOAD', Filter::post('SHOW_MEDIA_DOWNLOAD'));
		$WT_TREE->setPreference('SHOW_NO_WATERMARK', Filter::post('SHOW_NO_WATERMARK'));
		$WT_TREE->setPreference('SHOW_PARENTS_AGE', Filter::postBool('SHOW_PARENTS_AGE'));
		$WT_TREE->setPreference('SHOW_PEDIGREE_PLACES', Filter::post('SHOW_PEDIGREE_PLACES'));
		$WT_TREE->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', Filter::postBool('SHOW_PEDIGREE_PLACES_SUFFIX'));
		$WT_TREE->setPreference('SHOW_RELATIVES_EVENTS', implode(',', Filter::postArray('SHOW_RELATIVES_EVENTS')));
		$WT_TREE->setPreference('SOUR_FACTS_ADD', implode(',', Filter::postArray('SOUR_FACTS_ADD')));
		$WT_TREE->setPreference('SOUR_FACTS_QUICK', implode(',', Filter::postArray('SOUR_FACTS_QUICK')));
		$WT_TREE->setPreference('SOUR_FACTS_UNIQUE', implode(',', Filter::postArray('SOUR_FACTS_UNIQUE')));
		$WT_TREE->setPreference('SUBLIST_TRIGGER_I', Filter::post('SUBLIST_TRIGGER_I', WT_REGEX_INTEGER, 200));
		$WT_TREE->setPreference('SURNAME_LIST_STYLE', Filter::post('SURNAME_LIST_STYLE'));
		$WT_TREE->setPreference('SURNAME_TRADITION', Filter::post('SURNAME_TRADITION'));
		$WT_TREE->setPreference('THEME_DIR', Filter::post('THEME_DIR'));
		$WT_TREE->setPreference('USE_SILHOUETTE', Filter::postBool('USE_SILHOUETTE'));
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
				Database::prepare("UPDATE `##gedcom` SET gedcom_name = ? WHERE gedcom_id = ?")->execute([$gedcom, $WT_TREE->getTreeId()]);
				Database::prepare("UPDATE `##site_setting` SET setting_value = ? WHERE setting_name='DEFAULT_GEDCOM' AND setting_value = ?")->execute([$gedcom, $WT_TREE->getName()]);
			} catch (\Exception $ex) {
				DebugBar::addThrowable($ex);

				// Probably a duplicate name.
			}
		}

		FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($WT_TREE->getTitle())), 'success');
		header('Location: ' . route('admin-trees'));

		return;
}

switch (Filter::get('action')) {
case 'general':
	$controller->setPageTitle(I18N::translate('Preferences') . ' — ' . e($WT_TREE->getTitle()));
	break;
default:
	header('Location: ' . route('admin-control-panel'));

	return;
}

$controller->pageHeader();

echo Bootstrap4::breadcrumbs([
	route('admin-control-panel') => I18N::translate('Control panel'),
	route('admin-trees')         => I18N::translate('Manage family trees'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<form class="form-horizontal" method="post">
	<?= Filter::getCsrf() ?>
	<input type="hidden" name="ged" value="<?= e($WT_TREE->getName()) ?>">

	<?php if (Filter::get('action') === 'general'): ?>

	<input type="hidden" name="action" value="general">

	<h3><?= I18N::translate('General') ?></h3>

	<!-- TREE TITLE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="title">
			<?= I18N::translate('Family tree title') ?>
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
				value="<?= e($WT_TREE->getPreference('title')) ?>"
				>
		</div>
	</div>

	<!-- TREE URL / FILENAME -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="gedcom">
			<?= I18N::translate('URL') ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group" dir="ltr">
				<div class="input-group-append">
					<span class="input-group-text">
						<?= WT_BASE_URL ?>?ged=
					</span>
				</div>
				<input
					class="form-control"
					id="gedcom"
					maxlength="255"
					name="gedcom"
					required
					type="text"
					value="<?= e($WT_TREE->getName()) ?>"
					>
			</div>
			<p class="small text-muted">
				<?= /* I18N: help text for family tree / GEDCOM file names */ I18N::translate('Avoid spaces and punctuation. A family name might be a good choice.') ?>
			</p>
		</div>
	</div>

	<!-- LANGUAGE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="LANGUAGE">
			<?= /* I18N: A configuration setting */ I18N::translate('Language') ?>
		</label>
		<div class="col-sm-9">
			<select id="LANGUAGE" name="LANGUAGE" class="form-control">
				<?php foreach (I18N::activeLocales() as $active_locale): ?>
					<option value="<?= $active_locale->languageTag() ?>" <?= $WT_TREE->getPreference('LANGUAGE') === $active_locale->languageTag() ? 'selected' : '' ?>>
						<?= $active_locale->endonym() ?>
					</option>
				<?php endforeach ?>
			</select>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Language” configuration setting */ I18N::translate('If a visitor to the website has not selected a preferred language in their browser preferences, or they have selected an unsupported language, then this language will be used. Typically this applies to search engines.') ?>
			</p>
		</div>
	</div>

	<!-- PEDIGREE_ROOT_ID -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="PEDIGREE_ROOT_ID">
			<?= /* I18N: A configuration setting */ I18N::translate('Default individual') ?>
		</label>
		<div class="col-sm-9">
			<?= FunctionsEdit::formControlIndividual($WT_TREE, Individual::getInstance($WT_TREE->getPreference('PEDIGREE_ROOT_ID'), $WT_TREE), ['id' => 'PEDIGREE_ROOT_ID', 'name' => 'PEDIGREE_ROOT_ID']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Default individual” configuration setting */ I18N::translate('This individual will be selected by default when viewing charts and reports.') ?>
			</p>
		</div>
	</div>

	<!-- CALENDAR_FORMAT -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Calendar conversion') ?>
				<label class="sr-only" for="CALENDAR_FORMAT0">
					<?= /* I18N: A configuration setting */ I18N::translate('Calendar conversion') ?> 1
				</label>
				<label class="sr-only" for="CALENDAR_FORMAT1">
					<?= /* I18N: A configuration setting */ I18N::translate('Calendar conversion') ?> 2
				</label>
			</legend>
			<div class="col-sm-9">
				<div class=row">
					<div class="col-sm-6" style="padding-left: 0;">
						<?= Bootstrap4::select(FunctionsEdit::optionsCalendarConversions(), $CALENDAR_FORMATS[0], ['name' => 'CALENDAR_FORMAT0']) ?>
					</div>
					<div class="col-sm-6" style="padding-right: 0;">
						<?= Bootstrap4::select(FunctionsEdit::optionsCalendarConversions(), $CALENDAR_FORMATS[1], ['name' => 'CALENDAR_FORMAT1']) ?>
					</div>
				</div>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Calendar conversion” configuration setting */ I18N::translate('Different calendar systems are used in different parts of the world, and many other calendar systems have been used in the past. Where possible, you should enter dates using the calendar in which the event was originally recorded. You can then specify a conversion, to show these dates in a more familiar calendar. If you regularly use two calendars, you can specify two conversions and dates will be converted to both the selected calendars.') ?>
				</p>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Calendar conversion” configuration setting */ I18N::translate('Dates are only converted if they are valid for the calendar. For example, only dates between %1$s and %2$s will be converted to the French calendar and only dates after %3$s will be converted to the Gregorian calendar.', $french_calendar_start->display(false, null, false), $french_calendar_end->display(false, null, false), $gregorian_calendar_start->display(false, null, false)) ?>
				</p>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Calendar conversion” configuration setting */ I18N::translate('In some calendars, days start at midnight. In other calendars, days start at sunset. The conversion process does not take account of the time, so for any event that occurs between sunset and midnight, the conversion between these types of calendar will be one day out.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- GENERATE_UIDS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Add unique identifiers') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('GENERATE_UIDS', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('GENERATE_UIDS'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Add unique identifiers” configuration setting */ I18N::translate('Unique identifiers allow the same record to be found in different family trees and in different systems. They will be added whenever records are created or updated. If you do not want unique identifiers to be displayed, you can hide them using the privacy rules.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= I18N::translate('Contact information') ?></h3>

	<!-- WEBTREES_EMAIL -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="WEBTREES_EMAIL">
			<?= /* I18N: A configuration setting */ I18N::translate('webtrees reply address') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="WEBTREES_EMAIL"
				maxlength="255"
				name="WEBTREES_EMAIL"
				required
				type="email"
				value="<?= e($WT_TREE->getPreference('WEBTREES_EMAIL')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “webtrees reply address” configuration setting */ I18N::translate('Email address to be used in the “From:” field of emails that webtrees creates automatically.<br><br>webtrees can automatically create emails to notify administrators of changes that need to be reviewed. webtrees also sends notification emails to users who have requested an account.<br><br>Usually, the “From:” field of these automatically created emails is something like <i>From: webtrees-noreply@yoursite</i> to show that no response to the email is required. To guard against spam or other email abuse, some email systems require each message’s “From:” field to reflect a valid email account and will not accept messages that are apparently from account <i>webtrees-noreply</i>.') ?>
			</p>
		</div>
	</div>

	<!-- CONTACT_USER_ID -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="CONTACT_USER_ID">
			<?= /* I18N: A configuration setting */ I18N::translate('Genealogy contact') ?>
		</label>
		<div class="col-sm-9">
			<select id="CONTACT_USER_ID" name="CONTACT_USER_ID" class="form-control">
				<option value=""></option>
				<?php foreach (User::all() as $user): ?>
					<?php if (Auth::isMember($WT_TREE, $user)): ?>
						<option value="<?= $user->getUserId() ?>" <?= $WT_TREE->getPreference('CONTACT_USER_ID') === $user->getUserId() ? 'selected' : '' ?>>
							<?= e($user->getRealName()) ?> - <?= e($user->getUserName()) ?>
						</option>
					<?php endif ?>
				<?php endforeach ?>
			</select>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Genealogy contact” configuration setting */ I18N::translate('The individual to contact about the genealogy data on this website.') ?>
			</p>
		</div>
	</div>

	<!-- WEBMASTER_USER_ID -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="WEBMASTER_USER_ID">
			<?= /* I18N: A configuration setting */ I18N::translate('Technical help contact') ?>
		</label>
		<div class="col-sm-9">
			<select id="WEBMASTER_USER_ID" name="WEBMASTER_USER_ID" class="form-control">
				<option value=""></option>
				<?php foreach (User::all() as $user): ?>
					<?php if (Auth::isMember($WT_TREE, $user)): ?>
						<option value="<?= $user->getUserId() ?>" <?= $WT_TREE->getPreference('WEBMASTER_USER_ID') === $user->getUserId() ? 'selected' : '' ?>>
							<?= e($user->getRealName()) ?> - <?= e($user->getUserName()) ?>
						</option>
					<?php endif ?>
				<?php endforeach ?>
			</select>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Technical help contact” configuration setting */ I18N::translate('The individual to be contacted about technical questions or errors encountered on your website.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Website') ?></h3>

	<!-- META_TITLE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="META_TITLE">
			<?= /* I18N: A configuration setting */ I18N::translate('Add to TITLE header tag') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="META_TITLE"
				maxlength="255"
				name="META_TITLE"
				type="text"
				value="<?= e($WT_TREE->getPreference('META_TITLE')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Add to TITLE header tag” configuration setting */ I18N::translate('This text will be appended to each page title. It will be shown in the browser’s title bar, bookmarks, etc.') ?>
			</p>
		</div>
	</div>

	<!-- META_DESCRIPTION -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="META_DESCRIPTION">
			<?= /* I18N: A configuration setting */ I18N::translate('Description META tag') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="META_DESCRIPTION"
				maxlength="255"
				name="META_DESCRIPTION"
				type="text"
				value="<?= e($WT_TREE->getPreference('META_DESCRIPTION')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Description META tag” configuration setting */ I18N::translate('The value to place in the “meta description” tag in the HTML page header. Leave this field empty to use the name of the family tree.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('User preferences') ?></h3>
	<!-- ALLOW_THEME_DROPDOWN -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Theme menu') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('ALLOW_THEME_DROPDOWN', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('ALLOW_THEME_DROPDOWN'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Theme dropdown selector for theme changes” configuration setting */ I18N::translate('The theme menu will only be shown if the website preferences allow users to select their own theme.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- THEME_DIR -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="THEME_DIR">
			<?= /* I18N: A configuration setting */ I18N::translate('Default theme') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($THEME_OPTIONS, $WT_TREE->getPreference('THEME_DIR'), ['id' => 'THEME_DIR', 'name' => 'THEME_DIR']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Default theme” configuration setting */ I18N::translate('You can change the appearance of webtrees using “themes”. Each theme has a different style, layout, color scheme, etc.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Media folders') ?></h3>

	<!-- MEDIA_DIRECTORY -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="MEDIA_DIRECTORY">
			<?= /* I18N: A configuration setting */ I18N::translate('Media folder') ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group" dir="ltr">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<?= WT_DATA_DIR ?>
					</span>
				</div>
				<input
					class="form-control"
					id="MEDIA_DIRECTORY"
					maxlength="255"
					name="MEDIA_DIRECTORY"
					type="text"
					value="<?= e($WT_TREE->getPreference('MEDIA_DIRECTORY')) ?>"
				>
			</div>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Media folder” configuration setting */ I18N::translate('This folder will be used to store the media files for this family tree.') ?>
				<?= /* I18N: Help text for the “Media folder” configuration setting */ I18N::translate('If you select a different folder, you must also move any media files from the existing folder to the new one.') ?>
				<?= /* I18N: Help text for the “Media folder” configuration setting */ I18N::translate('If two family trees use the same media folder, then they will be able to share media files. If they use different media folders, then their media files will be kept separate.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Media files') ?></h3>

	<!-- MEDIA_UPLOAD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="MEDIA_UPLOAD">
			<?= /* I18N: A configuration setting */ I18N::translate('Who can upload new media files') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select([Auth::PRIV_USER => I18N::translate('Show to members'), Auth::PRIV_NONE => I18N::translate('Show to managers'), Auth::PRIV_HIDE => I18N::translate('Hide from everyone')], $WT_TREE->getPreference('MEDIA_UPLOAD'), ['id' => 'MEDIA_UPLOAD', 'name' => 'MEDIA_UPLOAD']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Who can upload new media files” configuration setting */ I18N::translate('If you are concerned that users might upload inappropriate images, you can restrict media uploads to managers only.') ?>
			</p>
		</div>
	</div>

	<!-- SHOW_MEDIA_DOWNLOAD -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Show a download link in the media viewer') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::select(FunctionsEdit::optionsAccessLevels(), $WT_TREE->getPreference('SHOW_MEDIA_DOWNLOAD'), ['id' => 'SHOW_MEDIA_DOWNLOAD', 'name' => 'SHOW_MEDIA_DOWNLOAD']) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Show a download link in the media viewer” configuration setting */ I18N::translate('This option will make it easier for users to download images.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= I18N::translate('Thumbnail images') ?></h3>

	<!-- SHOW_HIGHLIGHT_IMAGES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Thumbnail images') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_HIGHLIGHT_IMAGES', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_HIGHLIGHT_IMAGES'), true) ?>
				<p class="small text-muted">
					<?= I18N::translate('Show thumbnail images in charts and family groups.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- USE_SILHOUETTE -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Use silhouettes') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('USE_SILHOUETTE', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('USE_SILHOUETTE'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Use silhouettes” configuration setting */ I18N::translate('Use silhouette images when no highlighted image for that individual has been specified. The images used are specific to the gender of the individual in question.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= I18N::translate('Watermarks') ?></h3>

	<!-- SHOW_NO_WATERMARK -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="SHOW_NO_WATERMARK">
			<?= I18N::translate('Images without watermarks') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select(FunctionsEdit::optionsAccessLevels(), $WT_TREE->getPreference('SHOW_NO_WATERMARK'), ['id' => 'SHOW_NO_WATERMARK', 'name' => 'SHOW_NO_WATERMARK']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Images without watermarks” configuration setting */ I18N::translate('Watermarks are optional and normally shown just to visitors.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Lists') ?></h3>

	<!-- SURNAME_LIST_STYLE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="SURNAME_LIST_STYLE">
			<?= I18N::translate('Surname list style') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($surname_list_styles, $WT_TREE->getPreference('SURNAME_LIST_STYLE'), ['id' => 'SURNAME_LIST_STYLE', 'name' => 'SURNAME_LIST_STYLE']) ?>
			<p class="small text-muted">
			</p>
		</div>
	</div>

	<!-- SUBLIST_TRIGGER_I -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="SUBLIST_TRIGGER_I">
			<?= /* I18N: A configuration setting */ I18N::translate('Maximum number of surnames on individual list') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="SUBLIST_TRIGGER_I"
				maxlength="5"
				name="SUBLIST_TRIGGER_I"
				required
				type="text"
				value="<?= e($WT_TREE->getPreference('SUBLIST_TRIGGER_I')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Maximum number of surnames on individual list” configuration setting */ I18N::translate('Long lists of individuals with the same surname can be broken into smaller sub-lists according to the first letter of the individual’s given name.<br><br>This option determines when sub-listing of surnames will occur. To disable sub-listing completely, set this option to zero.') ?>
			</p>
		</div>
	</div>

	<!-- SHOW_EST_LIST_DATES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Estimated dates for birth and death') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_EST_LIST_DATES', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_EST_LIST_DATES'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Estimated dates for birth and death” configuration setting */ I18N::translate('This option controls whether or not to show estimated dates for birth and death instead of leaving blanks on individual lists and charts for individuals whose dates are not known.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_LAST_CHANGE -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('The date and time of the last update') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_LAST_CHANGE', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_LAST_CHANGE'), true) ?>
				<p class="small text-muted">
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= I18N::translate('Charts') ?></h3>

	<!-- PEDIGREE_LAYOUT -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Default pedigree chart layout') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('PEDIGREE_LAYOUT', $layouts, $WT_TREE->getPreference('PEDIGREE_LAYOUT'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Default pedigree chart layout” tree configuration setting */ I18N::translate('This option indicates whether the pedigree chart should be generated in landscape or portrait mode.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- DEFAULT_PEDIGREE_GENERATIONS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="DEFAULT_PEDIGREE_GENERATIONS">
			<?= /* I18N: A configuration setting */ I18N::translate('Default pedigree generations') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="DEFAULT_PEDIGREE_GENERATIONS"
				maxlength="5"
				name="DEFAULT_PEDIGREE_GENERATIONS"
				required
				type="text"
				value="<?= e($WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Default pedigree generations” configuration setting */ I18N::translate('Set the default number of generations to display on descendancy and pedigree charts.') ?>
			</p>
		</div>
	</div>

	<!-- MAX_PEDIGREE_GENERATIONS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="MAX_PEDIGREE_GENERATIONS">
			<?= /* I18N: A configuration setting */ I18N::translate('Maximum pedigree generations') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="MAX_PEDIGREE_GENERATIONS"
				maxlength="5"
				name="MAX_PEDIGREE_GENERATIONS"
				type="text"
				value="<?= e($WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Maximum pedigree generations” configuration setting */ I18N::translate('Set the maximum number of generations to display on pedigree charts.') ?>
			</p>
		</div>
	</div>

	<!-- MAX_DESCENDANCY_GENERATIONS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="MAX_DESCENDANCY_GENERATIONS">
			<?= /* I18N: A configuration setting */ I18N::translate('Maximum descendancy generations') ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="MAX_DESCENDANCY_GENERATIONS"
				maxlength="5"
				name="MAX_DESCENDANCY_GENERATIONS"
				type="text"
				value="<?= e($WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS')) ?>"
				>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Maximum descendancy generations” configuration setting */ I18N::translate('Set the maximum number of generations to display on descendancy charts.') ?>
			</p>
		</div>
	</div>

	<!-- PEDIGREE_SHOW_GENDER -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Gender icon on charts') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('PEDIGREE_SHOW_GENDER', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('PEDIGREE_SHOW_GENDER'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Gender icon on charts” configuration setting */ I18N::translate('This option controls whether or not to show the individual’s gender icon on charts.<br><br>Since the gender is also indicated by the color of the box, this option doesn’t conceal the gender. The option simply removes some duplicate information from the box.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_PARENTS_AGE -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Age of parents next to child’s birthdate') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_PARENTS_AGE', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_PARENTS_AGE'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Age of parents next to child’s birthdate” configuration setting */ I18N::translate('This option controls whether or not to show age of father and mother next to child’s birthdate on charts.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_LDS_AT_GLANCE -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('LDS ordinance codes in chart boxes') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_LDS_AT_GLANCE', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_LDS_AT_GLANCE'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “LDS ordinance codes in chart boxes” configuration setting. "B", "E", "S" and "P" should not be translated. */ I18N::translate('This is a summary of the <abbr title="The Church of Jesus Christ of Latter-day Saints">LDS</abbr> ordinances for the individual. “B” indicates an LDS baptism. “E” indicates an LDS endowment. “S” indicates an LDS spouse sealing. “P” indicates an LDS child-to-parent sealing.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- CHART_BOX_TAGS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="CHART_BOX_TAGS">
			<?= I18N::translate('Other facts to show in charts') ?>
		</label>
		<div class="col-sm-9">
			<div class="input-group">
				<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('INDI'), explode(',', $WT_TREE->getPreference('CHART_BOX_TAGS')), ['id' => 'CHART_BOX_TAGS', 'name' => 'CHART_BOX_TAGS[]', 'class' => 'select2']) ?>
			</div>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Other facts to show in charts” configuration setting */ I18N::translate('This should be a comma or space separated list of facts, in addition to birth and death, that you want to appear in chart boxes such as the pedigree chart. This list requires you to use fact tags as defined in the GEDCOM 5.5.1 standard. For example, if you wanted the occupation to show up in the box, you would add “OCCU” to this field.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Individual pages') ?></h3>

	<!-- SHOW_RELATIVES_EVENTS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Show the events of close relatives on the individual page') ?>
			</legend>
			<div class="col-sm-3">
				<div class="form-check">
					<label for="_BIRT_GCHI">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_BIRT_GCHI" value="_BIRT_GCHI" <?= in_array('_BIRT_GCHI', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_BIRT_GCHI') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_BIRT_CHIL">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_BIRT_CHIL" value="_BIRT_CHIL" <?= in_array('_BIRT_CHIL', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_BIRT_CHIL') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_BIRT_SIBL">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_BIRT_SIBL" value="_BIRT_SIBL" <?= in_array('_BIRT_SIBL', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_BIRT_SIBL') ?>
					</label>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-check">
					<label for="_MARR_GCHI">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_GCHI" value="_MARR_GCHI" <?= in_array('_MARR_GCHI', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_MARR_GCHI') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_MARR_CHIL">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_CHIL" value="_MARR_CHIL" <?= in_array('_MARR_CHIL', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_MARR_CHIL') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_MARR_SIBL">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_SIBL" value="_MARR_SIBL" <?= in_array('_MARR_SIBL', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_MARR_SIBL') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_MARR_PARE">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_MARR_PARE" value="_MARR_PARE" <?= in_array('_MARR_PARE', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_MARR_PARE') ?>
					</label>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-check">
					<label for="_DEAT_GCHI">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_GCHI" value="_DEAT_GCHI" <?= in_array('_DEAT_GCHI', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_DEAT_GCHI') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_DEAT_CHIL">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_CHIL" value="_DEAT_CHIL" <?= in_array('_DEAT_CHIL', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_DEAT_CHIL') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_DEAT_SIBL">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_SIBL" value="_DEAT_SIBL" <?= in_array('_DEAT_SIBL', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_DEAT_SIBL') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_DEAT_PARE">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_PARE" value="_DEAT_PARE" <?= in_array('_DEAT_PARE', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_DEAT_PARE') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_DEAT_SPOU">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_SPOU" value="_DEAT_SPOU" <?= in_array('_DEAT_SPOU', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_DEAT_SPOU') ?>
					</label>
				</div>
				<div class="form-check">
					<label for="_DEAT_GPAR">
						<input name="SHOW_RELATIVES_EVENTS[]" type="checkbox" id="_DEAT_GPAR" value="_DEAT_GPAR" <?= in_array('_DEAT_GPAR', $relatives_events) ? 'checked' : '' ?>>
						<?= GedcomTag::getLabel('_DEAT_GPAR') ?>
					</label>
				</div>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_FACT_ICONS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Fact icons') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_FACT_ICONS', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_FACT_ICONS'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Fact icons” configuration setting */ I18N::translate('Some themes can display icons on the “Facts and events” tab.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- EXPAND_NOTES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Automatically expand notes') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('EXPAND_NOTES', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('EXPAND_NOTES'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Automatically expand notes” configuration setting */
					I18N::translate('This option controls whether or not to automatically display content of a <i>Note</i> record on the Individual page.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- EXPAND_SOURCES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Automatically expand sources') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('EXPAND_SOURCES', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('EXPAND_SOURCES'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Automatically expand sources” configuration setting */
					I18N::translate('This option controls whether or not to automatically display content of a <i>Source</i> record on the Individual page.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= I18N::translate('Places') ?></h3>

	<!-- SHOW_PEDIGREE_PLACES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Abbreviate place names') ?>
				<label class="sr-only" for="SHOW_PEDIGREE_PLACES_SUFFIX">
					<?= /* I18N: A configuration setting */ I18N::translate('Abbreviate place names') ?>
					<?= I18N::translate('first') ?> / <?= I18N::translate('last') ?>
				</label>
				<label class="sr-only" for="SHOW_PEDIGREE_PLACES">
					<?= /* I18N: A configuration setting */ I18N::translate('Abbreviate place names') ?>
				</label>
			</legend>
			<div class="col-sm-9">
				<?= /* I18N: The placeholders are edit controls. Show the [first/last] [1/2/3/4/5] parts of a place name */ I18N::translate(
					'Show the %1$s %2$s parts of a place name.',
					Bootstrap4::select([
						'0' => I18N::translateContext('Show the [first/last] [N] parts of a place name.', 'first'),
						'1' => I18N::translateContext('Show the [first/last] [N] parts of a place name.', 'last'),
					], $WT_TREE->getPreference('SHOW_PEDIGREE_PLACES_SUFFIX', ['name' => 'SHOW_PEDIGREE_PLACES_SUFFIX'])),
					Bootstrap4::select(FunctionsEdit::numericOptions(range(1, 9)), $WT_TREE->getPreference('SHOW_PEDIGREE_PLACES'), ['name' => 'SHOW_PEDIGREE_PLACES'])
				) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Abbreviate place names” configuration setting */ I18N::translate('Place names are frequently too long to fit on charts, lists, etc. They can be abbreviated by showing just the first few parts of the name, such as <i>village, county</i>, or the last few part of it, such as <i>region, country</i>.') ?>
				</p>
			</div>
		</div>
	</fieldset>

		<!-- GEONAMES_ACCOUNT -->
		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="GEONAMES_ACCOUNT">
				<?= I18N::translate('Use the GeoNames database for autocomplete on places') ?>
			</label>
			<div class="col-sm-9">
				<input
					class="form-control"
					dir="ltr"
					id="GEONAMES_ACCOUNT"
					maxlength="255"
					name="GEONAMES_ACCOUNT"
					type="text"
					value="<?= e($WT_TREE->getPreference('GEONAMES_ACCOUNT')) ?>"
				>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Use GeoNames database for autocomplete on places” configuration setting */ I18N::translate('The website www.geonames.org provides a large database of place names. This can be searched when entering new places. To use this feature, you must register for a free account at www.geonames.org and provide the username.') ?>
				</p>
			</div>
		</div>

		<h3><?= I18N::translate('Text') ?></h3>

	<!-- FORMAT_TEXT -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Format text and notes') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('FORMAT_TEXT', $formats, $WT_TREE->getPreference('FORMAT_TEXT'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Format text and notes” configuration setting */ I18N::translate('To ensure compatibility with other genealogy applications, notes, text, and transcripts should be recorded in simple, unformatted text. However, formatting is often desirable to aid presentation, comprehension, etc.') ?>
				</p>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Format text and notes” configuration setting */ I18N::translate('Markdown is a simple system of formatting, used on websites such as Wikipedia. It uses unobtrusive punctuation characters to create headings and sub-headings, bold and italic text, lists, tables, etc.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= I18N::translate('General') ?></h3>

	<!-- SHOW_GEDCOM_RECORD -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Allow users to see raw GEDCOM records') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_GEDCOM_RECORD', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('SHOW_GEDCOM_RECORD'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Allow users to see raw GEDCOM records” configuration setting */ I18N::translate('Setting this to <b>Yes</b> will place links on individuals, sources, and families to let users bring up another window containing the raw data taken right out of the GEDCOM file.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- HIDE_GEDCOM_ERRORS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('GEDCOM errors') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('HIDE_GEDCOM_ERRORS', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('HIDE_GEDCOM_ERRORS'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “GEDCOM errors” configuration setting */ I18N::translate('Many genealogy programs create GEDCOM files with custom tags, and webtrees understands most of them. When unrecognized tags are found, this option lets you choose whether to ignore them or display a warning message.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_COUNTER -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Hit counters') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_COUNTER', FunctionsEdit::optionsHideShow(), $WT_TREE->getPreference('SHOW_COUNTER'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Hit counters” configuration setting */ I18N::translate('Some pages can display the number of times that they have been visited.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<h3><?= /* I18N: Options for editing */ I18N::translate('Edit preferences') ?></h3>

	<h3><?= I18N::translate('Facts for individual records') ?></h3>

	<!-- INDI_FACTS_ADD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="INDI_FACTS_ADD">
			<?= I18N::translate('All individual facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('INDI'), explode(',', $WT_TREE->getPreference('INDI_FACTS_ADD')), ['id' => 'INDI_FACTS_ADD', 'name' => 'INDI_FACTS_ADD[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “All individual facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to individuals. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique individual facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- INDI_FACTS_UNIQUE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="INDI_FACTS_UNIQUE">
			<?= I18N::translate('Unique individual facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('INDI'), explode(',', $WT_TREE->getPreference('INDI_FACTS_UNIQUE')), ['id' => 'INDI_FACTS_UNIQUE', 'name' => 'INDI_FACTS_UNIQUE[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Unique individual facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to individuals. For example, if BIRT is in this list, users will not be able to add more than one BIRT record to an individual. Fact names that appear in this list must not also appear in the “All individual facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- QUICK_REQUIRED_FACTS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="QUICK_REQUIRED_FACTS">
			<?= I18N::translate('Facts for new individuals') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('INDI'), explode(',', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS')), ['id' => 'QUICK_REQUIRED_FACTS', 'name' => 'QUICK_REQUIRED_FACTS[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Facts for new individuals” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new individual. For example, if BIRT is in the list, fields for birth date and birth place will be shown on the form.') ?>
			</p>
		</div>
	</div>

	<!-- INDI_FACTS_QUICK -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="INDI_FACTS_QUICK">
			<?= I18N::translate('Quick individual facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('INDI'), explode(',', $WT_TREE->getPreference('INDI_FACTS_QUICK')), ['id' => 'INDI_FACTS_QUICK', 'name' => 'INDI_FACTS_QUICK[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Quick individual facts” configuration setting */ I18N::translate('The most common individual facts and events are listed separately, so that they can be added more easily.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Facts for family records') ?></h3>

	<!-- FAM_FACTS_ADD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="FAM_FACTS_ADD">
			<?= I18N::translate('All family facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('FAM'), explode(',', $WT_TREE->getPreference('FAM_FACTS_ADD')), ['id' => 'FAM_FACTS_ADD', 'name' => 'FAM_FACTS_ADD[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “All family facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to families. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique family facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- FAM_FACTS_UNIQUE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="FAM_FACTS_UNIQUE">
			<?= I18N::translate('Unique family facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('FAM'), explode(',', $WT_TREE->getPreference('FAM_FACTS_UNIQUE')), ['id' => 'FAM_FACTS_UNIQUE', 'name' => 'FAM_FACTS_UNIQUE[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Unique family facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to families. For example, if MARR is in this list, users will not be able to add more than one MARR record to a family. Fact names that appear in this list must not also appear in the “All family facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- QUICK_REQUIRED_FAMFACTS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="QUICK_REQUIRED_FAMFACTS">
			<?= I18N::translate('Facts for new families') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('FAM'), explode(',', $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS')), ['id' => 'QUICK_REQUIRED_FAMFACTS', 'name' => 'QUICK_REQUIRED_FAMFACTS[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Facts for new families” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new family. For example, if MARR is in the list, then fields for marriage date and marriage place will be shown on the form.') ?>
			</p>
		</div>
	</div>

	<!-- FAM_FACTS_QUICK -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="FAM_FACTS_QUICK">
			<?= I18N::translate('Quick family facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('FAM'), explode(',', $WT_TREE->getPreference('FAM_FACTS_QUICK')), ['id' => 'FAM_FACTS_QUICK', 'name' => 'FAM_FACTS_QUICK[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Quick family facts” configuration setting */ I18N::translate('The most common family facts and events are listed separately, so that they can be added more easily.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Facts for source records') ?></h3>

	<!-- SOUR_FACTS_ADD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="SOUR_FACTS_ADD">
			<?= I18N::translate('All source facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('SOUR'), explode(',', $WT_TREE->getPreference('SOUR_FACTS_ADD')), ['id' => 'SOUR_FACTS_ADD', 'name' => 'SOUR_FACTS_ADD[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “All source facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to sources. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique source facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- SOUR_FACTS_UNIQUE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="SOUR_FACTS_UNIQUE">
			<?= I18N::translate('Unique source facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('SOUR'), explode(',', $WT_TREE->getPreference('SOUR_FACTS_UNIQUE')), ['id' => 'SOUR_FACTS_UNIQUE', 'name' => 'SOUR_FACTS_UNIQUE[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Unique source facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to sources. For example, if TITL is in this list, users will not be able to add more than one TITL record to a source. Fact names that appear in this list must not also appear in the “All source facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- SOUR_FACTS_QUICK -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="SOUR_FACTS_QUICK">
			<?= I18N::translate('Quick source facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('SOUR'), explode(',', $WT_TREE->getPreference('SOUR_FACTS_QUICK')), ['id' => 'SOUR_FACTS_QUICK', 'name' => 'SOUR_FACTS_QUICK[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Quick source facts” configuration setting */ I18N::translate('The most common source facts are listed separately, so that they can be added more easily.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Facts for repository records') ?></h3>

	<!-- REPO_FACTS_ADD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="REPO_FACTS_ADD">
			<?= I18N::translate('All repository facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('REPO'), explode(',', $WT_TREE->getPreference('REPO_FACTS_ADD')), ['id' => 'REPO_FACTS_ADD', 'name' => 'REPO_FACTS_ADD[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “All repository facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can add to repositories. You can modify this list by removing or adding fact names, even custom ones, as necessary. Fact names that appear in this list must not also appear in the “Unique repository facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- REPO_FACTS_UNIQUE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="REPO_FACTS_UNIQUE">
			<?= I18N::translate('Unique repository facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('REPO'), explode(',', $WT_TREE->getPreference('REPO_FACTS_UNIQUE')), ['id' => 'REPO_FACTS_UNIQUE', 'name' => 'REPO_FACTS_UNIQUE[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Unique repository facts” configuration setting */ I18N::translate('This is the list of GEDCOM facts that your users can only add once to repositories. For example, if NAME is in this list, users will not be able to add more than one NAME record to a repository. Fact names that appear in this list must not also appear in the “All repository facts” list.') ?>
			</p>
		</div>
	</div>

	<!-- REPO_FACTS_QUICK -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="REPO_FACTS_QUICK">
			<?= I18N::translate('Quick repository facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('REPO'), explode(',', $WT_TREE->getPreference('REPO_FACTS_QUICK')), ['id' => 'REPO_FACTS_QUICK', 'name' => 'REPO_FACTS_QUICK[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Quick repository facts” configuration setting */ I18N::translate('The most common repository facts are listed separately, so that they can be added more easily.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Advanced fact preferences') ?></h3>

	<!-- ADVANCED_NAME_FACTS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="ADVANCED_NAME_FACTS">
			<?= I18N::translate('Advanced name facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('NAME'), explode(',', $WT_TREE->getPreference('ADVANCED_NAME_FACTS')), ['id' => 'ADVANCED_NAME_FACTS', 'name' => 'ADVANCED_NAME_FACTS[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Advanced name facts” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown on the add/edit name form. If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic, or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store names in several different alphabets.') ?>
			</p>
		</div>
	</div>

	<!-- ADVANCED_PLAC_FACTS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="ADVANCED_PLAC_FACTS">
			<?= I18N::translate('Advanced place name facts') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::multiSelect(GedcomTag::getPicklistFacts('PLAC'), explode(',', $WT_TREE->getPreference('ADVANCED_PLAC_FACTS')), ['id' => 'ADVANCED_PLAC_FACTS', 'name' => 'ADVANCED_PLAC_FACTS[]', 'class' => 'select2']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Advanced place name facts” configuration setting */ I18N::translate('This is a comma separated list of GEDCOM fact tags that will be shown when you add or edit place names. If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic, or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store place names in several different alphabets.') ?>
			</p>
		</div>
	</div>

	<h3><?= I18N::translate('Other preferences') ?></h3>

	<!-- SURNAME_TRADITION -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Surname tradition') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SURNAME_TRADITION', SurnameTradition::allDescriptions(), $WT_TREE->getPreference('SURNAME_TRADITION'), false) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Surname tradition” configuration setting */ I18N::translate('When you add a new family member, a default surname can be provided. This surname will depend on the local tradition.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- FULL_SOURCES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Use full source citations') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('FULL_SOURCES', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('FULL_SOURCES'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Use full source citations” configuration setting */ I18N::translate('Source citations can include fields to record the quality of the data (primary, secondary, etc.) and the date the event was recorded in the source. If you don’t use these fields, you can disable them when creating new source citations.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- PREFER_LEVEL2_SOURCES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Source type') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('PREFER_LEVEL2_SOURCES', $source_types, $WT_TREE->getPreference('PREFER_LEVEL2_SOURCES'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Source type” configuration setting */ I18N::translate('When adding new close relatives, you can add source citations to the records (individual and family) or to the facts and events (birth, marriage, and death). This option controls whether records or facts will be selected by default.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- NO_UPDATE_CHAN -->
	<fieldset class="form-group">
			<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Keep the existing “last change” information') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('NO_UPDATE_CHAN', FunctionsEdit::optionsNoYes(), $WT_TREE->getPreference('NO_UPDATE_CHAN'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Keep the existing ‘last change’ information” configuration setting */ I18N::translate('When a record is edited, the user and timestamp are recorded. Sometimes it is desirable to keep the existing “last change” information, for example when making minor corrections to someone else’s data. This option controls whether this feature is selected by default.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<?php endif ?>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="admin_trees_manage.php">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= I18N::translate('cancel') ?>
			</a>
			<!-- Coming soon
			<div class="form-check">
				<?php if (count(Tree::getAll()) > 1): ?>
				<label>
					<input type="checkbox" name="all_trees">
					<?= /* I18N: Label for checkbox */ I18N::translate('Apply these preferences to all family trees') ?>
				</label>
				<?php endif ?>
			</div>
			<div class="form-check">
				<label>
					<input type="checkbox" name="new_trees">
					<?= /* I18N: Label for checkbox */ I18N::translate('Apply these preferences to new family trees') ?>
				</label>
			</div>
		</div>
		-->
	</div>
</form>
