<?php
// TreeView module class
//
// Tip : you could change the number of generations loaded before ajax calls both in individual page and in treeview page to optimize speed and server load
//
// Copyright (C) 2014 webtrees development team
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

class tree_WT_Module extends WT_Module implements WT_Module_Tab {
	var $headers; // CSS and script to include in the top of <head> section, before theme’s CSS
	var $js; // the TreeViewHandler javascript

	// Extend WT_Module. This title should be normalized when this module will be added officially
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Interactive tree');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Interactive tree” module */ WT_I18N::translate('An interactive tree, showing all the ancestors and descendants of an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 68;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $controller;

		require_once WT_MODULES_DIR . $this->getName() . '/class_treeview.php';
		$tv = new TreeView('tvTab');
		list($html, $js) = $tv->drawViewport($controller->record, 3);
		return
			'<script src="' . $this->js() . '"></script>' .
			'<script>' . $js . '</script>' .
			$html;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER;
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return false;
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
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

	// Extend WT_Module
	// We define here actions to proceed when called, either by Ajax or not
	/**
	 * @param string $mod_action
	 *
	 * @throws Exception
	 */
	public function modAction($mod_action) {
		require_once WT_MODULES_DIR . $this->getName() . '/class_treeview.php';
		switch ($mod_action) {
		case 'treeview':
			global $controller;
			$controller = new WT_Controller_Chart();
			$tv = new TreeView('tv');
			ob_start();

			$person = $controller->getSignificantIndividual();

			list($html, $js) = $tv->drawViewport($person, 4);

			$controller
				->setPageTitle(WT_I18N::translate('Interactive tree of %s', $person->getFullName()))
				->pageHeader()
				->addExternalJavascript($this->js())
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
			Zend_Session::writeClose();
			header('Content-Type: text/html; charset=UTF-8');
			$pid = WT_Filter::get('pid', WT_REGEX_XREF);
			$i = WT_Filter::get('instance');
			$tv = new TreeView($i);
			$individual = WT_Individual::getInstance($pid);
			if ($individual) {
				echo $tv->getDetails($individual);
			}
			break;

		case 'getPersons':
			Zend_Session::writeClose();
			header('Content-Type: text/html; charset=UTF-8');
			$q = WT_Filter::get('q');
			$i = WT_Filter::get('instance');
			$tv = new TreeView($i);
			echo $tv->getPersons($q);
			break;

		default:
			header('HTTP/1.0 404 Not Found');
			break;
		}
	}

	public function css() {
		return WT_STATIC_URL . WT_MODULES_DIR . $this->getName() . '/css/treeview.css';
	}

	public function js() {
		return WT_STATIC_URL . WT_MODULES_DIR . $this->getName() . '/js/treeview.js';
	}
}
