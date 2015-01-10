<?php
// GEDFact page
//
// GEDFact information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2008 PGV Development Team.
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

$controller = new WT_Controller_Individual();

echo '<link href="'.WT_STATIC_URL.WT_MODULES_DIR.'GEDFact_assistant/css/gf_styles.css" rel="stylesheet" type="text/css" media="screen">';

global $tabno, $linkToID, $SEARCH_SPIDER;
global $SHOW_AGE_DIFF;
global $GEDCOM;
global $show_full;
global $famid, $censyear, $censdate;

$summary=
	$controller->record->format_first_major_fact(WT_EVENTS_BIRT, 2).
	$controller->record->format_first_major_fact(WT_EVENTS_DEAT, 2);

require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_MEDIA/media_1_ctrl.php';
