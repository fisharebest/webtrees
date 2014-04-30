<?php
// Template for drawing the height-restricted blocks on the portal pages
//
// This template expects that the following variables will be set
// $id - the DOM id for the block div
// $title - the title of the block
// $class - the additional class of the block
// $content - the content of the block
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id: block_small_temp.php 12663 2011-11-10 00:29:50Z greg $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

?>
<div id="<?php echo $id; ?>" class="block" >
	<div class="blockheader">
		<?php echo $title; ?>
	</div>
	<div class="blockcontent small_inner_block <?php echo $class; ?>">
		<?php echo $content; ?>
	</div>
</div>
