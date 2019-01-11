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
    private function totalMediaTypeQuery(string $type): int
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
        return I18N::number($this->totalMediaTypeQuery('all'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaAudio(): string
    {
        return I18N::number($this->totalMediaTypeQuery('audio'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaBook(): string
    {
        return I18N::number($this->totalMediaTypeQuery('book'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCard(): string
    {
        return I18N::number($this->totalMediaTypeQuery('card'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCertificate(): string
    {
        return I18N::number($this->totalMediaTypeQuery('certificate'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCoatOfArms(): string
    {
        return I18N::number($this->totalMediaTypeQuery('coat'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaDocument(): string
    {
        return I18N::number($this->totalMediaTypeQuery('document'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaElectronic(): string
    {
        return I18N::number($this->totalMediaTypeQuery('electronic'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaMagazine(): string
    {
        return I18N::number($this->totalMediaTypeQuery('magazine'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaManuscript(): string
    {
        return I18N::number($this->totalMediaTypeQuery('manuscript'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaMap(): string
    {
        return I18N::number($this->totalMediaTypeQuery('map'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaFiche(): string
    {
        return I18N::number($this->totalMediaTypeQuery('fiche'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaFilm(): string
    {
        return I18N::number($this->totalMediaTypeQuery('film'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaNewspaper(): string
    {
        return I18N::number($this->totalMediaTypeQuery('newspaper'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaPainting(): string
    {
        return I18N::number($this->totalMediaTypeQuery('painting'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaPhoto(): string
    {
        return I18N::number($this->totalMediaTypeQuery('photo'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaTombstone(): string
    {
        return I18N::number($this->totalMediaTypeQuery('tombstone'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaVideo(): string
    {
        return I18N::number($this->totalMediaTypeQuery('video'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaOther(): string
    {
        return I18N::number($this->totalMediaTypeQuery('other'));
    }

    /**
     * @inheritDoc
     */
    public function totalMediaUnknown(): string
    {
        return I18N::number($this->totalMediaTypeQuery('unknown'));
    }

    /**
     * Returns a sorted list of media types and their total counts.
     *
     * @param int $tot The total number of media files
     *
     * @return array
     */
    private function getSortedMediaTypeList(int $tot): array
    {
        $med_types = $this->getMediaTypes();
        $media     = [];
        $c         = 0;
        $max       = 0;

        foreach ($med_types as $type) {
            $count = $this->totalMediaTypeQuery($type);

            if ($count > 0) {
                $media[$type] = $count;

                if ($count > $max) {
                    $max = $count;
                }

                $c += $count;
            }
        }

        $count = $this->totalMediaTypeQuery('unknown');
        if ($count > 0) {
            $media['unknown'] = $tot - $c;
            if ($tot - $c > $max) {
                $max = $count;
            }
        }

        if (($max / $tot) > 0.6 && \count($media) > 10) {
            arsort($media);
            $media = \array_slice($media, 0, 10);
            $c     = $tot;

            foreach ($media as $cm) {
                $c -= $cm;
            }

            if (isset($media['other'])) {
                $media['other'] += $c;
            } else {
                $media['other'] = $c;
            }
        }

        asort($media);

        return $media;
    }

    /**
     * @inheritDoc
     */
    public function chartMedia(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $tot   = $this->totalMediaTypeQuery('all');
        $media = $this->getSortedMediaTypeList($tot);

        return (new ChartMedia())
            ->chartMedia($tot, $media, $size, $color_from, $color_to);
    }
}
