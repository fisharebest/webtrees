<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class GEDFact_assistant_WT_Module extends WT_Module {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Census assistant');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Census assistant” module */ WT_I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case '_CENS/census_3_find':
			// TODO: this file should be a method in this class
			require WT_ROOT.WT_MODULES_DIR.$this->getName().'/_CENS/census_3_find.php';
			break;
		case 'media_3_find':
			self::media_3_find();
			break;
		case 'media_query_3a':
			self::media_query_3a();
			break;
		default:
			echo $mod_action;
			header('HTTP/1.0 404 Not Found');
		}
	}

	private static function media_3_find() {
		global $MEDIA_DIRECTORY, $ABBREVIATE_CHART_LABELS;

		$controller=new WT_Controller_Simple();
		
		$type           ='indi';
		$filter         =safe_GET('filter');
		$action         =safe_GET('action');
		$callback       ='paste_id';
		$media          =safe_GET('media');
		$external_links =safe_GET('external_links');
		$directory      =safe_GET('directory', WT_REGEX_NOSCRIPT, $MEDIA_DIRECTORY);
		$multiple       =safe_GET_bool('multiple');
		$showthumb      =safe_GET_bool('showthumb');
		$all            =safe_GET_bool('all');
		$subclick       =safe_GET('subclick');
		$choose         =safe_GET('choose', WT_REGEX_NOSCRIPT, '0all');
		
		$controller
			->setPageTitle(WT_I18N::translate('Find an individual'))
			->pageHeader();
		
		echo '<script>';
		?>
		
			function pasterow(id, name, gend, yob, age, bpl) {
				window.opener.opener.insertRowToTable(id, name, '', gend, '', yob, age, 'Y', '', bpl);
			}
		
			function pasteid(id, name, thumb) {
				if (thumb) {
					window.opener.<?php echo $callback; ?>(id, name, thumb);
					<?php if (!$multiple) echo "window.close();"; ?>
				} else {
					// GEDFact_assistant ========================
					if (window.opener.document.getElementById('addlinkQueue')) {
						window.opener.insertRowToTable(id, name);
						// Check if Indi, Fam or source ===================
						/*
						if (id.match("I")=="I") {
							var win01 = window.opener.window.open('edit_interface.php?action=addmedia_links&noteid=newnote&pid='+id, 'win01', edit_window_specs);
							if (window.focus) {win01.focus();}
						} else if (id.match("F")=="F") {
							// TODO --- alert('Opening Navigator with family id entered will come later');
						}
						*/
					}
					window.opener.<?php echo $callback; ?>(id);
					if (window.opener.pastename) window.opener.pastename(name);
					<?php if (!$multiple) echo "window.close();"; ?>
				}
			}
			function checknames(frm) {
				if (document.forms[0].subclick) button = document.forms[0].subclick.value;
				else button = "";
				if (frm.filter.value.length<2&button!="all") {
					alert("<?php echo WT_I18N::translate('Please enter more than one character'); ?>");
					frm.filter.focus();
					return false;
				}
				if (button=="all") {
					frm.filter.value = "";
				}
				return true;
			}
		<?php
		echo '</script>';
		
		echo "<div align=\"center\">";
		echo "<table class=\"list_table width90\" border=\"0\">";
		echo "<tr><td style=\"padding: 10px;\" valign=\"top\" class=\"facts_label03 width90\">"; // start column for find text header
		echo $controller->getPageTitle();
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<br>";
		echo '<button onclick="window.close();">', WT_I18N::translate('close'), '</button>';
		echo "<br>";
		
		$filter = trim($filter);
		$filter_array=explode(' ', preg_replace('/ {2,}/', ' ', $filter));
		echo "<table class=\"tabs_table width90\"><tr>";
		$myindilist=search_indis_names($filter_array, array(WT_GED_ID), 'AND');
		if ($myindilist) {
			echo "<td class=\"list_value_wrap\"><ul>";
			usort($myindilist, array('WT_GedcomRecord', 'Compare'));
			foreach ($myindilist as $indi) {
				$nam = htmlspecialchars($indi->getFullName());
				echo "<li><a href=\"#\" onclick=\"pasterow(
					'".$indi->getXref()."' ,
					'".$nam."' ,
					'".$indi->getSex()."' ,
					'".$indi->getbirthyear()."' ,
					'".(1901-$indi->getbirthyear())."' ,
					'".$indi->getbirthplace()."'); return false;\">
					<b>".$indi->getFullName()."</b>&nbsp;&nbsp;&nbsp;";
	
				$born=WT_Gedcom_Tag::getLabel('BIRT');
				echo "</span><br><span class=\"list_item\">", $born, " ", $indi->getbirthyear(), "&nbsp;&nbsp;&nbsp;", $indi->getbirthplace(), "</span></a></li>";
			echo "<hr>";
			}
			echo '</ul></td></tr><tr><td class="list_label">', WT_I18N::translate('Total individuals: %s', count($myindilist)), '</tr></td>';
		} else {
			echo "<td class=\"list_value_wrap\">";
			echo WT_I18N::translate('No results found.');
			echo "</td></tr>";
		}
		echo "</table>";
		echo '</div>';
	}

	private static function media_query_3a() {
		$iid2 = safe_GET('iid');

		$controller=new WT_Controller_Simple();
		$controller
			->setPageTitle(WT_I18N::translate('Link to an existing media object'))
			->pageHeader();
		
		$record=WT_GedcomRecord::getInstance($iid2);
		if ($record) {
			$headjs='';
			if ($record->getType()=='FAM') {
				if ($record->getHusband()) {
					$headjs=$record->getHusband()->getXref();
				} elseif ($record->getWife()) {
					$headjs=$record->getWife()->getXref();
				}
			}
			?>
			<script>
			function insertId() {
				if (window.opener.document.getElementById('addlinkQueue')) {
					// alert('Please move this alert window and examine the contents of the pop-up window, then click OK')
					window.opener.insertRowToTable('<?php echo $record->getXref(); ?>', '<?php echo htmlSpecialChars($record->getFullName()); ?>', '<?php echo $headjs; ?>');
					window.close();
				}
			}
			</script>
			<?php
		
		} else {
			?>
			<script>
			function insertId() {
				window.opener.alert('<?php echo strtoupper($iid2); ?> - <?php echo WT_I18N::translate('Not a valid Individual, Family or Source ID'); ?>');
				window.close();
			}
			</script>
			<?php
		}
		?>		
		<script>window.onLoad = insertId();</script>
		<?php
	}
}
