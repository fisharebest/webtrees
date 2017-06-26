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
 * Helper functions to generate markup for FontAwesome.
 *
 * @link http://fontawesome.io/accessibility
 */
class FontAwesome extends Html {
	/** Which font-awesome icon to use for which action/entity */
	const ICONS = [
		// Application icons
		'add'                => 'fa fa-plus wt-icon-add',
		'calendar'           => 'fa fa-calendar wt-icon-calendar',
		'cancel'             => 'fa fa-close wt-icon-cancel',
		'coordinates'        => 'fa fa-map-marker wt-icon-coordinates',
		'copy'               => 'fa fa-copy wt-icon-copy',
		'delete'             => 'fa fa-trash-o wt-icon-delete',
		'download'           => 'fa fa-download wt-icon-download',
		'edit'               => 'fa fa-pencil wt-icon-edit',
		'help'               => 'fa fa-info-circle wt-icon-help',
		'email'              => 'fa fa-envelope-o wt-icon-email',
		'keyboard'           => 'fa fa-keyboard-o wt-icon-keyboard',
		'pin'                => 'fa fa-thumb-tack wt-icon-pin',
		'preferences'        => 'fa fa-wrench wt-icon-preferences',
		'save'               => 'fa fa-check wt-icon-save',
		'warning'            => 'fa fa-warning wt-icon-warning',
		// Arrows (start/end variants require fontawesome-rtl library)
		'arrow-down'         => 'fa fa-arrow-down wt-icon-arrow-down',
		'arrow-end'          => 'fa fa-arrow-end wt-icon-arrow-end',
		'arrow-start'        => 'fa fa-arrow-start wt-icon-arrow-start',
		'arrow-up'           => 'fa fa-arrow-up wt-icon-arrow-up',
		// GEDCOM records
		'family'             => 'fa fa-users wt-icon-family',
		'individual'         => 'fa fa-user wt-icon-individual',
		'note'               => 'fa fa-sticky-note-o wt-icon-note',
		'media'              => 'fa fa-file-imate-o wt-icon-media',
		'repository'         => 'fa fa-institution wt-icon-repository',
		'source'             => 'fa fa-file-text-o wt-icon-source',
		'submitter'          => 'fa fa-user-o wt-icon-submitter',
		'upload'             => 'fa fa-upload wt-icon-upload',
		// External sites and applications
		'bing-maps'          => 'fa fa-icon-map-o wt-icon-bing-maps',
		'google-maps'        => 'fa fa-icon-map-o wt-icon-google-maps',
		'openstreetmap'      => 'fa fa-icon-map-o wt-icon-openstreetmap',
	];

	/**
	 * Generate a decorative icon.
	 *
	 * These icons are shown in addition to other text, and should be ignored
	 * by assistive technology.
	 *
	 * @param string   $icon       The icon to show
	 * @param string[] $attributes Additional HTML attributes
	 *
	 * @return string
	 */
	public static function decorativeIcon($icon, $attributes = []) {
		$attributes['class']       = self::ICONS[$icon];
		$attributes['aria-hidden'] = 'true';

		return '<i ' . self::attributes($attributes) . '></i>';
	}

	/**
	 * Generate a semantic icon.
	 *
	 * These icons convey meaning, such as status/type/mode, and need
	 * to allow assistive technology to display this meaning.
	 *
	 * @param string   $icon       The icon to show
	 * @param string   $title      The meaning of the icon
	 * @param string[] $attributes Additional HTML attributes
	 *
	 * @return string
	 */
	public static function semanticIcon($icon, $title, $attributes = []) {
		$attributes['title'] = $title;

		return self::decorativeIcon($icon, $attributes) . '<span class="sr-only">' . $title . '</span>';
	}

	/**
	 * Generate a link icon.
	 *
	 * @param string   $icon       The icon to show
	 * @param string   $title      The meaning of the icon
	 * @param string[] $attributes Additional HTML attributes
	 *
	 * @return string
	 */
	public static function linkIcon($icon, $title, $attributes = []) {
		$attributes['aria-label'] = strip_tags($title);

		return '<a ' . self::attributes($attributes) . '>' . self::decorativeIcon($icon, ['title' => $title]) . '</a>';
	}
}
