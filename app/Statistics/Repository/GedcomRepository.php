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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\GedcomRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

/**
 * Statistics submodule providing all GEDCOM related methods.
 */
class GedcomRepository implements GedcomRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

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
     * Get information from the GEDCOM's HEAD record.
     *
     * @return string[]
     */
    private function gedcomHead(): array
    {
        $title   = '';
        $version = '';
        $source  = '';

        $head = GedcomRecord::getInstance('HEAD', $this->tree);

        if ($head !== null) {
            $sour = $head->getFirstFact('SOUR');

            if ($sour !== null) {
                $source = $sour->value();
                $title = $sour->attribute('NAME');
                $version = $sour->attribute('VERS');
            }
        }

        return [
            $title,
            $version,
            $source,
        ];
    }

    /**
     * Get the name used for GEDCOM files and URLs.
     *
     * @return string
     */
    public function gedcomFilename(): string
    {
        return $this->tree->name();
    }

    /**
     * Get the internal ID number of the tree.
     *
     * @return int
     */
    public function gedcomId(): int
    {
        return $this->tree->id();
    }

    /**
     * Get the descriptive title of the tree.
     *
     * @return string
     */
    public function gedcomTitle(): string
    {
        return e($this->tree->title());
    }

    /**
     * Get the software originally used to create the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedSoftware(): string
    {
        $head = $this->gedcomHead();
        return $head[0];
    }

    /**
     * Get the version of software which created the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedVersion(): string
    {
        $head = $this->gedcomHead();

        if ($head === null) {
            return '';
        }

        // Fix broken version string in Family Tree Maker
        if (strpos($head[1], 'Family Tree Maker ') !== false) {
            $p       = strpos($head[1], '(') + 1;
            $p2      = strpos($head[1], ')');
            $head[1] = substr($head[1], $p, $p2 - $p);
        }

        // Fix EasyTree version
        if ($head[2] === 'EasyTree') {
            $head[1] = substr($head[1], 1);
        }

        return $head[1];
    }

    /**
     * Get the date the GEDCOM file was created.
     *
     * @return string
     */
    public function gedcomDate(): string
    {
        $head = GedcomRecord::getInstance('HEAD', $this->tree);

        if ($head !== null) {
            $fact = $head->getFirstFact('DATE');

            if ($fact) {
                return (new Date($fact->value()))->display();
            }
        }

        return '';
    }

    /**
     * When was this tree last updated?
     *
     * @return string
     */
    public function gedcomUpdated(): string
    {
        $row = DB::table('dates')
            ->select(['d_year', 'd_month', 'd_day'])
            ->where('d_julianday1', '=', function (Builder $query) {
                $query->selectRaw('MAX(d_julianday1)')
                    ->from('dates')
                    ->where('d_file', '=', $this->tree->id())
                    ->where('d_fact', '=', 'CHAN');
            })
            ->first();

        if ($row) {
            $date = new Date("{$row->d_day} {$row->d_month} {$row->d_year}");
            return $date->display();
        }

        return $this->gedcomDate();
    }

    /**
     * What is the significant individual from this tree?
     *
     * @return string
     */
    public function gedcomRootId(): string
    {
        return $this->tree->getPreference('PEDIGREE_ROOT_ID');
    }
}
