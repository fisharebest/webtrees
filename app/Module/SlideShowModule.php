<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use function app;

/**
 * Class SlideShowModule
 */
class SlideShowModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Slide show” module */
        return I18N::translate('Random images from the current family tree.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $ctype
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $ctype = '', array $cfg = []): string
    {
        $request       = app(ServerRequestInterface::class);
        $default_start = $this->getBlockSetting($block_id, 'start', '0');
        $filter        = $this->getBlockSetting($block_id, 'filter', 'all');
        $controls      = $this->getBlockSetting($block_id, 'controls', '1');
        $start         = (bool) $request->get('start', $default_start);

        $media_types = [
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
        ];

        $media_types = array_filter($media_types);

        // We can apply the filters using SQL
        // Do not use "ORDER BY RAND()" - it is very slow on large tables. Use PHP::array_rand() instead.
        $all_media = DB::table('media')
            ->join('media_file', static function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->where('media.m_file', '=', $tree->id())
            ->whereIn('media_file.multimedia_format', ['jpg', 'jpeg', 'png', 'gif', 'tiff', 'bmp'])
            ->whereIn('media_file.source_media_type', $media_types)
            ->pluck('media.m_id')
            ->all();

        // Keep looking through the media until a suitable one is found.
        $random_media = null;
        while (!empty($all_media)) {
            $n          = array_rand($all_media);
            $media      = Media::getInstance($all_media[$n], $tree);
            $media_file = $media->firstImageFile();
            if ($media->canShow() && $media_file instanceof MediaFile && !$media_file->isExternal()) {
                // Check if it is linked to a suitable individual
                foreach ($media->linkedIndividuals('OBJE') as $indi) {
                    if (
                        $filter === 'all' ||
                        $filter === 'indi' && strpos($indi->gedcom(), "\n1 OBJE @" . $media->xref() . '@') !== false ||
                        $filter === 'event' && strpos($indi->gedcom(), "\n2 OBJE @" . $media->xref() . '@') !== false
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
                'media_file'          => $random_media->firstImageFile(),
                'show_controls'       => $controls,
                'start_automatically' => $start,
                'tree'                => $tree,
            ]);
        } else {
            $content = I18N::translate('This family tree has no images to display.');
        }

        if ($ctype !== '') {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $config_url,
                'title'      => $this->title(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Slide show');
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
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
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
    public function editBlockConfiguration(Tree $tree, int $block_id): void
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
