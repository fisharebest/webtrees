<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

define('WT_SCRIPT_NAME', 'admin_trees_merge.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Merge family trees'))
	->pageHeader();

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<?php

$tree1_id = Filter::post('tree1_id');
$tree2_id = Filter::post('tree2_id');

if ($tree1_id && $tree2_id != $tree1_id) {
	// Every XREF used by both trees
	$xrefs = Database::prepare(
		"SELECT xref, type FROM (" .
		" SELECT i_id AS xref, 'INDI' AS type FROM `##individuals` WHERE i_file = ?" .
		"  UNION " .
		" SELECT f_id AS xref, 'FAM' AS type FROM `##families` WHERE f_file = ?" .
		"  UNION " .
		" SELECT s_id AS xref, 'SOUR' AS type FROM `##sources` WHERE s_file = ?" .
		"  UNION " .
		" SELECT m_id AS xref, 'OBJE' AS type FROM `##media` WHERE m_file = ?" .
		"  UNION " .
		" SELECT o_id AS xref, o_type AS type FROM `##other` WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')" .
		") AS this_tree JOIN (" .
		" SELECT xref FROM `##change` WHERE gedcom_id = ?" .
		"  UNION " .
		" SELECT i_id AS xref FROM `##individuals` WHERE i_file = ?" .
		"  UNION " .
		" SELECT f_id AS xref FROM `##families` WHERE f_file = ?" .
		"  UNION " .
		" SELECT s_id AS xref FROM `##sources` WHERE s_file = ?" .
		"  UNION " .
		" SELECT m_id AS xref FROM `##media` WHERE m_file = ?" .
		"  UNION " .
		" SELECT o_id AS xref FROM `##other` WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')" .
		") AS other_trees USING (xref)"
	)->execute(array(
		$tree1_id, $tree1_id, $tree1_id, $tree1_id, $tree1_id,
		$tree2_id, $tree2_id, $tree2_id, $tree2_id, $tree2_id, $tree2_id,
	))->fetchAssoc();

	if ($xrefs) {
		$tree1 = Tree::findById($tree1_id);
		$tree2 = Tree::findById($tree2_id);
		echo
			'<p>', I18N::translate('In a family tree, each record has an internal reference number (called an “XREF”) such as “F123” or “R14”.'), '</p>',
			'<p>',
			I18N::plural(
				/* I18N: An XREF is the identification number used in GEDCOM files. */
				'The two family trees have %1$s record which uses the same “XREF”.',
				'The two family trees have %1$s records which use the same “XREF”.',
				count($xrefs), count($xrefs)
			),
			'</p>',
			'<p>',
			I18N::translate('You must renumber the records in one of the trees before you can merge them.'),
			'</p>',
			'<p>',
			'<a class="current" href="admin_trees_renumber.php?ged=', $tree1->getNameUrl(), '">',
			I18N::translate('Renumber family tree'), ' — ', $tree1->getTitleHtml(),
			'</a>',
			'</p>',
			'<p>',
			'<a class="current" href="admin_trees_renumber.php?ged=', $tree2->getNameUrl(), '">',
			I18N::translate('Renumber family tree'), ' — ', $tree2->getTitleHtml(),
			'</a>',
			'</p>';
	} else {
		Database::beginTransaction();
		Database::exec(
			"LOCK TABLE" .
			" `##individuals` WRITE," .
			" `##individuals` AS individuals2 READ," .
			" `##families` WRITE," .
			" `##families` AS families2 READ," .
			" `##sources` WRITE," .
			" `##sources` AS sources2 READ," .
			" `##media` WRITE," .
			" `##media` AS media2 READ," .
			" `##other` WRITE," .
			" `##other` AS other2 READ," .
			" `##name` WRITE," .
			" `##name` AS name2 READ," .
			" `##placelinks` WRITE," .
			" `##placelinks` AS placelinks2 READ," .
			" `##change` WRITE," .
			" `##change` AS change2 READ," .
			" `##dates` WRITE," .
			" `##dates` AS dates2 READ," .
			" `##default_resn` WRITE," .
			" `##default_resn` AS default_resn2 READ," .
			" `##hit_counter` WRITE," .
			" `##hit_counter` AS hit_counter2 READ," .
			" `##link` WRITE," .
			" `##link` AS link2 READ"
		);
		Database::prepare(
			"INSERT INTO `##individuals` (i_id, i_file, i_rin, i_sex, i_gedcom)" .
			" SELECT i_id, ?, i_rin, i_sex, i_gedcom FROM `##individuals` AS individuals2 WHERE i_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##families` (f_id, f_file, f_husb, f_wife, f_gedcom, f_numchil)" .
			" SELECT f_id, ?, f_husb, f_wife, f_gedcom, f_numchil FROM `##families` AS families2 WHERE f_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##sources` (s_id, s_file, s_name, s_gedcom)" .
			" SELECT s_id, ?, s_name, s_gedcom FROM `##sources` AS sources2 WHERE s_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##media` (m_id, m_ext, m_type, m_titl, m_filename, m_file, m_gedcom)" .
			" SELECT m_id, m_ext, m_type, m_titl, m_filename, ?, m_gedcom FROM `##media` AS media2 WHERE m_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##other` (o_id, o_file, o_type, o_gedcom)" .
			" SELECT o_id, ?, o_type, o_gedcom FROM `##other` AS other2 WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##name` (n_file, n_id, n_num, n_type, n_sort, n_full, n_surname, n_surn, n_givn, n_soundex_givn_std, n_soundex_surn_std, n_soundex_givn_dm, n_soundex_surn_dm)" .
			" SELECT ?, n_id, n_num, n_type, n_sort, n_full, n_surname, n_surn, n_givn, n_soundex_givn_std, n_soundex_surn_std, n_soundex_givn_dm, n_soundex_surn_dm FROM `##name` AS name2 WHERE n_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##placelinks` (pl_p_id, pl_gid, pl_file)" .
			" SELECT pl_p_id, pl_gid, ? FROM `##placelinks` AS placelinks2 WHERE pl_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##dates` (d_day, d_month, d_mon, d_year, d_julianday1, d_julianday2, d_fact, d_gid, d_file, d_type)" .
			" SELECT d_day, d_month, d_mon, d_year, d_julianday1, d_julianday2, d_fact, d_gid, ?, d_type FROM `##dates` AS dates2 WHERE d_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##default_resn` (gedcom_id, xref, tag_type, resn)" .
			" SELECT ?, xref, tag_type, resn FROM `##default_resn` AS default_resn2 WHERE gedcom_id = ?"
		)->execute(array($tree2_id, $tree1_id));
		Database::prepare(
			"INSERT INTO `##link` (l_file, l_from, l_type, l_to)" .
			" SELECT ?, l_from, l_type, l_to FROM `##link` AS link2 WHERE l_file = ?"
		)->execute(array($tree2_id, $tree1_id));
		// This table may contain old (deleted) references, which could clash.  IGNORE these.
		Database::prepare(
			"INSERT IGNORE INTO `##change` (change_time, status, gedcom_id, xref, old_gedcom, new_gedcom, user_id)" .
			" SELECT change_time, status, ?, xref, old_gedcom, new_gedcom, user_id FROM `##change` AS change2 WHERE gedcom_id = ?"
		)->execute(array($tree2_id, $tree1_id));
		// This table may contain old (deleted) references, which could clash.  IGNORE these.
		Database::prepare(
			"INSERT IGNORE INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count)" .
			" SELECT ?, page_name, page_parameter, page_count FROM `##hit_counter` AS hit_counter2 WHERE gedcom_id = ? AND page_name <> 'index.php'"
		)->execute(array($tree2_id, $tree1_id));
		Database::exec("UNLOCK TABLES");
		Database::commit();
		echo '<p>', I18N::translate('The family trees have been merged successfully.'), '</p>';
	}
} else {
	echo '<form method="post">';
	echo '<input type="hidden" name="go" value="1">';
	echo '<p>', I18N::translate(/* I18N:  Copy all the records from [family tree 1] into [family tree 2] */
		'Copy all the records from %1$s into %2$s.',
		FunctionsEdit::selectEditControl('tree1_id', Tree::getIdList(), '', null),
		FunctionsEdit::selectEditControl('tree2_id', Tree::getIdList(), '', null)
	),
	'</p>';

	echo '<button type="submit" class="btn btn-primary">';
	echo '<i class="fa fa-check"></i> ', /* I18N: Button label */ I18N::translate('continue');
	echo '</button>';
	echo '</form>';
}
