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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Module\CkeditorModule;
use PDO;

require 'includes/session.php';

$controller = new PageController;

$ctype      = Filter::get('ctype', 'user|gedcom', 'user');
$action     = Filter::get('action', 'delete', Filter::post('action', 'save'));
$news_id    = Filter::getInteger('news_id', 0, PHP_INT_MAX, Filter::postInteger('news_id'));

$news = Database::prepare("SELECT user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS date, subject, body FROM `##news` WHERE news_id = :news_id")->execute(['news_id' => $news_id])->fetchOneRow(PDO::FETCH_ASSOC);

if (empty($news)) {
	$news = [
		'user_id'   => $ctype === 'user' ? Auth::id() : null,
		'gedcom_id' => $ctype === 'gedcom' ? $controller->tree()->getTreeId() : null,
		'date'      => WT_TIMESTAMP,
		'subject'   => Filter::post('subject'),
		'body'      => Filter::post('body'),
	];
}
// If we can't edit this item, go back to the home/my page
if ($ctype === 'user' && $news['user_id'] != Auth::id() || $ctype === 'gedcom' && !Auth::isManager($controller->tree())) {
	header('Location: ' . WT_BASE_URL . 'index.php?ctype=' . $ctype . '&ged=' . $controller->tree()->getNameUrl());

	return;
}

switch ($action) {
case 'delete':
	Database::prepare("DELETE FROM `##news` WHERE news_id = :news_id")->execute(['news_id'   => $news_id,]);

	header('Location: ' . WT_BASE_URL . 'index.php?ctype=' . $ctype . '&ged=' . $controller->tree()->getNameUrl());

	return;

case 'save':
	if ($news_id > 0) {
		Database::prepare(
			"UPDATE `##news` SET subject = :subject, body = :body, updated = CURRENT_TIMESTAMP WHERE news_id = :news_id"
		)->execute([
			'subject' => Filter::post('subject'),
			'body'    => Filter::post('body'),
			'news_id' => $news_id,
		]);
	} else {
		Database::prepare(
			"INSERT INTO `##news` (user_id, gedcom_id, subject, body, updated) VALUES (NULLIF(:user_id, ''), NULLIF(:gedcom_id, '') ,:subject ,:body, CURRENT_TIMESTAMP)"
		)->execute([
			'user_id'   => $news['user_id'],
			'gedcom_id' => $news['gedcom_id'],
			'subject'   => $news['subject'],
			'body'      => $news['body'],
		]);
	}

	header('Location: ' . WT_BASE_URL . 'index.php?ctype=' . $ctype . '&ged=' . $controller->tree()->getNameUrl());

	return;
}

$controller->setPageTitle(I18N::translate('Add/edit a journal/news entry'));
$controller->pageHeader();

if (Module::getModuleByName('ckeditor')) {
	CkeditorModule::enableEditor($controller);
}

?>
<h2><?= $controller->getPageTitle() ?></h2>

<form method="post">
	<input type="hidden" name="ged" value="<?= $controller->tree()->getNameUrl() ?>">
	<input type="hidden" name="action" value="save">

	<table>
		<tr>
			<th>
				<label for="subject">
					<?= I18N::translate('Title') ?>
				</label>
			</th>
		<tr>
		<tr>
			<td>
				<input type="text" id="subject" name="subject" size="50" dir="auto" autofocus value="<?= Filter::escapeHtml($news['subject']) ?>">
			</td>
		</tr>
		<tr>
			<th>
				<label for="body">
					<?= I18N::translate('Content') ?>
				</label>
			</th>
		</tr>
		<tr>
			<td>
				<textarea id="body" name="body" class="html-edit" cols="80" rows="10" dir="auto"><?= Filter::escapeHtml($news['body']) ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="<?= I18N::translate('save') ?>">
			</td>
		</tr>
	</table>
</form>
