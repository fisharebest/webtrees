<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\GedcomRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

use function str_contains;

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

        $head = Factory::header()->make('HEAD', $this->tree);

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
     * @return string
     */
    public function gedcomFilename(): string
    {
        return $this->tree->name();
    }

    /**
     * @return int
     */
    public function gedcomId(): int
    {
        return $this->tree->id();
    }

    /**
     * @return string
     */
    public function gedcomTitle(): string
    {
        return e($this->tree->title());
    }

    /**
     * @return string
     */
    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcomHead()[0];
    }

    /**
     * @return string
     */
    public function gedcomCreatedVersion(): string
    {
        $head = $this->gedcomHead();

        // Fix broken version string in Family Tree Maker
        if (str_contains($head[1], 'Family Tree Maker ')) {
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
     * @return string
     * @throws \Exception
     */
    public function gedcomDate(): string
    {
        $head = Factory::header()->make('HEAD', $this->tree);

        if ($head instanceof Header) {
            $fact = $head->facts(['DATE'])->first();

            if ($fact instanceof Fact) {
                return Carbon::make($fact->value())->local()->isoFormat('LL');
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function gedcomUpdated(): string
    {
        $row = DB::table('change')
            ->where('gedcom_id', '=', $this->tree->id())
            ->where('status', '=', 'accepted')
            ->orderBy('change_id', 'DESC')
            ->select(['change_time'])
            ->first();

        if ($row === null) {
            return $this->gedcomDate();
        }

        return Carbon::make($row->change_time)->local()->isoFormat('LL');
    }

    /**
     * @return string
     */
    public function gedcomRootId(): string
    {
        return $this->tree->getPreference('PEDIGREE_ROOT_ID');
    }
}
