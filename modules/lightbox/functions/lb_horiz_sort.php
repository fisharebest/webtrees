<?php
/**
 * Lightbox Album Include for Horizontal Album sort
 *
 * Various printing functions used to print fact records
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
 * @subpackage Module
 * @version $Id$
  * @author Brian Holland
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

/*
    <script type="text/javascript" src="js/conio/prototype.js"></script>
    <script type="text/javascript" src="js/scriptaculous/scriptaculous.js"></script>
*/
?>
	<script language="JavaScript">
	function getGroupOrder() {
		// var sections = document.getElementsByClassName('section');
		var sections = $$('.section');
		
		var alerttext = '';
		var order = '';

		sections.each(function(section) {
			order += Sortable.sequence(section) + ',';
			alerttext = order;
		});
		alert(alerttext);
		return false;
	}

	</script>

	<style type="text/css">

	body, div {
		font-family: Arial, Helvetica;
		font-size: 12px;
	}

	ul {
		width: 100%;
		list-style-type:none;
		color: black;
	}

	li.facts_value {
		padding: 2px;
		cursor: move;
		border: none;
		text-align: center;
    }

	</style>

<script type="text/javascript">
// <![CDATA[
    //sections = [ 'group1','group2' ];

	<?php if ($rownum1>0) { ?>
		Sortable.create( 'thumblist_1', 	{ tag:'li', dropOnEmpty: false, constraint: false, only:'facts_value' } );
	<?php } ?>
	<?php if ($rownum2>0) { ?>
		Sortable.create( 'thumblist_2', 	{ tag:'li', dropOnEmpty: false, constraint: false, only:'facts_value' } );
	<?php } ?>
	<?php if ($rownum3>0) { ?>
		Sortable.create( "thumblist_3", 	{ tag:'li', dropOnEmpty: false, constraint: false, only:'facts_value' } );
	<?php } ?>
	<?php if ($rownum4>0) { ?>
		Sortable.create( "thumblist_4", 	{ tag:'li', dropOnEmpty: false, constraint: false, only:'facts_value' } );
	<?php } ?>

//		Sortable.create( "thumblist", 	{ tag:'li', dropOnEmpty: false, constraint: false, only:'facts_value' } );
	// ]]>
</script>

