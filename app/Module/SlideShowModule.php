<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Theme;

/**
 * Class SlideShowModule
 */
class SlideShowModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Slide show');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Slide show” module */ I18N::translate('Random images from the current family tree.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = array()) {
		global $ctype, $WT_TREE;

		$filter   = $this->getBlockSetting($block_id, 'filter', 'all');
		$controls = $this->getBlockSetting($block_id, 'controls', '1');
		$start    = $this->getBlockSetting($block_id, 'start', '0') || Filter::getBool('start');

		// We can apply the filters using SQL
		// Do not use "ORDER BY RAND()" - it is very slow on large tables. Use PHP::array_rand() instead.
		$all_media = Database::prepare(
			"SELECT m_id FROM `##media`" .
			" WHERE m_file = ?" .
			" AND m_ext  IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')" .
			" AND m_type IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')"
		)->execute(array(
			$WT_TREE->getTreeId(),
			$this->getBlockSetting($block_id, 'filter_avi', '0') ? 'avi' : null,
			$this->getBlockSetting($block_id, 'filter_bmp', '1') ? 'bmp' : null,
			$this->getBlockSetting($block_id, 'filter_gif', '1') ? 'gif' : null,
			$this->getBlockSetting($block_id, 'filter_jpeg', '1') ? 'jpg' : null,
			$this->getBlockSetting($block_id, 'filter_jpeg', '1') ? 'jpeg' : null,
			$this->getBlockSetting($block_id, 'filter_mp3', '0') ? 'mp3' : null,
			$this->getBlockSetting($block_id, 'filter_ole', '1') ? 'ole' : null,
			$this->getBlockSetting($block_id, 'filter_pcx', '1') ? 'pcx' : null,
			$this->getBlockSetting($block_id, 'filter_pdf', '0') ? 'pdf' : null,
			$this->getBlockSetting($block_id, 'filter_png', '1') ? 'png' : null,
			$this->getBlockSetting($block_id, 'filter_tiff', '1') ? 'tiff' : null,
			$this->getBlockSetting($block_id, 'filter_wav', '0') ? 'wav' : null,
			$this->getBlockSetting($block_id, 'filter_audio', '0') ? 'audio' : null,
			$this->getBlockSetting($block_id, 'filter_book', '1') ? 'book' : null,
			$this->getBlockSetting($block_id, 'filter_card', '1') ? 'card' : null,
			$this->getBlockSetting($block_id, 'filter_certificate', '1') ? 'certificate' : null,
			$this->getBlockSetting($block_id, 'filter_coat', '1') ? 'coat' : null,
			$this->getBlockSetting($block_id, 'filter_document', '1') ? 'document' : null,
			$this->getBlockSetting($block_id, 'filter_electronic', '1') ? 'electronic' : null,
			$this->getBlockSetting($block_id, 'filter_fiche', '1') ? 'fiche' : null,
			$this->getBlockSetting($block_id, 'filter_film', '1') ? 'film' : null,
			$this->getBlockSetting($block_id, 'filter_magazine', '1') ? 'magazine' : null,
			$this->getBlockSetting($block_id, 'filter_manuscript', '1') ? 'manuscript' : null,
			$this->getBlockSetting($block_id, 'filter_map', '1') ? 'map' : null,
			$this->getBlockSetting($block_id, 'filter_newspaper', '1') ? 'newspaper' : null,
			$this->getBlockSetting($block_id, 'filter_other', '1') ? 'other' : null,
			$this->getBlockSetting($block_id, 'filter_painting', '1') ? 'painting' : null,
			$this->getBlockSetting($block_id, 'filter_photo', '1') ? 'photo' : null,
			$this->getBlockSetting($block_id, 'filter_tombstone', '1') ? 'tombstone' : null,
			$this->getBlockSetting($block_id, 'filter_video', '0') ? 'video' : null,
		))->fetchOneColumn();

		// Keep looking through the media until a suitable one is found.
		$random_media = null;
		while ($all_media) {
			$n     = array_rand($all_media);
			$media = Media::getInstance($all_media[$n], $WT_TREE);
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

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
			$title = '<a class="icon-admin" title="' . I18N::translate('Configure') . '" href="block_edit.php?block_id=' . $block_id . '&amp;ged=' . $WT_TREE->getNameHtml() . '&amp;ctype=' . $ctype . '"></a>';
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
								jQuery("#block_' . $block_id . '").load("index.php?ctype=' . $ctype . '&action=ajax&block_id=' . $block_id . '&start=1");
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
				$content .= '<a href="' . $individual->getHtmlUrl() . '">' . I18N::translate('View the individual') . ' — ' . $individual->getFullName() . '</a><br>';
			}
			foreach ($random_media->linkedFamilies('OBJE') as $family) {
				$content .= '<a href="' . $family->getHtmlUrl() . '">' . I18N::translate('View the family') . ' — ' . $family->getFullName() . '</a><br>';
			}
			foreach ($random_media->linkedSources('OBJE') as $source) {
				$content .= '<a href="' . $source->getHtmlUrl() . '">' . I18N::translate('View the source') . ' — ' . $source->getFullName() . '</a><br>';
			}
			$content .= '<br><div class="indent">';
			$content .= FunctionsPrint::printFactNotes($random_media->getGedcom(), "1", false);
			$content .= '</div>';
			$content .= '</td></tr></table>';
			$content .= '</div>'; // random_picture_content
			$content .= '</div>'; // random_picture_container
		} else {
			$content = I18N::translate('This family tree has no images to display.');
		}
		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
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

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'filter', Filter::post('filter', 'indi|event|all', 'all'));
			$this->setBlockSetting($block_id, 'controls', Filter::postBool('controls'));
			$this->setBlockSetting($block_id, 'start', Filter::postBool('start'));
			$this->setBlockSetting($block_id, 'filter_avi', Filter::postBool('filter_avi'));
			$this->setBlockSetting($block_id, 'filter_bmp', Filter::postBool('filter_bmp'));
			$this->setBlockSetting($block_id, 'filter_gif', Filter::postBool('filter_gif'));
			$this->setBlockSetting($block_id, 'filter_jpeg', Filter::postBool('filter_jpeg'));
			$this->setBlockSetting($block_id, 'filter_mp3', Filter::postBool('filter_mp3'));
			$this->setBlockSetting($block_id, 'filter_ole', Filter::postBool('filter_ole'));
			$this->setBlockSetting($block_id, 'filter_pcx', Filter::postBool('filter_pcx'));
			$this->setBlockSetting($block_id, 'filter_pdf', Filter::postBool('filter_pdf'));
			$this->setBlockSetting($block_id, 'filter_png', Filter::postBool('filter_png'));
			$this->setBlockSetting($block_id, 'filter_tiff', Filter::postBool('filter_tiff'));
			$this->setBlockSetting($block_id, 'filter_wav', Filter::postBool('filter_wav'));
			$this->setBlockSetting($block_id, 'filter_audio', Filter::postBool('filter_audio'));
			$this->setBlockSetting($block_id, 'filter_book', Filter::postBool('filter_book'));
			$this->setBlockSetting($block_id, 'filter_card', Filter::postBool('filter_card'));
			$this->setBlockSetting($block_id, 'filter_certificate', Filter::postBool('filter_certificate'));
			$this->setBlockSetting($block_id, 'filter_coat', Filter::postBool('filter_coat'));
			$this->setBlockSetting($block_id, 'filter_document', Filter::postBool('filter_document'));
			$this->setBlockSetting($block_id, 'filter_electronic', Filter::postBool('filter_electronic'));
			$this->setBlockSetting($block_id, 'filter_fiche', Filter::postBool('filter_fiche'));
			$this->setBlockSetting($block_id, 'filter_film', Filter::postBool('filter_film'));
			$this->setBlockSetting($block_id, 'filter_magazine', Filter::postBool('filter_magazine'));
			$this->setBlockSetting($block_id, 'filter_manuscript', Filter::postBool('filter_manuscript'));
			$this->setBlockSetting($block_id, 'filter_map', Filter::postBool('filter_map'));
			$this->setBlockSetting($block_id, 'filter_newspaper', Filter::postBool('filter_newspaper'));
			$this->setBlockSetting($block_id, 'filter_other', Filter::postBool('filter_other'));
			$this->setBlockSetting($block_id, 'filter_painting', Filter::postBool('filter_painting'));
			$this->setBlockSetting($block_id, 'filter_photo', Filter::postBool('filter_photo'));
			$this->setBlockSetting($block_id, 'filter_tombstone', Filter::postBool('filter_tombstone'));
			$this->setBlockSetting($block_id, 'filter_video', Filter::postBool('filter_video'));
		}

		$filter   = $this->getBlockSetting($block_id, 'filter', 'all');
		$controls = $this->getBlockSetting($block_id, 'controls', '1');
		$start    = $this->getBlockSetting($block_id, 'start', '0') || Filter::getBool('start');

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: Label for a configuration option */ I18N::translate('Show only individuals, events, or all');
		echo '</td><td class="optionbox">';
		echo FunctionsEdit::selectEditControl('filter', array('indi' => I18N::translate('Individuals'), 'event' => I18N::translate('Facts and events'), 'all' => I18N::translate('All')), null, $filter, '');
		echo '</td></tr>';

		$filters = array(
			'avi'         => $this->getBlockSetting($block_id, 'filter_avi', '0'),
			'bmp'         => $this->getBlockSetting($block_id, 'filter_bmp', '1'),
			'gif'         => $this->getBlockSetting($block_id, 'filter_gif', '1'),
			'jpeg'        => $this->getBlockSetting($block_id, 'filter_jpeg', '1'),
			'mp3'         => $this->getBlockSetting($block_id, 'filter_mp3', '0'),
			'ole'         => $this->getBlockSetting($block_id, 'filter_ole', '1'),
			'pcx'         => $this->getBlockSetting($block_id, 'filter_pcx', '1'),
			'pdf'         => $this->getBlockSetting($block_id, 'filter_pdf', '0'),
			'png'         => $this->getBlockSetting($block_id, 'filter_png', '1'),
			'tiff'        => $this->getBlockSetting($block_id, 'filter_tiff', '1'),
			'wav'         => $this->getBlockSetting($block_id, 'filter_wav', '0'),
			'audio'       => $this->getBlockSetting($block_id, 'filter_audio', '0'),
			'book'        => $this->getBlockSetting($block_id, 'filter_book', '1'),
			'card'        => $this->getBlockSetting($block_id, 'filter_card', '1'),
			'certificate' => $this->getBlockSetting($block_id, 'filter_certificate', '1'),
			'coat'        => $this->getBlockSetting($block_id, 'filter_coat', '1'),
			'document'    => $this->getBlockSetting($block_id, 'filter_document', '1'),
			'electronic'  => $this->getBlockSetting($block_id, 'filter_electronic', '1'),
			'fiche'       => $this->getBlockSetting($block_id, 'filter_fiche', '1'),
			'film'        => $this->getBlockSetting($block_id, 'filter_film', '1'),
			'magazine'    => $this->getBlockSetting($block_id, 'filter_magazine', '1'),
			'manuscript'  => $this->getBlockSetting($block_id, 'filter_manuscript', '1'),
			'map'         => $this->getBlockSetting($block_id, 'filter_map', '1'),
			'newspaper'   => $this->getBlockSetting($block_id, 'filter_newspaper', '1'),
			'other'       => $this->getBlockSetting($block_id, 'filter_other', '1'),
			'painting'    => $this->getBlockSetting($block_id, 'filter_painting', '1'),
			'photo'       => $this->getBlockSetting($block_id, 'filter_photo', '1'),
			'tombstone'   => $this->getBlockSetting($block_id, 'filter_tombstone', '1'),
			'video'       => $this->getBlockSetting($block_id, 'filter_video', '0'),
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
						echo $filters[$typeName] ? 'checked' : '';
						echo '> ' . $typeValue . '</label></td>';
					}
					?>
				</tr>
			</table>
		</td>
	</tr>

	<?php

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: Label for a configuration option */ I18N::translate('Show slide show controls');
		echo '</td><td class="optionbox">';
		echo FunctionsEdit::editFieldYesNo('controls', $controls);
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: Label for a configuration option */ I18N::translate('Start slide show on page load');
		echo '</td><td class="optionbox">';
		echo FunctionsEdit::editFieldYesNo('start', $start);
		echo '</td></tr>';
	}
}
