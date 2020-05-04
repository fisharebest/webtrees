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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\GedcomRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

/**
 * A repository providing methods for GEDCOM related statistics.
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

        $head = Header::getInstance('HEAD', $this->tree);

        if ($head instanceof Header) {
            $sour = $head->facts(['SOUR'])->first();

            if ($sour instanceof Fact) {
                $source  = $sour->value();
                $title   = $sour->attribute('NAME');
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
     * @inheritDoc
     */
    public function gedcomFilename(): string
    {
        return $this->tree->name();
    }

    /**
     * @inheritDoc
     */
    public function gedcomId(): int
    {
        return $this->tree->id();
    }

    /**
     * @inheritDoc
     */
    public function gedcomTitle(): string
    {
        return e($this->tree->title());
    }

    /**
     * @inheritDoc
     */
    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcomHead()[0];
    }

    /**
     * @inheritDoc
     */
    public function gedcomCreatedVersion(): string
    {
        $head = $this->gedcomHead();

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
     * @inheritDoc
     */
    public function gedcomDate(): string
    {
        $head = Header::getInstance('HEAD', $this->tree);

        if ($head instanceof Header) {
            $fact = $head->facts(['DATE'])->first();

            if ($fact instanceof Fact) {
                return (new Date($fact->value()))->display();
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function gedcomUpdated(): string
    {
        $row = DB::table('dates')
            ->select(['d_year', 'd_month', 'd_day'])
            ->where('d_julianday1', '=', function (Builder $query): void {
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
     * @inheritDoc
     */
    public function gedcomRootId(): string
    {
        return $this->tree->getPreference('PEDIGREE_ROOT_ID');
    }
}
