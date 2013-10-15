<?php

// Functions for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2013 JustCarmen.
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
// $Id: functions.php 2013-09-15 JustCarmen $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// This theme comes with a optional module to set a few theme options
function getThemeOption ($option) {	
	if (array_key_exists('justblack_theme_options', WT_Module::getActiveModules())) {
		$themeOptions = new justblack_theme_options_WT_Module;
		$JB_SETTINGS = $themeOptions->getSettings();
		$setting = $JB_SETTINGS[strtoupper($option)];
		return $setting;	
	}
}

// variables needed in justblack.js
function getJBScriptVars() {
	global $controller, $SHOW_NO_WATERMARK;
	WT_USER_ACCESS_LEVEL > $SHOW_NO_WATERMARK ? $useWatermark = 1 : $useWatermark = 0;	
	$controller->addInlineJavascript('
			// JustBlack Theme variables
			var WT_SERVER_NAME = "'.WT_SERVER_NAME.'";
			var WT_SCRIPT_PATH = "'.WT_SCRIPT_PATH.'";
			var WT_CSS_URL = "'.WT_CSS_URL.'";
			var WT_THEME_JUSTBLACK = "'.WT_THEME_JUSTBLACK.'";
			var WT_TREE_TITLE = "'.strip_tags(WT_TREE_TITLE).'";
			var useWatermark  = '.$useWatermark.';
			var useGviewer = '.getThemeOption('gviewer_pdf').';
			var fullPdfText = "'.WT_I18N::translate('Open this file in full browser window').'";
	', WT_Controller_Base::JS_PRIORITY_HIGH);
}

// Theme setting for the header section
function getJBheader() {	
	// are we taking the default header image, a custom one or none?
	$path = WT_CSS_URL.'images/';
	switch (getThemeOption('header')) {
		case 'custom':	
			$exts = array('png','jpg', 'gif');
			foreach($exts as $ext) {
				if(file_exists($path.'custom_header.'.$ext)){
					$header_image_style = 'background-image:url('.$path.'custom_header.'.$ext.'); height: '.getThemeOption('headerheight').'px';
					$header_menu_style = 'height: '.getThemeOption('headerheight').'px';
				}
			}
			break;
		case 'none':
			$header_image_style = 'height: '.getThemeOption('headerheight').'px';
			$header_menu_style = $header_image_style;
			break;
		default:
			$header_image_style = 'background-image:url('.$path.'header.jpg)';
			$header_menu_style = '';
			break;
	}
	
	switch (getThemeOption('treetitle')) {
		case '0':
			$title = '';
			break;
		case '1':	
			$pos 	= explode(',',getThemeOption('titlepos'));
			$top 	= $pos[0];
			$right	= $pos[1];
			$bottom = $pos[2];
			$left 	= $pos[3];
			
			$top >= $bottom ? $posV = 'top:'.$top : $posV = 'bottom:'.$bottom;
			$left >= $right ? $posH = 'left:'.$left : $posH = 'right:'.$right;
			
			$font_size = 'font-size:'.getThemeOption('titlesize').'px';			
			$title = '<div id="tree-title" dir="auto" style="'.$posV.';'.$posH.'"><h1 style="'.$font_size.'">'.WT_TREE_TITLE.'</h1></div>';
			break;
		default:
			$title = '<div id="tree-title" dir="auto"><h1>'.WT_TREE_TITLE.'</h1></div>';
			break;
	}
	
	$html =	'<div id="header">
				<div id="header-image" style="'.$header_image_style.'"></div>	
				<div id="header-menu" style="'.$header_menu_style.'">'.$title.'
					<div id="extra-menu">
						<ul class="dropdown">'.WT_MenuBar::getThemeMenu();
							if (!getThemeOption('flags')|| getThemeOption('flags') == 0) $html .= WT_MenuBar::getLanguageMenu();
	$html .= '			</ul></div>
					<div id="login-menu">'.getJBLoginMenu().'</div>
				</div>
			</div>';
	
	return $html;
}

// Menus
function getJBTopMenu() {
	global $controller;
	$menus = getThemeOption('menu_order');
	
	if($menus) {
		$jb_controller = new justblack_theme_options_WT_Module;
		$menus = $jb_controller->checkModule($menus);
		$list = null;
		foreach($menus as $menu) {
			$label		= $menu['label'];
			$sort 		= $menu['sort'];
			$function 	= $menu['function'];
			if($sort > 0) {
				if ($function == 'getModuleMenu') {						
					$module = $label.'_WT_Module';
					$modulemenu = new $module;
					$item = $modulemenu->getMenu();						
				} elseif ($label == 'compact') {
					$item = $jb_controller->$function();
				} elseif ($label == 'media') {
					$item = $jb_controller->$function();
					// hide the original submenu item
					$controller->addInlineJavascript('
						jQuery("li#menu-list-obje").hide();
					');
				} else {							
					$item = WT_MenuBar::$function();
				}
				$list[] = $item;
			}
		}			
		$output = implode('', $list);
	} else {
		$output = 
			WT_MenuBar::getGedcomMenu().
			WT_MenuBar::getMyPageMenu().
			WT_MenuBar::getChartsMenu().
			WT_MenuBar::getListsMenu().
			WT_MenuBar::getCalendarMenu().
			WT_MenuBar::getReportsMenu().
			WT_MenuBar::getSearchMenu().
			implode('', WT_MenuBar::getModuleMenus());
	}	
	return '<ul id="main-menu">'.$output.'</ul>';
}

function getJBSearch () {
	$searchform = '	<form action="search.php" method="post">
					<input type="hidden" name="action" value="general"/>
					<input type="hidden" name="topsearch" value="yes"/>
					<input type="search" name="query" size="20" placeholder="'. WT_I18N::translate('Search'). '" dir="auto"/>
					<input type="image" class="searchbtn" src="'.WT_CSS_URL.'images/buttons/search_go.png" alt="'.WT_I18N::translate('Search').'" title="'.WT_I18N::translate('Search').'">
					</form>';
	return $searchform;
}

function getJBFlags() {
	if (getThemeOption('flags') == 1) {
		$menu=WT_MenuBar::getLanguageMenu();
		$user_id = getUserID();
		$user_lang = get_user_setting($user_id, 'language');
		
		if ($menu && $menu->submenus) {
			$output ='<div id="lang-menu"><ul>';
			foreach ($menu->submenus as $submenu) {
				if ($submenu) {
					$link = '';
					if ($submenu->link) {
						if ($submenu->target !== null) {
							$link .= ' target="'.$submenu->target.'"';
						}
						if ($submenu->link=='#' && $submenu->onclick !== null) {
								$link .= ' onclick="'.$submenu->onclick.'"';
						}
						$lang_code = str_replace('menu-language-', '', $submenu->id);
						$lang_code == $user_lang ? $output .= '<li id="'.$submenu->id.'" title="'.$submenu->label.'" class="lang-active">' : $output .= '<li id="'.$submenu->id.'" title="'.$submenu->label.'">';
						$output .= '<a class="'.$submenu->iconclass.'" href="'.$submenu->link.'"'.$link.'></a></li>';
					}	
				}
			}
			$output .='</ul></div>';					
		return $output;
		}
	}
}

function getJBLoginMenu() {	
	if (WT_USER_ID) {
		$output = '<a class="link" href="edituser.php">'.WT_I18N::translate('Logged in as ').getUserName(WT_USER_ID).'</a> | ';
		if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
			$output .= '<a class="link" href="#" onclick="window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;">'. WT_I18N::translate('Pending changes').'</a>&nbsp;|&nbsp;';
		}
		$output .= logout_link();
	} 
	else {
		$output = login_link();
	}	
	return $output;				
}

// Extended thumbnails - code original from library/WT/Media.php line 391.
// Called from  library/WT/Person.php function displayImage() from line 300.
// Thumbnails for personbox template
// Display the prefered image for this individual.
// Use an icon if no image is available.
function getJBThumb($person, $max_thumbsize, $square = '') {
	global $USE_SILHOUETTE;

	$media = $person->findHighlightedMedia();
	if ($media) {
		$mediasrc = $media->getServerFilename();		
		if (file_exists($mediasrc) && $data = getimagesize($mediasrc)) { // extra check to be sure the thumb can be created.
						
			// Thumbnail exists - use it.
			if ($media->isExternal()) {
				// Use an icon
				$mime_type = str_replace('/', '-', $media->mimeType());
				$image =
					'<i' .
					' dir="'   . 'auto'                           	. '"' . // For the tool-tip
					' class="' . 'icon-mime-' . $mime_type        	. '"' .
					' title="' . strip_tags($media->getFullName()) 	. '"' .
					'></i>';
			} else {
				// Create a thumbnail image
				if($media->mimeType() == 'image/jpeg') {				
					
					list($width_orig, $height_orig) = getimagesize($mediasrc);  
					$imagesrc = imagecreatefromjpeg($mediasrc);
					$ratio_orig = $width_orig/$height_orig;
					$thumbwidth = $thumbheight = $max_thumbsize;
					
					if($square == true) {
						if ($thumbwidth/$thumbheight > $ratio_orig) {
						   $new_height = $thumbwidth/$ratio_orig;
						   $new_width = $thumbwidth;
						} else {
						   $new_width = $thumbheight*$ratio_orig;
						   $new_height = $thumbheight;
						}				
					}
					else {
						if ($width_orig > $height_orig) {
							$new_height = $thumbheight/$ratio_orig;
							$new_width 	= $thumbwidth;
						} elseif ($height_orig > $width_orig) {
						   $new_width 	= $thumbheight*$ratio_orig;
						   $new_height 	= $thumbheight;
						} else {
							$new_width 	= $thumbwidth;
							$new_height = $thumbheight;
						}	
					}
				   
					$process = imagecreatetruecolor(round($new_width), round($new_height));			   
					imagecopyresampled($process, $imagesrc, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);				
					$square == true ? $thumb = imagecreatetruecolor($thumbwidth, $thumbheight) : $thumb = imagecreatetruecolor($new_width, $new_height); 					
					imagecopyresampled($thumb, $process, 0, 0, 0, 0, $thumbwidth, $thumbheight, $thumbwidth, $thumbheight);
				
					imagedestroy($process);
					imagedestroy($imagesrc);			
				
					ob_start();imagejpeg($thumb,null,80);$thumb = ob_get_clean();	
					$src = 'data:image/jpeg;base64,' .base64_encode($thumb);
					
					$image =
					'<img' . 
					' dir="'	. 'auto'                         	. '"' . // For the tool-tip
					' src="'	. $src 								. '"' .
					' alt="'	. strip_tags($media->getFullName()) . '"' .
					' title="'	. strip_tags($media->getFullName()) . '"' .
					'>';
				
				} else {
					$src = $media->getHtmlUrlDirect('thumb');
				}
				
				$image =
					'<img' . 
					' dir="'	. 'auto'                         	. '"' . // For the tool-tip
					' src="'	. $src 								. '"' .
					' alt="'	. strip_tags($media->getFullName()) . '"' .
					' title="'	. strip_tags($media->getFullName()) . '"' .
					'>';
									
				return
						'<a' .
						' class="'          . 'gallery'                          . '"' .
						' href="'           . $media->getHtmlUrlDirect('main')    . '"' .
						' type="'           . $media->mimeType()                  . '"' .
						' data-obje-url="'  . $media->getHtmlUrl()                . '"' .
						' data-obje-note="' . htmlspecialchars($media->getNote()) . '"' .
						' data-title="'     . strip_tags($media->getFullName())   . '"' .
				'>' . $image . '</a>';
			}	
		} else { $noThumb = true; }
	} else { $noThumb = true; }
	
	if ($noThumb == true) {
		if ($USE_SILHOUETTE) {
			return '<i class="icon-silhouette-' . $person->getSex() . '"></i>';
		} else {
			return '';
		}
	}
}

?>