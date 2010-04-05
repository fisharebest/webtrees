<?php
/**
 * Finnish Date Functions that can be used by any page in PGV
 * Other functions that are specific to Finnish can be added here too
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_FI_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Localise a date. "[qualifier] date [qualifier date] [qualifier]"
////////////////////////////////////////////////////////////////////////////////
function date_localisation_fi(&$q1, &$d1, &$q2, &$d2, &$q3) {
	// Constant 'ta' is appended to the Finnish month values, if a day value exists
	$d1=preg_replace("/(\b\d{1,2}\D+kuu\b)/", "$1ta", $d1);
	$d2=preg_replace("/(\b\d{1,2}\D+kuu\b)/", "$1ta", $d2);
}

//-- functions to calculate finnish specific genitive names
// NOTE this function is incomplete and probably very inefficient.
// I've decided that for now the task is beyond me, I have looked
// for a freely availiable algorithm and failed to find one.
// it is best left to a finnish speaker
function getFirstRelationsName_fi($pid)
{
	// In Finnish we want the genitive form of the name
	$person=Person::getInstance($pid);
	if ($person) {
		$name=$person->getFullName();
	} else {
		$name='';
	}

	// for now I have been asked to remove the body of this function - if any Finnish
	// speaker can sort this out I would be grateful.
    return $name;

    // First we look for Consonant gradation
    if(preg_match("/kki$/", $name))
	{
	    preg_replace("/kki$/", "kin", $name);
	}
    else if(preg_match("/kka$/", $name))
	{
	    preg_replace("/kka$/", "kan", $name);
	}
    else if(preg_match("/ppi$/", $name))
	{
	    preg_replace("/ppi$/", "pin", $name);
	}
    else if(preg_match("/ppa$/", $name))
	{
	    preg_replace("/ppa$/", "pan", $name);
	}
    else if(preg_match("/tti$/", $name))
	{
	    preg_replace("/tti$/", "tin", $name);
	}
    else if(preg_match("/tta$/", $name))
	{
	    preg_replace("/tta$/", "tan", $name);
	}
    else if(preg_match("/nti$/", $name))
	{
	    preg_replace("/nti$/", "nnin", $name);
	}
    else if(preg_match("/nta$/", $name))
	{
	    preg_replace("/nta$/", "nnan", $name);
	}


    //Now we sort out endings
	// Names ending in 'e' now end 'een'
    else if(preg_match("/e$/", $name))
	{
	    $name = $name . "en";
	}
	// Names ending 'nen' now end 'sen'
	else if(preg_match("/nen$/", $name))
	{
	    preg_replace("/nen$/", "sen", $name);
	}
	// Names ending 'n' now end 'men'
	else if(preg_match("/n$/", $name))
	{
	    preg_replace("/n$/", "men", $name);
	}
	// Names ending 'si' now end 'den'
	else if(preg_match("/si$/", $name))
	{
	    preg_replace("/si$/", "den", $name);
	}
	// Names ending 'is' now end 'iin'
	else if(preg_match("/is$/", $name))
	{
	    preg_replace("/is$/", "iin", $name);
	}
	// Names ending 'as' now end 'aan'
	else if(preg_match("/as$/", $name))
	{
	    preg_replace("/as$/", "aan", $name);
	}
	// Names ending 'a' now end 'aan'
	else if(preg_match("/a$/", $name))
	{
	    preg_replace("/a$/", "aan", $name);
	}
	// Names ending 'us' now end 'ksen'
	else if(preg_match("/us$/", $name))
	{
	    preg_replace("/us$/", "ksen", $name);
	}
	// Names ending 'ys' now end 'ksen'
	else if(preg_match("/ys$/", $name))
	{
	    preg_replace("/ys$/", "ksen", $name);
	}
	// Names ending 'os' now end 'ksen'
	else if(preg_match("/os$/", $name))
	{
	    preg_replace("/os$/", "ksen", $name);
	}
	// Names ending 'ös' now end 'ksen'
	else if(preg_match("/ös$/", $name))
	{
	    preg_replace("/ös$/", "ksen", $name);
	}
	// All other names have 'n' appended
	else
	{
	    $name = $name . "n";
	}

    return $name;
}

?>
