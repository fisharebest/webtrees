<?php
namespace Fisharebest\Webtrees;
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class markitup_WT_Module
 * @package Fisharebest\Webtrees
 */
class markItUpModule extends Module {
	const MIU = 'markitup-1.1.14-custom';
	const MIU_SKIN = 'markitup'; // or simple

	private static $set;
	private static $modname;
	private static $miu_url;     // base location of markItUp
	private static $miuset_url;  // location of markItUp set

	/** {@inheritdoc} */
	public function __construct($directory) {
		global $WT_TREE;

		parent::__construct($directory);

		switch ($WT_TREE->getPreference('FORMAT_TEXT')) {
			case '':
				self::$set = 'html';
				break;
			case 'markdown':
				self::$set = 'markdown';
				break;
			default:
				self::$set = 'default';
		}
		self::$modname    = $this->getName();
		self::$miu_url    = WT_MODULES_DIR . self::$modname . '/' . self::MIU . '/';
		self::$miuset_url = self::$miu_url . 'sets/' . self::$set . '/';
	}

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. */
			I18N::translate('markItUp! editor');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: */
			I18N::translate('markItUp! universal markup editor');
	}

	/** {@inheritdoc} */
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
	 */
	private function preview() {
		global $WT_TREE;

		$controller = new SimpleController;
		$controller->pageHeader();

		// Can't use Filter::formatText() when then format isn't markdown because
		// the '<' & '>' characters are replaced by their filtered entities
		// thus rendering any embedded html useless
		$stats = new Stats($WT_TREE);
		$data = $stats->embedTags(Filter::post('data'));
		echo (self::$set === 'markdown') ? Filter::formatText($data, $WT_TREE) : $data;
	}

	/**
	 * static Function loadEditor
	 *
	 * Load editor in preparation for markup editing
	 *
	 * This function needs to be called *after* we have sent the page header and
	 * before we have sent the page footer.
	 *
	 * @param BaseController $controller
	 */
	public static function loadEditor($controller) {
		$controller
			->addExternalJavascript(self::$miu_url . 'jquery.markitup.min.js')
			->addExternalJavascript(self::$miuset_url . 'set.js')
			->addInlineJavascript("
				jQuery('head')
					.append(\"<link type='text/css' rel='stylesheet' href='" . self::$miu_url . "skins/" . self::MIU_SKIN . "/style.css'>\")
					.append(\"<link type='text/css' rel='stylesheet' href='" . self::$miuset_url . "style.css'>\");
			");
	}

	/**
	 * static Function enableEditor
	 *
	 * Attach the editor to some DOM elements
	 *
	 * This function must be called after loadEditor
	 *
	 * @param BaseController $controller
	 * @param string $element
	 */
	public static function enableEditor($controller, $element) {
		$controller
			->addInlineJavascript("
				jQuery('$element')
				.css('resize', 'none')
				.markItUp(
					jQuery.extend(
						mySettings, {
							nameSpace: '" . self::$set . "',
							resizeHandle: false,
							previewAutoRefresh: true,
							previewInWindow: 'width=700, height=400, resizable=yes, scrollbars=yes',
							previewParserPath:'module.php?mod=" . self::$modname . "&mod_action=preview'
						}
					)
				);
			");
	}
}
return new markItUpModule(__DIR__);
