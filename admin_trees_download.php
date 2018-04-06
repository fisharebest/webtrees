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
use Fisharebest\Webtrees\Functions\FunctionsExport;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

require 'includes/session.php';

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate($controller->tree()->getTitleHtml()) . ' — ' . I18N::translate('Export a GEDCOM file'))
	->restrictAccess(Auth::isManager($controller->tree()));

// Validate user parameters
$action           = Filter::get('action', 'download');
$convert          = Filter::get('convert');
$zip              = Filter::get('zip');
$media            = Filter::get('media');
$media_path       = Filter::get('media-path');
$privatize_export = Filter::get('privatize_export', 'none|visitor|user|gedadmin');

if ($action === 'download') {
	switch ($privatize_export) {
		default:
		case 'gedadmin':
			$access_level = Auth::PRIV_NONE;
			break;
		case 'user':
			$access_level = Auth::PRIV_USER;
			break;
		case 'visitor':
			$access_level = Auth::PRIV_PRIVATE;
			break;
		case 'none':
			$access_level = Auth::PRIV_HIDE;
			break;
	}

	$exportOptions = [
		'privatize' => $privatize_export,
		'toANSI'    => $convert === 'on' ? 'yes' : 'no',
		'path'      => $media_path,
	];

	// What to call the downloaded file
	$download_filename = $controller->tree()->getName();
	if (strtolower(substr($download_filename, -4, 4)) != '.ged') {
		$download_filename .= '.ged';
	}

	if ($zip === 'on' || $media === 'on') {
		// Export the GEDCOM to an in-memory stream.
		$tmp_stream = tmpfile();
		FunctionsExport::exportGedcom($controller->tree(), $tmp_stream, $exportOptions);
		rewind($tmp_stream);

		// Create a new/empty .ZIP file
		$temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
		$zip_filesystem = new Filesystem(new ZipArchiveAdapter($temp_zip_file));
		$zip_filesystem->writeStream($download_filename, $tmp_stream);

		if ($media === 'on') {
			$rows = Database::prepare(
				"SELECT m_id, m_gedcom FROM `##media` WHERE m_file = :tree_id"
			)->execute([
				'tree_id' => $controller->tree()->getTreeId(),
			])->fetchAll();

			$path = $controller->tree()->getPreference('MEDIA_DIRECTORY');
			foreach ($rows as $row) {
				$record = Media::getInstance($row->m_id, $controller->tree(), $row->m_gedcom);
				if ($record->canShow()) {
					foreach ($record->mediaFiles() as $media_file) {
						if (file_exists($media_file->getServerFilename())) {
							$fp = fopen($media_file->getServerFilename(), 'r');
							$zip_filesystem->writeStream($path . $media_file->filename(), $fp);
							fclose($fp);
						}
					}
				}
			}
		}

		// The ZipArchiveAdapter may or may not close the stream.
		if (is_resource($tmp_stream)) {
			fclose($tmp_stream);
		}

		// Need to force-close the filesystem
		$zip_filesystem = null;

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $download_filename . '.zip"');
		header('Content-length: ' . filesize($temp_zip_file));
		readfile($temp_zip_file);
		unlink($temp_zip_file);
	} else {
		header('Content-Type: text/plain; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"');
		// Stream the GEDCOM file straight to the browser.
		$stream = fopen('php://output', 'w');
		FunctionsExport::exportGedcom($controller->tree(), $stream, $exportOptions);
		fclose($stream);
	}

	return;
}

$controller->pageHeader();

echo Bootstrap4::breadcrumbs([
	route('admin-control-panel')              => I18N::translate('Control panel'),
	'admin_trees_manage.php' => I18N::translate('Manage family trees'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<form class="form form-horizontal" method="post" action="admin_trees_export.php">
	<?= Filter::getCsrf() ?>
	<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">

	<div class="row form-group">
		<label for="submit-export" class="col-sm-3 col-form-label">
			<?= I18N::translate('A file on the server') ?>
		</label>
		<div class="col-sm-9">
			<button id="submit-export" type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>

<hr>

<form class="form form-horizontal">
	<input type="hidden" name="action" value="download">
	<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">

	<!-- DOWNLOAD OPTIONS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Export preferences') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="zip">
						<?= I18N::translate('Compress the GEDCOM file') ?>
					</label>
				</div>
				<p class="small muted">
					<?= I18N::translate('To reduce the size of the download, you can compress the data into a .ZIP file. You will need to uncompress the .ZIP file before you can use it.') ?>
				</p>

				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="media">
						<?= I18N::translate('Include media (automatically zips files)') ?>
					</label>
				</div>

				<?php if ($controller->tree()->getPreference('GEDCOM_MEDIA_PATH')): ?>
					<label>
						<input type="checkbox" name="media-path" value="<?= e($controller->tree()->getPreference('GEDCOM_MEDIA_PATH')) ?>">
						<?= /* I18N: A media path (e.g. C:\aaa\bbb\ccc\) in a GEDCOM file */ I18N::translate('Add the GEDCOM media path to filenames') ?>
					</label>
					<p>
						<?= /* I18N: %s is the name of a folder. */ I18N::translate('Media filenames will be prefixed by %s.', '<code dir="ltr">' . e($controller->tree()->getPreference('GEDCOM_MEDIA_PATH')) . '</code>') ?>
					</p>
				<?php endif ?>

				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="convert">
						<?= I18N::translate('Convert from UTF-8 to ISO-8859-1') ?>
					</label>
				</div>
				<p class="small muted">
					<?= I18N::translate('webtrees uses UTF-8 encoding for accented letters, special characters and non-Latin scripts. If you want to use this GEDCOM file with genealogy software that does not support UTF-8, then you can create it using ISO-8859-1 encoding.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- PRIVACY OPTIONS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Apply privacy settings') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="none" checked>
						<?= I18N::translate('None') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="gedadmin">
						<?= I18N::translate('Manager') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="user">
						<?= I18N::translate('Member') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="visitor">
						<?= I18N::translate('Visitor') ?>
					</label>
				</div>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<label for="submit-export" class="col-sm-3 col-form-label">
			<?= I18N::translate('A file on your computer') ?>
		</label>
		<div class="col-sm-9">
			<button id="submit-export" type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>
