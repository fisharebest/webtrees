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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Google\ChartMedia;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Expression;

class MediaRepository
{
    private ColorService $color_service;

    private Tree $tree;

    /**
     * Available media types.
     */
    private const string MEDIA_TYPE_ALL         = 'all';
    private const string MEDIA_TYPE_AUDIO       = 'audio';
    private const string MEDIA_TYPE_BOOK        = 'book';
    private const string MEDIA_TYPE_CARD        = 'card';
    private const string MEDIA_TYPE_CERTIFICATE = 'certificate';
    private const string MEDIA_TYPE_COAT        = 'coat';
    private const string MEDIA_TYPE_DOCUMENT    = 'document';
    private const string MEDIA_TYPE_ELECTRONIC  = 'electronic';
    private const string MEDIA_TYPE_FICHE       = 'fiche';
    private const string MEDIA_TYPE_FILM        = 'film';
    private const string MEDIA_TYPE_MAGAZINE    = 'magazine';
    private const string MEDIA_TYPE_MANUSCRIPT  = 'manuscript';
    private const string MEDIA_TYPE_MAP         = 'map';
    private const string MEDIA_TYPE_NEWSPAPER   = 'newspaper';
    private const string MEDIA_TYPE_PAINTING    = 'painting';
    private const string MEDIA_TYPE_PHOTO       = 'photo';
    private const string MEDIA_TYPE_TOMBSTONE   = 'tombstone';
    private const string MEDIA_TYPE_VIDEO       = 'video';
    private const string MEDIA_TYPE_OTHER       = 'other';
    private const string MEDIA_TYPE_UNKNOWN     = '';

    public function __construct(ColorService $color_service, Tree $tree)
    {
        $this->color_service = $color_service;
        $this->tree          = $tree;
    }

    public function totalMedia(string $type = self::MEDIA_TYPE_ALL): string
    {
        $query = DB::table('media_file')->where('m_file', '=', $this->tree->id());

        if ($type !== self::MEDIA_TYPE_ALL) {
            $query->where('source_media_type', '=', $type);
        }

        return I18N::number($query->count());
    }

    public function totalMediaAudio(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_AUDIO);
    }

    public function totalMediaBook(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_BOOK);
    }

    public function totalMediaCard(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_CARD);
    }

    public function totalMediaCertificate(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_CERTIFICATE);
    }

    public function totalMediaCoatOfArms(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_COAT);
    }

    public function totalMediaDocument(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_DOCUMENT);
    }

    public function totalMediaElectronic(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_ELECTRONIC);
    }

    public function totalMediaFiche(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_FICHE);
    }

    public function totalMediaFilm(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_FILM);
    }

    public function totalMediaMagazine(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_MAGAZINE);
    }

    public function totalMediaManuscript(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_MANUSCRIPT);
    }

    public function totalMediaMap(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_MAP);
    }

    public function totalMediaNewspaper(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_NEWSPAPER);
    }

    public function totalMediaPainting(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_PAINTING);
    }

    public function totalMediaPhoto(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_PHOTO);
    }

    public function totalMediaTombstone(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_TOMBSTONE);
    }

    public function totalMediaVideo(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_VIDEO);
    }

    public function totalMediaOther(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_OTHER);
    }

    public function totalMediaUnknown(): string
    {
        return $this->totalMedia(self::MEDIA_TYPE_UNKNOWN);
    }

    public function chartMedia(string|null $color_from = null, string|null $color_to = null): string
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
