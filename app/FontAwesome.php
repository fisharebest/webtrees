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
		'add'         => 'fas fa-plus wt-icon-add',
		'calendar'    => 'fas fa-calendar-alt wt-icon-calendar',
		'cancel'      => 'fas fa-times wt-icon-cancel',
		'coordinates' => 'fas fa-map-marker-alt wt-icon-coordinates',
		'copy'        => 'far fa-copy wt-icon-copy',
		'delete'      => 'far fa-trash-alt wt-icon-delete',
		'download'    => 'fas fa-download wt-icon-download',
		'drag-handle' => 'fas fa-bars wt-icon-drag-handle',
		'edit'        => 'fas fa-pencil-alt wt-icon-edit',
		'help'        => 'fas fa-info-circle wt-icon-help',
		'email'       => 'far fa-envelope wt-icon-email',
		'keyboard'    => 'far fa-keyboard wt-icon-keyboard',
		'pin'         => 'fas fa-thumbtack wt-icon-pin',
		'preferences' => 'fas fa-wrench wt-icon-preferences',
		'search'      => 'fas fa-search wt-icon-search',
		'save'        => 'fas fa-check wt-icon-save',
		'sort'        => 'fas fa-sort wt-icon-sort',
		'warning'     => 'fas fa-exclamation-triangle wt-icon-warning',
		// Arrows (start/end variants require fontawesome-rtl library)
		'arrow-down'  => 'fas fa-arrow-down wt-icon-arrow-down',
		'arrow-end'   => 'fas fa-arrow-end wt-icon-arrow-end',
		'arrow-start' => 'fas fa-arrow-start wt-icon-arrow-start',
		'arrow-up'    => 'fas fa-arrow-up wt-icon-arrow-up',
		// Modules
		'block'      => 'fas fa-th-list fa-flip-horizontal wt-icon-block',
		'block-user' => 'far fa-user wt-icon-block-user',
		'block-tree' => 'fas fa-tree wt-icon-block-tree',
		'chart'      => 'fas fa-sitemap wt-icon-chart',
		'menu'       => 'far fa-bar wt-icon-menu',
		'report'     => 'far fa-file wt-icon-report',
		'sidebar'    => 'fas fa-pause wt-icon-sidebar',
		'tab'        => 'far fa-folder wt-icon-tab',
		'theme'      => 'fas fa-paint-brush wt-icon-theme',
		// GEDCOM records
		'family'     => 'fas fa-users wt-icon-family',
		'individual' => 'far fa-user wt-icon-individual',
		'note'       => 'far fa-sticky-note wt-icon-note',
		'media'      => 'far fa-file-image wt-icon-media',
		'repository' => 'fas fa-university wt-icon-repository',
		'source'     => 'far fa-file-alt wt-icon-source',
		'submitter'  => 'far fa-user wt-icon-submitter',
		'upload'     => 'fas fa-upload wt-icon-upload',
		// External sites and applications
		'bing-maps'     => 'fas fa-map wt-icon-bing-maps',
		'google-maps'   => 'fas fa-map wt-icon-google-maps',
		'openstreetmap' => 'fas fa-map wt-icon-openstreetmap',
		// Slideshow
		'media-play' => 'fas fa-play wt-icon-media-play',
		'media-stop' => 'fas fa-stop wt-icon-media-stop',
		'media-next' => 'fas fa-step-forward wt-icon-media-next',
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
