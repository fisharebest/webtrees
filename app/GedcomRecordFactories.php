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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use stdClass;

class GedcomRecordFactories implements GedcomRecordFactoriesInterface {

  /** @var GedcomRecord[][] Allow getInstance() to return references to existing objects */
  protected $gedcom_record_cache;

  /** @var stdClass[][] Fetch all pending edits in one database query */
  protected $pending_record_cache;
  
  protected $factories = [];

  public function __construct() {
  }
  
  public function setFactory(string $type, GedcomRecordFactory $factory): void 
  {
      $this->factories[$type] = $factory;
  }

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
  public function getInstance(string $xref, Tree $tree, string $gedcom = null) {
    $tree_id = $tree->id();

    // Is this record already in the cache?
    if (isset($this->gedcom_record_cache[$xref][$tree_id])) {
      return $this->gedcom_record_cache[$xref][$tree_id];
    }

    // Do we need to fetch the record from the database?
    if ($gedcom === null) {
      $gedcom = GedcomRecord::retrieveGedcomRecord($xref, $tree_id);
    }

    // If we can edit, then we also need to be able to see pending records.
    if (Auth::isEditor($tree)) {
      if (!isset($this->pending_record_cache[$tree_id])) {
        // Fetch all pending records in one database query
        $this->pending_record_cache[$tree_id] = [];
        $rows = DB::table('change')
                ->where('gedcom_id', '=', $tree_id)
                ->where('status', '=', 'pending')
                ->orderBy('change_id')
                ->select(['xref', 'new_gedcom'])
                ->get();

        foreach ($rows as $row) {
          $this->pending_record_cache[$tree_id][$row->xref] = $row->new_gedcom;
        }
      }

      $pending = $this->pending_record_cache[$tree_id][$xref] ?? null;
    } else {
      // There are no pending changes for this record
      $pending = null;
    }

    // No such record exists
    if ($gedcom === null && $pending === null) {
      return null;
    }

    // No such record, but a pending creation exists
    if ($gedcom === null) {
      $gedcom = '';
    }

    // Create the object
    if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@ (' . Gedcom::REGEX_TAG . ')/', $gedcom . $pending, $match)) {
      $xref = $match[1]; // Collation - we may have requested I123 and found i123
      $type = $match[2];
    } elseif (preg_match('/^0 (HEAD|TRLR)/', $gedcom . $pending, $match)) {
      $xref = $match[1];
      $type = $match[1];
    } elseif ($gedcom . $pending) {
      throw new Exception('Unrecognized GEDCOM record: ' . $gedcom);
    } else {
      // A record with both pending creation and pending deletion
      $type = static::RECORD_TYPE;
    }

    $factory = $this->factories[$type] ?? null;
    if ($factory !== null) {
      $record = $factory->createRecord($xref, $gedcom, $pending, $tree);

      // Store it in the cache
      $this->gedcom_record_cache[$xref][$tree_id] = $record;

      return $record;
    }

    switch ($type) {
      case Individual::RECORD_TYPE:
          $record = new Individual($xref, $gedcom, $pending, $tree);
          break;

      case Family::RECORD_TYPE:
          $record = new Family($xref, $gedcom, $pending, $tree);
          break;

      case Source::RECORD_TYPE:
          $record = new Source($xref, $gedcom, $pending, $tree);
          break;

      case Media::RECORD_TYPE:
          $record = new Media($xref, $gedcom, $pending, $tree);
          break;

      case Repository::RECORD_TYPE:
          $record = new Repository($xref, $gedcom, $pending, $tree);
          break;

      case Note::RECORD_TYPE:
          $record = new Note($xref, $gedcom, $pending, $tree);
          break;

      case Submitter::RECORD_TYPE:
          $record = new Submitter($xref, $gedcom, $pending, $tree);
          break;
        
      default:
        $record = new GedcomRecord($xref, $gedcom, $pending, $tree);
        break;
    }

    // Store it in the cache
    $this->gedcom_record_cache[$xref][$tree_id] = $record;

    return $record;
  }

  public function clearCache(): void {
    // Clear the cache
    $this->gedcom_record_cache = [];
    $this->pending_record_cache = [];
  }

}
