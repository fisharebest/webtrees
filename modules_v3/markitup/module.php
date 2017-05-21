<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
// module developed by David Drury

class markitup_WT_Module extends WT_Module {
	CONST MIU = 'markitup-1.1.14-custom';
	CONST MIU_SKIN = 'simple';

	private static $set = 'html';
	private static $modname = null;
	private static $WT_MIU_URL;
	private static $WT_MIUSET_URL;

	public function __construct() {
		global $WT_TREE;
		parent::__construct();
		self::$modname = str_replace('_WT_Module', '', __CLASS__);
		if (isset($WT_TREE) && $WT_TREE->getPreference('FORMAT_TEXT') === 'markdown') {
			self::$set = 'markdown';
		}
		self::$WT_MIU_URL    = WT_MODULES_DIR . self::$modname . '/' . self::MIU . '/';
		self::$WT_MIUSET_URL = self::$WT_MIU_URL . 'sets/' . self::$set . '/';
	}

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module. */
			WT_I18N::translate('markItUp editor');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: */
			WT_I18N::translate('markItUp markdown and HTML editor');
	}

	public function modAction($mod_action) {
		switch ($mod_action) {
			case 'preview':
				$this->preview();
				break;
			default:
				header('HTTP/1.0 404 Not Found');
		}
	}

	/**
	 * Function preview
	 *
	 * @return void
	 */
	private function preview() {
		global $WT_TREE;
		$text       = WT_Filter::post('data');
		$article = $WT_TREE->getPreference('FORMAT_TEXT') == '' ?  $text :  WT_Filter::formatText($text, $WT_TREE);
		$controller = new WT_Controller_Simple;
		$controller->pageHeader();
		$stats = new WT_Stats(WT_GEDCOM);
		echo $stats->embedTags($article);
	}

	/**
	 * Load editor in preparation for markup editing
	 *
	 * This function needs to be called *after* we have sent the page header and
	 * before we have sent the page footer.
	 *
	 * @param WT_Controller_Base $controller
	 *
	 * @return void
	 */
	public static function loadEditor($controller) {
		$controller
			->addExternalJavascript(self::$WT_MIU_URL . 'jquery.markitup.min.js')
			->addExternalJavascript(self::$WT_MIUSET_URL . 'set.js')

			->addInlineJavascript("
				jQuery('head')
					.append(\"<link type='text/css' rel='stylesheet' href='" . self::$WT_MIU_URL . "skins/" . self::MIU_SKIN . "/style.css'>\")
					.append(\"<link type='text/css' rel='stylesheet' href='" . self::$WT_MIUSET_URL . "style.css'>\");
			");
	}

	/**
	 * static Function enableEditor
	 *
	 * Attach the editor to some DOM elements
	 *
	 * This function must be called after loadEditor
	 *
	 * @param WT_Controller_Base $controller
	 * @param string $element
	 *
	 * @return void
	 */
	public static function enableEditor($controller, $element) {
		$controller
			->addInlineJavascript("
				jQuery('" . $element . "').markItUp(
					jQuery.extend(
						mySettings, {
							previewInWindow: 'width=700, height=400, resizable=yes, scrollbars=yes',
							previewParserPath:'module.php?mod=" . self::$modname . "&mod_action=preview'
						}
					)
				);
			");
	}
}
