<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @subpackage Modules
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class media_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return i18n::translate('Media');
	}

	// Extend WT_Module
	public function getDescription() {
		return i18n::translate('Adds a tab to the individual page which displays the media items linked to an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 50;
	}
	
	protected $mediaCount = null;

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $MULTI_MEDIA;
		return ($MULTI_MEDIA && $this->get_media_count()>0);
	}
	
	// Implement WT_Module_Tab
	public function getTabContent() {
		global $MULTI_MEDIA, $NAV_MEDIA;
		
		ob_start();
		// For Reorder media ------------------------------------
		if (WT_USER_CAN_EDIT) {
			echo "<center>";
			require_once './includes/media_tab_head.php';
			echo "</center>";
		}
		?>
		<div id="media_content">
		<table class="facts_table">
		<?php
		$media_found = false;
		if (!$this->controller->indi->canDisplayDetails()) {
			print "<tr><td class=\"facts_value\">";
			print_privacy_error();
			print "</td></tr>";
		}
		else {
			$media_found = print_main_media($this->controller->pid, 0, true);
			if (!$media_found) print "<tr><td id=\"no_tab4\" colspan=\"2\" class=\"facts_value\">".i18n::translate('There are no media objects for this individual.')."</td></tr>\n";
			//-- New Media link
			if (!$this->controller->isPrintPreview() && WT_USER_CAN_EDIT && $this->controller->indi->canDisplayDetails()) {
		?>
				<tr>
					<td class="facts_label"><?php echo i18n::translate('Add media'), help_link('add_media'); ?></td>
					<td class="facts_value">
						<a href="javascript:;" onclick="window.open('addmedia.php?action=showmediaform&linktoid=<?php print $this->controller->pid; ?>', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;"> <?php echo i18n::translate('Add a new media item'); ?></a><br />
						<a href="javascript:;" onclick="window.open('inverselink.php?linktoid=<?php print $this->controller->pid; ?>&linkto=person', '_blank', 'top=50,left=50,width=400,height=300,resizable=1,scrollbars=1'); return false;"><?php echo i18n::translate('Link to an existing Media item'); ?></a>
					</td>
				</tr>
			<?php
			}
		}
		?>
		</table>
			</div>
	<?php
		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}
	
	/**
	* get the number of media items for this person
	* @return int
	*/
	function get_media_count() {
		if ($this->mediaCount===null) {
			$ct = preg_match("/\d OBJE/", $this->controller->indi->getGedcomRecord());
			foreach ($this->controller->indi->getSpouseFamilies() as $k=>$sfam)
				$ct += preg_match("/\d OBJE/", $sfam->getGedcomRecord());
			$this->mediaCount = $ct;
		}
		return $this->mediaCount;
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}
	
	// Implement WT_Module_Tab
	public function getJSCallback() {
		return '';
	}
	
}
