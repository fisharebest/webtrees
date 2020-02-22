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

namespace Fisharebest\Webtrees;

use Exception;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;

interface GedcomRecordFactoriesInterface {

  /**
   * Add (or replace) a factory for a specific type, e.g. 'INDI', 'FAM' ...
   */
  public function setFactory(string $type, GedcomRecordFactory $factory): void;

  /**
   * Get an instance of a GedcomRecord object. For single records,
   * we just receive the XREF. For bulk records (such as lists
   * and search results) we can receive the GEDCOM data as well.
   *
   * @param string      $xref
   * @param Tree        $tree
   * @param string|null $gedcom
   *
   * @throws Exception
   * @return GedcomRecord|Individual|Family|Source|Repository|Media|Note|null
   */
  public function getInstance(string $xref, Tree $tree, string $gedcom = null);

  public function clearCache(): void;

}
