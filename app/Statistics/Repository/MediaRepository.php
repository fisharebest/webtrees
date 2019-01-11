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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Google\ChartMedia;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\MediaRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

/**
 * A repository providing methods for media type related statistics.
 */
class MediaRepository implements MediaRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * List of GEDCOM media types.
     *
     * @var string[]
     */
    private static $media_types = [
        'audio',
        'book',
        'card',
        'certificate',
        'coat',
        'document',
        'electronic',
        'magazine',
        'manuscript',
        'map',
        'fiche',
        'film',
        'newspaper',
        'painting',
        'photo',
        'tombstone',
        'video',
        'other',
    ];

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return array
     */
    private function getMediaTypes(): array
    {
        return self::$media_types;
    }

    /**
     * Count the number of media records with a given type.
     *
     * @param string $type The media type
     *
     * @return int
     */
    public function totalMediaType(string $type): int
    {
        if (($type !== 'all')
            && ($type !== 'unknown')
            && !\in_array($type, $this->getMediaTypes(), true)
        ) {
            return 0;
        }

        $query = DB::table('media')
            ->where('m_file', '=', $this->tree->id());

        if ($type !== 'all') {
            if ($type === 'unknown') {
                // There has to be a better way then this :(
                foreach ($this->getMediaTypes() as $t) {
                    // Use function to add brackets
                    $query->where(function (Builder $query) use ($t) {
                        $query->where('m_gedcom', 'not like', '%3 TYPE ' . $t . '%')
                            ->where('m_gedcom', 'not like', '%1 _TYPE ' . $t . '%');
                    });
                }
            } else {
                // Use function to add brackets
                $query->where(function (Builder $query) use ($type) {
                    $query->where('m_gedcom', 'like', '%3 TYPE ' . $type . '%')
                        ->orWhere('m_gedcom', 'like', '%1 _TYPE ' . $type . '%');
                });
            }
        }

        return $query->count();
    }

    /**
     * @inheritDoc
     */
    public function totalMedia(): string
    {
        return I18N::number($this->totalMediaType('all'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaAudio(): string
    {
        return I18N::number($this->totalMediaType('audio'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaBook(): string
    {
        return I18N::number($this->totalMediaType('book'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCard(): string
    {
        return I18N::number($this->totalMediaType('card'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCertificate(): string
    {
        return I18N::number($this->totalMediaType('certificate'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCoatOfArms(): string
    {
        return I18N::number($this->totalMediaType('coat'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaDocument(): string
    {
        return I18N::number($this->totalMediaType('document'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaElectronic(): string
    {
        return I18N::number($this->totalMediaType('electronic'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaMagazine(): string
    {
        return I18N::number($this->totalMediaType('magazine'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaManuscript(): string
    {
        return I18N::number($this->totalMediaType('manuscript'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaMap(): string
    {
        return I18N::number($this->totalMediaType('map'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaFiche(): string
    {
        return I18N::number($this->totalMediaType('fiche'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaFilm(): string
    {
        return I18N::number($this->totalMediaType('film'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaNewspaper(): string
    {
        return I18N::number($this->totalMediaType('newspaper'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaPainting(): string
    {
        return I18N::number($this->totalMediaType('painting'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaPhoto(): string
    {
        return I18N::number($this->totalMediaType('photo'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaTombstone(): string
    {
        return I18N::number($this->totalMediaType('tombstone'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaVideo(): string
    {
        return I18N::number($this->totalMediaType('video'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaOther(): string
    {
        return I18N::number($this->totalMediaType('other'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaUnknown(): string
    {
        return I18N::number($this->totalMediaType('unknown'));
    }

    /**
     * @inheritDoc
     */
    public function chartMedia(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $tot       = $this->totalMediaType('all');
        $med_types = $this->getMediaTypes();

        return (new ChartMedia($this->tree))
            ->chartMedia($tot, $med_types, $size, $color_from, $color_to);
    }
}
