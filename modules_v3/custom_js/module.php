<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * Copyright (C) 2015 JustCarmen
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
use Fisharebest\Webtrees\Controller\BaseController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;

class CustomJavaScriptModule extends AbstractModule implements ModuleConfigInterface, ModuleMenuInterface {

	public function __construct() {
		parent::__construct('custom_js');
	}

	// Extend WT_Module
	public function getTitle() {
		return I18N::translate('Custom JavaScript');
	}

	// Extend WT_Module
	public function getDescription() {
		return I18N::translate('Allows you to easily add Custom JavaScript to your webtrees site.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch ($mod_action) {
			case 'admin_config':
				$controller = new PageController;
				$controller
					->restrictAccess(Auth::isAdmin())
					->setPageTitle($this->getTitle())
					->pageHeader()
					->addInlineJavascript('
						jQuery("head").append("<style type=\"text/css\">h2, button {margin-left:15px}</style>");
					');

				if (Filter::postBool('save') && Filter::checkCsrf()) {
					$this->setSetting('CJS_FOOTER', Filter::post('NEW_CJS_FOOTER'));
					Log::addConfigurationLog($this->getTitle() . ' config updated');
				}

				$CJS_FOOTER = $this->getSetting('CJS_FOOTER');
				?>
				<ol class="breadcrumb small">
					<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
					<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
					<li class="active"><?php echo $controller->getPageTitle(); ?></li>
				</ol>
				<h2><?php echo $this->getTitle(); ?></h2>
				<form method="post" name="custom-javascript-form">
					<?php echo Filter::getCsrf(); ?>
					<input type="hidden" name="save" value="1">
					<div class="form-group col-sm-12">
						<label class="control-label col-sm-8">
							<textarea class="form-control" rows="15" name="NEW_CJS_FOOTER"><?php echo $CJS_FOOTER; ?></textarea>
						</label>						
					</div>
					<button class="btn btn-primary" type="submit">						
						<i class="fa fa-check"></i>
						<?php echo I18N::translate('save'); ?>
					</button>
				</form>
				<?php
				break;

			default:
				http_response_code(404);
				break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
	}

	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 999;
	}

	// Implement WT_Module_Menu
	public function getMenu() {
		// We don't actually have a menu - this is just a convenient "hook" to execute
		// code at the right time during page execution
		global $controller, $WT_TREE;
		
		if (Theme::theme()->themeId() !== '_administration') {
			$cjs_footer = $this->getSetting('CJS_FOOTER');
			if (strpos($cjs_footer, '#') !== false) {
				# parse for embedded keywords
				$stats = new Stats($WT_TREE);
				$cjs_footer = $stats->embedTags($cjs_footer);
			}
			$controller->addInlineJavaScript($cjs_footer, BaseController::JS_PRIORITY_LOW);
		}
		return null;
	}

}

return new CustomJavaScriptModule;
