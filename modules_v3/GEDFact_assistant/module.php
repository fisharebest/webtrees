<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
		return /* I18N: Description of the "Census assistant" module */ WT_I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
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
		global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $ABBREVIATE_CHART_LABELS;

		$controller=new WT_Controller_Simple();
		
		$type           =safe_GET('type', WT_REGEX_ALPHA, 'indi');
		$filter         =safe_GET('filter');
		$action         =safe_GET('action');
		$callback       =safe_GET('callback', WT_REGEX_NOSCRIPT, 'paste_id');
		$create         =safe_GET('create');
		$media          =safe_GET('media');
		$external_links =safe_GET('external_links');
		$directory      =safe_GET('directory', WT_REGEX_NOSCRIPT, $MEDIA_DIRECTORY);
		$multiple       =safe_GET_bool('multiple');
		$showthumb      =safe_GET_bool('showthumb');
		$all            =safe_GET_bool('all');
		$subclick       =safe_GET('subclick');
		$choose         =safe_GET('choose', WT_REGEX_NOSCRIPT, '0all');
		$level          =safe_GET('level', WT_REGEX_INTEGER, 0);
		
		if ($showthumb) {
			$thumbget='&amp;showthumb=true';
		} else {
			$thumbget='';
		}
		
		if ($subclick=='all') {
			$all=true;
		}
		
		$embed = substr($choose, 0, 1)=="1";
		$chooseType = substr($choose, 1);
		if ($chooseType!="media" && $chooseType!="0file") {
			$chooseType = "all";
		}
		
		//-- force the thumbnail directory to have the same layout as the media directory
		//-- Dots and slashes should be escaped for the preg_replace
		$srch = "/".addcslashes($MEDIA_DIRECTORY, '/.')."/";
		$repl = addcslashes($MEDIA_DIRECTORY."thumbs/", '/.');
		$thumbdir = stripcslashes(preg_replace($srch, $repl, $directory));
		
		//-- prevent script from accessing an area outside of the media directory
		//-- and keep level consistency
		if (($level < 0) || ($level > $MEDIA_DIRECTORY_LEVELS)) {
			$directory = $MEDIA_DIRECTORY;
			$level = 0;
		} elseif (preg_match("'^$MEDIA_DIRECTORY'", $directory)==0) {
			$directory = $MEDIA_DIRECTORY;
			$level = 0;
		}
		// End variables for find media
		
		// Users will probably always want the same language, so remember their setting
		if (!$language_filter) {
			$language_filter=get_user_setting(WT_USER_ID, 'default_language_filter');
		} else {
			set_user_setting(WT_USER_ID, 'default_language_filter', $language_filter);
		}
		require WT_ROOT.'includes/specialchars.php';
		// End variables for Find Special Character
		
		switch ($type) {
		case "indi":
			$controller->setPageTitle(WT_I18N::translate('Find an individual'));
			break;
		case "fam":
			$controller->setPageTitle(WT_I18N::translate('Find a family'));
			break;
		case "media":
			$controller->setPageTitle(WT_I18N::translate('Find a media object'));
			$action="filter";
			break;
		case "place":
			$controller->setPageTitle(WT_I18N::translate('Find a place'));
			$action="filter";
			break;
		case "repo":
			$controller->setPageTitle(WT_I18N::translate('Find a repository'));
			$action="filter";
			break;
		case "note":
			$controller->setPageTitle(WT_I18N::translate('Find a note'));
			$action="filter";
			break;
		case "source":
			$controller->setPageTitle(WT_I18N::translate('Find a source'));
			$action="filter";
			break;
		case "specialchar":
			$controller->setPageTitle(WT_I18N::translate('Find a special character'));
			$action="filter";
			break;
		}
		$controller->pageHeader();
		
		echo '<script>';
		?>
		
			function pasterow(id, name, gend, yob, age, bpl) {
				window.opener.opener.insertRowToTable(id, name, '', gend, '', yob, age, 'Y', '', bpl);
				<?php // if (!$multiple) echo "window.close();"; ?>
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
		
		$options = array();
		$options["option"][]= "findindi";
		$options["option"][]= "findfam";
		$options["option"][]= "findmedia";
		$options["option"][]= "findplace";
		$options["option"][]= "findrepo";
		$options["option"][]= "findnote";
		$options["option"][]= "findsource";
		$options["option"][]= "findspecialchar";
		$options["form"][]= "formindi";
		$options["form"][]= "formfam";
		$options["form"][]= "formmedia";
		$options["form"][]= "formplace";
		$options["form"][]= "formrepo";
		$options["form"][]= "formnote";
		$options["form"][]= "formsource";
		$options["form"][]= "formspecialchar";
		
		echo "<div align=\"center\">";
		echo "<table class=\"list_table width90\" border=\"0\">";
		echo "<tr><td style=\"padding: 10px;\" valign=\"top\" class=\"facts_label03 width90\">"; // start column for find text header
		
		switch ($type) {
		case "indi":
			echo WT_I18N::translate('Find an individual');
			break;
		case "fam":
			echo WT_I18N::translate('Find a family');
			break;
		case "media":
			echo WT_I18N::translate('Find a media object');
			break;
		case "place":
			echo WT_I18N::translate('Find a place');
			break;
		case "repo":
			echo WT_I18N::translate('Find a repository');
			break;
		case "note":
			echo WT_I18N::translate('Find a note');
			break;
		case "source":
			echo WT_I18N::translate('Find a source');
			break;
		case "specialchar":
			echo WT_I18N::translate('Find a special character');
			break;
		}
		
		echo "</td>"; // close column for find text header
		
		// start column for find options
		echo "</tr><tr><td class=\"list_value\" style=\"padding: 0px;\">";
		
		// Show indi and hide the rest
		if ($type == "indi") {
			echo "<div align=\"center\">";
			echo "<form name=\"filterindi\" method=\"get\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"indi\">";
			echo "<input type=\"hidden\" name=\"multiple\" value=\"$multiple\">";
		/*
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Name contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" value=\"", WT_I18N::translate('Filter'), "\"><br>";
			echo "</td></tr></table>";
		*/
			echo "</form></div>";
		}
		
		// Show fam and hide the rest
		if ($type == "fam") {
			echo "<div align=\"center\">";
			echo "<form name=\"filterfam\" method=\"get\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"fam\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"multiple\" value=\"$multiple\">";
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Name contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" value=\"", WT_I18N::translate('Filter'), "\"><br>";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		
		// Show media and hide the rest
		if ($type == 'media') {
			echo "<div align=\"center\">";
			echo "<form name=\"filtermedia\" method=\"get\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"choose\" value=\"", $choose, "\">";
			echo "<input type=\"hidden\" name=\"directory\" value=\"", $directory, "\">";
			echo "<input type=\"hidden\" name=\"thumbdir\" value=\"", $thumbdir, "\">";
			echo "<input type=\"hidden\" name=\"level\" value=\"", $level, "\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"media\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Media contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo help_link('simple_filter');
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" wstyle=\"padding: 5px;\">";
			echo "<input type=\"checkbox\" name=\"showthumb\" value=\"true\"";
			if ($showthumb) echo "checked=\"checked\"";
			echo "onclick=\"#\" onclick=\"this.form.submit();\">", WT_I18N::translate('Show thumbnails');
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" name=\"search\" value=\"", WT_I18N::translate('Filter'), "\" onclick=\"this.form.subclick.value=this.name\">&nbsp;";
			echo "<input type=\"submit\" name=\"all\" value=\"", WT_I18N::translate('Display all'), "\" onclick=\"this.form.subclick.value=this.name\">";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		
		// Show place and hide the rest
		if ($type == "place") {
			echo "<div align=\"center\">";
			echo "<form name=\"filterplace\" method=\"get\"  onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"place\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Place contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" name=\"search\" value=\"", WT_I18N::translate('Filter'), "\" onclick=\"this.form.subclick.value=this.name\">&nbsp;";
			echo "<input type=\"submit\" name=\"all\" value=\"", WT_I18N::translate('Display all'), "\" onclick=\"this.form.subclick.value=this.name\">";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		
		// Show repo and hide the rest
		if ($type == "repo") {
			echo "<div align=\"center\">";
			echo "<form name=\"filterrepo\" method=\"get\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"repo\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Repository contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" name=\"search\" value=\"", WT_I18N::translate('Filter'), "\" onclick=\"this.form.subclick.value=this.name\">&nbsp;";
			echo "<input type=\"submit\" name=\"all\" value=\"", WT_I18N::translate('Display all'), "\" onclick=\"this.form.subclick.value=this.name\">";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		
		// Show Shared Notes and hide the rest
		if ($type == "note") {
			echo "<div align=\"center\">";
			echo "<form name=\"filternote\" method=\"get\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"note\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Shared Note contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" name=\"search\" value=\"", WT_I18N::translate('Filter'), "\" onclick=\"this.form.subclick.value=this.name\">&nbsp;";
			echo "<input type=\"submit\" name=\"all\" value=\"", WT_I18N::translate('Display all'), "\" onclick=\"this.form.subclick.value=this.name\">";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		
		// Show source and hide the rest
		if ($type == "source") {
			echo "<div align=\"center\">";
			echo "<form name=\"filtersource\" method=\"get\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"source\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"$callback\">";
			echo "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo WT_I18N::translate('Source contains:'), " <input type=\"text\" name=\"filter\" value=\"";
			if ($filter) echo $filter;
			echo "\">";
			echo "</td></tr>";
			echo "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
			echo "<input type=\"submit\" name=\"search\" value=\"", WT_I18N::translate('Filter'), "\" onclick=\"this.form.subclick.value=this.name\">&nbsp;";
			echo "<input type=\"submit\" name=\"all\" value=\"", WT_I18N::translate('Display all'), "\" onclick=\"this.form.subclick.value=this.name\">";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		
		// Show specialchar and hide the rest
		if ($type == "specialchar") {
			echo "<div align=\"center\">";
			echo "<form name=\"filterspecialchar\" method=\"get\" action=\"find.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"filter\">";
			echo "<input type=\"hidden\" name=\"type\" value=\"specialchar\">";
			echo "<input type=\"hidden\" name=\"callback\" value=\"", $callback, "\">";
			echo "<input type=\"hidden\" name=\"magnify\" value=\"", $magnify, "\">";
			echo "<table class=\"list_table width100\" border=\"0\">";
			echo "<tr><td class=\"list_label\" style=\"padding: 5px;\">";
			echo "<select id=\"language_filter\" name=\"language_filter\" onchange=\"submit();\">";
			echo "<option value=\"\">", WT_I18N::translate('Change language'), "</option>";
			$language_options = "";
			foreach ($specialchar_languages as $key=>$value) {
				$language_options.= "<option value=\"$key\">$value</option>";
			}
			$language_options = str_replace("\"$language_filter\"", "\"$language_filter\" selected", $language_options);
			echo $language_options;
			echo "</select><br><a href=\"#\" onclick=\"setMagnify()\">", WT_I18N::translate('Magnify'), "</a>";
			echo "</td></tr></table>";
			echo "</form></div>";
		}
		// end column for find options
		echo "</td></tr>";
		echo "</table>"; // Close table with find options
		
		echo "<br>";
		echo "<a href=\"#\" onclick=\"window.close();\">", WT_I18N::translate('Close Window'), "</a><br>";
		echo "<br>";
		
		if ($action=="filter") {
			$filter = trim($filter);
			$filter_array=explode(' ', preg_replace('/ {2,}/', ' ', $filter));
		
			// Output Individual
			if ($type == "indi") {
				echo "<table class=\"tabs_table width90\"><tr>";
				$myindilist=search_indis_names($filter_array, array(WT_GED_ID), 'AND');
				if ($myindilist) {
					echo "<td class=\"list_value_wrap\"><ul>";
					usort($myindilist, array('WT_GedcomRecord', 'Compare'));
					foreach ($myindilist as $indi) {
						//echo $indi->format_list('li', true);
						$nam = htmlspecialchars($indi->getFullName());
						echo "<li><a href=\"#\" onclick=\"pasterow(
							'".$indi->getXref()."' ,
							'".$nam."' ,
							'".$indi->getSex()."' ,
							'".$indi->getbirthyear()."' ,
							'".(1901-$indi->getbirthyear())."' ,
							'".$indi->getbirthplace()."'); return false;\">
							<b>".$indi->getFullName()."</b>&nbsp;&nbsp;&nbsp;";
		
							if ($ABBREVIATE_CHART_LABELS) {
								$born=WT_Gedcom_Tag::getAbbreviation('BIRT');
							} else {
								$born=WT_Gedcom_Tag::getLabel('BIRT');
							}
		
		
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
			}
		
			// Output Family
			if ($type == "fam") {
				echo "<table class=\"tabs_table width90\"><tr>";
				// Get the famrecs with hits on names from the family table
				// Get the famrecs with hits in the gedcom record from the family table
				$myfamlist = array_unique(array_merge(
					search_fams_names($filter_array, array(WT_GED_ID), 'AND'),
					search_fams($filter_array, array(WT_GED_ID), 'AND', true)
				));
				if ($myfamlist) {
					$curged = $GEDCOM;
					echo "<td class=\"list_value_wrap\"><ul>";
					usort($myfamlist, array('WT_GedcomRecord', 'Compare'));
					foreach ($myfamlist as $family) {
						echo $family->format_list('li', true);
					}
					echo '</ul></td></tr><tr><td class="list_label">', WT_I18N::translate('Total families: %s', count($myfamlist)), '</tr></td>';
				} else {
					echo "<td class=\"list_value_wrap\">";
					echo WT_I18N::translate('No results found.');
					echo "</td></tr>";
				}
				echo "</table>";
			}
		
			// Output Media
			if ($type == "media") {
				global $dirs;
		
				$medialist = get_medialist(true, $directory);
		
				echo "<table class=\"tabs_table width90\">";
				// Show link to previous folder
				if ($level>0) {
					$levels = explode("/", $directory);
					$pdir = "";
					for ($i=0; $i<count($levels)-2; $i++) $pdir.=$levels[$i]."/";
					$levels = explode("/", $thumbdir);
					$pthumb = "";
					for ($i=0; $i<count($levels)-2; $i++) $pthumb.=$levels[$i]."/";
					$uplink = "<a href=\"find.php?directory=".rawurlencode($pdir)."&amp;thumbdir=".rawurlencode($pthumb)."&amp;level=".($level-1)."{$thumbget}&amp;type=media&amp;choose={$choose}\">&nbsp;&nbsp;&nbsp;&lt;-- <span dir=\"ltr\">".$pdir."</span>&nbsp;&nbsp;&nbsp;</a><br>";
				}
		
				// Start of media directory table
				echo "<table class=\"list_table width90\">";
		
				// Tell the user where he is
				echo "<tr>";
					echo "<td class=\"topbottombar\" colspan=\"2\">";
						echo WT_I18N::translate('Current directory');
						echo "<br>";
						echo substr($directory, 0, -1);
					echo "</td>";
				echo "</tr>";
		
				// display the directory list
				if (count($dirs) || $level) {
					sort($dirs);
					if ($level) {
						echo "<tr><td class=\"list_value\" colspan=\"2\">";
						echo $uplink, "</td></tr>";
					}
					echo "<tr><td class=\"descriptionbox\" colspan=\"2\">";
					echo "<a href=\"find.php?directory=".rawurlencode($directory)."&amp;thumbdir=".str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $directory)."&amp;level={$level}{$thumbget}&amp;external_links=http&amp;type=media&amp;choose={$choose}\">", WT_I18N::translate('External objects'), "</a>";
					echo "</td></tr>";
					foreach ($dirs as $indexval => $dir) {
						echo "<tr><td class=\"list_value\" colspan=\"2\">";
						echo "<a href=\"find.php?directory=".rawurlencode($directory.$dir)."/&amp;thumbdir=".rawurlencode($directory.$dir)."/&amp;level=".($level+1)."{$thumbget}&amp;type=media&amp;choose={$choose}\"><span dir=\"ltr\">", $dir, "</span></a>";
						echo "</td></tr>";
					}
				}
				echo "<tr><td class=\"descriptionbox\" colspan=\"2\"></td></tr>";
		
				/**
				 * This action generates a thumbnail for the file
				 *
				 * @name $create->thumbnail
				 */
				if ($create=="thumbnail") {
					$filename = $_REQUEST["file"];
					generate_thumbnail($directory.$filename, $thumbdir.$filename);
				}
		
				echo "<br>";
		
				// display the images TODO x across if lots of files??
				if (count($medialist) > 0) {
					foreach ($medialist as $indexval => $media) {
		
						// Check if the media belongs to the current folder
						preg_match_all("/\//", $media["FILE"], $hits);
						$ct = count($hits[0]);
		
						if (($ct <= $level+1 && $external_links != "http" && !isFileExternal($media["FILE"])) || (isFileExternal($media["FILE"]) && $external_links == "http")) {
							// simple filter to reduce the number of items to view
							$isvalid = filterMedia($media, $filter, 'http');
							if ($isvalid && $chooseType!="all") {
								if ($chooseType=="0file" && !empty($media["XREF"])) $isvalid = false; // skip linked media files
								if ($chooseType=="media" && empty($media["XREF"])) $isvalid = false; // skip unlinked media files
							}
							if ($isvalid) {
								if ($media["EXISTS"] && media_filesize($media["FILE"]) != 0) {
									$imgsize = findImageSize($media["FILE"]);
									$imgwidth = $imgsize[0]+40;
									$imgheight = $imgsize[1]+150;
								}
								else {
									$imgwidth = 0;
									$imgheight = 0;
								}
		
								echo "<tr>";
		
								//-- thumbnail field
								if ($showthumb) {
									echo "<td class=\"list_value width10\">";
									if (isset($media["THUMB"])) echo "<a href=\"#\" onclick=\"return openImage('", rawurlencode($media["FILE"]), "', $imgwidth, $imgheight);\"><img src=\"", filename_decode($media["THUMB"]), "\" width=\"50\" alt=\"\"></a>";
									else echo "&nbsp;";
								}
		
								//-- name and size field
								echo "<td class=\"list_value\">";
								if ($media["TITL"] != "") {
									echo "<b>", htmlspecialchars($media["TITL"]), "</b><br>";
								}
								if (!$embed) {
									echo "<a href=\"#\" onclick=\"pasteid('", addslashes($media["FILE"]), "');\"><span dir=\"ltr\">", $media["FILE"], "</span></a> -- ";
								}
								else echo "<a href=\"#\" onclick=\"pasteid('", $media["XREF"], "','", addslashes($media["TITL"]), "','", addslashes($media["THUMB"]), "');\"><span dir=\"ltr\">", $media["FILE"], "</span></a> -- ";
								echo "<a href=\"#\" onclick=\"return openImage('", rawurlencode($media["FILE"]), "', $imgwidth, $imgheight);\">", WT_I18N::translate('View'), "</a><br>";
								if (!$media["EXISTS"] && !isFileExternal($media["FILE"])) echo $media["FILE"], "<br><span class=\"error\">", WT_I18N::translate('The filename entered does not exist.'), "</span><br>";
								else if (!isFileExternal($media["FILE"]) && !empty($imgsize[0])) {
									echo WT_Gedcom_Tag::getLabelValue('__IMAGE_SIZE__', $imgsize[0].' × '.$imgsize[1]);
								}
								if ($media["LINKED"]) {
									echo WT_I18N::translate('This media object is linked to the following:'), "<br>";
									foreach ($media["LINKS"] as $indi => $type_record) {
										if ($type_record!='INDI' && $type_record!='FAM' && $type_record!='SOUR' && $type_record!='OBJE') continue;
										$record=WT_GedcomRecord::getInstance($indi);
										echo '<br><a href="', $record->getHtmlUrl(), '">';
										switch($type_record) {
										case 'INDI':
											echo WT_I18N::translate('View Person'), ' - ';
											break;
										case 'FAM':
											echo WT_I18N::translate('View Family'), ' - ';
											break;
										case 'SOUR':
											echo WT_I18N::translate('View Source'), ' - ';
											break;
										case 'OBJE':
											echo WT_I18N::translate('View Object'), ' - ';
											break;
										}
										echo $record->getFullName(), '</a>';
									}
								} else {
									echo WT_I18N::translate('This media object is not linked to any GEDCOM record.');
								}
								echo "</td>";
							}
						}
					}
				}
				else {
					echo "<tr><td class=\"list_value_wrap\">";
					echo WT_I18N::translate('No results found.');
					echo "</td></tr>";
				}
				echo "</table>";
			}
		
			// Output Places
			if ($type == "place") {
				echo "<table class=\"tabs_table width90\"><tr>";
				$placelist = array();
				if ($all || $filter) {
					$placelist=find_place_list($filter);
					$ctplace = count($placelist);
					if ($ctplace>0) {
						$revplacelist = array();
						foreach ($placelist as $indexval => $place) {
							$levels = explode(',', $place); // -- split the place into comma seperated values
							$levels = array_reverse($levels); // -- reverse the array so that we get the top level first
							$placetext = "";
							$j=0;
							foreach ($levels as $indexval => $level) {
								if ($j>0) $placetext .= ", ";
								$placetext .= trim($level);
								$j++;
							}
							$revplacelist[] = $placetext;
						}
						uasort($revplacelist, "utf8_strcasecmp");
						echo "<td class=\"list_value_wrap\"><ul>";
						foreach ($revplacelist as $place) {
							echo "<li><a href=\"#\" onclick=\"pasteid('", str_replace(array("'", '"'), array("\'", '&quot;'), $place), "');\">", htmlspecialchars($place), "</a></li>";
						}
						echo "</ul></td></tr>";
						echo "<tr><td class=\"list_label\">", WT_I18N::translate('Places found'), " ", $ctplace;
						echo "</td></tr>";
					}
					else {
						echo "<tr><td class=\"list_value_wrap\"><ul>";
						echo WT_I18N::translate('No results found.');
						echo "</td></tr>";
					}
				}
				echo "</table>";
			}
		
			// Output Repositories
			if ($type == "repo") {
				echo "<table class=\"tabs_table width90\"><tr>";
				$repo_list = get_repo_list(WT_GED_ID);
				if ($repo_list) {
					echo "<td class=\"list_value_wrap\"><ul>";
					foreach ($repo_list as $repo) {
						echo '<li><a href="', $repo->getHtmlUrl(), '" onclick="pasteid(\'', $repo->getXref(), '\');"><span class="list_item">', $repo->getFullName(),'</span></a></li>';
					}
					echo "</ul></td></tr>";
					echo "<tr><td class=\"list_label\">", WT_I18N::translate('Repositories found'), " ", count($repo_list);
					echo "</td></tr>";
				}
				else {
					echo "<tr><td class=\"list_value_wrap\">";
					echo WT_I18N::translate('No results found.');
					echo "</td></tr>";
				}
				echo "</table>";
			}
		
			// Output Shared Notes
			if ($type=="note") {
				echo '<table class="tabs_table width90">';
				if ($filter) {
					$mynotelist = search_notes($filter_array, array(WT_GED_ID), 'AND', true);
				} else {
					$mynotelist = get_note_list(WT_GED_ID);
				}
				if ($mynotelist) {
					usort($mynotelist, array('WT_GedcomRecord', 'Compare'));
					echo '<tr><td class="list_value_wrap"><ul>';
					foreach ($mynotelist as $note) {
						echo '<li><a href="', $note->getHtmlUrl(), '" onclick="pasteid(\'', $note->getXref(), '\');"><span class="list_item">', $note->getFullName(),'</span></a></li>';
					}
					echo '</ul></td></tr><tr><td class="list_label">', WT_I18N::translate('Shared Notes found'), ' ', count($mynotelist), '</td></tr>';
				}
				else {
					echo '<tr><td class="list_value_wrap">', WT_I18N::translate('No results found.'), '</td></tr>';
				}
				echo '</table>';
			}
		
			// Output Sources
			if ($type=="source") {
				echo '<table class="tabs_table width90">';
				if ($filter) {
					$mysourcelist = search_sources($filter_array, array(WT_GED_ID), 'AND', true);
				} else {
					$mysourcelist = get_source_list(WT_GED_ID);
				}
				if ($mysourcelist) {
					usort($mysourcelist, array('WT_GedcomRecord', 'Compare'));
					echo '<tr><td class="list_value_wrap"><ul>';
					foreach ($mysourcelist as $source) {
						echo '<li><a href="', $source->getHtmlUrl(), '" onclick="pasteid(\'', $source->getXref(), '\');"><span class="list_item">', $source->getFullName(),'</span></a></li>';
					}
					echo '</ul></td></tr><tr><td class="list_label">', WT_I18N::translate('Total sources: %s', count($mysourcelist)), '</td></tr>';
				}
				else {
					echo '<tr><td class="list_value_wrap">', WT_I18N::translate('No results found.'), '</td></tr>';
				}
				echo '</table>';
			}
		
			// Output Special Characters
			if ($type == "specialchar") {
				echo "<table class=\"tabs_table width90\"><tr><td class=\"list_value center wrap\"><br>";
				// lower case special characters
				foreach ($lcspecialchars as $key=>$value) {
					echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $value, '\');">', $key, '</a> ';
				}
				echo '<br><br>';
				//upper case special characters
				foreach ($ucspecialchars as $key=>$value) {
					echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $value, '\');">', $key, '</a> ';
				}
				echo '<br><br>';
				// other special characters (not letters)
				foreach ($otherspecialchars as $key=>$value) {
					echo '<a class="largechars" href="#" onclick="return window.opener.paste_char(\'', $value, '\');">', $key, '</a> ';
				}
				echo '<br><br></td></tr></table>';
			}
		}
		echo "</div>"; // Close div that centers table
		
		// Set focus to the input field
		echo '<script>document.filter', $type, '.filter.focus();</script>';
	}

	private static function media_query_3a() {
		$iid2 = safe_GET('iid');

		$controller=new WT_Controller_Simple();
		$controller->setPageTitle(WT_I18N::translate('Link media'));
		$controller->pageHeader();
		
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
			'<script>'
			function insertId() {
				if (window.opener.document.getElementById('addlinkQueue')) {
					// alert('Please move this alert window and examine the contents of the pop-up window, then click OK')
					window.opener.insertRowToTable("<?php echo $record->getXref(); ?>", "<?php echo htmlSpecialChars($record->getFullName()); ?>", "<?php echo $headjs; ?>");
					window.close();
				}
			}
			'</script>'
			<?php
		
		} else {
			?>
			'<sccript>'
			function insertId() {
				window.opener.alert('<?php echo strtoupper($iid2); ?> - <?php echo WT_I18N::translate('Not a valid Individual, Family or Source ID'); ?>');
				window.close();
			}
			'</script>'
			<?php
		}
		?>		
		'<script>'window.onLoad = insertId();'</script>'
		<?php
	}
}
