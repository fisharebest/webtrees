<?php
/**
 * GEDCOM Statistics Compatability Class
 *
 * This class provides backwards compatability for older Advanced HTML block
 * tags.  It should not be used for new projects.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @author Patrick Kellum
 * @package webtrees
 * @subpackage Lists
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_STATS_COMPAT_PHP', '');

require_once WT_ROOT.'includes/classes/class_stats.php';
class stats_compat extends stats
{
///////////////////////////////////////////////////////////////////////////////
// GEDCOM                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function GEDCOM() {return $this->gedcomFilename();}
	function GEDCOM_ID() {return $this->gedcomID();}
	function GEDCOM_TITLE() {return $this->gedcomTitle();}
	function CREATED_SOFTWARE() {return $this->gedcomCreatedSoftware();}
	function CREATED_VERSION() {return $this->gedcomCreatedVersion();}
	function CREATED_DATE() {return $this->gedcomDate();}
	function GEDCOM_UPDATED() {return $this->gedcomUpdated();}
	function HIGHLIGHT() {return $this->gedcomHighlight();}
	function HIGHLIGHT_LEFT() {return $this->gedcomHighlightLeft();}
	function HIGHLIGHT_RIGHT() {return $this->gedcomHighlightRight();}

///////////////////////////////////////////////////////////////////////////////
// Totals                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function TOTAL_INDI() {return $this->totalIndividuals();}
	function TOTAL_FAM() {return $this->totalFamilies();}
	function TOTAL_SOUR() {return $this->totalSources();}
	function TOTAL_OTHER() {return $this->totalOtherRecords();}
	function TOTAL_SURNAMES() {return $this->totalSurnames();}
	function TOTAL_EVENTS() {return $this->totalEvents();}
	function TOTAL_EVENTS_BIRTH() {return $this->totalEventsBirth();}
	function TOTAL_EVENTS_DEATH() {return $this->totalEventsDeath();}
	function TOTAL_EVENTS_MARRIAGE() {return $this->totalEventsMarriage();}
	function TOTAL_EVENTS_OTHER() {return $this->totalEventsOther();}
	function TOTAL_MALES() {return $this->totalSexMales();}
	function TOTAL_FEMALES() {return $this->totalSexFemales();}
	function TOTAL_UNKNOWN_SEX() {return $this->totalSexUnknown();}
	function TOTAL_USERS() {return $this->totalUsers();}
	function TOTAL_MEDIA() {return $this->totalMedia();}

///////////////////////////////////////////////////////////////////////////////
// Births                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function FIRST_BIRTH() {return $this->firstBirth();}
	function FIRST_BIRTH_YEAR() {return $this->firstBirthYear();}
	function FIRST_BIRTH_NAME() {return $this->firstBirthName();}
	function FIRST_BIRTH_PLACE() {return $this->firstBirthPlace();}
	function LAST_BIRTH() {return $this->lastBirth();}
	function LAST_BIRTH_YEAR() {return $this->lastBirthYear();}
	function LAST_BIRTH_NAME() {return $this->lastBirthName();}
	function LAST_BIRTH_PLACE() {return $this->lastBirthPlace();}

///////////////////////////////////////////////////////////////////////////////
// Deaths                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function FIRST_DEATH() {return $this->firstDeath();}
	function FIRST_DEATH_YEAR() {return $this->firstDeathYear();}
	function FIRST_DEATH_NAME() {return $this->firstDeathName();}
	function FIRST_DEATH_PLACE() {return $this->firstDeathPlace();}
	function LAST_DEATH() {return $this->lastDeath();}
	function LAST_DEATH_YEAR() {return $this->lastDeathYear();}
	function LAST_DEATH_NAME() {return $this->lastDeathName();}
	function LAST_DEATH_PLACE() {return $this->lastDeathPlace();}

///////////////////////////////////////////////////////////////////////////////
// Lifespan                                                                  //
///////////////////////////////////////////////////////////////////////////////

	function LONG_LIFE() {return $this->longestLife();}
	function LONG_LIFE_AGE() {return $this->longestLifeAge();}
	function LONG_LIFE_NAME() {return $this->longestLifeName();}
	function TOP10_OLDEST() {return $this->topTenOldest();}
	function TOP10_OLDEST_LIST() {return $this->topTenOldestList();}
	function AVG_LIFE() {return $this->averageLifespan();}

///////////////////////////////////////////////////////////////////////////////
// Events                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function FIRST_EVENT() {return $this->firstEvent();}
	function FIRST_EVENT_YEAR() {return $this->firstEventYear();}
	function FIRST_EVENT_TYPE() {return $this->firstEventType();}
	function FIRST_EVENT_NAME() {return $this->firstEventName();}
	function FIRST_EVENT_PLACE() {return $this->firstEventPlace();}
	function LAST_EVENT() {return $this->lastEvent();}
	function LAST_EVENT_YEAR() {return $this->lastEventYear();}
	function LAST_EVENT_TYPE() {return $this->lastEventType();}
	function LAST_EVENT_NAME() {return $this->lastEventName();}
	function LAST_EVENT_PLACE() {return $this->lastEventPlace();}

///////////////////////////////////////////////////////////////////////////////
// Family Size                                                               //
///////////////////////////////////////////////////////////////////////////////

	function MOST_CHILD() {return $this->largestFamily();}
	function MOST_CHILD_TOTAL() {return $this->largestFamilySize();}
	function MOST_CHILD_NAME() {return $this->largestFamilyName();}
	function TOP10_BIGFAM() {return $this->topTenLargestFamily();}
	function TOP10_BIGFAM_LIST() {return $this->topTenLargestFamilyList();}
	function AVG_CHILD() {return $this->averageChildren();}

///////////////////////////////////////////////////////////////////////////////
// Contact                                                                   //
///////////////////////////////////////////////////////////////////////////////

	function WEBMASTER_CONTACT() {return $this->contactWebmaster();}
	function GEDCOM_CONTACT() {return $this->contactGedcom();}

///////////////////////////////////////////////////////////////////////////////
// Date & Time                                                               //
///////////////////////////////////////////////////////////////////////////////

	function SERVER_DATE() {return $this->serverDate();}
	function SERVER_TIME() {return $this->serverTime();}
	function SERVER_TIME_24() {return $this->serverTime24();}
	function SERVER_TIMEZONE() {return $this->serverTimezone();}
	function LOCAL_DATE() {return $this->browserDate();}
	function LOCAL_TIME() {return $this->browserTime();}
	function LOCAL_TIME_24() {return $this->browserTime24();}
	function LOCAL_TIMEZONE() {return $this->browserTimezone();}

///////////////////////////////////////////////////////////////////////////////
// Misc.                                                                     //
///////////////////////////////////////////////////////////////////////////////

	function COMMON_SURNAMES() {return $this->commonSurnames();}
}
?>
