<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Google\ChartMedia;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\MediaRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

use function array_slice;
use function arsort;
use function asort;
use function count;
use function in_array;

/**
 * A repository providing methods for media type related statistics.
 */
class MediaRepository implements MediaRepositoryInterface
{
    private ColorService $color_service;

    private Tree $tree;

    /**
     * Available media types.
     */
    private const MEDIA_TYPE_ALL         = 'all';
    private const MEDIA_TYPE_AUDIO       = 'audio';
    private const MEDIA_TYPE_BOOK        = 'book';
    private const MEDIA_TYPE_CARD        = 'card';
    private const MEDIA_TYPE_CERTIFICATE = 'certificate';
    private const MEDIA_TYPE_COAT        = 'coat';
    private const MEDIA_TYPE_DOCUMENT    = 'document';
    private const MEDIA_TYPE_ELECTRONIC  = 'electronic';
    private const MEDIA_TYPE_FICHE       = 'fiche';
    private const MEDIA_TYPE_FILM        = 'film';
    private const MEDIA_TYPE_MAGAZINE    = 'magazine';
    private const MEDIA_TYPE_MANUSCRIPT  = 'manuscript';
    private const MEDIA_TYPE_MAP         = 'map';
    private const MEDIA_TYPE_NEWSPAPER   = 'newspaper';
    private const MEDIA_TYPE_PAINTING    = 'painting';
    private const MEDIA_TYPE_PHOTO       = 'photo';
    private const MEDIA_TYPE_TOMBSTONE   = 'tombstone';
    private const MEDIA_TYPE_VIDEO       = 'video';
    private const MEDIA_TYPE_OTHER       = 'other';
    private const MEDIA_TYPE_UNKNOWN     = 'unknown';

    /**
     * List of GEDCOM media types.
     */
    private const MEDIA_TYPES = [
        self::MEDIA_TYPE_AUDIO,
        self::MEDIA_TYPE_BOOK,
        self::MEDIA_TYPE_CARD,
        self::MEDIA_TYPE_CERTIFICATE,
        self::MEDIA_TYPE_COAT,
        self::MEDIA_TYPE_DOCUMENT,
        self::MEDIA_TYPE_ELECTRONIC,
        self::MEDIA_TYPE_FICHE,
        self::MEDIA_TYPE_FILM,
        self::MEDIA_TYPE_MAGAZINE,
        self::MEDIA_TYPE_MANUSCRIPT,
        self::MEDIA_TYPE_MAP,
        self::MEDIA_TYPE_NEWSPAPER,
        self::MEDIA_TYPE_PAINTING,
        self::MEDIA_TYPE_PHOTO,
        self::MEDIA_TYPE_TOMBSTONE,
        self::MEDIA_TYPE_VIDEO,
        self::MEDIA_TYPE_OTHER,
    ];

    /**
     * @param ColorService $color_service
     * @param Tree         $tree
     */
    public function __construct(ColorService $color_service, Tree $tree)
    {
        $this->color_service = $color_service;
        $this->tree          = $tree;
    }

    /**
     * Returns the number of media records of the given type.
     *
     * @param string $type The media type to query
     *
     * @return int
     */
    private function totalMediaTypeQuery(string $type): int
    {
        if ($type !== self::MEDIA_TYPE_ALL && $type !== self::MEDIA_TYPE_UNKNOWN && !in_array($type, self::MEDIA_TYPES, true)) {
            return 0;
        }

        $query = DB::table('media')
            ->where('m_file', '=', $this->tree->id());

        if ($type !== self::MEDIA_TYPE_ALL) {
            if ($type === self::MEDIA_TYPE_UNKNOWN) {
                // There has to be a better way then this :(
                foreach (self::MEDIA_TYPES as $t) {
                    // Use function to add brackets
                    $query->where(static function (Builder $query) use ($t): void {
                        $query->where('m_gedcom', 'not like', '%3 TYPE ' . $t . '%')
                            ->where('m_gedcom', 'not like', '%1 _TYPE ' . $t . '%');
                    });
                }
            } else {
                // Use function to add brackets
                $query->where(static function (Builder $query) use ($type): void {
                    $query->where('m_gedcom', 'like', '%3 TYPE ' . $type . '%')
                        ->orWhere('m_gedcom', 'like', '%1 _TYPE ' . $type . '%');
                });
            }
        }

        return $query->count();
    }

    /**
     * @return string
     */
    public function totalMedia(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_ALL));
    }

    /**
     * @return string
     */
    public function totalMediaAudio(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_AUDIO));
    }

    /**
     * @return string
     */
    public function totalMediaBook(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_BOOK));
    }

    /**
     * @return string
     */
    public function totalMediaCard(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_CARD));
    }

    /**
     * @return string
     */
    public function totalMediaCertificate(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_CERTIFICATE));
    }

    /**
     * @return string
     */
    public function totalMediaCoatOfArms(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_COAT));
    }

    /**
     * @return string
     */
    public function totalMediaDocument(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_DOCUMENT));
    }

    /**
     * @return string
     */
    public function totalMediaElectronic(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_ELECTRONIC));
    }

    /**
     * @return string
     */
    public function totalMediaFiche(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_FICHE));
    }

    /**
     * @return string
     */
    public function totalMediaFilm(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_FILM));
    }

    /**
     * @return string
     */
    public function totalMediaMagazine(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_MAGAZINE));
    }

    /**
     * @return string
     */
    public function totalMediaManuscript(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_MANUSCRIPT));
    }

    /**
     * @return string
     */
    public function totalMediaMap(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_MAP));
    }

    /**
     * @return string
     */
    public function totalMediaNewspaper(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_NEWSPAPER));
    }

    /**
     * @return string
     */
    public function totalMediaPainting(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_PAINTING));
    }

    /**
     * @return string
     */
    public function totalMediaPhoto(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_PHOTO));
    }

    /**
     * @return string
     */
    public function totalMediaTombstone(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_TOMBSTONE));
    }

    /**
     * @return string
     */
    public function totalMediaVideo(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_VIDEO));
    }

    /**
     * @return string
     */
    public function totalMediaOther(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_OTHER));
    }

    /**
     * @return string
     */
    public function totalMediaUnknown(): string
    {
        return I18N::number($this->totalMediaTypeQuery(self::MEDIA_TYPE_UNKNOWN));
    }

    /**
     * Returns a sorted list of media types and their total counts.
     *
     * @param int $tot The total number of media files
     *
     * @return array<string,int>
     */
    private function getSortedMediaTypeList(int $tot): array
    {
        $media = [];
        $c     = 0;
        $max   = 0;

        foreach (self::MEDIA_TYPES as $type) {
            $count = $this->totalMediaTypeQuery($type);

            if ($count > 0) {
                $media[$type] = $count;

                if ($count > $max) {
                    $max = $count;
                }

                $c += $count;
            }
        }

        $count = $this->totalMediaTypeQuery(self::MEDIA_TYPE_UNKNOWN);
        if ($count > 0) {
            $media[self::MEDIA_TYPE_UNKNOWN] = $tot - $c;
            if ($tot - $c > $max) {
                $max = $count;
            }
        }

        if (count($media) > 10 && $max / $tot > 0.6) {
            arsort($media);
            $media = array_slice($media, 0, 10);
            $c     = $tot;

            foreach ($media as $cm) {
                $c -= $cm;
            }

            if (isset($media[self::MEDIA_TYPE_OTHER])) {
                $media[self::MEDIA_TYPE_OTHER] += $c;
            } else {
                $media[self::MEDIA_TYPE_OTHER] = $c;
            }
        }

        asort($media);

        return $media;
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(string $color_from = null, string $color_to = null): string
    {
        $tot   = $this->totalMediaTypeQuery(self::MEDIA_TYPE_ALL);
        $media = $this->getSortedMediaTypeList($tot);

        return (new ChartMedia($this->color_service))
            ->chartMedia($media, $color_from, $color_to);
    }
}
