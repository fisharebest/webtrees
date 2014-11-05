<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

class random_media_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */WT_I18N::translate('Slide show');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Slide show” module */ WT_I18N::translate('Random images from the current family tree.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $foundlist;

		$filter  =get_block_setting($block_id, 'filter',   'all');
		$controls=get_block_setting($block_id, 'controls', true);
		$start   =get_block_setting($block_id, 'start',    false) || WT_Filter::getBool('start');
		$block   =get_block_setting($block_id, 'block',    true);

		// We can apply the filters using SQL
		// Do not use "ORDER BY RAND()" - it is very slow on large tables.  Use PHP::array_rand() instead.
		$all_media=WT_DB::prepare(
			"SELECT m_id FROM `##media`" .
			" WHERE m_file = ?" .
			" AND m_ext  IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')" .
			" AND m_type IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')"
		)->execute(array(
			WT_GED_ID,
			get_block_setting($block_id, 'filter_avi',         false) ? 'avi'         : NULL,
			get_block_setting($block_id, 'filter_bmp',         true ) ? 'bmp'         : NULL,
			get_block_setting($block_id, 'filter_gif',         true ) ? 'gif'         : NULL,
			get_block_setting($block_id, 'filter_jpeg',        true ) ? 'jpg'         : NULL,
			get_block_setting($block_id, 'filter_jpeg',        true ) ? 'jpeg'        : NULL,
			get_block_setting($block_id, 'filter_mp3',         false) ? 'mp3'         : NULL,
			get_block_setting($block_id, 'filter_ole',         true ) ? 'ole'         : NULL,
			get_block_setting($block_id, 'filter_pcx',         true ) ? 'pcx'         : NULL,
			get_block_setting($block_id, 'filter_pdf',         false) ? 'pdf'         : NULL,
			get_block_setting($block_id, 'filter_png',         true ) ? 'png'         : NULL,
			get_block_setting($block_id, 'filter_tiff',        true ) ? 'tiff'        : NULL,
			get_block_setting($block_id, 'filter_wav',         false) ? 'wav'         : NULL,
			get_block_setting($block_id, 'filter_audio',       false) ? 'audio'       : NULL,
			get_block_setting($block_id, 'filter_book',        true ) ? 'book'        : NULL,
			get_block_setting($block_id, 'filter_card',        true ) ? 'card'        : NULL,
			get_block_setting($block_id, 'filter_certificate', true ) ? 'certificate' : NULL,
			get_block_setting($block_id, 'filter_coat',        true ) ? 'coat'        : NULL,
			get_block_setting($block_id, 'filter_document',    true ) ? 'document'    : NULL,
			get_block_setting($block_id, 'filter_electronic',  true ) ? 'electronic'  : NULL,
			get_block_setting($block_id, 'filter_fiche',       true ) ? 'fiche'       : NULL,
			get_block_setting($block_id, 'filter_film',        true ) ? 'film'        : NULL,
			get_block_setting($block_id, 'filter_magazine',    true ) ? 'magazine'    : NULL,
			get_block_setting($block_id, 'filter_manuscript',  true ) ? 'manuscript'  : NULL,
			get_block_setting($block_id, 'filter_map',         true ) ? 'map'         : NULL,
			get_block_setting($block_id, 'filter_newspaper',   true ) ? 'newspaper'   : NULL,
			get_block_setting($block_id, 'filter_other',       true ) ? 'other'       : NULL,
			get_block_setting($block_id, 'filter_painting',    true ) ? 'painting'    : NULL,
			get_block_setting($block_id, 'filter_photo',       true ) ? 'photo'       : NULL,
			get_block_setting($block_id, 'filter_tombstone',   true ) ? 'tombstone'   : NULL,
			get_block_setting($block_id, 'filter_video',       false) ? 'video'       : NULL,
		))->fetchOneColumn();

		// Keep looking through the media until a suitable one is found.
		$random_media=null;
		while ($all_media) {
			$n=array_rand($all_media);
			$media=WT_Media::getInstance($all_media[$n]);
			if ($media->canShow() && !$media->isExternal()) {
				// Check if it is linked to a suitable individual
				foreach ($media->linkedIndividuals('OBJE') as $indi) {
					if (
						$filter === 'all' ||
						$filter === 'indi'  && strpos($indi->getGedcom(), "\n1 OBJE @" . $media->getXref() . '@') !==false ||
						$filter === 'event' && strpos($indi->getGedcom(), "\n2 OBJE @" . $media->getXref() . '@') !==false
					) {
						// Found one :-)
						$random_media=$media;
						break 2;
					}
				}
			}
			unset($all_media[$n]);
		};

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title='<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title='';
		}
		$title.=$this->getTitle();

		if ($random_media) {
			$content = "<div id=\"random_picture_container$block_id\">";
			if ($controls) {
				if ($start) {
					$icon_class = 'icon-media-stop';
				} else {
					$icon_class = 'icon-media-play';
				}
				$content .= '<div dir="ltr" class="center" id="random_picture_controls' . $block_id .'"><br>';
				$content .= "<a href=\"#\" onclick=\"togglePlay(); return false;\" id=\"play_stop\" class=\"".$icon_class."\" title=\"".WT_I18N::translate('Play')."/".WT_I18N::translate('Stop').'"></a>';
				$content .= '<a href="#" onclick="jQuery(\'#block_'.$block_id.'\').load(\'index.php?ctype='.$ctype.'&amp;action=ajax&amp;block_id='.$block_id.'\');return false;" title="'.WT_I18N::translate('Next image').'" class="icon-media-next"></a>';
				$content .= '</div><script>
					var play = false;
						function togglePlay() {
							if (play) {
								play = false;
								jQuery("#play_stop").removeClass("icon-media-stop").addClass("icon-media-play");
							}
							else {
								play = true;
								playSlideShow();
								jQuery("#play_stop").removeClass("icon-media-play").addClass("icon-media-stop");
							}
						}

						function playSlideShow() {
							if (play) {
								window.setTimeout("reload_image()", 6000);
							}
						}
						function reload_image() {
							if (play) {
								jQuery("#block_'.$block_id.'").load("index.php?ctype='.$ctype.'&action=ajax&block_id='.$block_id.'&start=1");
							}
						}
					</script>';
			}
			if ($start) {
				$content .= '<script>togglePlay();</script>';
			}
			$content .= '<div class="center" id="random_picture_content'.$block_id.'">';
			$content .= '<table id="random_picture_box"><tr><td';

			if ($block) {
				$content .= ' class="details1"';
			} else {
				$content .= ' class="details2"';
			}
			$content .= ' >';
			$content .= $random_media->displayImage();

			if ($block) {
				$content .= '<br>';
			} else {
				$content .= '</td><td class="details2">';
			}
			$content .= '<a href="'.$random_media->getHtmlUrl().'"><b>'. $random_media->getFullName() .'</b></a><br>';
			foreach ($random_media->linkedIndividuals('OBJE') as $individual) {
				$content .= '<a href="' . $individual->getHtmlUrl() . '">' . WT_I18N::translate('View person') . ' — ' . $individual->getFullname().'</a><br>';
			}
			foreach ($random_media->linkedFamilies('OBJE') as $family) {
				$content .= '<a href="' . $family->getHtmlUrl() . '">' . WT_I18N::translate('View family') . ' — ' . $family->getFullname().'</a><br>';
			}
			foreach ($random_media->linkedSources('OBJE') as $source) {
				$content .= '<a href="' . $source->getHtmlUrl() . '">' . WT_I18N::translate('View source') . ' — ' . $source->getFullname().'</a><br>';
			}
			$content .= '<br><div class="indent">';
			$content .= print_fact_notes($random_media->getGedcom(), "1", false);
			$content .= '</div>';
			$content .= '</td></tr></table>';
			$content .= '</div>'; // random_picture_content
			$content .= '</div>'; // random_picture_container
		} else {
			$content = WT_I18N::translate('This family tree has no images to display.');
		}
		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
		} else {
			return $content;
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
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'filter',             WT_Filter::post('filter', 'indi|event|all', 'all'));
			set_block_setting($block_id, 'controls',           WT_Filter::postBool('controls'));
			set_block_setting($block_id, 'start',              WT_Filter::postBool('start'));
			set_block_setting($block_id, 'filter_avi',         WT_Filter::postBool('filter_avi'));
			set_block_setting($block_id, 'filter_bmp',         WT_Filter::postBool('filter_bmp'));
			set_block_setting($block_id, 'filter_gif',         WT_Filter::postBool('filter_gif'));
			set_block_setting($block_id, 'filter_jpeg',        WT_Filter::postBool('filter_jpeg'));
			set_block_setting($block_id, 'filter_mp3',         WT_Filter::postBool('filter_mp3'));
			set_block_setting($block_id, 'filter_ole',         WT_Filter::postBool('filter_ole'));
			set_block_setting($block_id, 'filter_pcx',         WT_Filter::postBool('filter_pcx'));
			set_block_setting($block_id, 'filter_pdf',         WT_Filter::postBool('filter_pdf'));
			set_block_setting($block_id, 'filter_png',         WT_Filter::postBool('filter_png'));
			set_block_setting($block_id, 'filter_tiff',        WT_Filter::postBool('filter_tiff'));
			set_block_setting($block_id, 'filter_wav',         WT_Filter::postBool('filter_wav'));
			set_block_setting($block_id, 'filter_audio',       WT_Filter::postBool('filter_audio'));
			set_block_setting($block_id, 'filter_book',        WT_Filter::postBool('filter_book'));
			set_block_setting($block_id, 'filter_card',        WT_Filter::postBool('filter_card'));
			set_block_setting($block_id, 'filter_certificate', WT_Filter::postBool('filter_certificate'));
			set_block_setting($block_id, 'filter_coat',        WT_Filter::postBool('filter_coat'));
			set_block_setting($block_id, 'filter_document',    WT_Filter::postBool('filter_document'));
			set_block_setting($block_id, 'filter_electronic',  WT_Filter::postBool('filter_electronic'));
			set_block_setting($block_id, 'filter_fiche',       WT_Filter::postBool('filter_fiche'));
			set_block_setting($block_id, 'filter_film',        WT_Filter::postBool('filter_film'));
			set_block_setting($block_id, 'filter_magazine',    WT_Filter::postBool('filter_magazine'));
			set_block_setting($block_id, 'filter_manuscript',  WT_Filter::postBool('filter_manuscript'));
			set_block_setting($block_id, 'filter_map',         WT_Filter::postBool('filter_map'));
			set_block_setting($block_id, 'filter_newspaper',   WT_Filter::postBool('filter_newspaper'));
			set_block_setting($block_id, 'filter_other',       WT_Filter::postBool('filter_other'));
			set_block_setting($block_id, 'filter_painting',    WT_Filter::postBool('filter_painting'));
			set_block_setting($block_id, 'filter_photo',       WT_Filter::postBool('filter_photo'));
			set_block_setting($block_id, 'filter_tombstone',   WT_Filter::postBool('filter_tombstone'));
			set_block_setting($block_id, 'filter_video',       WT_Filter::postBool('filter_video'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$filter=get_block_setting($block_id, 'filter', 'all');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show only individuals, events, or all?');
		echo '</td><td class="optionbox">';
		echo select_edit_control('filter', array('indi'=>WT_I18N::translate('Individuals'), 'event'=>WT_I18N::translate('Facts and events'), 'all'=>WT_I18N::translate('All')), null, $filter, '');
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
		echo WT_I18N::translate('Filter');
?>
	</td>
		<td class="optionbox">
			<center><b><?php echo WT_Gedcom_Tag::getLabel('FORM'); ?></b></center>
			<table class="width100">
				<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_avi"
				<?php if ($filters['avi']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;avi&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_bmp"
				<?php if ($filters['bmp']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;bmp&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_gif"
				<?php if ($filters['gif']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;gif&nbsp;&nbsp;</td>
				</tr>
		<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_jpeg"
				<?php if ($filters['jpeg']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;jpeg&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_mp3"
				<?php if ($filters['mp3']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;mp3&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_ole"
				<?php if ($filters['ole']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;ole&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_pcx"
				<?php if ($filters['pcx']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;pcx&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_pdf"
				<?php if ($filters['pdf']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;pdf&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_png"
				<?php if ($filters['png']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;png&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_tiff"
				<?php if ($filters['tiff']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;tiff&nbsp;&nbsp;</td>
			<td class="width33"><input type="checkbox" value="yes"
				name="filter_wav"
				<?php if ($filters['wav']) echo " checked=\"checked\""; ?>>&nbsp;&nbsp;wav&nbsp;&nbsp;</td>
					<td class="width33">&nbsp;</td>
					<td class="width33">&nbsp;</td>
				</tr>
			</table>
			<br>
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
					echo ">&nbsp;&nbsp;".$typeValue."&nbsp;&nbsp;</td>";
				}
				?>
				</tr>
			</table>
	</td>
	</tr>

	<?php

		$controls=get_block_setting($block_id, 'controls', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show slide show controls?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('controls', $controls);
		echo '</td></tr>';

		$start=get_block_setting($block_id, 'start', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Start slide show on page load?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('start', $start);
		echo '</td></tr>';
	}
}
