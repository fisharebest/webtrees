<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Elements\SourceMediaType;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function app;
use function array_filter;
use function in_array;
use function str_contains;
use function strtolower;

/**
 * Class SlideShowModule
 */
class SlideShowModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Show media linked to events or individuals.
    private const LINK_ALL        = 'all';
    private const LINK_EVENT      = 'event';
    private const LINK_INDIVIDUAL = 'indi';

    // How long to show each slide (seconds)
    private const DELAY = 6;

    // New data is normalized.  Old data may contain jpg/jpeg, tif/tiff.
    private const SUPPORTED_FORMATS = ['bmp', 'gif', 'jpeg', 'jpg', 'png', 'tif', 'tiff', 'webp'];

    private LinkedRecordService $linked_record_service;

    /**
     * @param LinkedRecordService $linked_record_service
     */
    public function __construct(LinkedRecordService $linked_record_service)
    {
        $this->linked_record_service = $linked_record_service;
    }

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
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $request       = app(ServerRequestInterface::class);
        $default_start = (bool) $this->getBlockSetting($block_id, 'start');
        $filter_links  = $this->getBlockSetting($block_id, 'filter', self::LINK_ALL);
        $controls      = $this->getBlockSetting($block_id, 'controls', '1');
        $start         = Validator::queryParams($request)->boolean('start', $default_start);

        $filter_types = [
            $this->getBlockSetting($block_id, 'filter_audio', '0') ? SourceMediaType::VALUE_AUDIO : null,
            $this->getBlockSetting($block_id, 'filter_book', '1') ? SourceMediaType::VALUE_BOOK : null,
            $this->getBlockSetting($block_id, 'filter_card', '1') ? SourceMediaType::VALUE_CARD : null,
            $this->getBlockSetting($block_id, 'filter_certificate', '1') ? SourceMediaType::VALUE_CERTIFICATE : null,
            $this->getBlockSetting($block_id, 'filter_coat', '1') ? SourceMediaType::VALUE_COAT : null,
            $this->getBlockSetting($block_id, 'filter_document', '1') ? SourceMediaType::VALUE_DOCUMENT : null,
            $this->getBlockSetting($block_id, 'filter_electronic', '1') ? SourceMediaType::VALUE_ELECTRONIC : null,
            $this->getBlockSetting($block_id, 'filter_fiche', '1') ? SourceMediaType::VALUE_FICHE : null,
            $this->getBlockSetting($block_id, 'filter_film', '1') ? SourceMediaType::VALUE_FILM : null,
            $this->getBlockSetting($block_id, 'filter_magazine', '1') ? SourceMediaType::VALUE_MAGAZINE : null,
            $this->getBlockSetting($block_id, 'filter_manuscript', '1') ? SourceMediaType::VALUE_MANUSCRIPT : null,
            $this->getBlockSetting($block_id, 'filter_map', '1') ? SourceMediaType::VALUE_MAP : null,
            $this->getBlockSetting($block_id, 'filter_newspaper', '1') ? SourceMediaType::VALUE_NEWSPAPER : null,
            $this->getBlockSetting($block_id, 'filter_other', '1') ? SourceMediaType::VALUE_OTHER : null,
            $this->getBlockSetting($block_id, 'filter_painting', '1') ? SourceMediaType::VALUE_PAINTING : null,
            $this->getBlockSetting($block_id, 'filter_photo', '1') ? SourceMediaType::VALUE_PHOTO : null,
            $this->getBlockSetting($block_id, 'filter_tombstone', '1') ? SourceMediaType::VALUE_TOMBSTONE : null,
            $this->getBlockSetting($block_id, 'filter_video', '0') ? SourceMediaType::VALUE_VIDEO : null,
        ];

        $filter_types = array_filter($filter_types);

        // The type "other" includes media without a type.
        if (in_array(SourceMediaType::VALUE_OTHER, $filter_types, true)) {
            $filter_types[] = '';
        }

        // We can apply the filters using SQL, but it is more efficient to shuffle in PHP.
        $random_row = DB::table('media')
            ->join('media_file', static function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->where('media.m_file', '=', $tree->id())
            ->whereIn('media_file.multimedia_format', self::SUPPORTED_FORMATS)
            ->whereIn('media_file.source_media_type', $filter_types)
            ->select(['media.*'])
            ->get()
            ->shuffle()
            ->first(function (object $row) use ($filter_links, $tree): bool {
                $media = Registry::mediaFactory()->make($row->m_id, $tree, $row->m_gedcom);

                if ($media === null || !$media->canShow() || $media->firstImageFile() === null) {
                    return false;
                }

                foreach ($this->linked_record_service->linkedIndividuals($media) as $individual) {
                    switch ($filter_links) {
                        case self::LINK_ALL:
                            return true;

                        case self::LINK_INDIVIDUAL:
                            return str_contains($individual->gedcom(), "\n1 OBJE @" . $media->xref() . '@');

                        case self::LINK_EVENT:
                            return str_contains($individual->gedcom(), "\n2 OBJE @" . $media->xref() . '@');
                    }
                }

                return false;
            });

        $random_media = null;

        if ($random_row !== null) {
            $random_media = Registry::mediaFactory()->make($random_row->m_id, $tree, $random_row->m_gedcom);
        }

        if ($random_media instanceof Media) {
            $content = view('modules/random_media/slide-show', [
                'block_id'            => $block_id,
                'delay'               => self::DELAY,
                'linked_families'     => $this->linked_record_service->linkedFamilies($random_media),
                'linked_individuals'  => $this->linked_record_service->linkedIndividuals($random_media),
                'linked_sources'      => $this->linked_record_service->linkedSources($random_media),
                'media'               => $random_media,
                'media_file'          => $random_media->firstImageFile(),
                'show_controls'       => $controls,
                'start_automatically' => $start,
                'tree'                => $tree,
            ]);
        } else {
            $content = I18N::translate('This family tree has no images to display.');
        }

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
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

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
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
        $this->setBlockSetting($block_id, 'filter', Validator::parsedBody($request)->string('filter'));
        $this->setBlockSetting($block_id, 'controls', Validator::parsedBody($request)->string('controls'));
        $this->setBlockSetting($block_id, 'start', Validator::parsedBody($request)->string('start'));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_AUDIO), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_AUDIO, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_BOOK), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_BOOK, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_CARD), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_CARD, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_CERTIFICATE), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_CERTIFICATE, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_COAT), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_COAT, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_DOCUMENT), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_DOCUMENT, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_ELECTRONIC), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_ELECTRONIC, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_FICHE), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_FICHE, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_FILM), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_FILM, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_MAGAZINE), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_MAGAZINE, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_MANUSCRIPT), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_MANUSCRIPT, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_MAP), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_MAP, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_NEWSPAPER), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_NEWSPAPER, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_OTHER), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_OTHER, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_PAINTING), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_PAINTING, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_PHOTO), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_PHOTO, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_TOMBSTONE), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_TOMBSTONE, false));
        $this->setBlockSetting($block_id, 'filter_' . strtolower(SourceMediaType::VALUE_VIDEO), (string) Validator::parsedBody($request)->boolean(SourceMediaType::VALUE_VIDEO, false));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        $filter   = $this->getBlockSetting($block_id, 'filter', self::LINK_ALL);
        $controls = $this->getBlockSetting($block_id, 'controls', '1');
        $start    = $this->getBlockSetting($block_id, 'start', '0');

        $filters = [
            SourceMediaType::VALUE_AUDIO       => $this->getBlockSetting($block_id, 'filter_audio', '0'),
            SourceMediaType::VALUE_BOOK        => $this->getBlockSetting($block_id, 'filter_book', '1'),
            SourceMediaType::VALUE_CARD        => $this->getBlockSetting($block_id, 'filter_card', '1'),
            SourceMediaType::VALUE_CERTIFICATE => $this->getBlockSetting($block_id, 'filter_certificate', '1'),
            SourceMediaType::VALUE_COAT       => $this->getBlockSetting($block_id, 'filter_coat', '1'),
            SourceMediaType::VALUE_DOCUMENT   => $this->getBlockSetting($block_id, 'filter_document', '1'),
            SourceMediaType::VALUE_ELECTRONIC => $this->getBlockSetting($block_id, 'filter_electronic', '1'),
            SourceMediaType::VALUE_FICHE      => $this->getBlockSetting($block_id, 'filter_fiche', '1'),
            SourceMediaType::VALUE_FILM       => $this->getBlockSetting($block_id, 'filter_film', '1'),
            SourceMediaType::VALUE_MAGAZINE   => $this->getBlockSetting($block_id, 'filter_magazine', '1'),
            SourceMediaType::VALUE_MANUSCRIPT => $this->getBlockSetting($block_id, 'filter_manuscript', '1'),
            SourceMediaType::VALUE_MAP        => $this->getBlockSetting($block_id, 'filter_map', '1'),
            SourceMediaType::VALUE_NEWSPAPER  => $this->getBlockSetting($block_id, 'filter_newspaper', '1'),
            SourceMediaType::VALUE_OTHER      => $this->getBlockSetting($block_id, 'filter_other', '1'),
            SourceMediaType::VALUE_PAINTING   => $this->getBlockSetting($block_id, 'filter_painting', '1'),
            SourceMediaType::VALUE_PHOTO      => $this->getBlockSetting($block_id, 'filter_photo', '1'),
            SourceMediaType::VALUE_TOMBSTONE  => $this->getBlockSetting($block_id, 'filter_tombstone', '1'),
            SourceMediaType::VALUE_VIDEO      => $this->getBlockSetting($block_id, 'filter_video', '0'),
        ];

        $formats = array_filter(Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values());

        return view('modules/random_media/config', [
            'controls' => $controls,
            'filter'   => $filter,
            'filters'  => $filters,
            'formats'  => $formats,
            'start'    => $start,
        ]);
    }
}
