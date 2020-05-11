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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaPage;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

/**
 * A GEDCOM media (OBJE) object.
 */
class Media extends GedcomRecord
{
    public const RECORD_TYPE = 'OBJE';

    protected const ROUTE_NAME = MediaPage::class;

    /**
     * A closure which will create a record from a database row.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::media()
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public static function rowMapper(Tree $tree): Closure
    {
        return Factory::media()->mapper($tree);
    }

    /**
     * Get an instance of a media object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::media()
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return Media|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null): ?Media
    {
        return Factory::media()->make($xref, $tree, $gedcom);
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
        $linked_ids = DB::table('link')
            ->where('l_file', '=', $this->tree->id())
            ->where('l_to', '=', $this->xref)
            ->pluck('l_from');

        foreach ($linked_ids as $linked_id) {
            $linked_record = Factory::gedcomRecord()->make($linked_id, $this->tree);
            if ($linked_record instanceof GedcomRecord && !$linked_record->canShow($access_level)) {
                return false;
            }
        }

        // ... otherwise apply default behavior
        return parent::canShowByType($access_level);
    }

    /**
     * Get the media files for this media object
     *
     * @return Collection<MediaFile>
     */
    public function mediaFiles(): Collection
    {
        return $this->facts(['FILE'])
            ->map(function (Fact $fact): MediaFile {
                return new MediaFile($fact->gedcom(), $this);
            });
    }

    /**
     * Get the first media file that contains an image.
     *
     * @return MediaFile|null
     */
    public function firstImageFile(): ?MediaFile
    {
        return $this->mediaFiles()
            ->first(static function (MediaFile $media_file): bool {
                return $media_file->isImage() && !$media_file->isExternal();
            });
    }

    /**
     * Get the first note attached to this media object
     *
     * @return string
     */
    public function getNote(): string
    {
        $fact = $this->facts(['NOTE'])->first();

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
    public function extractNames(): void
    {
        $names = [];
        foreach ($this->mediaFiles() as $media_file) {
            $names[] = $media_file->title();
        }
        foreach ($this->mediaFiles() as $media_file) {
            $names[] = $media_file->filename();
        }
        $names = array_filter(array_unique($names));

        if ($names === []) {
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
        $media_file = $this->mediaFiles()->first();

        if ($media_file instanceof MediaFile) {
            return $media_file->displayImage($width, $height, $fit, $attributes);
        }

        // No image?
        return '';
    }
}
