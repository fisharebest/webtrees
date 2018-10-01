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

namespace Fisharebest\Webtrees;

/**
 * A GEDCOM place (PLAC) object.
 */
class Place
{
    const GEDCOM_SEPARATOR = ', ';

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
            $this->gedcom_place = explode(self::GEDCOM_SEPARATOR, $gedcom_place);
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
        return end($this->gedcom_place);
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
            $place_id = (int) Database::prepare(
                "SELECT p_id FROM `##places` WHERE p_parent_id = :parent_id AND p_place = :place AND p_file = :tree_id"
            )->execute([
                'parent_id' => $place_id,
                'place'     => $place,
                'tree_id'   => $this->tree->getTreeId(),
            ])->fetchOne();
        }

        return (int) $place_id;
    }

    /**
     * Get the higher level place.
     *
     * @return Place
     */
    public function getParentPlace(): Place
    {
        return new self(implode(self::GEDCOM_SEPARATOR, array_slice($this->gedcom_place, 1)), $this->tree);
    }

    /**
     * Get the lower level places.
     *
     * @return Place[]
     */
    public function getChildPlaces(): array
    {
        $children = [];
        if ($this->getPlaceId()) {
            $parent_text = self::GEDCOM_SEPARATOR . $this->getGedcomName();
        } else {
            $parent_text = '';
        }

        $rows = Database::prepare(
            "SELECT p_place FROM `##places`" .
            " WHERE p_parent_id = :parent_id AND p_file = :tree_id" .
            " ORDER BY p_place COLLATE :collation"
        )->execute([
            'parent_id' => $this->getPlaceId(),
            'tree_id'   => $this->tree->getTreeId(),
            'collation' => I18N::collation(),
        ])->fetchOneColumn();
        foreach ($rows as $row) {
            $children[] = new self($row . $parent_text, $this->tree);
        }

        return $children;
    }

    /**
     * Create a URL to the place-hierarchy page.
     *
     * @return string
     */
    public function getURL(): string
    {
        return e(route('place-hierarchy', [
            'parent' => array_reverse($this->gedcom_place),
            'ged'    => $this->tree->getName(),
        ]));
    }

    /**
     * Format this name for GEDCOM data.
     *
     * @return string
     */
    public function getGedcomName(): string
    {
        return implode(self::GEDCOM_SEPARATOR, $this->gedcom_place);
    }

    /**
     * Format this place for display on screen.
     *
     * @return string
     */
    public function getPlaceName(): string
    {
        $place = reset($this->gedcom_place);

        return $place ? '<span dir="auto">' . e($place) . '</span>' : I18N::translate('unknown');
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
            $short_name = implode(self::GEDCOM_SEPARATOR, array_slice($this->gedcom_place, -$SHOW_PEDIGREE_PLACES));
        } else {
            // The *first* $SHOW_PEDIGREE_PLACES components
            $short_name = implode(self::GEDCOM_SEPARATOR, array_slice($this->gedcom_place, 0, $SHOW_PEDIGREE_PLACES));
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

    /**
     * Fetch all places from the database.
     *
     * @param Tree $tree
     *
     * @return Place[]
     */
    public static function allPlaces(Tree $tree): array
    {
        $places = [];
        $rows   =
            Database::prepare(
                "SELECT CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place)" .
                " FROM      `##places` AS p1" .
                " LEFT JOIN `##places` AS p2 ON (p1.p_parent_id = p2.p_id)" .
                " LEFT JOIN `##places` AS p3 ON (p2.p_parent_id = p3.p_id)" .
                " LEFT JOIN `##places` AS p4 ON (p3.p_parent_id = p4.p_id)" .
                " LEFT JOIN `##places` AS p5 ON (p4.p_parent_id = p5.p_id)" .
                " LEFT JOIN `##places` AS p6 ON (p5.p_parent_id = p6.p_id)" .
                " LEFT JOIN `##places` AS p7 ON (p6.p_parent_id = p7.p_id)" .
                " LEFT JOIN `##places` AS p8 ON (p7.p_parent_id = p8.p_id)" .
                " LEFT JOIN `##places` AS p9 ON (p8.p_parent_id = p9.p_id)" .
                " WHERE p1.p_file = :tree_id" .
                " ORDER BY CONCAT_WS(', ', p9.p_place, p8.p_place, p7.p_place, p6.p_place, p5.p_place, p4.p_place, p3.p_place, p2.p_place, p1.p_place) COLLATE :collate"
            )
                ->execute([
                    'tree_id' => $tree->getTreeId(),
                    'collate' => I18N::collation(),
                ])->fetchOneColumn();
        foreach ($rows as $row) {
            $places[] = new self($row, $tree);
        }

        return $places;
    }

    /**
     * Search for a place name.
     *
     * @param string $filter
     * @param Tree   $tree
     *
     * @return Place[]
     */
    public static function findPlaces($filter, Tree $tree): array
    {
        $places = [];
        $rows   =
            Database::prepare(
                "SELECT CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place)" .
                " FROM      `##places` AS p1" .
                " LEFT JOIN `##places` AS p2 ON (p1.p_parent_id = p2.p_id)" .
                " LEFT JOIN `##places` AS p3 ON (p2.p_parent_id = p3.p_id)" .
                " LEFT JOIN `##places` AS p4 ON (p3.p_parent_id = p4.p_id)" .
                " LEFT JOIN `##places` AS p5 ON (p4.p_parent_id = p5.p_id)" .
                " LEFT JOIN `##places` AS p6 ON (p5.p_parent_id = p6.p_id)" .
                " LEFT JOIN `##places` AS p7 ON (p6.p_parent_id = p7.p_id)" .
                " LEFT JOIN `##places` AS p8 ON (p7.p_parent_id = p8.p_id)" .
                " LEFT JOIN `##places` AS p9 ON (p8.p_parent_id = p9.p_id)" .
                " WHERE CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) LIKE CONCAT('%', :filter_1, '%') AND CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) NOT LIKE CONCAT('%,%', :filter_2, '%') AND p1.p_file = :tree_id" .
                " ORDER BY  CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) COLLATE :collation"
            )->execute([
                'filter_1' => preg_quote($filter),
                'filter_2' => preg_quote($filter),
                'tree_id' => $tree->getTreeId(),
                'collation' => I18N::collation(),
            ])->fetchOneColumn();
        foreach ($rows as $row) {
            $places[] = new self($row, $tree);
        }

        return $places;
    }
}
