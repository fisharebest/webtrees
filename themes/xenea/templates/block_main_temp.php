<?php
/**
 * Template for drawing the main blocks on the portal pages
 *
 * This template expects that the following variables will be set
 * $id - the DOM id for the block div
 * $title - the title of the block
 * $content - the content of the block
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Themes
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

?>
<div id="<?php print $id; ?>" class="block" >
	<table class="blockheader" cellpadding="0" cellspacing="0" style="padding:0;margin:0;">
		<tr>
			<td class="blockh1" ></td>
			<td class="blockh2" >
				<div class="blockhc"><b><?php print $title ?></b></div>
			</td>
			<td class="blockh3"></td>
		</tr>
	</table>
	<div class="blockcontent">
		<?php print $content ?>
	</div>
</div>
