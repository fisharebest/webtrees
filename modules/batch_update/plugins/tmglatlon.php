<?php
/**
 * Batch Update plugin for phpGedView - convert TMG lat/lon data
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008 Greg Roach.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Module
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class tmglatlon_bu_plugin extends base_plugin {
	static function getName() {
		return i18n::translate('Fix TMG latlon data');
	}

	static function getDescription() {
		return i18n::translate('Converts The Master Genealogist\'s proprietary lat/lon format to the GEDCOM 5.5.1 standard that PGV can read.  Note: changes are not highlighted in the final output shown below.');
	}
	
	// the default getActionPreview crashes on certain records, override the preview to just show the "after" results instead of the changes
	// try removing this when bug 2177311 is fixed
	function getActionPreview($xref, $gedrec) {
		return '<pre>'.$this->updateRecord($xref, $gedrec).'</pre>';
	}

	function getRecordTypesToUpdate() {
		return array('INDI', 'FAM');
	}

	static function doesRecordNeedUpdate($xref, $gedrec) {
		return preg_match("/^2 PLAC.*, \d\d\d\d\d\d[NS]\d\d\d\d\d\d\d[EW]/m", $gedrec);
	}

	// converts The Master Genealogist's (TMG) proprietary lat/lon format :
	//    2 PLAC St Columba's Church, 2340 W. Lehigh Avenue, Philadelphia, Philadelphia Co, Pennsylvania, USA, 395945N0751013W
	// to the GEDCOM 5.5.1 standard that PGV can read:
	//    2 PLAC St Columba's Church, 2340 W. Lehigh Avenue, Philadelphia, Philadelphia Co, Pennsylvania, USA\n3 MAP\n4 LATI N39.9958\n4 LONG W75.1703
	// see patch 1527087 for more details
	static function updateRecord($xref, $gedrec) {
		$gedrec = preg_replace_callback(
			"/^(2 PLAC(.*)), (\d\d)(\d\d)(\d\d)([NS])(\d\d\d)(\d\d)(\d\d)([EW])/m",
			array('plugin', '_updateRecord_callback'),
			$gedrec
		);
		return $gedrec;
	}

	static function _updateRecord_callback($m)
	{
		$lineending = "\n";
		$strLATI = $m[6].(round($m[3]+($m[4]/60)+($m[5]/3600),4));
		$strLONG = $m[10].(round($m[7]+($m[8]/60)+($m[9]/3600),4));
		$strReturn = $m[1].$lineending.'3 MAP'.$lineending.'4 LATI '.$strLATI.$lineending.'4 LONG '.$strLONG;
		return $strReturn;
	}

}
