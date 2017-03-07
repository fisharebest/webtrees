<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

/**
 * Helper functions to generate markup for Bootstrap 4.
 *
 * @link https://www.getbootstrap.com
 */
class Bootstrap4 extends Html {
	/**
	 * Generate a badge containing a count of items.
	 *
	 * @param array $items
	 */
	public static function badgeCount(array $items) {
		if (empty($items)) {
			return '';
		} else {
			return '<span class="badge badge-default">' . I18N::number(count($items)) . '</span>';
		}
	}

	/**
	 * Generate a breadcrumb trail.
	 *
	 * @param array  $hierarchy
	 * @param string $active
	 *
	 * @return string
	 */
	public static function breadcrumbs(array $hierarchy, $active) {
		$html = '<ol class="breadcrumb">';

		foreach ($hierarchy as $url => $label) {
			$html .= '<li class="breadcrumb-item"><a href="' . $url . '">' . $label . '</a></li>';
		}

		$html .= '<li class="breadcrumb-item active">' . $active . '</li></ol>';

		return $html;
	}

	/**
	 * Generate a checkbox.
	 *
	 * @param string   $label
	 * @param bool     $inline
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	public static function checkbox($label, $inline, $attributes = []) {
		if ($inline) {
			$class = 'form-check form-check-inline';
		} else {
			$class = 'form-check';
		}

		$input_attributes = self::attributes([
				'class'   => 'form-check-input',
				'type'    => 'checkbox',
				'value'   => '1',
			] + $attributes);

		return
			'<div class="' . $class . '">' .
			'<label class="form-check-label">' .
			'<input ' . $input_attributes . '> ' . Filter::escapeHtml($label) .
			'</label>' .
			'</div>';
	}

	/**
	 * Create a set of radio buttons for a form
	 *
	 * @param string   $name The ID for the form element
	 * @param string[] $values Array of value=>display items
	 * @param string   $selected The currently selected item
	 * @param bool     $inline
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	public static function radioButtons($name, $values, $selected, $inline, $attributes = []) {
		// An empty string is not the same as zero (but is the same as NULL).
		if ($selected === null) {
			$selected = '0';
		}

		if ($inline) {
			$class = 'form-check form-check-inline';
		} else {
			$class = 'form-check';
		}

		$html = '';
		foreach ($values as $value => $label) {
			$input_attributes = self::attributes([
				'class'   => 'form-check-input',
				'type'    => 'radio',
				'name'    => $name,
				'value'   => $value,
				'checked' => (string) $value === (string) $selected,
			] + $attributes);

			$html .=
				'<div class="' . $class . '">' .
				'<label class="form-check-label">' .
				'<input ' . $input_attributes . '> ' . Filter::escapeHtml($label) .
				'</label>' .
				'</div>';
		}

		return $html;
	}

	/**
	 * Create a <select> control for a form.
	 *
	 * @param string[] $options
	 * @param string   $selected
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	public static function select($options, $selected, $attributes = []) {
		$html = '';
		foreach ($options as $value => $option) {
			$option_attributes = self::attributes([
				'value'    => $value,
				'selected' => (string) $value === (string) $selected,
			]);

			$html .= '<option ' . $option_attributes . '>' . Filter::escapeHtml($option) . '</option>';
		}

		if (empty($attributes['class'])) {
			$attributes['class'] = 'form-control';
		} elseif (strpos($attributes['class'], 'form-control') === false) {
			$attributes['class'] .= ' form-control';
		}

		$select_attributes = self::attributes($attributes);

		return '<select ' . $select_attributes . '>' . $html . '</select>';
	}
}
