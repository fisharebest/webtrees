<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_export.php';

class opensearch_WT_Module extends WT_Module implements WT_Module_Config{
	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('OpenSearch');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('Generate OpenSearch file that identify a search engine on this site.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_generate':
			$this->genereate();
			break;
		case 'admin_config':
			$this->config();
			break;
		default:
			die("Internal error - unknown action: $mod_action");
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}
	
	private function config() {
		if (WT_USER_IS_ADMIN) {
			print_header($this->getTitle());
			echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_generate">', WT_I18N::translate('Generate an OpenSearch file'), '</a>';
			echo help_link('OPENSEARCH', $this->getName());
			print_footer();
		} else {
			if (WT_USER_ID) {
				header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
				exit;
			} else {
				header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url=module.php?mod=', $this->getName());
				exit;
			}
		}
	}
	
	private function genereate() {
		if (WT_USER_IS_ADMIN) {
			header('Content-Type: application/opensearchdescription+xml; charset=utf-8');
			header('Content-Disposition: attachment; filename="opensearch.xml"');
			echo '<?xml version="1.0" encoding="UTF-8" ?>';
			echo '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">';
			echo '<ShortName>'.WT_I18N::translate('Search').' ' .get_gedcom_setting(WT_GED_ID, 'title').'</ShortName>';
			echo '<Description>'.WT_I18N::translate('Search').' ' .get_gedcom_setting(WT_GED_ID, 'title').'</Description>';
			echo '<InputEncoding>UTF-8</InputEncoding>';
			echo '<Url type="text/html" template="' . WT_SERVER_NAME.WT_SCRIPT_PATH. 'search.php?action=general&amp;topsearch=yes&amp;query={searchTerms}"/>';
			echo '<Url type="application/x-suggestions+json" template="' . WT_SERVER_NAME.WT_SCRIPT_PATH. 'autocomplete.php?limit=20&amp;field=NAME&amp;fmt=json&amp;q={searchTerms}"/>';
			echo'<Image height="16" width="16" type="image/x-icon">' . WT_SERVER_NAME.WT_SCRIPT_PATH . 'favicon.ico</Image>';
			echo '</OpenSearchDescription>';
			AddToLog("creation of OpenSearch file", 'auth');
			exit;
		} else {
			if (WT_USER_ID) {
				header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
				exit;
			} else {
				header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url=module.php?mod=', $this->getName());
				exit;
			}
		}
	}
}
