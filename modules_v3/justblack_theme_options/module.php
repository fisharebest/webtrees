<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// $Id: module.php 13838 2013-07-01 v1.1 JustCarmen$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class justblack_theme_options_WT_Module extends WT_Module implements WT_Module_Config {
	
	public function __construct() {
		// Load any local user translations
		if (is_dir(WT_MODULES_DIR.$this->getName().'/language')) {
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('gettext', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo', WT_LOCALE)
				);
			}
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('array', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php', WT_LOCALE)
				);
			}
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.csv')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('csv', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.csv', WT_LOCALE)
				);
			}
		}
	}
		
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module  */ WT_I18N::translate('JustBlack Theme Options');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the module */ WT_I18N::translate('Set options for the JustBlack theme within the admin interface');
	}
	
	public function getSettings($key = '') {
		// get module settings
		$module = $this->getName();
		$JB_TREETITLE 				= get_module_setting($module, 'JB_TREETITLE');
		$JB_TITLEPOS				= get_module_setting($module, 'JB_TITLEPOS');
		$JB_TITLESIZE				= get_module_setting($module, 'JB_TITLESIZE');
		$JB_HEADER 					= get_module_setting($module, 'JB_HEADER');
		$JB_HEADERIMG				= get_module_setting($module, 'JB_HEADERIMG');
		$JB_HEADERHEIGHT			= get_module_setting($module, 'JB_HEADERHEIGHT');
		$JB_FLAGS					= get_module_setting($module, 'JB_FLAGS');		
		$JB_COMPACT_MENU 			= get_module_setting($module, 'JB_COMPACT_MENU');
		$JB_COMPACT_MENU_REPORTS 	= get_module_setting($module, 'JB_COMPACT_MENU_REPORTS');
		$JB_MEDIA_MENU				= get_module_setting($module, 'JB_MEDIA_MENU');
		$JB_MEDIA_MENU_LINK			= get_module_setting($module, 'JB_MEDIA_MENU_LINK');
		$JB_GVIEWER_PDF				= get_module_setting($module, 'JB_GVIEWER_PDF');
		$JB_MENU_ORDER				= unserialize(get_module_setting($module, 'JB_MENU_ORDER'));		
			
		// get defaults if there are no settings
		if (!isset($JB_TREETITLE)) 				$JB_TREETITLE 				= '1';
		if (empty($JB_TITLEPOS)) 				$JB_TITLEPOS	 			= '110px,0,0,52%';
		if (empty($JB_TITLESIZE)) 				$JB_TITLESIZE	 			= '20';
		if (!isset($JB_HEADER)) 				$JB_HEADER 					= 'default';
		if (!isset($JB_HEADERIMG)) 				$JB_HEADERIMG 				= WT_I18N::translate('no custom header image set');
		if (!isset($JB_HEADERHEIGHT)) 			$JB_HEADERHEIGHT			= '150';
		if (!isset($JB_FLAGS)) 					$JB_FLAGS 					= '0';
		if (!isset($JB_COMPACT_MENU)) 			$JB_COMPACT_MENU 			= '0';
		if (!isset($JB_COMPACT_MENU_REPORTS)) 	$JB_COMPACT_MENU_REPORTS 	= '1';
		if (!isset($JB_MEDIA_MENU)) 			$JB_MEDIA_MENU			 	= '0';
		if (!isset($JB_MEDIA_MENU_LINK)) 		$JB_MEDIA_MENU_LINK		 	= '';
		if (!isset($JB_GVIEWER_PDF)) 			$JB_GVIEWER_PDF		 		= '0';
		if (empty($JB_MENU_ORDER)) 				$JB_MENU_ORDER				= $this->getMenuOrder();
						
		$JB_SETTINGS = array(
			'TREETITLE'				=> $JB_TREETITLE,
			'TITLEPOS'				=> $JB_TITLEPOS,
			'TITLESIZE'				=> $JB_TITLESIZE,
			'HEADER'				=> $JB_HEADER,
			'HEADERIMG'				=> $JB_HEADERIMG,
			'HEADERHEIGHT'			=> $JB_HEADERHEIGHT,
			'FLAGS'					=> $JB_FLAGS,
			'COMPACT_MENU'			=> $JB_COMPACT_MENU,
			'COMPACT_MENU_REPORTS'	=> $JB_COMPACT_MENU_REPORTS,
			'MEDIA_MENU'			=> $JB_MEDIA_MENU,
			'MEDIA_MENU_LINK'		=> $JB_MEDIA_MENU_LINK,
			'GVIEWER_PDF'			=> $JB_GVIEWER_PDF,
			'MENU_ORDER'			=> $JB_MENU_ORDER
		);
		
		if($key) return($JB_SETTINGS[strtoupper($key)]);
		else return $JB_SETTINGS;		
	}	
	
	private function getOptionValue($key, $type) {			
		$pkey = 'JB_'.strtoupper($key);
		switch($type) {
			case('checkbox'):
				isset($_POST[$pkey]) ? $value = '1' : $value = '0';
			break;
			case('textbox'):
				is_array($_POST[$pkey]) ? $value = serialize($_POST[$pkey]) : $value = $_POST[$pkey];
			break;
			case ('selectbox'):
				$current = $this->getSettings($key);
				isset($_POST[$pkey]) ? $value = $_POST[$pkey] : $value = $current;
			break;
			case('sortable'):
				if ($key == 'menu_order') {		
					$MENU_ORDER = $this->sortArray($_POST[$pkey], 'sort');					
					$value = serialize($MENU_ORDER);
				}				
			break;			
		}		
		return $value;	
	}
	
	// Search within a multiple dimensional array	
	private function searchArray($array, $key, $value) {
		$results = array();
		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value)
				$results[] = $array;	
			foreach ($array as $subarray)
				$results = array_merge($results, $this->searchArray($subarray, $key, $value));
		}	
		return $results;
	}
	
	// Sort the array according to the $key['SORT'] input.
	private function sortArray($array, $sort_by){
		foreach ($array as $pos =>  $val) {
			$tmp_array[$pos] = $val[$sort_by];
		}
		asort($tmp_array);
	   
		foreach ($tmp_array as $pos =>  $val){
			$return_array[$pos]['title'] = $array[$pos]['title'];
			$return_array[$pos]['label'] = $array[$pos]['label'];
			$return_array[$pos]['sort'] = $array[$pos]['sort'];
			$return_array[$pos]['function'] = $array[$pos]['function'];
		}
		return $return_array;
    }
	
	private function getChecked($value) {
		$value == 1 ? $checked = 'checked="checked"' : $checked = "";	
		return $checked;
	}
	
	private function getMenuOrder() {
		$menulist = array(
			array(
				'title'		=> WT_I18N::translate('View'),
				'label'		=> 'compact',
				'sort' 		=> '0',
				'function' 	=> 'getCompactMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Media'),
				'label'		=> 'media',
				'sort' 		=> '0',
				'function' 	=> 'getMediaMenu'
			),
			array(			
				'title'		=> WT_I18N::translate('Home page'),
				'label'		=> 'homepage',
				'sort' 		=> '1',
				'function' 	=> 'getGedcomMenu'
			),
			array(
				'title'		=> WT_I18N::translate('My page'),
				'label'		=> 'mypage',
				'sort' 		=> '2',
				'function' 	=> 'getMyPageMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Charts'),
				'label'		=> 'charts',
				'sort' 		=> '3',
				'function' 	=> 'getChartsMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Lists'),
				'label'		=> 'lists',
				'sort' 		=> '4',
				'function' 	=> 'getListsMenu'
			),
			array(
				'title'		=>	WT_I18N::translate('Calendar'),
				'label'		=> 'calendar',
				'sort' 		=> '5',
				'function' 	=> 'getCalendarMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Reports'),
				'label'		=> 'reports',
				'sort' 		=> '6',
				'function' 	=> 'getReportsMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Search'),
				'label'		=> 'search',
				'sort' 		=> '7',
				'function' 	=> 'getSearchMenu'
			),
		);
		
		$modules=WT_Module::getActiveMenus();
		// don't list known fakemenus but put them in the database with a sort-order of 99 
		$fakeMenus 	= array('custom_js', 'fancy_imagebar', 'simpl_branches');
		$i = 8;
		foreach ($modules as $module) {
			$sort = in_array($module->getName(), $fakeMenus) ? '99' : $i;		
			$menulist[] = array(					
				'title'		=> $module->getTitle(),
				'label'		=> $module->getName(),
				'sort' 		=> $sort,
				'function' 	=> 'getModuleMenu'
			);
			$i++;	
		}
		return $menulist;
	}	
	
	// get our own Compact Menu
	public function getCompactMenu() {
		global $controller, $SEARCH_SPIDER;
		
		if ($SEARCH_SPIDER) return null;
		
		$indi_xref=$controller->getSignificantIndividual()->getXref();		
		$menu = new WT_Menu(WT_I18N::translate('View'), 'pedigree.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-view');
		
		if ($this->getSettings('compact_menu_reports') == 1) {
			$submenu_items = array(
				WT_MenuBar::getChartsMenu(),
				WT_MenuBar::getListsMenu(),
				WT_MenuBar::getReportsMenu(),
				WT_MenuBar::getCalendarMenu()
			);		
		}
		else {	
			$submenu_items = array(
				WT_MenuBar::getChartsMenu(),
				WT_MenuBar::getListsMenu(),
				WT_MenuBar::getCalendarMenu()
			);
		}
		
		foreach ($submenu_items as $submenu) {
			$id = explode("-", $submenu->id);
			$new_id = implode("-", array($id[0], 'view', $id[1]));		
			$submenu->id = $new_id;	
			$submenu->label = '<span>'.$submenu->label.'</span>';	
			$menu->addSubmenu($submenu);		
		};	
		return $menu;
	}
	
	// get the media Menu as Main menu item with folders as submenu-items
	public function getMediaMenu() {
		global $controller, $SEARCH_SPIDER, $MEDIA_DIRECTORY;
		
		if ($SEARCH_SPIDER) return null;
		
		$menulink = $this->getSettings('media_menu_link');
		$menu = new WT_Menu(WT_I18N::translate('Media'), 'medialist.php?action=filter&amp;search=yes&amp;folder='.urlencode(rtrim($menulink, "/")).'&amp;sortby=title&amp;max=20&amp;filter=&amp;columns=2', 'menu-media');		
		
		$folders = array_values(WT_Query_Media::folderList());
		foreach ($folders as $key => $folder) {
			$medialist = WT_Query_Media::mediaList($folder, 'exclude', 'file', '');
			if(count($medialist) > 0) {
				$name = substr($folder, 0, -1);
				if(empty($name)) $name = WT_I18N::translate('Media');
				$title = ucfirst($name);
				$submenu = new WT_Menu($title, 'medialist.php?action=filter&amp;search=yes&amp;folder='.urlencode(rtrim($folder, "/")).'&amp;sortby=title&amp;max=20&amp;filter=&amp;columns=2', 'menu-media-folder'.$key);
				$menu->addSubmenu($submenu);
			}
		};	
		return $menu;
	}
	
	// function to check if a module menu is still active (after options are set)
	public function checkModule($menulist) {
		$modules=WT_Module::getActiveMenus();		
		
		// delete deactivated modules from the list
		foreach ($menulist as $menu) {
			if	($menu['function'] == 'getModuleMenu') {
				if (array_key_exists($menu['label'], $modules)) {
					$new_list[] = $menu;
				}
			}							
			else {
				$new_list[] = $menu;
			}
		}	
		
		// add newly activated modules to the list
		foreach ($modules as $module) {			
			if(!$this->searchArray($menulist, 'label', $module->getName())) {
				$new_list[] = array(					
					'title'		=> $module->getTitle(),
					'label'		=> $module->getName(),
					'sort' 		=> '49', // can not be 0 (=trashmenu), can not be 99 (=fakemenu)
					'function' 	=> 'getModuleMenu'
				);	
			}
		}		
		return $new_list;
	}
	
	// set an extra class for some menuitems
	private function getStatus($label) {
		if ($label == 'homepage' || $label == 'mypage') {
		 	$status = ' ui-state-disabled';
		} elseif ($label == 'charts' || $label == 'lists' || $label == 'calendar') {
			$status = ' menu_extended';
		} elseif ($label == 'reports') {
			$status = ' menu_extended menu_reports';
		} elseif ($label == 'compact') {
			$status = ' menu_compact';
		} elseif ($label == 'media') {
			$status = ' menu_media';
		} else {
			$status = '';
		}
		return $status;
	}
	
	private function resizeHeader($imgSrc, $type, $thumbwidth, $thumbheight) {
		//getting the image dimensions 
		list($width_orig, $height_orig) = getimagesize($imgSrc);  		
		$ratio_orig = $width_orig/$height_orig;
		
		if (($width_orig > $height_orig && $width_orig < $thumbwidth) || ($height_orig > $width_orig && $height_orig < $thumbheight)) return false;
	   
		if ($thumbwidth/$thumbheight > $ratio_orig) {
		   $new_height = $thumbwidth/$ratio_orig;
		   $new_width = $thumbwidth;
		   
		} else {
		   $new_width = $thumbheight*$ratio_orig;
		   $new_height = $thumbheight;
		}
		
		$y_mid = $new_height/2;
		
		// return resized header image	
		switch ($type) {		
			case 'jpg':
			case 'jpeg':
				$image = @imagecreatefromjpeg($imgSrc);
				$thumb = @imagecreatetruecolor(round($new_width), $thumbheight);	   
				
				@imagecopyresampled($thumb, $image, 0, 0, 0, ($y_mid-($thumbheight/2)), $new_width, $new_height, $width_orig, $height_orig);
				imagedestroy($image);
				
				return imagejpeg($thumb,$imgSrc,100);
				break;
			case 'gif':
				$image = @imagecreatefromgif($imgSrc);
				$thumb = @imagecreatetruecolor(round($new_width), $thumbheight);					
				
				@imagecopyresampled($thumb, $image, 0, 0, 0, ($y_mid-($thumbheight/2)), $new_width, $new_height, $width_orig, $height_orig); 
				@imagecolortransparent($thumb, @imagecolorallocate($thumb, 0, 0, 0));
				imagedestroy($image);
				
				return imagegif($thumb,$imgSrc,100);
				break;
			case 'png':
				$image = @imagecreatefrompng($imgSrc);
				@imagealphablending($image, false);				
				
				$thumb = @imagecreatetruecolor(round($new_width), $thumbheight);	
				@imagealphablending($thumb, false);
				@imagesavealpha($thumb, true);
				
				@imagecopyresampled($thumb, $image, 0, 0, 0, ($y_mid-($thumbheight/2)), $new_width, $new_height, $width_orig, $height_orig); 				
				imagedestroy($image);
				
				return imagepng($thumb,$imgSrc,0);
				break;
		}
	}	
		
	private function deleteCustomHeader($path, $kExt = '') { // $kExt = extension to keep. If not set delete all custom headers regardless extension.		
		$exts = array('png','jpg', 'gif');		
		
		foreach($exts as $ext) {
			if($ext != $kExt && file_exists($path.'custom_header.'.$ext)){
				@unlink($path.'custom_header.'.$ext);									
			}
		}	
	}
	
	private function addMessage($controller, $type, $msg) {
		if ($type == "success") $class = "ui-state-highlight";
		if ($type == "error") $class = "ui-state-error";		
		$controller->addInlineJavaScript('
			jQuery("#error").text("'.$msg.'").addClass("'.$class.'").show("normal");
			setTimeout(function() {
				jQuery("#error").hide("normal");
			}, 10000);		
		');	
	}
	
	// Extend WT_Module_Config
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			require WT_ROOT.'includes/functions/functions_edit.php';				
			$controller=new WT_Controller_Page;
			$controller
				->requireAdminLogin()
				->setPageTitle(WT_I18N::translate('Options for the JustBlack theme'))
				->pageHeader()
				->addInlineJavaScript ('
					function toggleFields(checkbox, field, reverse) {
						var checkbox = jQuery(checkbox)
						var field = jQuery(field)
						if(!reverse) {
							if ((checkbox).is(":checked")) field.show();
							else field.hide();							
							checkbox.click(function(){
								if (this.checked) field.show();
								else field.hide();															    
							});	
						}
						else {
							if ((checkbox).is(":checked")) field.hide();
							else field.show();							
							checkbox.click(function(){
								if (this.checked) field.hide();
								else field.show();															    
							});	
						}
					}						
					
					toggleFields("#treetitle", ".titlepos, .titlesize");
					toggleFields("#resize", ".headerheight", true);
					toggleFields("#compact_menu", ".reports");
					toggleFields("#media_menu", ".media_link");
										
					jQuery("#header option").each(function() {
						if(jQuery(this).val() == "'.$this->getOptionValue('header', 'selectbox').'") {
							jQuery(this).prop("selected", true);
						}						
					});
					
					jQuery("#header").each(function(){
						if(jQuery(this).val() == "custom") jQuery(".upload").show();
						else jQuery(".upload").hide();
						if(jQuery(this).val() !== "default") jQuery(".headerheight").show();
						else jQuery(".headerheight").hide();		
					});
					jQuery("#header").change(function(){
						if(jQuery(this).val() == "custom") jQuery(".upload").show();
						else jQuery(".upload").hide();
						if(jQuery(this).val() !== "default") jQuery(".headerheight").show();
						else jQuery(".headerheight").hide();						
					});
						
					jQuery("#compact_menu").click(function() {
						if (jQuery("#compact_menu_reports").is(":checked")) var menu_extended = jQuery(".menu_extended");
						else var menu_extended = jQuery(".menu_extended:not(.menu_reports)");
						
						if (this.checked) {
							jQuery(menu_extended).appendTo(jQuery("#trashMenu")).hide();
							jQuery(".menu_compact").insertAfter(jQuery(".ui-state-disabled:last")).show();
						}
						else {
							jQuery(".menu_compact").appendTo(jQuery("#trashMenu")).hide();
							jQuery(menu_extended).insertAfter(jQuery(".ui-state-disabled:last")).show();
						}
						jQuery("#sortMenu, #trashMenu").trigger("sortupdate")					
					});
					
					jQuery("#compact_menu_reports").click(function() {
						if (this.checked) jQuery(".menu_reports").appendTo(jQuery("#trashMenu")).hide();
						else jQuery(".menu_reports").insertAfter(jQuery(".menu_compact")).show();
						jQuery("#sortMenu, #trashMenu").trigger("sortupdate")					
					});
					
					jQuery("#media_menu").click(function() {						
						if (this.checked) {
							jQuery(".menu_media").appendTo(jQuery("#sortMenu")).show();
						}
						else {
							jQuery(".menu_media").appendTo(jQuery("#trashMenu")).hide();
						}
						jQuery("#sortMenu, #trashMenu").trigger("sortupdate")					
					});
					
					jQuery("#media_menu_link option").each(function() {
						if(jQuery(this).val() == "'.$this->getOptionValue('media_menu_link', 'selectbox').'") {
							jQuery(this).prop("selected", true);
						}						
					});
					
					 jQuery("#sortMenu").sortable({
						items: "li:not(.ui-state-disabled)"
					}).disableSelection();
					
					//-- update the order numbers after drag-n-drop sorting is complete
					jQuery("#sortMenu").bind("sortupdate", function(event, ui) {
						jQuery("#"+jQuery(this).attr("id")+" input[id^=menu_order_sort]").each(
							function (index, value) {
								value.value = index+1;
							}
						);
						jQuery("#trashMenu input[id^=menu_order_sort]").attr("value", "0");
					}); 
				');	
						
			$update = WT_Filter::postBool('update');
			$reset = WT_Filter::postBool('reset');
			
			if (isset($update)) {				
				$path = WT_STATIC_URL.'themes/justblack/css/images/';
				// Check if the custom header option is set and if we are dealing with a valid image
				if ($this->getOptionValue('header', 'selectbox') == 'custom') {
					if (empty($_FILES['JB_HEADERIMG']['name']) || !preg_match('/^image\/(png|gif|jpeg)/', $_FILES['JB_HEADERIMG']['type'])){
						// suppress error message if there is already a header set
						if($this->getSettings('header') != 'custom') {
							$error = true;
							$this->addMessage($controller, 'error', WT_I18N::translate('Error: You have not uploaded an image or the image you have uploaded is not a valid image! Your settings are not saved.'));
						}
					}
					else { // process image
						$type = strtolower(substr(strrchr($_FILES['JB_HEADERIMG']['name'], '.'), 1));
						$serverFileName = $path.'custom_header.'.$type;
						if(WT_Filter::postBool('resize') == true)	$this->resizeHeader($_FILES['JB_HEADERIMG']['tmp_name'], $type, '800', '150');
						
						if (move_uploaded_file($_FILES['JB_HEADERIMG']['tmp_name'], $serverFileName)) {
							chmod($serverFileName, WT_PERM_FILE);							
							$this->addMessage($controller, 'success', WT_I18N::translate('Your custom header image is succesfully saved.'));
							
							// remove old header images from the server							
							$this->deleteCustomHeader($path, $type); //$type here is the extension to keep.
						} 
						set_module_setting($this->getName(), 'JB_HEADERIMG', $_FILES['JB_HEADERIMG']['name']);	
					}
				}
				else { // no custom header
					$this->deleteCustomHeader($path);
					WT_DB::prepare("DELETE FROM `##module_setting` WHERE setting_name = 'JB_HEADERIMG'")->execute();
				}
				
				if (!isset($error)) {			
					// Put new values into the database
					set_module_setting($this->getName(), 'JB_TREETITLE', 			$this->getOptionValue('treetitle', 'checkbox'));
					set_module_setting($this->getName(), 'JB_TITLEPOS',		 		$this->getOptionValue('titlepos', 'textbox'));
					set_module_setting($this->getName(), 'JB_TITLESIZE',		 	$this->getOptionValue('titlesize', 'textbox'));
					set_module_setting($this->getName(), 'JB_HEADER',				$this->getOptionValue('header', 'selectbox'));													
					set_module_setting($this->getName(), 'JB_FLAGS',				$this->getOptionValue('flags', 'checkbox'));		
					set_module_setting($this->getName(), 'JB_COMPACT_MENU',			$this->getOptionValue('compact_menu', 'checkbox'));	
					set_module_setting($this->getName(), 'JB_COMPACT_MENU_REPORTS',	$this->getOptionValue('compact_menu_reports', 'checkbox'));
					set_module_setting($this->getName(), 'JB_MEDIA_MENU',			$this->getOptionValue('media_menu', 'checkbox'));
					set_module_setting($this->getName(), 'JB_MEDIA_MENU_LINK',		$this->getOptionValue('media_menu_link', 'selectbox'));
					set_module_setting($this->getName(), 'JB_GVIEWER_PDF',			$this->getOptionValue('gviewer_pdf', 'checkbox'));					
					set_module_setting($this->getName(), 'JB_MENU_ORDER',			$this->getOptionValue('menu_order', 'sortable'));
					
					// Only set headerheight when 'custom' or 'none' is chosen from selectbox
					if($this->getOptionValue('header', 'selectbox') == 'default' || WT_Filter::postBool('resize') == true) {
						WT_DB::prepare("DELETE FROM `##module_setting` WHERE setting_name = 'JB_HEADERHEIGHT'")->execute();
					}
					else {
						set_module_setting($this->getName(), 'JB_HEADERHEIGHT',	$this->getOptionValue('headerheight', 'textbox'));	
					}
				}			
				AddToLog($this->getTitle().' updated', 'settings');
			}
			
			if (isset($reset)) {				
				WT_DB::prepare(
				"DELETE FROM `##module_setting` WHERE setting_name LIKE 'JB%'"
			)->execute();
				AddToLog($this->getTitle().' reset to default values', 'config');
				$controller->addInlineJavascript('jQuery("option.default").prop("selected", true); jQuery(".upload").hide()');
			}
			
			$JB_SETTINGS = $this->getSettings();
			$error = '';	
			
			// Admin page content
			$html = '<link rel="stylesheet" href="'.WT_MODULES_DIR.$this->getName().'/style.css" type="text/css">
				<div id="jb_options">	
					<div id="error" style="display:none"></div>				
					<h2>'.$controller->getPageTitle().'</h2>
					<form method="post" name="JustBlack Theme Options" action="'.$this->getConfigLink().'" enctype="multipart/form-data">
						<div class="block_left">
							<div class="field">
								<label for="treetitle">'.WT_I18N::translate('Use the Family tree title in the header?').help_link('treetitle', $this->getName()).'</label>
								<input type="checkbox" id="treetitle" name="JB_TREETITLE" '.$this->getChecked($JB_SETTINGS['TREETITLE']).' />
							</div>
							<div class="field titlepos">
								<label for="titlepos">'.WT_I18N::translate('Position of the Family tree title').help_link('treetitle_position', $this->getName()).'</label>
								<input type="textbox" id="titlepos" name="JB_TITLEPOS" value="'.$JB_SETTINGS['TITLEPOS'].'" />								
							</div>
							<div class="field titlesize">
								<label for="titlesize">'.WT_I18N::translate('Size of the Family tree title').'</label>
								<input type="textbox" id="titlesize" name="JB_TITLESIZE" size="2" value="'.$JB_SETTINGS['TITLESIZE'].'" /> px								
							</div>
							<div class="field">
								<label for="header">'.WT_I18N::translate('Use header image?').'</label>
								<select id="header" name="JB_HEADER">
									<option class="default" value="default">'.WT_I18N::translate('Default').'</option>
									<option value="custom">'.WT_I18N::translate('Custom').'</option>
									<option value="none">'.WT_I18N::translate('None').'</option>
								</select>
							</div>
							<div class="field upload title">
								<label for="current_headerimg">'.WT_I18N::translate('Current custom header-image').'</label>';
								$ext = strtolower(substr(strrchr($JB_SETTINGS['HEADERIMG'], '.'), 1));
								if(file_exists(WT_STATIC_URL.'themes/justblack/css/images/custom_header.'.$ext)){
										$ext == 'jpg' ? $type = 'image/jpeg' : $type = 'image/'.$ext;
										$html .= '	<a class="gallery" type="'.$type.'" href="'.WT_STATIC_URL.'themes/justblack/css/images/custom_header.'.$ext.'">
														<span class="current_headerimg">'.$JB_SETTINGS['HEADERIMG'].'</span>
													</a>';																			
								}	
								else {
										$html .= '	<span class="current_headerimg">'.$JB_SETTINGS['HEADERIMG'].'</span>';
								}
				$html .= '	</div>
							<div class="field upload">
								<label for="headerimg">'.WT_I18N::translate('Upload a (new) custom header image').'</label>
								<input type="file" id="headerimg" name="JB_HEADERIMG" /><br/>'.
								checkbox('resize', false, 'id="resize"').'<label for="resize">'.WT_I18N::translate('Resize (800x150px)').'</label>
							</div>
							<div class="field headerheight">
								<label for="headerheight">'.WT_I18N::translate('Height of the header area').'</label>
								<input type="textbox" id="headerheight" name="JB_HEADERHEIGHT" size="2" value="'.$JB_SETTINGS['HEADERHEIGHT'].'" /> px
							</div>
							<div class="field">
								<label for="flags">'.WT_I18N::translate('Use flags in header bar as language menu?').help_link('flags', $this->getName()).'</label>
								<input type="checkbox" id="flags" name="JB_FLAGS" '.$this->getChecked($JB_SETTINGS['FLAGS']).' />
							</div>
							<div class="field">
								<label for="compact_menu">'.WT_I18N::translate('Use a compact menu?').'</label>
								<input type="checkbox" id="compact_menu" name="JB_COMPACT_MENU" '.$this->getChecked($JB_SETTINGS['COMPACT_MENU']).' />
							</div>
							<div class="field reports">
								<label for="compact_menu_reports">'.WT_I18N::translate('Include the reports topmenu in the compact \'View\' topmenu?').'</label>
								<input type="checkbox" id="compact_menu_reports" name="JB_COMPACT_MENU_REPORTS" '.$this->getChecked($JB_SETTINGS['COMPACT_MENU_REPORTS']).' />
							</div>	
							<div class="field">
								<label for="media_menu">'.WT_I18N::translate('Media menu in topmenu?').help_link('media_menu', $this->getName()).'</label>
								<input type="checkbox" id="media_menu" name="JB_MEDIA_MENU" '.$this->getChecked($JB_SETTINGS['MEDIA_MENU']).' />
							</div>	
							<div class="field media_link">								
								<label for="media_menu_link">'.WT_I18N::translate('Choose a folder as default for the main menu link').help_link('media_folder', $this->getName()).'</label>								
								<select id="media_menu_link" name="JB_MEDIA_MENU_LINK">';
								$folders = WT_Query_Media::folderList();
									foreach ($folders as $folder) {
										if(empty($folder)) $folder = WT_I18N::translate('Media').'/';
				$html .=				'<option value="'.$folder.'">'.ucfirst($folder).'</option>';
									}
				$html .=		'</select>
							</div>	
							<div class="field">
								<label for="gviewer_pdf">'.WT_I18N::translate('Use google docs viewer for pdf\'s?').help_link('gviewer', $this->getName()).'</label>
								<input type="checkbox" id="gviewer_pdf" name="JB_GVIEWER_PDF" '.$this->getChecked($JB_SETTINGS['GVIEWER_PDF']).' />
							</div>														
							<div id="buttons">
								<input type="submit" name="update" value="'.WT_I18N::translate('Save').'" />&nbsp;&nbsp;
								<input type="submit" name="reset" value="'.WT_I18N::translate('Reset').'" />
							</div>
						</div>
						<div class="block_right">';							
				$html .= '	<div class="block_left">
								<h3>'.WT_I18N::translate('Sort Topmenu items').help_link('sort_topmenu', $this->getName()).'</h3>';
								$menulist 	= $this->checkModule($JB_SETTINGS['MENU_ORDER']);
								foreach($menulist as $menu) {																		
									if($menu['sort'] == 0) $trashMenu[] = $menu;
									elseif ($menu['sort'] == 99) $fakeMenu[] = $menu;
									else $activeMenu[] = $menu;
								}
								$i=1;
								if (isset($activeMenu)) {
									$html .= '
									<ul id="sortMenu">';										
										foreach ($activeMenu as $menu) {
											$html .= '<li class="ui-state-default'.$this->getStatus($menu['label']).'">';
											foreach ($menu as $key => $val) {
												$html .= '<input type="hidden" id="menu_order_'.$key.'_'.$i.'" name="JB_MENU_ORDER['.$i.']['.$key.']" value="'.$val.'"/>';
											}
											$html .= '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$menu['title'].'</li>';
											$i++;
										}								
				$html .= '			</ul>';
								}
								if (isset($trashMenu)) {
				$html .= '			<ul id="trashMenu">'; // trashcan for toggling the compact menu.
										foreach ($trashMenu as $menu) {
											$html .= '<li class="ui-state-default'.$this->getStatus($menu['label']).'">';
											foreach ($menu as $key => $val) {
												$html .= '<input type="hidden" id="menu_order_'.$key.'_'.$i.'" name="JB_MENU_ORDER['.$i.']['.$key.']" value="'.$val.'"/>';
											}
											$html .= '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$menu['title'].'</li>';										
											$i++;
										}			
				$html .= '			</ul>';
								}
								if (isset($fakeMenu)) {
				$html .= '			<div id="fakeMenu">';
										foreach ($fakeMenu as $menu) {
											foreach ($menu as $key => $val) {
												$html .= '<input type="hidden" id="menu_order_'.$key.'_'.$i.'" name="JB_MENU_ORDER['.$i.']['.$key.']" value="'.$val.'"/>';
											}									
											$i++;
										}			
				$html .= '			</div>';
								}
				$html .= '</div>				
					</form>
				</div>';               
				
			// output
			ob_start();			
			$html .= ob_get_clean();
			echo $html;				
			break;
		}
	}
	
	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}		
}