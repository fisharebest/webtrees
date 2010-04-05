<?php
/**
 * Footer for print-friendly Cloudy theme pages
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
<script type="text/javascript" language="javascript" >
 function show_divs(){
        if (document.getElementById('index_small_blocks'))
        {
                var smallblocks = document.getElementById('index_small_blocks');
                var blocks = document.getElementById('index_main_blocks');
                smallblocks.style.visibility = 'visible';
                smallblocks.style.display = 'inline';
                blocks.style.visibility = 'visible';
                blocks.style.display = 'inline';
        }
}
window.onload = function() { show_divs(); }
</script>
