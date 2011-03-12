<?php
/**
 * TreeView Module help text.
 *
 * This file is included from the application help_text.php script.
 * It simply needs to set $title and $text for the help topic $help_topic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 Daniel Faivre
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
 *
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$imgStyle = ' style="height: 22px; width: 22px; border: 0 none"';
switch ($help) {
case 'TV_MODULE':
	$title = WT_I18N::translate('TreeView Module');
	$text = WT_I18N::translate('Interactive and printable genealogical tree.').
		'<br /><br />'.
		WT_I18N::translate('Commands').':'.'<br /><table><tbody>'.
		'<tr><td><img src="'.$WT_IMAGES['zoomin'].'"'.$imgStyle.' alt="zoomin" /></td><td>'.
		WT_I18N::translate('Zoom in : enlarge texts and person\'s boxes.').'</td></tr>'.
		'<tr><td><img src="'.$WT_IMAGES['zoomout'].'"'.$imgStyle.' alt="zoomout" /></td><td>'.
		WT_I18N::translate('Zoom out : reduce texts and person\'s boxes.').'</td></tr>'.
		'<tr><td><img src="'.WT_MODULES_DIR.'/tree/images/zoom0.png"'.$imgStyle.' alt="nozoom" /></td><td>'.
		WT_I18N::translate('No zoom.').'</td></tr>'.
		'<tr><td><img src="'.$WT_IMAGES['ldarrow'].'"'.$imgStyle.'alt="alignLeft" /></td><td>'.
		WT_I18N::translate('Align on top left corner. Useful before printing.').'</td></tr>'.
		'<tr><td><img src="'.$WT_IMAGES['patriarch'].'"'.$imgStyle.' alt="center" /></td><td>'.
		WT_I18N::translate('Center on the root person.').'</td></tr>'.
		'<tr><td><img src="'.$WT_IMAGES['rdarrow'].'"'.$imgStyle.' alt="alignRight" /></td><td>'.
		WT_I18N::translate('Align on top right corner.').'</td></tr>'.
		'<tr><td><img src="'.WT_MODULES_DIR.'/tree/images/dates.png"'.$imgStyle.' alt="hide dates" /></td><td>'.
		WT_I18N::translate('Hide/show dates on small boxes.').'</td></tr>'.
		'<tr><td><img src="'.WT_MODULES_DIR.'/tree/images/compact.png"'.$imgStyle.' alt="c/e" /></td><td>'.
		WT_I18N::translate('Compact tree / fixed boxes : switch beetween fixed-width boxes and compact tree. <i>fixed-width</i> display one generation\'s boxes in one column, and <i>compact</i> display more persons on the same area.').'</td></tr>'.
		/* function not enabled yet	'<tr><td><img src="'.$WT_IMAGES['media'].'"'.$imgStyle.' alt="open" /></td><td>'.
		WT_I18N::translate('Open details for all displayed boxes. Could be long.').'</td></tr>'.*/
		'<tr><td><img src="'.$WT_IMAGES["fambook"].'"'.$imgStyle.' alt="close" /></td><td>'.
		WT_I18N::translate('Close all opened boxes.').'</td></tr>'.			
		'<tr><td><img src="'.WT_MODULES_DIR.'/tree/images/print.png"'.$imgStyle.' alt="print" /></td><td>'.
		WT_I18N::translate('Download full resolution medias instead of thumbnails for opened person\'s boxes and open the print dialog when done.').'</td></tr>'.
		'<tr><td><img src="'.$WT_IMAGES["sfamily"].'"'.$imgStyle.' alt="partners" /></td><td>'.
		WT_I18N::translate('Show / hide multiples life partners or spouses.').'</td></tr>'.
		'</tbody></table>'.
		WT_I18N::translate('
- when you drag, align or center the tree, person\'s boxes are loaded and added until the tree is completely loaded.

- the treeview toolbar is draggable everywhere you want on the tree view.

- you can directly access to an individual record with one click on a person\'s gender icon.

- a click elsewhere on a family\'s box load and display more detailed informations about this family. When details are displayed, you can click on the tree icon beside a person\'s name to go to these person\'s interactive TreeView, or on the family\'s icon beside a life partner to go to these family\'s page. If the Lightbox module is enabled, a click on the person\'s thumbnail, when exists, open the associated image in the lightbox. When a person have some life partners, the lightbox display the main medias of these partners as a diaporama.

- your preferences are stored in local cookies: zoom level, fixed or compact, switch last / all life partners, style.

<u>Printing tips:</u> 
- to print a big tree on one big sheet, you can set a big paper size and print in a file (you can find large format printing services almost everywhere).
- you can display details for the persons you want before printing the tree in a "What-You-See-Is-What-You-Get" mode.
- to print the lines between person boxes, enable the printing of background colors and images in your browser\'s settings.
- you can play with the TreeView zoom feature and the printing paper size setting to print any tree.
- if you encounter some printing issues on Linux systems, you can change the print-to-file driver, from lpr to kprinter (even if you don\'t use KDE). In Firefox, that is set in the "about:config" page. 
');
	break;
}
