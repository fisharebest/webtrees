<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Illuminate\Database\Query\Expression;

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
    private const MEDIA_TYPE_UNKNOWN     = '';

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
     * @param string $type
     *
     * @return string
     */
    public function totalMedia(string $type = self::MEDIA_TYPE_ALL): string
    {
        $query = DB::table('media_file')->where('m_file', '=', $this->tree->id());

        if ($type !== self::MEDIA_TYPE_ALL) {
            $query->where('source_media_type', '=', $type);
        }

        return I18N::number($query->count());
    }

    /**
     * @return string
     */
    public function totalMediaAudio(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_AUDIO);
    }

    /**
     * @return string
     */
    public function totalMediaBook(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_BOOK);
    }

    /**
     * @return string
     */
    public function totalMediaCard(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_CARD);
    }

    /**
     * @return string
     */
    public function totalMediaCertificate(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_CERTIFICATE);
    }

    /**
     * @return string
     */
    public function totalMediaCoatOfArms(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_COAT);
    }

    /**
     * @return string
     */
    public function totalMediaDocument(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_DOCUMENT);
    }

    /**
     * @return string
     */
    public function totalMediaElectronic(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_ELECTRONIC);
    }

    /**
     * @return string
     */
    public function totalMediaFiche(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_FICHE);
    }

    /**
     * @return string
     */
    public function totalMediaFilm(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_FILM);
    }

    /**
     * @return string
     */
    public function totalMediaMagazine(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_MAGAZINE);
    }

    /**
     * @return string
     */
    public function totalMediaManuscript(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_MANUSCRIPT);
    }

    /**
     * @return string
     */
    public function totalMediaMap(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_MAP);
    }

    /**
     * @return string
     */
    public function totalMediaNewspaper(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_NEWSPAPER);
    }

    /**
     * @return string
     */
    public function totalMediaPainting(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_PAINTING);
    }

    /**
     * @return string
     */
    public function totalMediaPhoto(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_PHOTO);
    }

    /**
     * @return string
     */
    public function totalMediaTombstone(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_TOMBSTONE);
    }

    /**
     * @return string
     */
    public function totalMediaVideo(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_VIDEO);
    }

    /**
     * @return string
     */
    public function totalMediaOther(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_OTHER);
    }

    /**
     * @return string
     */
    public function totalMediaUnknown(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_UNKNOWN);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(?string $color_from = null, ?string $color_to = null): string
    {
        $media = DB::table('media_file')
            ->where('m_file', '=', $this->tree->id())
            ->groupBy('source_media_type')
            ->pluck(new Expression('COUNT(*) AS total'), 'source_media_type')
            ->map(static fn (string $n): int => (int) $n)
            ->all();

        return (new ChartMedia($this->color_service))
            ->chartMedia($media, $color_from, $color_to);
    }
}
