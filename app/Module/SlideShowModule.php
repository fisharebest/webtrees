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

/**
 * Class SlideShowModule
 */
class SlideShowModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Slide show');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Slide show” module */ I18N::translate('Random images from the current family tree.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $ctype;

		$filter   = get_block_setting($block_id, 'filter', 'all');
		$controls = get_block_setting($block_id, 'controls', '1');
		$start    = get_block_setting($block_id, 'start', '0') || Filter::getBool('start');

		// We can apply the filters using SQL
		// Do not use "ORDER BY RAND()" - it is very slow on large tables.  Use PHP::array_rand() instead.
		$all_media = Database::prepare(
			"SELECT m_id FROM `##media`" .
			" WHERE m_file = ?" .
			" AND m_ext  IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')" .
			" AND m_type IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')"
		)->execute(array(
			WT_GED_ID,
			get_block_setting($block_id, 'filter_avi', '0') ? 'avi' : null,
			get_block_setting($block_id, 'filter_bmp', '1') ? 'bmp' : null,
			get_block_setting($block_id, 'filter_gif', '1') ? 'gif' : null,
			get_block_setting($block_id, 'filter_jpeg', '1') ? 'jpg' : null,
			get_block_setting($block_id, 'filter_jpeg', '1') ? 'jpeg' : null,
			get_block_setting($block_id, 'filter_mp3', '0') ? 'mp3' : null,
			get_block_setting($block_id, 'filter_ole', '1') ? 'ole' : null,
			get_block_setting($block_id, 'filter_pcx', '1') ? 'pcx' : null,
			get_block_setting($block_id, 'filter_pdf', '0') ? 'pdf' : null,
			get_block_setting($block_id, 'filter_png', '1') ? 'png' : null,
			get_block_setting($block_id, 'filter_tiff', '1') ? 'tiff' : null,
			get_block_setting($block_id, 'filter_wav', '0') ? 'wav' : null,
			get_block_setting($block_id, 'filter_audio', '0') ? 'audio' : null,
			get_block_setting($block_id, 'filter_book', '1') ? 'book' : null,
			get_block_setting($block_id, 'filter_card', '1') ? 'card' : null,
			get_block_setting($block_id, 'filter_certificate', '1') ? 'certificate' : null,
			get_block_setting($block_id, 'filter_coat', '1') ? 'coat' : null,
			get_block_setting($block_id, 'filter_document', '1') ? 'document' : null,
			get_block_setting($block_id, 'filter_electronic', '1') ? 'electronic' : null,
			get_block_setting($block_id, 'filter_fiche', '1') ? 'fiche' : null,
			get_block_setting($block_id, 'filter_film', '1') ? 'film' : null,
			get_block_setting($block_id, 'filter_magazine', '1') ? 'magazine' : null,
			get_block_setting($block_id, 'filter_manuscript', '1') ? 'manuscript' : null,
			get_block_setting($block_id, 'filter_map', '1') ? 'map' : null,
			get_block_setting($block_id, 'filter_newspaper', '1') ? 'newspaper' : null,
			get_block_setting($block_id, 'filter_other', '1') ? 'other' : null,
			get_block_setting($block_id, 'filter_painting', '1') ? 'painting' : null,
			get_block_setting($block_id, 'filter_photo', '1') ? 'photo' : null,
			get_block_setting($block_id, 'filter_tombstone', '1') ? 'tombstone' : null,
			get_block_setting($block_id, 'filter_video', '0') ? 'video' : null,
		))->fetchOneColumn();

		// Keep looking through the media until a suitable one is found.
		$random_media = null;
		while ($all_media) {
			$n = array_rand($all_media);
			$media = Media::getInstance($all_media[$n]);
			if ($media->canShow() && !$media->isExternal()) {
				// Check if it is linked to a suitable individual
				foreach ($media->linkedIndividuals('OBJE') as $indi) {
					if (
						$filter === 'all' ||
						$filter === 'indi' && strpos($indi->getGedcom(), "\n1 OBJE @" . $media->getXref() . '@') !== false ||
						$filter === 'event' && strpos($indi->getGedcom(), "\n2 OBJE @" . $media->getXref() . '@') !== false
					) {
						// Found one :-)
						$random_media = $media;
						break 2;
					}
				}
			}
			unset($all_media[$n]);
		};

		$id = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		if ($random_media) {
			$content = "<div id=\"random_picture_container$block_id\">";
			if ($controls) {
				if ($start) {
					$icon_class = 'icon-media-stop';
				} else {
					$icon_class = 'icon-media-play';
				}
				$content .= '<div dir="ltr" class="center" id="random_picture_controls' . $block_id . '"><br>';
				$content .= "<a href=\"#\" onclick=\"togglePlay(); return false;\" id=\"play_stop\" class=\"" . $icon_class . "\" title=\"" . I18N::translate('Play') . "/" . I18N::translate('Stop') . '"></a>';
				$content .= '<a href="#" onclick="jQuery(\'#block_' . $block_id . '\').load(\'index.php?ctype=' . $ctype . '&amp;action=ajax&amp;block_id=' . $block_id . '\');return false;" title="' . I18N::translate('Next image') . '" class="icon-media-next"></a>';
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
								jQuery("#block_'.$block_id . '").load("index.php?ctype=' . $ctype . '&action=ajax&block_id=' . $block_id . '&start=1");
							}
						}
					</script>';
			}
			if ($start) {
				$content .= '<script>togglePlay();</script>';
			}
			$content .= '<div class="center" id="random_picture_content' . $block_id . '">';
			$content .= '<table id="random_picture_box"><tr><td class="details1">';
			$content .= $random_media->displayImage();

			$content .= '<br>';
			$content .= '<a href="' . $random_media->getHtmlUrl() . '"><b>' . $random_media->getFullName() . '</b></a><br>';
			foreach ($random_media->linkedIndividuals('OBJE') as $individual) {
				$content .= '<a href="' . $individual->getHtmlUrl() . '">' . I18N::translate('View individual') . ' — ' . $individual->getFullname() . '</a><br>';
			}
			foreach ($random_media->linkedFamilies('OBJE') as $family) {
				$content .= '<a href="' . $family->getHtmlUrl() . '">' . I18N::translate('View family') . ' — ' . $family->getFullname() . '</a><br>';
			}
			foreach ($random_media->linkedSources('OBJE') as $source) {
				$content .= '<a href="' . $source->getHtmlUrl() . '">' . I18N::translate('View source') . ' — ' . $source->getFullname() . '</a><br>';
			}
			$content .= '<br><div class="indent">';
			$content .= print_fact_notes($random_media->getGedcom(), "1", false);
			$content .= '</div>';
			$content .= '</td></tr></table>';
			$content .= '</div>'; // random_picture_content
			$content .= '</div>'; // random_picture_container
		} else {
			$content = I18N::translate('This family tree has no images to display.');
		}
		if ($template) {
			echo Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			set_block_setting($block_id, 'filter', Filter::post('filter', 'indi|event|all', 'all'));
			set_block_setting($block_id, 'controls', Filter::postBool('controls'));
			set_block_setting($block_id, 'start', Filter::postBool('start'));
			set_block_setting($block_id, 'filter_avi', Filter::postBool('filter_avi'));
			set_block_setting($block_id, 'filter_bmp', Filter::postBool('filter_bmp'));
			set_block_setting($block_id, 'filter_gif', Filter::postBool('filter_gif'));
			set_block_setting($block_id, 'filter_jpeg', Filter::postBool('filter_jpeg'));
			set_block_setting($block_id, 'filter_mp3', Filter::postBool('filter_mp3'));
			set_block_setting($block_id, 'filter_ole', Filter::postBool('filter_ole'));
			set_block_setting($block_id, 'filter_pcx', Filter::postBool('filter_pcx'));
			set_block_setting($block_id, 'filter_pdf', Filter::postBool('filter_pdf'));
			set_block_setting($block_id, 'filter_png', Filter::postBool('filter_png'));
			set_block_setting($block_id, 'filter_tiff', Filter::postBool('filter_tiff'));
			set_block_setting($block_id, 'filter_wav', Filter::postBool('filter_wav'));
			set_block_setting($block_id, 'filter_audio', Filter::postBool('filter_audio'));
			set_block_setting($block_id, 'filter_book', Filter::postBool('filter_book'));
			set_block_setting($block_id, 'filter_card', Filter::postBool('filter_card'));
			set_block_setting($block_id, 'filter_certificate', Filter::postBool('filter_certificate'));
			set_block_setting($block_id, 'filter_coat', Filter::postBool('filter_coat'));
			set_block_setting($block_id, 'filter_document', Filter::postBool('filter_document'));
			set_block_setting($block_id, 'filter_electronic', Filter::postBool('filter_electronic'));
			set_block_setting($block_id, 'filter_fiche', Filter::postBool('filter_fiche'));
			set_block_setting($block_id, 'filter_film', Filter::postBool('filter_film'));
			set_block_setting($block_id, 'filter_magazine', Filter::postBool('filter_magazine'));
			set_block_setting($block_id, 'filter_manuscript', Filter::postBool('filter_manuscript'));
			set_block_setting($block_id, 'filter_map', Filter::postBool('filter_map'));
			set_block_setting($block_id, 'filter_newspaper', Filter::postBool('filter_newspaper'));
			set_block_setting($block_id, 'filter_other', Filter::postBool('filter_other'));
			set_block_setting($block_id, 'filter_painting', Filter::postBool('filter_painting'));
			set_block_setting($block_id, 'filter_photo', Filter::postBool('filter_photo'));
			set_block_setting($block_id, 'filter_tombstone', Filter::postBool('filter_tombstone'));
			set_block_setting($block_id, 'filter_video', Filter::postBool('filter_video'));
		}

		$filter   = get_block_setting($block_id, 'filter', 'all');
		$controls = get_block_setting($block_id, 'controls', '1');
		$start    = get_block_setting($block_id, 'start', '0') || Filter::getBool('start');

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Show only individuals, events, or all?');
		echo '</td><td class="optionbox">';
		echo select_edit_control('filter', array('indi'=> I18N::translate('Individuals'), 'event'=> I18N::translate('Facts and events'), 'all'=> I18N::translate('All')), null, $filter, '');
		echo '</td></tr>';

		$filters = array(
			'avi'        =>get_block_setting($block_id, 'filter_avi', '0'),
			'bmp'        =>get_block_setting($block_id, 'filter_bmp', '1'),
			'gif'        =>get_block_setting($block_id, 'filter_gif', '1'),
			'jpeg'       =>get_block_setting($block_id, 'filter_jpeg', '1'),
			'mp3'        =>get_block_setting($block_id, 'filter_mp3', '0'),
			'ole'        =>get_block_setting($block_id, 'filter_ole', '1'),
			'pcx'        =>get_block_setting($block_id, 'filter_pcx', '1'),
			'pdf'        =>get_block_setting($block_id, 'filter_pdf', '0'),
			'png'        =>get_block_setting($block_id, 'filter_png', '1'),
			'tiff'       =>get_block_setting($block_id, 'filter_tiff', '1'),
			'wav'        =>get_block_setting($block_id, 'filter_wav', '0'),
			'audio'      =>get_block_setting($block_id, 'filter_audio', '0'),
			'book'       =>get_block_setting($block_id, 'filter_book', '1'),
			'card'       =>get_block_setting($block_id, 'filter_card', '1'),
			'certificate'=>get_block_setting($block_id, 'filter_certificate', '1'),
			'coat'       =>get_block_setting($block_id, 'filter_coat', '1'),
			'document'   =>get_block_setting($block_id, 'filter_document', '1'),
			'electronic' =>get_block_setting($block_id, 'filter_electronic', '1'),
			'fiche'      =>get_block_setting($block_id, 'filter_fiche', '1'),
			'film'       =>get_block_setting($block_id, 'filter_film', '1'),
			'magazine'   =>get_block_setting($block_id, 'filter_magazine', '1'),
			'manuscript' =>get_block_setting($block_id, 'filter_manuscript', '1'),
			'map'        =>get_block_setting($block_id, 'filter_map', '1'),
			'newspaper'  =>get_block_setting($block_id, 'filter_newspaper', '1'),
			'other'      =>get_block_setting($block_id, 'filter_other', '1'),
			'painting'   =>get_block_setting($block_id, 'filter_painting', '1'),
			'photo'      =>get_block_setting($block_id, 'filter_photo', '1'),
			'tombstone'  =>get_block_setting($block_id, 'filter_tombstone', '1'),
			'video'      =>get_block_setting($block_id, 'filter_video', '0'),
		);

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Filter');
		?>
	</td>
	<td class="optionbox">
		<center><b><?php echo GedcomTag::getLabel('FORM'); ?></b></center>
		<table class="width100">
			<tr>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_avi" <?php echo $filters['avi'] ? 'checked' : ''; ?>>
						avi
				</td>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_bmp" <?php echo $filters['bmp'] ? 'checked' : ''; ?>>
						bmp
					</label>
				</td>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_gif" <?php echo $filters['gif'] ? 'checked' : ''; ?>>
						gif
					</label>
				</td>
			</tr>
			<tr>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_jpeg" <?php echo $filters['jpeg'] ? 'checked' : ''; ?>>
						jpeg
					</label>
				</td>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_mp3" <?php echo $filters['mp3'] ? 'checked' : ''; ?>>
						mp3
					</label>
				</td>
					<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_ole" <?php echo $filters['ole'] ? 'checked' : ''; ?>>
						ole
					</label>
				</td>
			</tr>
			<tr>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_pcx" <?php echo $filters['pcx'] ? 'checked' : ''; ?>>
						pcx
					</label>
				</td>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_pdf" <?php echo $filters['pdf'] ? 'checked' : ''; ?>>
						pdf
					</label>
				</td>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_png" <?php echo $filters['png'] ? 'checked' : ''; ?>>
						png
					</label>
				</td>
			</tr>
			<tr>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_tiff" <?php echo $filters['tiff'] ? 'checked' : ''; ?>>
						tiff
					</label>
				</td>
				<td class="width33">
					<label>
						<input type="checkbox" value="yes" name="filter_wav" <?php echo $filters['wav'] ? 'checked' : ''; ?>>
						wav
					</label>
				</td>
				<td class="width33"></td>
				<td class="width33"></td>
			</tr>
		</table>
			<br>
			<center><b><?php echo GedcomTag::getLabel('TYPE'); ?></b></center>
				<table class="width100">
					<tr>
					<?php
					//-- Build the list of checkboxes
					$i = 0;
					foreach (GedcomTag::getFileFormTypes() as $typeName => $typeValue) {
						$i++;
						if ($i > 3) {
							$i = 1;
							echo '</tr><tr>';
						}
						echo '<td class="width33"><label><input type="checkbox" value="yes" name="filter_' . $typeName . '" ';
						echo ($filters[$typeName]) ? 'checked' : '';
						echo '> ' . $typeValue . '</label></td>';
					}
					?>
				</tr>
			</table>
		</td>
	</tr>

	<?php

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Show slide show controls?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('controls', $controls);
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Start slide show on page load?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('start', $start);
		echo '</td></tr>';
	}
}
