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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlideShowModule
 */
class SlideShowModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Slide show');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Slide show” module */
        return I18N::translate('Random images from the current family tree.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param bool     $template
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, bool $template = true, array $cfg = []): string
    {
        global $ctype;

        $filter   = $this->getBlockSetting($block_id, 'filter', 'all');
        $controls = $this->getBlockSetting($block_id, 'controls', '1');
        $start    = (bool) $this->getBlockSetting($block_id, 'start', '0');

        // We can apply the filters using SQL
        // Do not use "ORDER BY RAND()" - it is very slow on large tables. Use PHP::array_rand() instead.
        $all_media = Database::prepare(
            "SELECT m_id FROM `##media`" .
            " JOIN `##media_file` USING (m_file, m_id)" .
            " WHERE m_file = ?" .
            " AND multimedia_format  IN ('jpg', 'jpeg', 'png', 'gif', 'tiff', 'bmp')" .
            " AND source_media_type IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')"
        )->execute([
            $tree->getTreeId(),
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
            $n          = array_rand($all_media);
            $media      = Media::getInstance($all_media[$n], $tree);
            $media_file = $media->firstImageFile();
            if ($media->canShow() && $media_file !== null && !$media_file->isExternal()) {
                // Check if it is linked to a suitable individual
                foreach ($media->linkedIndividuals('OBJE') as $indi) {
                    if (
                        ($filter === 'all') ||
                        ($filter === 'indi' && strpos($indi->getGedcom(), "\n1 OBJE @" . $media->getXref() . '@') !== false) ||
                        ($filter === 'event' && strpos($indi->getGedcom(), "\n2 OBJE @" . $media->getXref() . '@') !== false)
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
            $content = view('modules/random_media/slide-show', [
                'block_id'            => $block_id,
                'media'               => $random_media,
                'media_file'          => $media_file,
                'show_controls'       => $controls,
                'start_automatically' => $start,
                'tree'                => $tree,
            ]);
        } else {
            $content = I18N::translate('This family tree has no images to display.');
        }

        if ($template) {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->getName(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->getName(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
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
    public function loadAjax(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isUserBlock(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGedcomBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
    {
        $this->setBlockSetting($block_id, 'filter', $request->get('filter', 'all'));
        $this->setBlockSetting($block_id, 'controls', $request->get('controls', ''));
        $this->setBlockSetting($block_id, 'start', $request->get('start', ''));
        $this->setBlockSetting($block_id, 'filter_audio', $request->get('filter_audio', ''));
        $this->setBlockSetting($block_id, 'filter_book', $request->get('filter_book', ''));
        $this->setBlockSetting($block_id, 'filter_card', $request->get('filter_card', ''));
        $this->setBlockSetting($block_id, 'filter_certificate', $request->get('filter_certificate', ''));
        $this->setBlockSetting($block_id, 'filter_coat', $request->get('filter_coat', ''));
        $this->setBlockSetting($block_id, 'filter_document', $request->get('filter_document', ''));
        $this->setBlockSetting($block_id, 'filter_electronic', $request->get('filter_electronic', ''));
        $this->setBlockSetting($block_id, 'filter_fiche', $request->get('filter_fiche', ''));
        $this->setBlockSetting($block_id, 'filter_film', $request->get('filter_film', ''));
        $this->setBlockSetting($block_id, 'filter_magazine', $request->get('filter_magazine', ''));
        $this->setBlockSetting($block_id, 'filter_manuscript', $request->get('filter_manuscript', ''));
        $this->setBlockSetting($block_id, 'filter_map', $request->get('filter_map', ''));
        $this->setBlockSetting($block_id, 'filter_newspaper', $request->get('filter_newspaper', ''));
        $this->setBlockSetting($block_id, 'filter_other', $request->get('filter_other', ''));
        $this->setBlockSetting($block_id, 'filter_painting', $request->get('filter_painting', ''));
        $this->setBlockSetting($block_id, 'filter_photo', $request->get('filter_photo', ''));
        $this->setBlockSetting($block_id, 'filter_tombstone', $request->get('filter_tombstone', ''));
        $this->setBlockSetting($block_id, 'filter_video', $request->get('filter_video', ''));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id)
    {
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

        echo view('modules/random_media/config', [
            'controls' => $controls,
            'filter'   => $filter,
            'filters'  => $filters,
            'formats'  => $formats,
            'start'    => $start,
        ]);
    }
}
