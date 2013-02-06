<?php
// UI for online updating of the config file.
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
//
// $Id$

define('WT_SCRIPT_NAME', 'admin_site_readme.php');
require './includes/session.php';

$controller=new WT_Controller_Page();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('README documentation'))
	->pageHeader();

function get_tag($txt,$tag){
	$offset = 0;
	$start_tag = "<".$tag;
	$end_tag = "</".$tag.">";
	$arr = array();
	do{
		$pos = strpos($txt,$start_tag,$offset);
		if($pos){
			$str_pos = strpos($txt,">",$pos)+1;
			$end_pos = strpos($txt,$end_tag,$str_pos);
			$len = $end_pos - $str_pos;
			$f_text = substr($txt,$str_pos,$len);
			$arr[] = $f_text;
			$offset = $end_pos;
		}
	}while($pos);
	return $arr;
}
	
echo '<div id="readme" dir="ltr" lang="en">'; // This information is always LTR/English

$url = 'readme.html';
$txt = file_get_contents($url);
$arr = get_tag($txt, "body");

foreach ($arr as $value) {
	echo $value;
}
	
echo '</div>';
