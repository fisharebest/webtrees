<?php
/**
 * Batch Update plugin for phpGedView - search/replace
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008 Greg Roach.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Module
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class search_replace_bu_plugin extends base_plugin {
	var $search =null; // Search string
	var $replace=null; // Replace string
	var $method =null; // simple/wildcards/regex
	var $regex  =null; // Search string, converted to a regex
	var $case   =null; // "i" for case insensitive, "" for case sensitive
	var $error  =null; // Message for bad user parameters

	static function getName() {
		return i18n::translate('Search and Replace');
	}

	static function getDescription() {
		return i18n::translate('Search and/or replace data in your GEDCOM using simple searches or advanced pattern matching.');
	}
	
	// Default is to operate on INDI records
	function getRecordTypesToUpdate() {
		return array('INDI', 'FAM', 'SOUR', 'REPO', 'NOTE', 'OBJE');
	}

	function doesRecordNeedUpdate($xref, $gedrec) {
		return !$this->error && preg_match('/(?:'.$this->regex.')/'.$this->case, $gedrec);
	}

	function updateRecord($xref, $gedrec) {
		return preg_replace('/'.$this->regex.'/'.$this->case, $this->replace, $gedrec);
	}

	function getOptions() {
		parent::getOptions();
		$this->search =safe_GET('search');
		$this->replace=safe_GET('replace');
		$this->method =safe_GET('method', array('exact', 'words', 'wildcards', 'regex'), 'exact');
		$this->case   =safe_GET('case', 'i');

		$this->error='';
		switch ($this->method) {
		case 'exact':
			$this->regex=preg_quote($this->search, '/');
			break;
		case 'words':
			$this->regex='\b'.preg_quote($this->search, '/').'\b';
			break;
		case 'wildcards':
			$this->regex='\b'.str_replace(array('\*', '\?'), array('.*', '.'), preg_quote($this->search, '/')).'\b';
			break;
		case 'regex':
			$this->regex=$this->search;
			// Check for invalid regexes
			// If the regex is bad, $ct will be left at -1
			$ct=-1;
			$ct=@preg_match('/'.$this->search.'/', '');
			if ($ct==-1) {
				$this->error='<br/><span class="error">'.i18n::translate('The regex appears to contain an error.  It can\'t be used.').'</span>';
			}
			break;
		}
	}

	function getOptionsForm() {
		$descriptions=array(
			'exact'=>i18n::translate('Match the exact text, even if it occurs in the middle of a word.'),
			'words'=>i18n::translate('Match the exact text, unless it occurs in the middle of a word.'),
			'wildcards'=>i18n::translate('Use a &laquo;?&raquo; to match a single character, use &laquo;*&raquo; to match zero or more characters.'),
			'regex'=>i18n::translate('Regular expressions are an advanced pattern matching technique.  See <a href="http://php.net/manual/en/regexp.reference.php" target="_new">php.net/manual/en/regexp.reference.php</a> for futher details.'),
		);

		return
			'<tr valign="top"><td class="list_label width20">'.i18n::translate('Search text/pattern').':</td>'.
			'<td class="optionbox wrap">'.
			'<input name="search" size="40" value="'.htmlspecialchars($this->search).
			'" onchange="this.form.submit();"></td></tr>'.

			'<tr valign="top"><td class="list_label width20">'.i18n::translate('Replacement text').':</td>'.
			'<td class="optionbox wrap">'.
			'<input name="replace" size="40" value="'.htmlspecialchars($this->replace).
			'" onchange="this.form.submit();"></td></tr>'.

			'<tr valign="top"><td class="list_label width20">'.i18n::translate('Search method').':</td>'.
			'<td class="optionbox wrap"><select name="method" onchange="this.form.submit();">'.
			'<option value="exact"'    .($this->method=='exact'     ? ' selected="selected"' : '').'>'.i18n::translate('Exact text')    .'</option>'.
			'<option value="words"'    .($this->method=='words'     ? ' selected="selected"' : '').'>'.i18n::translate('Whole words only')    .'</option>'.
			'<option value="wildcards"'.($this->method=='wildcards' ? ' selected="selected"' : '').'>'.i18n::translate('Wildcards').'</option>'.
			'<option value="regex"'    .($this->method=='regex'     ? ' selected="selected"' : '').'>'.i18n::translate('Regular expression')    .'</option>'.
			'</select><br/><i>'.$descriptions[$this->method].'</i>'.$this->error.'</td></tr>'.

			'<tr valign="top"><td class="list_label width20">'.i18n::translate('Case insensitive').':</td>'.
			'<td class="optionbox wrap">'.
			'<input type="checkbox" name="case" value="i" '.($this->case=='i' ? 'checked="checked"' : '').'" onchange="this.form.submit();">'.
			'<br/><i>'.i18n::translate('Tick this box to match both upper and lower case letters.').'</i></td></tr>'.
			parent::getOptionsForm();
	}
}
