<?php
// MySQL queries for the admin page
//
// webtrees: Web based Family History software
// Copyright (C) 2011 Greg Roach
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Query_Admin {
	public static function countIndiChangesToday($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##individuals` ON (gedcom_id=i_file AND i_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countIndiChangesWeek($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##individuals` ON (gedcom_id=i_file AND i_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countIndiChangesMonth($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##individuals` ON (gedcom_id=i_file AND i_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countFamChangesToday($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##families` ON (gedcom_id=f_file AND f_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countFamChangesWeek($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##families` ON (gedcom_id=f_file AND f_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countFamChangesMonth($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##families` ON (gedcom_id=f_file AND f_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countSourChangesToday($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##sources` ON (gedcom_id=s_file AND s_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countSourChangesWeek($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##sources` ON (gedcom_id=s_file AND s_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countSourChangesMonth($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##sources` ON (gedcom_id=s_file AND s_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countRepoChangesToday($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='REPO')".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countRepoChangesWeek($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='REPO')".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countRepoChangesMonth($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='REPO')".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countNoteChangesToday($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='NOTE')".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countNoteChangesWeek($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='NOTE')".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countNoteChangesMonth($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##other` ON (gedcom_id=o_file AND o_id=xref AND o_type='NOTE')".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countObjeChangesToday($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##media` ON (gedcom_id=m_file AND m_id=xref)".
				" WHERE status='accepted' AND DATE(change_time)= DATE(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countObjeChangesWeek($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##media` ON (gedcom_id=m_file AND m_id=xref)".
				" WHERE status='accepted' AND WEEK(change_time,2)= WEEK(NOW(),2) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}

	public static function countObjeChangesMonth($ged_id) {
		return
			WT_DB::prepare(
				"SELECT count(change_id) FROM `##change`".
				" JOIN `##media` ON (gedcom_id=m_file AND m_id=xref)".
				" WHERE status='accepted' AND MONTH(change_time)= MONTH(NOW()) AND gedcom_id=?"
			)
			->execute(array($ged_id))
			->fetchOne();
	}
}
