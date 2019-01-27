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

use Illuminate\Database\Capsule\Manager as DB;

/**
 * A GEDCOM place (PLAC) object.
 */
class Place
{
    /** @var string[] e.g. array('Westminster', 'London', 'England') */
    private $gedcom_place;

    /** @var Tree We may have the same place name in different trees. */
    private $tree;

    /**
     * Create a place.
     *
     * @param string $gedcom_place
     * @param Tree   $tree
     */
    public function __construct($gedcom_place, Tree $tree)
    {
        if ($gedcom_place === '') {
            $this->gedcom_place = [];
        } else {
            $this->gedcom_place = explode(Gedcom::PLACE_SEPARATOR, $gedcom_place);
        }
        $this->tree = $tree;
    }

    /**
     * Extract the country (last part) of a place name.
     *
     * @return string - e.g. "England"
     */
    public function lastPart(): string
    {
        return $this->gedcom_place[count($this->gedcom_place) - 1] ?? '';
    }

    /**
     * Get the identifier for a place.
     *
     * @return int
     */
    public function getPlaceId(): int
    {
        $place_id = 0;

        foreach (array_reverse($this->gedcom_place) as $place) {
            $place_id = (int) DB::table('places')
                ->where('p_file', '=', $this->tree->id())
                ->where('p_place', '=', $place)
                ->where('p_parent_id', '=', $place_id)
                ->value('p_id');
        }

        return $place_id;
    }

    /**
     * Get the higher level place.
     *
     * @return Place
     */
    public function getParentPlace(): Place
    {
        return new self(implode(Gedcom::PLACE_SEPARATOR, array_slice($this->gedcom_place, 1)), $this->tree);
    }

    /**
     * Get the lower level places.
     *
     * @return Place[]
     */
    public function getChildPlaces(): array
    {
        if ($this->getPlaceId()) {
            $parent_text = Gedcom::PLACE_SEPARATOR . $this->getGedcomName();
        } else {
            $parent_text = '';
        }

        return DB::table('places')
            ->where('p_file', '=', $this->tree->id())
            ->where('p_parent_id', '=', $this->getPlaceId())
            ->orderBy(DB::raw('p_place /*! COLLATE ' . I18N::collation() . ' */'))
            ->pluck('p_place')
            ->map(function (string $place) use ($parent_text): Place {
                return new self($place . $parent_text, $this->tree);
            })
            ->all();
    }

    /**
     * Create a URL to the place-hierarchy page.
     *
     * @return string
     */
    public function url(): string
    {
        return route('place-hierarchy', [
            'parent' => array_reverse($this->gedcom_place),
            'ged'    => $this->tree->name(),
        ]);
    }

    /**
     * Format this name for GEDCOM data.
     *
     * @return string
     */
    public function getGedcomName(): string
    {
        return implode(Gedcom::PLACE_SEPARATOR, $this->gedcom_place);
    }

    /**
     * Format this place for display on screen.
     *
     * @return string
     */
    public function getPlaceName(): string
    {
        if (empty($this->gedcom_place)) {
            return  I18N::translate('unknown');
        }

        return '<span dir="auto">' . e($this->gedcom_place[0]) . '</span>';
    }

    /**
     * Is this a null/empty/missing/invalid place?
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->gedcom_place);
    }

    /**
     * Generate the place name for display, including the full hierarchy.
     *
     * @return string
     */
    public function getFullName()
    {
        if (true) {
            // If a place hierarchy is a single entity
            return '<span dir="auto">' . e(implode(I18N::$list_separator, $this->gedcom_place)) . '</span>';
        }

        // If a place hierarchy is a list of distinct items
        $tmp = [];
        foreach ($this->gedcom_place as $place) {
            $tmp[] = '<span dir="auto">' . e($place) . '</span>';
        }

        return implode(I18N::$list_separator, $tmp);
    }

    /**
     * For lists and charts, where the full name won’t fit.
     *
     * @return string
     */
    public function getShortName()
    {
        $SHOW_PEDIGREE_PLACES = (int) $this->tree->getPreference('SHOW_PEDIGREE_PLACES');

        if ($SHOW_PEDIGREE_PLACES >= count($this->gedcom_place)) {
            // A short place name - no need to abbreviate
            return $this->getFullName();
        }

        // Abbreviate the place name, for lists
        if ($this->tree->getPreference('SHOW_PEDIGREE_PLACES_SUFFIX')) {
            // The *last* $SHOW_PEDIGREE_PLACES components
            $short_name = implode(Gedcom::PLACE_SEPARATOR, array_slice($this->gedcom_place, -$SHOW_PEDIGREE_PLACES));
        } else {
            // The *first* $SHOW_PEDIGREE_PLACES components
            $short_name = implode(Gedcom::PLACE_SEPARATOR, array_slice($this->gedcom_place, 0, $SHOW_PEDIGREE_PLACES));
        }

        // Add a tool-tip showing the full name
        return '<span title="' . e($this->getGedcomName()) . '" dir="auto">' . e($short_name) . '</span>';
    }

    /**
     * For the Place hierarchy "list all" option
     *
     * @return string
     */
    public function getReverseName(): string
    {
        $tmp = [];
        foreach (array_reverse($this->gedcom_place) as $place) {
            $tmp[] = '<span dir="auto">' . e($place) . '</span>';
        }

        return implode(I18N::$list_separator, $tmp);
    }
}
