<?php
  /**
 * GEDFact page
 *
 * GEDFact information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage Census Assistant
 * @version $Id$
 */
require_once './config.php';
require_once WT_ROOT.'includes/controllers/individual_ctrl.php'; 
$controller = new IndividualController(); 
$controller->init(); 
echo "<link href =\"modules/GEDFact_assistant/css/gf_styles.css\" 	rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />";

global $USE_THUMBS_MAIN, $mediacnt, $tabno;
global $linkToID;
global $SEARCH_SPIDER, $GOOGLEMAP_PH_CONTROLS;

		global $SHOW_ID_NUMBERS, $WT_IMAGE_DIR, $WT_IMAGES, $SHOW_AGE_DIFF;
		global $pgv_changes, $GEDCOM, $ABBREVIATE_CHART_LABELS;
		global $show_full; 
		global $famid, $censyear, $censdate;
		
// print_simple_header("Census");

		$summary=$controller->indi->format_first_major_fact(WT_EVENTS_BIRT, 2);
		if (!($controller->indi->isDead())) {
			// If alive display age
			$bdate=$controller->indi->getBirthDate();
			$age = GedcomDate::GetAgeGedcom($bdate);
		//	if ($age!="") {
		//		$summary.= "<span class=\"label\">".i18n::translate('Age').":</span><span class=\"field\"> ".get_age_at_event($age, true)."</span>";
		//	}
		}
		$summary.=$controller->indi->format_first_major_fact(WT_EVENTS_DEAT, 2);

		$controller->medialink_assistant();

// print_footer();
?>
