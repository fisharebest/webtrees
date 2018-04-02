<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;

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
	public function getBlock($block_id, $template = true, $cfg = []): string {
		global $ctype, $WT_TREE;

		$filter   = $this->getBlockSetting($block_id, 'filter', 'all');
		$controls = $this->getBlockSetting($block_id, 'controls', '1');
		$start    = $this->getBlockSetting($block_id, 'start', '0') || Filter::getBool('start');

		// We can apply the filters using SQL
		// Do not use "ORDER BY RAND()" - it is very slow on large tables. Use PHP::array_rand() instead.
		$all_media = Database::prepare(
			"SELECT m_id FROM `##media`" .
			" JOIN `##media_file` USING (m_file, m_id)" .
			" WHERE m_file = ?" .
			" AND multimedia_format  IN ('jpg', 'jpeg', 'png', 'gif', 'tiff', 'bmp')" .
			" AND source_media_type IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')"
		)->execute([
			$WT_TREE->getTreeId(),
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
		])->fetchOneColumn();

		// Keep looking through the media until a suitable one is found.
		$random_media = null;
		while ($all_media) {
			$n     = array_rand($all_media);
			$media = Media::getInstance($all_media[$n], $WT_TREE);
			$media_file = $media->firstImageFile();
			if ($media->canShow() && $media_file !== null && !$media_file->isExternal()) {
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
		}

		if ($random_media) {
			$content = view('blocks/slide-show', [
				'block_id'            => $block_id,
				'media'               => $random_media,
				'media_file'          => $media_file,
				'show_controls'       => $controls,
				'start_automatically' => $start,
			]);
		} else {
			$content = I18N::translate('This family tree has no images to display.');
		}

		if ($template) {
			if ($ctype === 'gedcom' && Auth::isManager($WT_TREE)) {
				$config_url = route('tree-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} elseif ($ctype === 'user' && Auth::check()) {
				$config_url = route('user-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} else {
				$config_url = '';
			}

			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => $config_url,
				'title'      => $this->getTitle(),
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock(): bool {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id) {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->setBlockSetting($block_id, 'filter', Filter::post('filter', 'indi|event|all', 'all'));
			$this->setBlockSetting($block_id, 'controls', Filter::postBool('controls'));
			$this->setBlockSetting($block_id, 'start', Filter::postBool('start'));
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

			return;
		}

		$filter   = $this->getBlockSetting($block_id, 'filter', 'all');
		$controls = $this->getBlockSetting($block_id, 'controls', '1');
		$start    = $this->getBlockSetting($block_id, 'start', '0');

		$filters = [
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
		];

		$formats = GedcomTag::getFileFormTypes();

		echo view('blocks/slide-show-config', [
			'controls' => $controls,
			'filter'   => $filter,
			'filters'  => $filters,
			'formats'  => $formats,
			'start'    => $start,
		]);
	}
}
