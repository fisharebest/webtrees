<?php
/**
 * Census Assistant Control module for phpGedView
 *
 * Census Proposed Text Area File
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 to 2010  PGV Development Team.  All rights reserved.
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
global $theme_name; 
?>

<script>
function help_window2(frm)  {
	var aWindow = window.open('http://wiki.phpgedview.net/en/index.php?title=GedFactAssistant_module#Sub-module:_Census_Assistant', 'HelpWindow',
	'scrollbars=yes,menubar=no,resizable=yes,location=no,toolbar=no,width=900,height=700');
	aWindow.focus();
	// set the target to the blank window
	frm.target = 'TableAddRow2NewWindow';
}
</script>

<!--   ---- The proposed Census Text -------- -->
<div class="optionbox cens_text">
<!--[if IE]><style>.cens_text{margin-top:-1.3em;}</style><![EndIf]-->
	<span><input type="button" value="<?php echo i18n::translate('Help'); ?>" onclick="javascript: help_window2(this.form)" /></span>
	<span><?php echo i18n::translate('Click &quot;Preview&quot; to copy Edit Input Fields'); ?></span>
	<span><input type="button" value="<?php echo i18n::translate('Preview'); ?>" onclick="preview();" /></span>
	<span><b><?php echo i18n::translate('Proposed Census Text&nbsp;&nbsp;'); ?></b></span>
	<span><input type="submit" value="<?php echo i18n::translate('Save'); ?>" onclick="caSave();" /></span>
	<br /><br />
	<span class="descriptionbox width15 nowrap <?php $TEXT_DIRECTION; ?>">
		<?php
			echo i18n::translate('Shared note'), help_link('SHARED_NOTE');
		?>
	</span>
	<div class="optionbox">
		<textarea wrap="off" name="NOTE" id="NOTE"></textarea><br />
		<?php if ($theme_name=="Minimal") { ?>
			<!--[if IE]><style>textarea#NOTE{height: 18.1em;}</style><![EndIf]-->
		<?php } ?>
		<center>
		<?php print_specialchar_link("NOTE",true); ?>
		</center>
	</div>
</div>




