<?php
// Census Assistant Control module for webtrees
//
// Census Proposed Text Area File
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2010 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
?>
<!-- The proposed Census Text -->
<div class="optionbox cens_text wrap">
<!--[if IE]><style>.cens_text{margin-top:-1.3em;}</style><![EndIf]-->
	<div class="cens_text_header">
		<span><input type="button" value="<?php echo WT_I18N::translate('Preview'); ?>" onclick="preview();"></span>
		<span><b><?php echo WT_I18N::translate('Proposed census text&nbsp;&nbsp;'); ?></b></span>
		<span><input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>" onclick="caSave();"></span>
	</div>
	<div class="optionbox">
		<textarea wrap="off" name="NOTE" id="NOTE"></textarea><br>
		<center>
		<?php echo print_specialchar_link('NOTE'); ?>
		</center>
	</div>
</div>
