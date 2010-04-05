<?php
/**
 * Lightbox Album module for phpGedView
 *
 * Display media Items using Lightbox
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2007  PHPGedView Development Team
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

?>
	<dd id="door1"><a href="javascript:;" onclick="tabswitch(1); return false;" ><?php echo i18n::translate('Personal Facts and Details')?></a></dd>
	<dd id="door2"><a href="javascript:;" onclick="tabswitch(2); return false;" ><?php echo i18n::translate('Notes')?></a></dd>
	<dd id="door3"><a href="javascript:;" onclick="tabswitch(3); return false;" ><?php echo i18n::translate('Sources')?></a></dd>

<?php
	if ($MULTI_MEDIA){
		if (!file_exists("modules/googlemap/defaultconfig.php")) {  ?>
			<?php if (file_exists("modules/lightbox/album.php") ) {?>
				<dd id="door4"><a href="javascript:;" onclick="tabswitch(4); return false;" ><?php print i18n::translate('Media') ?></a></dd>
				<dd id="door8"><a href="javascript:;" onclick="tabswitch(8); return false;" ><?php print i18n::translate('Album') ?></a></dd>
			<?php }
		}elseif (file_exists("modules/googlemap/defaultconfig.php")) {  ?>
			<?php if (file_exists("modules/lightbox/album.php") ) {?>
				<dd id="door4"><a href="javascript:;" onclick="tabswitch(4); return false;" ><?php print i18n::translate('Media') ?></a></dd>
				<dd id="door9"><a href="javascript:;" onclick="tabswitch(9); return false;" ><?php print i18n::translate('Album') ?></a></dd>
			<?php }
		}
	}
 ?>

	<dd id="door5"><a href="javascript:;" onclick="tabswitch(5); return false;" ><?php print i18n::translate('Close Relatives')?></a></dd>
	<dd id="door6"><a href="javascript:;" onclick="tabswitch(6); return false;" ><?php print i18n::translate('Tree')?></a></dd>
	<dd id="door7"><a href="javascript:;" onclick="tabswitch(7); return false;" ><?php print i18n::translate('Research Assistant')?></a></dd>
	<?php if (file_exists("modules/googlemap/defaultconfig.php")) { ?>
		<dd id="door8"><a href="javascript:;" onclick="tabswitch(8); if (loadedTabs[8]) {ResizeMap(); ResizeMap();} return false;" ><?php print i18n::translate('Map')?></a></dd>
	<?php } ?>
<!--	<dd id="door10"><a href="javascript:;" onclick="tabswitch(10); return false;" ><?php print "Spare Tab" ?></a></dd> -->
	
	<dd id="door0"><a href="javascript:;" onclick="tabswitch(0); if (loadedTabs[8]) {ResizeMap(); ResizeMap();} return false;" ><?php print i18n::translate('ALL')?></a></dd> 
  



