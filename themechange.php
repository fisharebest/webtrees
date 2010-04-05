<?php
/**
 * Allow visitor to change the theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @author John Finlay
 * @package webtrees
 * @subpackage Themes
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'themechange.php');
require './includes/session.php';

// Extract request variables
$mytheme =safe_GET('mytheme');
$frompage=safe_GET('frompage', WT_REGEX_URL, 'index.php');
// decode frompage address to recover the address with variables
$frompage = base64_decode($frompage);

// Only change to a valid theme
foreach (get_theme_names() as $themename=>$themedir) {
	if ($themedir==$mytheme) {
		$_SESSION['theme_dir']=$mytheme;
		// Make the change permanent, if allowed
		if (get_user_setting(WT_USER_ID, 'editaccount')=='Y') {
			set_user_setting(WT_USER_ID, 'theme', $mytheme);
		}
		break;
	}
}
// Go back to where we came from
header('Location: '.encode_url(decode_url($frompage), false));
?>
