<?php
/**
 * Default Privacy File
 *
 * This is the default privacy.php module that is distributed
 * with phpGedView.  Whenever a gedcom file is added to the PGV system
 * a copy of this file is placed in the <var>$INDEX_DIRECTORY</var> for each gedcom
 * so that each gedcom may use different privacy settings.
 *
 * This privacy module allows you to hide the names and/or details of living people.  Allows
 * authenticated users to view the details of living people.  It also allows Admins to change
 * privacy settings for specific gedcom records or individuals.
 *
 * This privacy file also acts as a module allowing programmers to extend the functionality of
 * the privacy settings or implement a different privacy model for each gedcom. To provide your own
 * privacy module simply implement the functions in this file and configure phpGedview to use your
 * new file.
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
 * $Id$
 * @package webtrees
 * @subpackage Privacy
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

/**
 * Privacy file version number
 *
 * This version number is used to track updates to the privacy functions between versions
 * so that the program can automatically update the gedcom specific privacy files during the upgrade
 * process.
 * @global string $PRIVACY_VERSION
 */
$PRIVACY_VERSION = "3.2";

/**
 * Set the access level for dead people
 *
 * Sets the access level required to view the information of dead people.
 * Can be one of the PRIV access levels: <var>WT_PRIV_HIDE</var>, <var>WT_PRIV_PUBLIC</var>, <var>WT_PRIV_USER</var>, <var>WT_PRIV_NONE</var>
 * The default value is <var>WT_PRIV_PUBLIC</var>.
 * @global integer $SHOW_DEAD_PEOPLE
 */
$SHOW_DEAD_PEOPLE = WT_PRIV_PUBLIC;

/**
 * Set the access level for the names of private people
 *
 * Sets the access level required to view the names of private people.
 * Can be one of the PRIV access levels: <var>WT_PRIV_HIDE</var>, <var>WT_PRIV_PUBLIC</var>, <var>WT_PRIV_USER</var>, <var>WT_PRIV_NONE</var>
 * The default value is <var>WT_PRIV_PUBLIC</var>.  Setting this to <var>WT_PRIV_USER</var> would mean that only
 * authenticated users can view names of private people and public visitors would only see the name
 * private.
 * @global integer $SHOW_LIVING_NAMES
 */
$SHOW_LIVING_NAMES = WT_PRIV_PUBLIC;

/**
 * Set the access level for sources
 *
 * Sets the access level required to view sources.
 * Can be one of the PRIV access levels: <var>WT_PRIV_HIDE</var>, <var>WT_PRIV_PUBLIC</var>, <var>WT_PRIV_USER</var>, <var>WT_PRIV_NONE</var>
 * The default value is <var>WT_PRIV_PUBLIC</var>.  Setting this to <var>WT_PRIV_USER</var> would mean that only
 * authenticated users can view sources.
 * @global integer $SHOW_LIVING_NAMES
 */
$SHOW_SOURCES = WT_PRIV_PUBLIC;

/**
 * how old a person must be before they are assumed to be dead
 *
 * The <var>$MAX_ALIVE_AGE</var> variable is referenced by the privacy functions to determine how
 * old a person must be before a person is assumed to be dead.
 * @global integer $MAX_ALIVE_AGE
 */
$MAX_ALIVE_AGE      = "120";

/**
 * Set the access level for the clippings cart
 *
 * Sets the access level required to view the clippings cart.
 * Can be one of the PRIV access levels:
 *		- <var>WT_PRIV_HIDE</var>
 *		- <var>WT_PRIV_PUBLIC</var>
 *		- <var>WT_PRIV_USER</var>
 *		- <var>WT_PRIV_NONE</var>
 * The default settings is set to <var>WT_PRIV_PUBLIC</var> allowing only authenticated users to have access.
 * @global integer $ENABLE_CLIPPINGS_CART
 */
$ENABLE_CLIPPINGS_CART = WT_PRIV_PUBLIC;
/**
 * Set the access level for the multi-site search
 *
 * Sets the access level required to use the multi-site search feature.
 * Can be one of the PRIV access levels:
 *		- <var>WT_PRIV_HIDE</var>
 *		- <var>WT_PRIV_PUBLIC</var>
 *		- <var>WT_PRIV_USER</var>
 *		- <var>WT_PRIV_NONE</var>
 * The default settings is set to <var>WT_PRIV_PUBLIC</var> allowing only authenticated users to have access.
 * @global integer $ENABLE_CLIPPINGS_CART
 */
$SHOW_MULTISITE_SEARCH = WT_PRIV_NONE;
/**
 * Set the program to use relationship privacy
 *
 * This tells the program that for private individuals calculate the relationship between the user
 * and the individual and use the relationship path setting <var>$MAX_RELATION_PATH_LENGTH</var>
 * to determine if the user has access.
 *
 * A <b>false</b> value means authenticated users can see the details of all living people
 * A <b>true</b> value means users can only see the private information of living people
 * they are related to.
 * @global boolean $USE_RELATIONSHIP_PRIVACY
 */
$USE_RELATIONSHIP_PRIVACY = false;

/**
 * Maximum path to allow when using relationship privacy
 *
 * This setting is the maximum path length to allow users to view.  The path length is defined as
 * the number of steps it takes to get from 1 individual to another.  The default value is 3, which
 * would allow someone to access up to their second cousins
 * @global integer $MAX_RELATION_PATH_LENGTH
 */
$MAX_RELATION_PATH_LENGTH = 3;
/**
 * Follow Marriage Relationships
 *
 * This setting tells the relationship privacy calculator whether or not to follow marriage
 * relationships.  Setting this to <b>true</b> would allow a user to view his brother's wife's
 * family for example.  The default setting is <b>true</b>.
 * @global boolean $CHECK_MARRIAGE_RELATIONS
 */
$CHECK_MARRIAGE_RELATIONS = true;

/**
 * Use Year Based Privacy
 *
 * This setting tells the privacy functions to change how the death status of an individual is
 * calculated based on how long it has been since they died.  So someone who died less than <var>$MAX_ALIVE_AGE</var>-25
 * years ago will still be shown as dead.
 * @global boolean $PRIVACY_BY_YEAR
 */
$PRIVACY_BY_YEAR = false;

/**
 * When privatizing a gedom record should the relationship links be left in or stripped out
 * @global boolean $SHOW_PRIVATE_RELATIONSHIPS
 */
$SHOW_PRIVATE_RELATIONSHIPS = false;

/**
 * Person Privacy array
 *
 * The person_privacy array provides users with the ability to override default
 * privacy settings for individuals, families, and sources in the gedcom.  Each index in the array
 * is a GEDCOM XRef ID and the value is a privacy level setting.
 *
 * For example, setting <samp>$person_privacy["I3"] = WT_PRIV_NONE;</samp> would mean that only
 * Admin users have access to the individual with ID "I3".
 * @global array $person_privacy
 */
//-- start person privacy --//
$person_privacy = array();
//-- end person privacy --//
/**
 * User Privacy Array
 *
 * The user_privacy array provides administrators the ability to override default
 * privacy settings for individuals, families, and sources in the gedcom based on the username
 * of the person attempting to access the record.  The first index in the array is the username that
 * the settings should apply to.  The second index is the GEDCOM XRef ID to apply the setting to.
 *
 * For example, setting <code>$user_privacy["john"]["I100"] = WT_PRIV_NONE;</code> would prevent the
 * user with username "john" from accessing the gedcom record for "I100" unless "john" is an admin
 * user.
 * @global array $user_privacy
 */
//-- start user privacy --//
$user_privacy = array();
//-- end user privacy --//
/**
 * Global Facts Array
 *
 * The global_facts array defines facts on a global level that should be hidden for all
 * individuals in the gedcom. The first index in the array is the GEDCOM tag name to hide.
 * The ["show"] element determines at what access level the fact is shown
 * the ["details"] element determins at what access level the details of a fact are shown
 *
 * Setting the "details" element without setting the "show" element would mean that users can view
 * that the fact exists but cannot see the details of the fact.
 *
 * The default privacy file hides all Social Security Numbers (SSN) for privacy.
 * @global array $global_facts
 */
//-- start global facts privacy --//
$global_facts = array();
$global_facts["SSN"]["show"] = WT_PRIV_NONE;
$global_facts["SSN"]["details"] = WT_PRIV_NONE;
//-- end global facts privacy --//

/**
 * Person Facts Array
 *
 * The person_facts array defines facts that are hidden for specific individuals, families, or sources
 * in the gedcom and the level at which they are hidden. The first element is the ID of the person,
 * the second element is the GEDCOM fact tag. The ["show"] element determines at what access level
 * the fact is shown. The ["details"] element determins at what access level the details of a fact
 * are shown.
 *
 * Setting the "details" element without setting the "show" element would mean that users can view
 * that the fact exists but cannot see the details of the fact.
 *
 * For example, setting <code>$person_facts["I6909"]["NOTE"]["show"] = WT_PRIV_USER;</code> and
 * <code>$person_facts["I6909"]["NOTE"]["details"] = WT_PRIV_USER;</code> would hide all of the NOTEs
 * for individual I6909 that were attached to the individual record.
 *
 * NOTE: This setting only applies to LEVEL 1 GEDCOM facts such as 1 NOTE.  So in the example above
 * the NOTEs that were attached to other facts would not be hidden, such as 1 EVEN 2 NOTE
 * @global array $person_facts
 */
//-- start person facts privacy --//
$person_facts = array();
//-- end person facts privacy --//

?>
