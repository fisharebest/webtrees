<?php
// Module help text.
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
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

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'privacy_status':
	$title=WT_I18N::translate('Privacy status');
	$text='<style>#privacy_status_help dt{float:left;clear:left;width:100px;}#privacy_status_help dd{margin: 0 0 8px 110px;}html[dir=\'rtl\'] #privacy_status_help dt{float:right;clear:right;}html[dir=\'rtl\'] #privacy_status_help dd{margin: 0 110px 8px 0;</style>';
	$text.=WT_I18N::translate('There are three possible indicators of privacy status: Dead, Presumed dead, and Living.<br>If <u>either of the first two</u> are set, then the person will be displayed  in accordance with the family tree and site privacy settings.<br>');
	$text.=WT_I18N::translate('The age at which a person is assumed to be dead is set at %s years.', $MAX_ALIVE_AGE);	
	$text.='<br><br><dl id="privacy_status_help">';	
	$text.=WT_I18N::translate('<dt>Dead</dt><dd>Used when a person is clearly marked as dead by the inclusion of a death record with a date or date range.</dd>');
	$text.=WT_I18N::translate('<dt>Presumed dead</dt><dd>This is set when a person either has a death recorded but with no date, or has no death record but <b>webtrees</b> has calculated that the person can reasonably be expected to be dead.</dd>');
	$text.=WT_I18N::translate('<dt>Living</dt><dd>If there is no record of a death and no other related facts that imply death, then the person is assumed to be living.</dd>');	
	$text.='</dl>';	
	break;
}
