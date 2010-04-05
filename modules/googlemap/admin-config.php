<?php
/**
 * Googlemap configuration User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 01 September 2005
 *
 * @package webtrees
 * @subpackage Admin
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require 'modules/googlemap/defaultconfig.php';

if (file_exists('modules/googlemap/config.php')) {
	require 'modules/googlemap/config.php';
}

print_header(i18n::translate('GoogleMap Configuration'));

if (WT_USER_IS_ADMIN) { ?>
<table class="center <?php print $TEXT_DIRECTION ?>">
   <tr>
	  <td colspan="2" class="topbottombar" style="text-align:center; "><?php echo i18n::translate('GoogleMap Configuration'); ?></td>
   </tr>
   <tr>
      <td class="optionbox"><a href="module.php?mod=googlemap&pgvaction=editconfig"><?php echo i18n::translate('Manage GoogleMap configuration');?></a><?php echo help_link('GOOGLEMAP_CONFIG','googlemap'); ?>
	  </td>
      <td class="optionbox"><a href="module.php?mod=googlemap&pgvaction=places"><?php echo i18n::translate('Edit geographic place locations');?></a><?php echo help_link('PLE_EDIT','googlemap'); ?>
	  </td>
   </tr>
   <tr>
      <td class="optionbox"><a href="module.php?mod=googlemap&pgvaction=placecheck"><?php echo i18n::translate('Place Check');?></a><?php echo help_link('GOOGLEMAP_PLACECHECK','googlemap'); ?>
	  </td>
      <td class="optionbox">&nbsp;
	  </td>
   </tr>
</table>
<?php 
print_footer();
} else {
	header("Location: login.php?url=module.php?mod=googlemap&pgvaction=admin-config");
	exit;
}
?>
