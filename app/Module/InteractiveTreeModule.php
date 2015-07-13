<?php
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\ChartController;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;

/**
 * Class InteractiveTreeModule
 * Tip : you could change the number of generations loaded before ajax calls both in individual page and in treeview page to optimize speed and server load
 */
class InteractiveTreeModule extends AbstractModule implements ModuleTabInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Interactive tree');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Interactive tree” module */ I18N::translate('An interactive tree, showing all the ancestors and descendants of an individual.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 68;
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		global $controller;

		$tv              = new TreeView('tvTab');
		list($html, $js) = $tv->drawViewport($controller->record, 3);

		return
			'<script src="' . $this->js() . '"></script>' .
			'<script src="' . WT_JQUERYUI_TOUCH_PUNCH_URL . '"></script>' .
			'<script>' . $js . '</script>' .
			$html;
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		return !Auth::isSearchEngine();
	}

	/** {@inheritdoc} */
	public function isGrayedOut() {
		return false;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		// We cannot use jQuery("head").append(<link rel="stylesheet" ...as jQuery is not loaded at this time
		return
			'<script>
			if (document.createStyleSheet) {
				document.createStyleSheet("' . $this->css() . '"); // For Internet Explorer
			} else {
				var newSheet=document.createElement("link");
    		newSheet.setAttribute("rel","stylesheet");
    		newSheet.setAttribute("type","text/css");
   			newSheet.setAttribute("href","' . $this->css() . '");
		    document.getElementsByTagName("head")[0].appendChild(newSheet);
			}
			</script>';
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		global $controller, $WT_TREE;

		switch ($mod_action) {
		case 'treeview':
			$controller = new ChartController;
			$tv         = new TreeView('tv');
			ob_start();

			$person = $controller->getSignificantIndividual();

			list($html, $js) = $tv->drawViewport($person, 4);

			$controller
				->setPageTitle(I18N::translate('Interactive tree of %s', $person->getFullName()))
				->pageHeader()
				->addExternalJavascript($this->js())
				->addExternalJavascript(WT_JQUERYUI_TOUCH_PUNCH_URL)
				->addInlineJavascript($js)
				->addInlineJavascript('
					if (document.createStyleSheet) {
						document.createStyleSheet("' . $this->css() . '"); // For Internet Explorer
					} else {
						jQuery("head").append(\'<link rel="stylesheet" type="text/css" href="' . $this->css() . '">\');
					}
				');
			echo $html;
			break;

		case 'getDetails':
			header('Content-Type: text/html; charset=UTF-8');
			$pid        = Filter::get('pid', WT_REGEX_XREF);
			$i          = Filter::get('instance');
			$tv         = new TreeView($i);
			$individual = Individual::getInstance($pid, $WT_TREE);
			if ($individual) {
				echo $tv->getDetails($individual);
			}
			break;

		case 'getPersons':
			header('Content-Type: text/html; charset=UTF-8');
			$q  = Filter::get('q');
			$i  = Filter::get('instance');
			$tv = new TreeView($i);
			echo $tv->getPersons($q);
			break;

		default:
			http_response_code(404);
			break;
		}
	}

	/**
	 * URL for our style sheet.
	 *
	 * @return string
	 */
	public function css() {
		return WT_STATIC_URL . WT_MODULES_DIR . $this->getName() . '/css/treeview.css';
	}

	/**
	 * URL for our JavaScript.
	 *
	 * @return string
	 */
	public function js() {
		return WT_STATIC_URL . WT_MODULES_DIR . $this->getName() . '/js/treeview.js';
	}
}
