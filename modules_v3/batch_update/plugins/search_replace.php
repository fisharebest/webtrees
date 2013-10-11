<?php
// Batch Update plugin for phpGedView - search/replace
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
		return WT_I18N::translate('Search and replace');
	}

	static function getDescription() {
		return /* I18N: Description of the “Search and replace” option of the batch update module */ WT_I18N::translate('Search and replace text, using simple searches or advanced pattern matching.');
	}

	// Default is to operate on INDI records
	function getRecordTypesToUpdate() {
		return array('INDI', 'FAM', 'SOUR', 'REPO', 'NOTE', 'OBJE');
	}

	function doesRecordNeedUpdate($xref, $gedrec) {
		return !$this->error && preg_match('/(?:'.$this->regex.')/m'.$this->case, $gedrec);
	}

	function updateRecord($xref, $gedrec) {
		// Allow "\n" to indicate a line-feed in replacement text.
		// Back-references such as $1, $2 are handled automatically.
		return preg_replace('/'.$this->regex.'/'.$this->case, str_replace('\n', "\n", $this->replace), $gedrec);
	}

	function getOptions() {
		parent::getOptions();
		$this->search  = WT_Filter::get('search');
		$this->replace = WT_Filter::get('replace');
		$this->method  = WT_Filter::get('method', 'exact|words|wildcards|regex', 'exact');
		$this->case    = WT_Filter::get('case', 'i');

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
				$this->error='<br><span class="error">'.WT_I18N::translate('The regex appears to contain an error.  It can’t be used.').'</span>';
			}
			break;
		}
	}

	function getOptionsForm() {
		$descriptions=array(
			'exact'=>WT_I18N::translate('Match the exact text, even if it occurs in the middle of a word.'),
			'words'=>WT_I18N::translate('Match the exact text, unless it occurs in the middle of a word.'),
			'wildcards'=>WT_I18N::translate('Use a &laquo;?&raquo; to match a single character, use &laquo;*&raquo; to match zero or more characters.'),
			'regex'=>WT_I18N::translate('Regular expressions are an advanced pattern matching technique.  See <a href="http://php.net/manual/en/regexp.reference.php" target="_blank">php.net/manual/en/regexp.reference.php</a> for futher details.'),
		);

		return
			'<tr><th>'.WT_I18N::translate('Search text/pattern').'</th>'.
			'<td>'.
			'<input name="search" size="40" value="'.WT_Filter::escapeHtml($this->search).
			'" onchange="this.form.submit();"></td></tr>'.

			'<tr><th>'.WT_I18N::translate('Replacement text').'</th>'.
			'<td>'.
			'<input name="replace" size="40" value="'.WT_Filter::escapeHtml($this->replace).
			'" onchange="this.form.submit();"></td></tr>'.

			'<tr><th>'.WT_I18N::translate('Search method').'</th>'.
			'<td><select name="method" onchange="this.form.submit();">'.
			'<option value="exact"'    .($this->method=='exact'     ? ' selected="selected"' : '').'>'.WT_I18N::translate('Exact text')    .'</option>'.
			'<option value="words"'    .($this->method=='words'     ? ' selected="selected"' : '').'>'.WT_I18N::translate('Whole words only')    .'</option>'.
			'<option value="wildcards"'.($this->method=='wildcards' ? ' selected="selected"' : '').'>'.WT_I18N::translate('Wildcards').'</option>'.
			'<option value="regex"'    .($this->method=='regex'     ? ' selected="selected"' : '').'>'.WT_I18N::translate('Regular expression')    .'</option>'.
			'</select><br><em>'.$descriptions[$this->method].'</em>'.$this->error.'</td></tr>'.

			'<tr><th>'.WT_I18N::translate('Case insensitive').'</th>'.
			'<td>'.
			'<input type="checkbox" name="case" value="i" '.($this->case=='i' ? 'checked="checked"' : '').'" onchange="this.form.submit();">'.
			'<br><em>'.WT_I18N::translate('Tick this box to match both upper and lower case letters.').'</em></td></tr>'.
			parent::getOptionsForm();
	}
}
