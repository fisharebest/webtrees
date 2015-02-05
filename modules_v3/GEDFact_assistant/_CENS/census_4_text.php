<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

?>
<!-- The proposed Census Text -->
<div class="optionbox cens_text wrap">
<!--[if IE]><style>.cens_text{margin-top:-1.3em;}</style><![EndIf]-->
	<div class="cens_text_header">
		<span><input type="button" value="<?php echo I18N::translate('Preview'); ?>" onclick="preview();"></span>
		<span><b><?php echo I18N::translate('Proposed census text&nbsp;&nbsp;'); ?></b></span>
		<span><input type="submit" value="<?php echo I18N::translate('Save'); ?>" onclick="caSave();"></span>
	</div>
	<div class="optionbox">
		<textarea wrap="off" name="NOTE" id="NOTE"></textarea><br>
		<center>
		<?php echo print_specialchar_link('NOTE'); ?>
		</center>
	</div>
</div>
