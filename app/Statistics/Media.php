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

namespace Fisharebest\Webtrees\Statistics;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Media
{
    /**
     * @var Tree
     */
    private $tree;

    /** @var string[] List of GEDCOM media types */
    private $media_types = [
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
    public function getMediaTypes(): array
    {
        return $this->media_types;
    }

    /**
     * Count the number of media records with a given type.
     *
     * @param string $type
     *
     * @return int
     */
    public function totalMediaType($type): int
    {
        if (($type !== 'all')
            && ($type !== 'unknown')
            && !in_array($type, $this->media_types)
        ) {
            return 0;
        }

        $sql  = "SELECT COUNT(*) AS tot FROM `##media` WHERE m_file=?";
        $vars = [$this->tree->id()];

        if ($type !== 'all') {
            if ($type === 'unknown') {
                // There has to be a better way then this :(
                foreach ($this->media_types as $t) {
                    $sql .= " AND (m_gedcom NOT LIKE ? AND m_gedcom NOT LIKE ?)";
                    $vars[] = "%3 TYPE {$t}%";
                    $vars[] = "%1 _TYPE {$t}%";
                }
            } else {
                $sql .= " AND (m_gedcom LIKE ? OR m_gedcom LIKE ?)";
                $vars[] = "%3 TYPE {$type}%";
                $vars[] = "%1 _TYPE {$type}%";
            }
        }

        return (int) Database::prepare($sql)->execute($vars)->fetchOne();
    }
}
