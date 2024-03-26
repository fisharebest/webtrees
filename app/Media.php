<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaPage;
use Illuminate\Support\Collection;

use function array_filter;
use function array_unique;

/**
 * A GEDCOM media (OBJE) object.
 */
class Media extends GedcomRecord
{
    public const RECORD_TYPE = 'OBJE';

    protected const ROUTE_NAME = MediaPage::class;

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
            $linked_record = Registry::gedcomRecordFactory()->make($linked_id, $this->tree);
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
     * @return Collection<int,MediaFile>
     */
    public function mediaFiles(): Collection
    {
        return $this->facts(['FILE'])
            ->map(fn (Fact $fact): MediaFile => new MediaFile($fact->gedcom(), $this));
    }

    /**
     * Get the first media file that contains an image.
     */
    public function firstImageFile(): MediaFile|null
    {
        return $this->mediaFiles()
            ->first(static fn (MediaFile $media_file): bool => $media_file->isImage() && !$media_file->isExternal());
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

        // Titles may be empty.
        $names = array_filter($names);

        if ($names === []) {
            foreach ($this->mediaFiles() as $media_file) {
                $names[] = $media_file->filename();
            }
        }

        // Name and title may be the same.
        $names = array_unique($names);

        // No media files in this media object?
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
        return (new XrefMedia(I18N::translate('Media')))
            ->labelValue('@' . $this->xref . '@', $this->tree());
    }

    /**
     * Display an image-thumbnail or a media-icon, and add markup for image viewers such as colorbox.
     *
     * @param int                  $width      Pixels
     * @param int                  $height     Pixels
     * @param string               $fit        "crop" or "contain"
     * @param array<string,string> $attributes Additional HTML attributes
     *
     * @return string
     */
    public function displayImage(int $width, int $height, string $fit, array $attributes): string
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

    /**
     * Lock the database row, to prevent concurrent edits.
     */
    public function lock(): void
    {
        DB::table('media')
            ->where('m_file', '=', $this->tree->id())
            ->where('m_id', '=', $this->xref())
            ->lockForUpdate()
            ->get();
    }
}
