<?php

/**
 * Class WT_Query_Admin - generate statistics for admin.php
 *
 * @package   webtrees
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */
class WT_Query_Admin {
	/**
	 * Count the number of individuals that have been edited today
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countIndiChangesToday($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##individuals` ON (gedcom_id=i_file AND i_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of individuals that have been edited this week
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countIndiChangesWeek($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##individuals` ON (gedcom_id=i_file AND i_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of individuals that have been edited this month
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countIndiChangesMonth($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##individuals` ON (gedcom_id=i_file AND i_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of families that have been edited today
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countFamChangesToday($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##families` ON (gedcom_id=f_file AND f_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of families that have been edited this week
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countFamChangesWeek($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##families` ON (gedcom_id=f_file AND f_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of families that have been edited this month
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countFamChangesMonth($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##families` ON (gedcom_id=f_file AND f_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of sources that have been edited today
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countSourChangesToday($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##sources` ON (gedcom_id=s_file AND s_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of sources that have been edited this week
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countSourChangesWeek($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##sources` ON (gedcom_id=s_file AND s_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of sources that have been edited this month
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countSourChangesMonth($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##sources` ON (gedcom_id=s_file AND s_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of repositories that have been edited today
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countRepoChangesToday($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='REPO')".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of repositories that have been edited this week
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countRepoChangesWeek($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='REPO')".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of repositories that have been edited this month
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countRepoChangesMonth($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='REPO')".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of note objects that have been edited today
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countNoteChangesToday($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='NOTE')".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of note objects that have been edited this week
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countNoteChangesWeek($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='NOTE')".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of note objects that have been edited this month
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countNoteChangesMonth($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='NOTE')".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of media objects that have been edited today
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countObjeChangesToday($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##media` ON (gedcom_id=m_file AND m_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of media objects that have been edited this week
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countObjeChangesWeek($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##media` ON (gedcom_id=m_file AND m_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}

	/**
	 * Count the number of media objects that have been edited this month
	 *
	 * @param integer $ged_id
	 *
	 * @return string
	 */
	public static function countObjeChangesMonth($ged_id) {
		return WT_I18N::number(
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##media` ON (gedcom_id=m_file AND m_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne()
		);
	}
}
