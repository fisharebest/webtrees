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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Controller\BaseController;
use Fisharebest\Webtrees\I18N;

/**
 * Class CkeditorModule
 */
class CkeditorModule extends AbstractModule {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. CKEditor is a trademark. Do not translate it? http://ckeditor.com */ I18N::translate('CKEditor™');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “CKEditor” module. WYSIWYG = “what you see is what you get” */ I18N::translate('Allow other modules to edit text using a “WYSIWYG” editor, instead of using HTML codes.');
	}

	/**
	 * Convert <textarea class="html-edit"> fields to CKEditor fields
	 *
	 * This function needs to be called *after* we have sent the page header and
	 * before we have sent the page footer.
	 *
	 * @param BaseController $controller
	 */
	public static function enableEditor($controller) {
		$controller
			->addExternalJavascript(WT_CKEDITOR_BASE_URL . 'ckeditor.js')
			->addExternalJavascript(WT_CKEDITOR_BASE_URL . 'adapters/jquery.js')
			// Need to specify the path before we load the libary
			->addInlineJavascript(
				'var CKEDITOR_BASEPATH="' . WT_CKEDITOR_BASE_URL . '";',
				BaseController::JS_PRIORITY_HIGH
			)
			// Enable for all browsers
			->addInlineJavascript('CKEDITOR.env.isCompatible = true;')
			// Disable toolbars
			->addInlineJavascript('CKEDITOR.config.removePlugins = "forms,newpage,preview,print,save,templates";')
			->addInlineJavascript('CKEDITOR.config.extraAllowedContent = 
    "area[shape,coords,href,target,alt,title];map[name];img[usemap]";')
			// Activate the editor
			->addInlineJavascript('jQuery(".html-edit").ckeditor(function(config){config.removePlugins = "forms";}, {
				language: "' . strtolower(WT_LOCALE) . '"
			});');
	}
}
