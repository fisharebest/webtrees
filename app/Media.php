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

use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * A GEDCOM media (OBJE) object.
 */
class Media extends GedcomRecord
{
    public const RECORD_TYPE = 'OBJE';

    protected const ROUTE_NAME = 'media';

    /**
     * Get an instance of a media object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @throws \Exception
     *
     * @return Media|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null)
    {
        $record = parent::getInstance($xref, $tree, $gedcom);

        if ($record instanceof Media) {
            return $record;
        }

        return null;
    }

    /**
     * Each object type may have its own special rules, and re-implement this function.
     *
     * @param int $access_level
     *
     * @return bool
     */
    protected function canShowByType(int $access_level): bool
    {
        // Hide media objects if they are attached to private records
        $linked_ids = Database::prepare(
            "SELECT l_from FROM `##link` WHERE l_to = ? AND l_file = ?"
        )->execute([
            $this->xref,
            $this->tree->id(),
        ])->fetchOneColumn();
        foreach ($linked_ids as $linked_id) {
            $linked_record = GedcomRecord::getInstance($linked_id, $this->tree);
            if ($linked_record && !$linked_record->canShow($access_level)) {
                return false;
            }
        }

        // ... otherwise apply default behaviour
        return parent::canShowByType($access_level);
    }

    /**
     * Fetch data from the database
     *
     * @param string $xref
     * @param int    $tree_id
     *
     * @return null|string
     */
    protected static function fetchGedcomRecord(string $xref, int $tree_id)
    {
        return DB::table('media')
            ->where('m_id', '=', $xref)
            ->where('m_file', '=', $tree_id)
            ->value('m_gedcom');
    }

    /**
     * Get the media files for this media object
     *
     * @return MediaFile[]
     */
    public function mediaFiles(): array
    {
        $media_files = [];

        foreach ($this->facts(['FILE']) as $fact) {
            $media_files[] = new MediaFile($fact->gedcom(), $this);
        }

        return $media_files;
    }

    /**
     * Get the first media file that contains an image.
     *
     * @return MediaFile|null
     */
    public function firstImageFile()
    {
        foreach ($this->mediaFiles() as $media_file) {
            if ($media_file->isImage()) {
                return $media_file;
            }
        }

        return null;
    }

    /**
     * Get the first note attached to this media object
     *
     * @return string
     */
    public function getNote()
    {
        $fact = $this->getFirstFact('NOTE');
        if ($fact instanceof Fact) {
            // Link to note object
            $note = $fact->target();
            if ($note instanceof Note) {
                return $note->getNote();
            }

            // Inline note
            return $fact->value();
        }

        return '';
    }

    /**
     * Extract names from the GEDCOM record.
     *
     * @return void
     */
    public function extractNames()
    {
        $names = [];
        foreach ($this->mediaFiles() as $media_file) {
            $names[] = $media_file->title();
        }
        foreach ($this->mediaFiles() as $media_file) {
            $names[] = $media_file->filename();
        }
        $names = array_filter(array_unique($names));

        if (empty($names)) {
            $names[] = $this->getFallBackName();
        }

        foreach ($names as $name) {
            $this->addName(static::RECORD_TYPE, $name, '');
        }
    }

    /**
     * This function should be redefined in derived classes to show any major
     * identifying characteristics of this record.
     *
     * @return string
     */
    public function formatListDetails(): string
    {
        ob_start();
        FunctionsPrintFacts::printMediaLinks($this->tree(), '1 OBJE @' . $this->xref() . '@', 1);

        return ob_get_clean();
    }

    /**
     * Display an image-thumbnail or a media-icon, and add markup for image viewers such as colorbox.
     *
     * @param int      $width      Pixels
     * @param int      $height     Pixels
     * @param string   $fit        "crop" or "contain"
     * @param string[] $attributes Additional HTML attributes
     *
     * @return string
     */
    public function displayImage($width, $height, $fit, $attributes = []): string
    {
        // Display the first image
        foreach ($this->mediaFiles() as $media_file) {
            if ($media_file->isImage()) {
                return $media_file->displayImage($width, $height, $fit, $attributes);
            }
        }

        // Display the first file of any type
        foreach ($this->mediaFiles() as $media_file) {
            return $media_file->displayImage($width, $height, $fit, $attributes);
        }

        // No image?
        return '';
    }
}
