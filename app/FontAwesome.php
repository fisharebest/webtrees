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
		'add'         => 'fa fa-plus wt-icon-add',
		'calendar'    => 'fas fa-calendar-alt wt-icon-calendar',
		'cancel'      => 'fas fa-times wt-icon-cancel',
		'coordinates' => 'fas fa-map-marker-alt wt-icon-coordinates',
		'copy'        => 'fa fa-copy wt-icon-copy',
		'delete'      => 'far fa-trash-alt wt-icon-delete',
		'download'    => 'fa fa-download wt-icon-download',
		'drag-handle' => 'fa fa-bars wt-icon-drag-handle',
		'edit'        => 'fas fa-pencil-alt wt-icon-edit',
		'help'        => 'fa fa-info-circle wt-icon-help',
		'email'       => 'far fa-envelope wt-icon-email',
		'keyboard'    => 'far fa-keyboard wt-icon-keyboard',
		'pin'         => 'fas fa-thumbtack wt-icon-pin',
		'preferences' => 'fa fa-wrench wt-icon-preferences',
		'search'      => 'fa fa-search wt-icon-search',
		'save'        => 'fa fa-check wt-icon-save',
		'sort'        => 'fa fa-sort wt-icon-sort',
		'warning'     => 'fas fa-exclamation-triangle wt-icon-warning',
		// Arrows (start/end variants require fontawesome-rtl library)
		'arrow-down'  => 'fa fa-arrow-down wt-icon-arrow-down',
		'arrow-end'   => 'fa fa-arrow-end wt-icon-arrow-end',
		'arrow-start' => 'fa fa-arrow-start wt-icon-arrow-start',
		'arrow-up'    => 'fa fa-arrow-up wt-icon-arrow-up',
		// Modules
		'block'      => 'fa fa-th-list wt-icon-block',
		'block-user' => 'fa fa-user wt-icon-block-user',
		'block-tree' => 'fa fa-tree wt-icon-block-tree',
		'chart'      => 'fa fa-share-alt wt-icon-chart',
		'menu'       => 'fa fa-list-ul wt-icon-menu',
		'report'     => 'fa fa-file wt-icon-report',
		'sidebar'    => 'fa fa-pause wt-icon-sidebar',
		'tab'        => 'fa fa-folder wt-icon-tab',
		'theme'      => 'fa fa-paint-brush wt-icon-theme',
		// GEDCOM records
		'family'     => 'fa fa-users wt-icon-family',
		'individual' => 'fa fa-user wt-icon-individual',
		'note'       => 'far fa-sticky-note wt-icon-note',
		'media'      => 'far fa-file-image wt-icon-media',
		'repository' => 'fas fa-university wt-icon-repository',
		'source'     => 'far fa-file-alt wt-icon-source',
		'submitter'  => 'far fa-user wt-icon-submitter',
		'upload'     => 'fa fa-upload wt-icon-upload',
		// External sites and applications
		'bing-maps'     => 'fa fa-icon-map-o wt-icon-bing-maps',
		'google-maps'   => 'fa fa-icon-map-o wt-icon-google-maps',
		'openstreetmap' => 'fa fa-icon-map-o wt-icon-openstreetmap',
		// Slideshow
		'media-play' => 'fa fa-play wt-icon-media-play',
		'media-stop' => 'fa fa-stop wt-icon-media-stop',
		'media-next' => 'fa fa-step-forward wt-icon-media-next',
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
		if (empty($attributes['class'])) {
			$attributes['class'] = self::ICONS[$icon];
		} else {
			$attributes['class'] .= ' ' . self::ICONS[$icon];
		}
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
		$title                    = strip_tags($title);
		$attributes['aria-label'] = $title;

		return '<a ' . self::attributes($attributes) . '>' . self::decorativeIcon($icon, ['title' => $title]) . '</a>';
	}
}
