<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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

class random_media_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return WT_I18N::translate('Random Media');
	}

	// Extend class WT_Module
	public function getDescription() {
		return WT_I18N::translate('The Random Media block randomly selects a photo or other media item from the currently active database and displays it to the user.<br /><br />The administrator determines whether this block can show media items associated with persons or events.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $foundlist, $MULTI_MEDIA, $TEXT_DIRECTION, $WT_IMAGES;
		global $MEDIA_EXTERNAL, $MEDIA_DIRECTORY;
		global $MEDIATYPE, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;

		if (!$MULTI_MEDIA) return;

		$filter  =get_block_setting($block_id, 'filter',   'all');
		$controls=get_block_setting($block_id, 'controls', true);
		$start   =get_block_setting($block_id, 'start',    false) || safe_GET_bool('start');
		$block   =get_block_setting($block_id, 'block',    true);

		$filters=array(
			'avi'        =>get_block_setting($block_id, 'filter_avi', false),
			'bmp'        =>get_block_setting($block_id, 'filter_bmp', true),
			'gif'        =>get_block_setting($block_id, 'filter_gif', true),
			'jpg'        =>get_block_setting($block_id, 'filter_jpeg', true),
			'jpeg'       =>get_block_setting($block_id, 'filter_jpeg', true),
			'mp3'        =>get_block_setting($block_id, 'filter_mp3', false),
			'ole'        =>get_block_setting($block_id, 'filter_ole', true),
			'pcx'        =>get_block_setting($block_id, 'filter_pcx', true),
			'pdf'        =>get_block_setting($block_id, 'filter_pdf', false),
			'png'        =>get_block_setting($block_id, 'filter_png', true),
			'tiff'       =>get_block_setting($block_id, 'filter_tiff', true),
			'wav'        =>get_block_setting($block_id, 'filter_wav', false),
			'audio'      =>get_block_setting($block_id, 'filter_audio', false),
			'book'       =>get_block_setting($block_id, 'filter_book', true),
			'card'       =>get_block_setting($block_id, 'filter_card', true),
			'certificate'=>get_block_setting($block_id, 'filter_certificate', true),
			'coat'       =>get_block_setting($block_id, 'filter_coat', true),
			'document'   =>get_block_setting($block_id, 'filter_document', true),
			'electronic' =>get_block_setting($block_id, 'filter_electronic', true),
			'fiche'      =>get_block_setting($block_id, 'filter_fiche', true),
			'film'       =>get_block_setting($block_id, 'filter_film', true),
			'magazine'   =>get_block_setting($block_id, 'filter_magazine', true),
			'manuscript' =>get_block_setting($block_id, 'filter_manuscript', true),
			'map'        =>get_block_setting($block_id, 'filter_map', true),
			'newspaper'  =>get_block_setting($block_id, 'filter_newspaper', true),
			'other'      =>get_block_setting($block_id, 'filter_other', true),
			'painting'   =>get_block_setting($block_id, 'filter_painting', true),
			'photo'      =>get_block_setting($block_id, 'filter_photo', true),
			'tombstone'  =>get_block_setting($block_id, 'filter_tombstone', true),
			'video'      =>get_block_setting($block_id, 'filter_video', false),
		);
		if ($cfg) {
			foreach (array('filter', 'controls', 'start', 'filter_avi', 'filter_bmp', 'filter_gif', 'filter_jpeg', 'filter_mp3', 'filter_ole', 'filter_pcx', 'filter_pdf', 'filter_png', 'filter_tiff', 'filter_wav', 'filter_audio', 'filter_book', 'filter_card', 'filter_certificate', 'filter_coat', 'filter_document', 'filter_electronic', 'filter_fiche', 'filter_film', 'filter_magazine', 'filter_manuscript', 'filter_map', 'filter_newspaper', 'filter_other', 'filter_painting', 'filter_photo', 'filter_tombstone', 'filter_video', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		$medialist = array();
		$foundlist = array();

		$medialist = get_medialist(false, '', true, true);
		$ct = count($medialist);
		if ($ct>0) {
			$i=0;
			$disp = false;
			//-- try up to 40 times to get a media to display
			while ($i<40) {
				$error = false;
				$value = array_rand($medialist);
				if (WT_DEBUG) {
					echo "<br />";print_r($medialist[$value]);echo "<br />";
					echo "Trying ".$medialist[$value]["XREF"]."<br />";
				}
				$links = $medialist[$value]["LINKS"];
				$disp = ($medialist[$value]["EXISTS"]>0) && $medialist[$value]["LINKED"] && $medialist[$value]["CHANGE"]!="delete" ;
				if (WT_DEBUG && !$disp && !$error) {$error = true; echo "<span class=\"error\">".$medialist[$value]["XREF"]." File does not exist, or is not linked to anyone, or is marked for deletion.</span><br />";}

				$disp &= canDisplayRecord($medialist[$value]["GEDFILE"], $medialist[$value]["GEDCOM"]);

				if (WT_DEBUG && !$disp && !$error) {$error = true; echo "<span class=\"error\">".$medialist[$value]["XREF"]." Failed to pass privacy</span><br />";}

				$isExternal = isFileExternal($medialist[$value]["FILE"]);

				if ($block && !$isExternal) $disp &= ($medialist[$value]["THUMBEXISTS"]>0);
				if (WT_DEBUG && !$disp && !$error) {$error = true; echo "<span class=\"error\">".$medialist[$value]["XREF"]." thumbnail file could not be found</span><br />";}

				// Filter according to format and type  (Default: unless configured otherwise, don't filter)
				if ($medialist[$value]['FORM']!='' && !array_key_exists($medialist[$value]['FORM'], $filters)) {
					$disp=false;
				} elseif ($medialist[$value]['TYPE']!='' && !array_key_exists($medialist[$value]['TYPE'], $filters)) {
					$disp=false;
				} elseif (!empty($medialist[$value]["FORM"]) && !$filters[$medialist[$value]["FORM"]]) {
					$disp=false;
				} elseif (!empty($medialist[$value]["TYPE"]) && !$filters[$medialist[$value]["TYPE"]]) {
					$disp=false;
				}
				if (WT_DEBUG && !$disp && !$error) {$error = true; echo "<span class=\"error\">".$medialist[$value]["XREF"]." failed Format or Type filters</span><br />";
				}

				if ($disp && count($links) != 0) {
					if ($disp && $filter!="all") {
						// Apply filter criteria
						$ct = preg_match("/0 (@.*@) OBJE/", $medialist[$value]["GEDCOM"], $match);
						$objectID = $match[1];
						//-- we could probably use the database for this filter
						foreach ($links as $key=>$type) {
							$gedrec = find_gedcom_record($key, WT_GED_ID);
							$ct2 = preg_match("/(\d) OBJE {$objectID}/", $gedrec, $match2);
							if ($ct2>0) {
								$objectRefLevel = $match2[1];
								if ($filter=="indi" && $objectRefLevel!="1") $disp = false;
								if ($filter=="event" && $objectRefLevel=="1") $disp = false;
								if (WT_DEBUG && !$disp && !$error) {$error = true; echo "<span class=\"error\">".$medialist[$value]["XREF"]." failed to pass config filter</span><br />";}
							}
							else $disp = false;
						}
					}
				}
				//-- leave the loop if we find an image that works
				if ($disp) {
					break;
				}
				//-- otherwise remove the private media item from the list
				else {
					if (WT_DEBUG) echo "<span class=\"error\">".$medialist[$value]["XREF"]." Will not be shown</span><br />";
					unset($medialist[$value]);
				}
				//-- if there are no more media items, then try to get some more
				if (count($medialist)==0) $medialist = get_medialist(false, '', true, true);
				$i++;
			}
			if (!$disp) {
				return false;
			}
			$id=$this->getName().$block_id;
			$title='';
			$content = '';
			if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
				if ($ctype=="gedcom") {
					$name = WT_GEDCOM;
				} else {
					$name = WT_USER_NAME;
				}
				$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
				$title .= "<img class=\"adminicon\" src=\"{$WT_IMAGES['admin']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".WT_I18N::translate('Configure').'" /></a>';
			}
			$title .= WT_I18N::translate('Random Picture');
			$title .= help_link('index_media');
			$content = "<div id=\"random_picture_container$block_id\">";
			if ($controls) {
				if ($start) {
					$image = "stop";
				} else {
					$image = "rarrow";
				}
				$linkNextImage = "<a href=\"javascript: ".WT_I18N::translate('Next image')."\" onclick=\"jQuery('#block_{$block_id}').load('index.php?action=ajax&block_id={$block_id}');return false;\"><img src=\"{$WT_IMAGES['rdarrow']}\" border=\"0\" alt=\"".WT_I18N::translate('Next image')."\" title=\"".WT_I18N::translate('Next image')."\" /></a>";

				$content .= "<div class=\"center\" id=\"random_picture_controls$block_id\"><br />";
				if ($TEXT_DIRECTION=="rtl") $content .= $linkNextImage;
				$content .= "<a href=\"javascript: ".WT_I18N::translate('Play')."/".WT_I18N::translate('Stop')."\" onclick=\"togglePlay(); return false;\">";
				if (isset($WT_IMAGES[$image])) $content .= "<img id=\"play_stop\" src=\"{$WT_IMAGES[$image]}\" border=\"0\" alt=\"".WT_I18N::translate('Play')."/".WT_I18N::translate('Stop')."\" title=\"".WT_I18N::translate('Play')."/".WT_I18N::translate('Stop')."\" />";
				else $content .= WT_I18N::translate('Play')."/".WT_I18N::translate('Stop');
				$content .= "</a>";
				if ($TEXT_DIRECTION=="ltr") $content .= $linkNextImage;
				$content .= '</div>'.WT_JS_START.'
					var play = false;
						function togglePlay() {
							if (play) {
								play = false;
								imgid = document.getElementById("play_stop");
								imgid.src = \''.$WT_IMAGES["rarrow"].'\';
							}
							else {
								play = true;
								playSlideShow();
								imgid = document.getElementById("play_stop");
								imgid.src = \''.$WT_IMAGES["stop"].'\';
							}
						}

						function playSlideShow() {
							if (play) {
								window.setTimeout(\'reload_image()\', 6000);
							}
						}
						function reload_image() {
							jQuery(\'#block_'.$block_id.'\').load(\'index.php?ctype='.$ctype.'&action=ajax&block_id='.$block_id.'&start=1\');
						}
					'.WT_JS_END;
			}
			if ($start) {
				$content .= WT_JS_START.'togglePlay();'.WT_JS_END;
			}
			$content .= "<div class=\"center\" id=\"random_picture_content$block_id\">";
			$imgsize = findImageSize($medialist[$value]["FILE"]);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$content .= "<table id=\"random_picture_box\" width=\"100%\"><tr><td valign=\"top\"";

			if ($block) $content .= " align=\"center\" class=\"details1\"";
			else $content .= " class=\"details2\"";
			$mediaid = $medialist[$value]["XREF"];

//LBox --------  change for Lightbox Album --------------------------------------------
?>
<script type="text/javascript">
<!--
function openPic(filename, width, height) {
		height=height+50;
		screenW = screen.width;
		screenH = screen.height;
		if (width>screenW-100) width=screenW-100;
		if (height>screenH-110) height=screenH-120;
		if ((filename.search(/\.je?pg$/gi)!=-1)||(filename.search(/\.gif$/gi)!=-1)||(filename.search(/\.png$/gi)!=-1)||(filename.search(/\.bmp$/gi)!=-1))
			win02 = window.open('imageview.php?filename='+filename,'win02','top=50,left=150,height='+height+',width='+width+',scrollbars=1,resizable=1');
			// win03.resizeTo(winWidth 2,winHeight 30);
		else window.open(unescape(filename),'win02','top=50,left=150,height='+height+',width='+width+',scrollbars=1,resizable=1');
		win02.focus();
	}
-->
</script><?php

			if (WT_USE_LIGHTBOX) {
				// $content .= " ><a href=\"javascript:;\" onclick=\"return openPic('".$medialist[$value]["FILE"]."', $imgwidth, $imgheight);\">";
				// $content .= " ><a href=\"javascript:;\" onclick=\"return openImage('".$medialist[$value]["FILE"]."', $imgwidth, $imgheight);\">";
				// $content .= "><a href=\"" . $medialist[$value]["FILE"] . "\" rel=\"clearbox[general_4]\" title=\"" . $mediaid . "\">";
				$content .= " ><a href=\"mediaviewer.php?mid=".$mediaid."\">";
			} else
// ---------------------------------------------------------------------------------------------


			if ($USE_MEDIA_VIEWER) {
					$content .= " ><a href=\"mediaviewer.php?mid=".$mediaid."\">";
			}
			else {
					$content .= " ><a href=\"javascript:;\" onclick=\"return openImage('".$medialist[$value]["FILE"]."', $imgwidth, $imgheight);\">";
			}
			$mediaTitle = "";
			if (!empty($medialist[$value]["TITL"])) {
				$mediaTitle = PrintReady($medialist[$value]["TITL"]);
			}
			else $mediaTitle = basename($medialist[$value]["FILE"]);
			if ($block) {
				$content .= "<img src=\"".$medialist[$value]["THUMB"]."\" border=\"0\" class=\"thumbnail\"";
				if ($isExternal) $content .= " width=\"".$THUMBNAIL_WIDTH."\"";
			} else {
				$content .= "<img src=\"".$medialist[$value]["FILE"]."\" border=\"0\" class=\"thumbnail\" ";
				$imgsize = findImageSize($medialist[$value]["FILE"]);
				if ($imgsize[0] > 175) $content .= "width=\"175\" ";
			}
			$content .= " alt=\"{$mediaTitle}\" title=\"{$mediaTitle}\" />";
			$content .= "</a>";
			if ($block) $content .= "<br />";
			else $content .= "</td><td class=\"details2\">";
			$content .= "<a href=\"mediaviewer.php?mid=".$mediaid."\">";
			$content .= "<b>". $mediaTitle ."</b>";
			$content .= "</a><br />";

			ob_start();
			PrintMediaLinks($medialist[$value]["LINKS"], "normal");
			$content .= ob_get_clean();
			$content .= "<br /><div class=\"indent" . ($TEXT_DIRECTION=="rtl"?"_rtl":"") . "\">";
			$content .= print_fact_notes($medialist[$value]["GEDCOM"], "1", false, true);
			$content .= "</div>";
			$content .= "</td></tr></table>";
			$content .= "</div>"; // random_picture_content
			$content .= "</div>"; // random_picture_container
			if ($template) {
				require WT_THEME_DIR.'templates/block_main_temp.php';
			} else {
				return $content;
			}
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'filter',             safe_POST('filter', array('indi', 'event', 'all'), 'all'));
			set_block_setting($block_id, 'controls',           safe_POST_bool('controls'));
			set_block_setting($block_id, 'start',              safe_POST_bool('start'));
			set_block_setting($block_id, 'filter_avi',         safe_POST_bool('filter_avi'));
			set_block_setting($block_id, 'filter_bmp',         safe_POST_bool('filter_bmp'));
			set_block_setting($block_id, 'filter_gif',         safe_POST_bool('filter_gif'));
			set_block_setting($block_id, 'filter_jpeg',        safe_POST_bool('filter_jpeg'));
			set_block_setting($block_id, 'filter_mp3',         safe_POST_bool('filter_mp3'));
			set_block_setting($block_id, 'filter_ole',         safe_POST_bool('filter_ole'));
			set_block_setting($block_id, 'filter_pcx',         safe_POST_bool('filter_pcx'));
			set_block_setting($block_id, 'filter_pdf',         safe_POST_bool('filter_pdf'));
			set_block_setting($block_id, 'filter_png',         safe_POST_bool('filter_png'));
			set_block_setting($block_id, 'filter_tiff',        safe_POST_bool('filter_tiff'));
			set_block_setting($block_id, 'filter_wav',         safe_POST_bool('filter_wav'));
			set_block_setting($block_id, 'filter_audio',       safe_POST_bool('filter_audio'));
			set_block_setting($block_id, 'filter_book',        safe_POST_bool('filter_book'));
			set_block_setting($block_id, 'filter_card',        safe_POST_bool('filter_card'));
			set_block_setting($block_id, 'filter_certificate', safe_POST_bool('filter_certificate'));
			set_block_setting($block_id, 'filter_coat',        safe_POST_bool('filter_coat'));
			set_block_setting($block_id, 'filter_document',    safe_POST_bool('filter_document'));
			set_block_setting($block_id, 'filter_electronic',  safe_POST_bool('filter_electronic'));
			set_block_setting($block_id, 'filter_fiche',       safe_POST_bool('filter_fiche'));
			set_block_setting($block_id, 'filter_film',        safe_POST_bool('filter_film'));
			set_block_setting($block_id, 'filter_magazine',    safe_POST_bool('filter_magazine'));
			set_block_setting($block_id, 'filter_manuscript',  safe_POST_bool('filter_manuscript'));
			set_block_setting($block_id, 'filter_map',         safe_POST_bool('filter_map'));
			set_block_setting($block_id, 'filter_newspaper',   safe_POST_bool('filter_newspaper'));
			set_block_setting($block_id, 'filter_other',       safe_POST_bool('filter_other'));
			set_block_setting($block_id, 'filter_painting',    safe_POST_bool('filter_painting'));
			set_block_setting($block_id, 'filter_photo',       safe_POST_bool('filter_photo'));
			set_block_setting($block_id, 'filter_tombstone',   safe_POST_bool('filter_tombstone'));
			set_block_setting($block_id, 'filter_video',       safe_POST_bool('filter_video'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$filter=get_block_setting($block_id, 'filter', 'all');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show only persons, events, or all?'), help_link('random_media_persons_or_all');
		echo '</td><td class="optionbox">';
		echo select_edit_control('filter', array('indi'=>WT_I18N::translate('Persons'), 'event'=>WT_I18N::translate('Events'), 'all'=>WT_I18N::translate('All')), null, $filter, '');
		echo '</td></tr>';

		$filters=array(
			'avi'        =>get_block_setting($block_id, 'filter_avi', false),
			'bmp'        =>get_block_setting($block_id, 'filter_bmp', true),
			'gif'        =>get_block_setting($block_id, 'filter_gif', true),
			'jpeg'       =>get_block_setting($block_id, 'filter_jpeg', true),
			'mp3'        =>get_block_setting($block_id, 'filter_mp3', false),
			'ole'        =>get_block_setting($block_id, 'filter_ole', true),
			'pcx'        =>get_block_setting($block_id, 'filter_pcx', true),
			'pdf'        =>get_block_setting($block_id, 'filter_pdf', false),
			'png'        =>get_block_setting($block_id, 'filter_png', true),
			'tiff'       =>get_block_setting($block_id, 'filter_tiff', true),
			'wav'        =>get_block_setting($block_id, 'filter_wav', false),
			'audio'      =>get_block_setting($block_id, 'filter_audio', false),
			'book'       =>get_block_setting($block_id, 'filter_book', true),
			'card'       =>get_block_setting($block_id, 'filter_card', true),
			'certificate'=>get_block_setting($block_id, 'filter_certificate', true),
			'coat'       =>get_block_setting($block_id, 'filter_coat', true),
			'document'   =>get_block_setting($block_id, 'filter_document', true),
			'electronic' =>get_block_setting($block_id, 'filter_electronic', true),
			'fiche'      =>get_block_setting($block_id, 'filter_fiche', true),
			'film'       =>get_block_setting($block_id, 'filter_film', true),
			'magazine'   =>get_block_setting($block_id, 'filter_magazine', true),
			'manuscript' =>get_block_setting($block_id, 'filter_manuscript', true),
			'map'        =>get_block_setting($block_id, 'filter_map', true),
			'newspaper'  =>get_block_setting($block_id, 'filter_newspaper', true),
			'other'      =>get_block_setting($block_id, 'filter_other', true),
			'painting'   =>get_block_setting($block_id, 'filter_painting', true),
			'photo'      =>get_block_setting($block_id, 'filter_photo', true),
			'tombstone'  =>get_block_setting($block_id, 'filter_tombstone', true),
			'video'      =>get_block_setting($block_id, 'filter_video', false),
		);

		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Filter'), help_link('random_media_filter');
?>
	</td>
		<td class="optionbox">
			<center><b><?php echo WT_Gedcom_Tag::getLabel('FORM'); ?></b></center>
			<table class="width100">
				<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_avi"
				<?php if ($filters['avi']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;avi&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_bmp"
				<?php if ($filters['bmp']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;bmp&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_gif"
				<?php if ($filters['gif']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;gif&nbsp;&nbsp;</td>
				</tr>
		<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_jpeg"
				<?php if ($filters['jpeg']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;jpeg&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_mp3"
				<?php if ($filters['mp3']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;mp3&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_ole"
				<?php if ($filters['ole']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;ole&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_pcx"
				<?php if ($filters['pcx']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;pcx&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_pdf"
				<?php if ($filters['pdf']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;pdf&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_png"
				<?php if ($filters['png']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;png&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_tiff"
				<?php if ($filters['tiff']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;tiff&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_wav"
				<?php if ($filters['wav']) echo " checked=\"checked\""; ?> />&nbsp;&nbsp;wav&nbsp;&nbsp;</td>
					<td class="width33">&nbsp;</td>
					<td class="width33">&nbsp;</td>
				</tr>
			</table>
			<br />
			<center><b><?php echo WT_Gedcom_Tag::getLabel('TYPE'); ?></b></center>
			<table class="width100">
				<tr>
				<?php
				//-- Build the list of checkboxes
				$i = 0;
				foreach (WT_Gedcom_Tag::getFileFormTypes() as $typeName => $typeValue) {
					$i++;
					if ($i > 3) {
						$i = 1;
						echo "</tr><tr>";
					}
					echo "<td class=\"width33\"><input type=\"checkbox\" value=\"yes\" name=\"filter_".$typeName."\"";
					if ($filters[$typeName]) echo " checked=\"checked\"";
					echo " />&nbsp;&nbsp;".$typeValue."&nbsp;&nbsp;</td>";
				}
				?>
				</tr>
			</table>
	</td>
	</tr>

	<?php

		$controls=get_block_setting($block_id, 'controls', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show slideshow controls?'), help_link('random_media_ajax_controls');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('controls', $controls);
		echo '</td></tr>';

		$start=get_block_setting($block_id, 'start', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Start slideshow on page load?'), help_link('random_media_start_slide');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('start', $start);
		echo '</td></tr>';
	}
}
