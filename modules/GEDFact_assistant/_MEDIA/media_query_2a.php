<?php
/**
 * Media Link Assistant Control module for phpGedView
 *
 * Media Link information about an individual
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
 * @package webtrees
 * @subpackage GEDFact_assistant
 * @version $Id$
*/

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}
?>

<html <?php echo i18n::html_markup(); ?>>
<head>
<script language="javascript">


	function addlinks(iname) {
		// iid=document.getElementById('gid').value;
		if (document.getElementById('gid').value == "") {
			alert(id_empty);
		}else{
			addmedia_links(document.getElementById('gid'), document.getElementById('gid').value, iname );
			return false;
		}
	}

	function openFamNav(id) {
		//id=document.getElementById('gid').value;
		if (id.match("I")=="I" || id.match("i")=="i") {
			id = id.toUpperCase();
			winNav = window.open('edit_interface.php?action=addmedia_links&noteid=newnote&pid='+id, 'winNav', 'top=50,left=640,width=300,height=630,resizable=1,scrollbars=1');
			if (window.focus) {winNav.focus();}
		}else if (id.match("F")=="F") {
			id = id.toUpperCase();
			// TODO --- alert('Opening Navigator with family id entered will come later');
		}
	}
	
</script>
</head>


<table border="0" cellpadding="1" cellspacing="2" >
<tr>
<td width="350" class="row2">
<?php
	require 'modules/GEDFact_assistant/_MEDIA/media_5_input.php';
?>
</td>
</tr>
</table>

</body>
</html>

