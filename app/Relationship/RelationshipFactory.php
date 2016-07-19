<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Relationship;

/**
 * Definitions for localized relationships.
 */
class RelationshipFactory {
	/**
	 * Create a relationship localization object from a locale name
	 *
	 * @param $locale "en-US", "fr", "zh-Hans", etc.
	 *
	 * @return RelationshipInterface
	 */
	public static function createRelationship($locale) {
		// Convert the locale into a class name
		$class = __NAMESPACE__ . '\Relationship' . implode(array_map(function ($x) {
				return ucfirst(strtolower($x));
			}, explode('-', $locale)));

		if (class_exists($class)) {
			return new $class;
		} else {
			return new RelationshipEn;
		}
	}
}
