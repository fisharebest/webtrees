<?php
/**
 * Sitemap configuration User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008  PGV Development Team. All rights reserved.
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
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

if (WT_USER_IS_ADMIN) { ?>
   <tr>
	  <td colspan="2" class="topbottombar" style="text-align:center; "><?php echo i18n::translate('Sitemap'); ?></td>
   </tr>
   <tr>
      <td class="optionbox"><a href="module.php?mod=sitemap"><?php print i18n::translate('Generate Sitemap files');?></a><?php echo help_link('SITEMAP','sitemap');?>
	  </td>
      <td class="optionbox">&nbsp;
	  </td>
   </tr>
<?php }

?>
