<?php
/**
 * Top-of-page links for Cloudy theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (c) 2002 to 2008  John Finlay and others.  All rights reserved.
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
 * @author w.a. bastein http://genealogy.bastein.biz
 * @package webtrees
 * @subpackage Themes
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

?>
</td></tr>
</table>
</div><!-- close div for div id="header" -->
<?php
// stupid browsers ;)
if ($BROWSERTYPE == "opera")
        print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"cellcontainer\"><tr><td valign=\"top\" width=\"100%\" colspan=\"5\" id=\"container\">";
else
        print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"10\" id=\"container\" width=\"100%\" ><tr><td valign=\"top\" width=\"100%\"  id=\"cellcontainer\">";
?>

<div id="content">
