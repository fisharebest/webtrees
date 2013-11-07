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
// $Id: module.php 13838 2013-07-01 07:41:26Z JustCarmen - Fancy ImageBar version 2.0$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_imagebar_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Menu {
		
	// Extend WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */ 'Fancy Image Bar';
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Sitemaps" module */ WT_I18N::translate('An imagebar with small images on your homepage and/or my page between header and content.');
	}
	
	private function getSettings() {
		// get module settings
		$FIB_IMAGES		= unserialize(get_module_setting($this->getName(), 'FIB_IMAGES'));
		$FIB_PAGES		= unserialize(get_module_setting($this->getName(), 'FIB_PAGES'));
		$FIB_RANDOM		= get_module_setting($this->getName(), 'FIB_RANDOM');
		$FIB_TONE		= get_module_setting($this->getName(), 'FIB_TONE');
		$FIB_SEPIA		= get_module_setting($this->getName(), 'FIB_SEPIA');
		$FIB_SIZE		= get_module_setting($this->getName(), 'FIB_SIZE');
			
		// get defaults if there are no settings
		if (empty($FIB_IMAGES)) 	$FIB_IMAGES 	= '';
		if (empty($FIB_PAGES)) 		$FIB_PAGES[] 	= 'Homepage';
		if (!isset($FIB_RANDOM)) 	$FIB_RANDOM 	= '1';
		if (!isset($FIB_TONE)) 		$FIB_TONE 		= 'Sepia';
		if (!isset($FIB_SEPIA)) 	$FIB_SEPIA 		= '40';
		if (!isset($FIB_SIZE)) 		$FIB_SIZE 		= '80';
		
		// determine how many thumbs we need at most (based on a users screen of 2400px);
		$FIB_MAX = ceil(2400/$FIB_SIZE);		
		
		$FIB_SETTINGS = array(
			'IMAGES' 	=> $FIB_IMAGES,
			'PAGES' 	=> $FIB_PAGES,
			'RANDOM'	=> $FIB_RANDOM,
			'TONE' 		=> $FIB_TONE,
			'SEPIA'		=> $FIB_SEPIA,
			'SIZE'		=> $FIB_SIZE,
			'MAX' 		=> $FIB_MAX
		);
		
		return $FIB_SETTINGS;		
	}	
	
	// Get the medialist from the database (source: library/WT/Query/Media.php)
	private function FancyImageBarMedia($admin_cfg = false) {
		
		$FIB_SETTINGS = $this->getSettings();
		
		if($admin_cfg == true) {
			$sql = "SELECT m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom FROM `##media` WHERE m_type=?";
			$args = array('photo');
		}
		elseif($FIB_SETTINGS['RANDOM'] == '1') { 
			$sql = "SELECT m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom FROM `##media` WHERE m_file=? AND m_type=? ORDER BY RAND()"; 
			$args = array(WT_GED_ID, 'photo');
		}
		else { 
			$sql = "SELECT m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom FROM `##media` WHERE m_file=? AND m_type=? ORDER BY m_id DESC"; 
			$args = array(WT_GED_ID, 'photo');
		};
		
		$rows = WT_DB::prepare($sql)->execute($args)->fetchAll();
		$list = array();
		foreach ($rows as $row) {
			$media = WT_Media::getInstance($row->xref, $row->gedcom_id, $row->gedcom);			
			if ($media->canShow() && $media->mimeType() == 'image/jpeg') { // function $media->canShow() causes a fatal memory limit error when not logged in.
				$list[] = $media;										   // Probably a bug in wt 1.5.0 svn because a lot of other pages are also not loading when not logged in.
			}															   // Wait until final release.
		}
		return $list;	
		print_r($media->getGedcomId());	
	}	
		
	private function FancyThumb($imgSrc, $thumbwidth, $thumbheight) { 
		//getting the image dimensions 
		list($width_orig, $height_orig) = getimagesize($imgSrc);  
		$image = imagecreatefromjpeg($imgSrc);
		$ratio_orig = $width_orig/$height_orig;
	   
		if ($thumbwidth/$thumbheight > $ratio_orig) {
		   $new_height = $thumbwidth/$ratio_orig;
		   $new_width = $thumbwidth;
		} else {
		   $new_width = $thumbheight*$ratio_orig;
		   $new_height = $thumbheight;
		}
		
		$process = imagecreatetruecolor(round($new_width), round($new_height));			   
		imagecopyresampled($process, $image, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);				
		
		$thumb = imagecreatetruecolor($thumbwidth, $thumbheight); 		
		imagecopyresampled($thumb, $process, 0, 0, 0, 0, $thumbwidth, $thumbheight, $thumbwidth, $thumbheight);
	
		imagedestroy($process);
		imagedestroy($image);
		return $thumb;
	}
	
	private function CreateFancyImageBar($srcImages, $thumbWidth, $thumbHeight, $numberOfThumbs) {		
		// defaults
		$pxBetweenThumbs = 0;
		$leftOffSet = $topOffSet = 0;
		$canvasWidth = ($thumbWidth + $pxBetweenThumbs) * $numberOfThumbs;
		$canvasHeight = $thumbHeight;

		// create the FancyImagebar canvas to put the thumbs on
		$FancyImageBar = imagecreatetruecolor($canvasWidth, $canvasHeight);
		
		foreach ($srcImages as $index => $thumb)
		{
			$x = ($index % $numberOfThumbs) * ($thumbWidth + $pxBetweenThumbs) + $leftOffSet;
		 	$y = floor($index / $numberOfThumbs) * ($thumbWidth + $pxBetweenThumbs) + $topOffSet;
		 		 			
		 	imagecopy($FancyImageBar, $thumb, $x, $y, 0, 0, $thumbWidth, $thumbHeight);		 			
		}	
		return $FancyImageBar;
	}
	
	private function FancyImageBarSepia($FancyImageBar, $depth) {
		imagetruecolortopalette($FancyImageBar,1,256);
		
		for ($c=0;$c<256;$c++) {    
			$col=imagecolorsforindex($FancyImageBar,$c);
			$new_col=floor($col['red']*0.2125+$col['green']*0.7154+$col['blue']*0.0721);
		if ($depth>0) {
				$r=$new_col+$depth;
				$g=floor($new_col+$depth/1.86);
				$b=floor($new_col+$depth/-3.48);
			} else {
				$r=$new_col;
				$g=$new_col;
				$b=$new_col;
			}
			imagecolorset($FancyImageBar,$c,max(0,min(255,$r)),max(0,min(255,$g)),max(0,min(255,$b)));
			
		}
		return $FancyImageBar;
	}
	
	// Extend WT_Module_Config
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
										
			$controller=new WT_Controller_Page;
			$controller
				->requireAdminLogin()
				->setPageTitle($this->getTitle())
				->pageHeader();				
			
			$update = WT_Filter::postBool('update');
			$reset = WT_Filter::postBool('reset');

			if (isset($update)) {
				for($i=0;$i<$_POST['count'];$i++) {
					if (isset($_POST['NEW_FIB_IMAGES_'.$i])) {
						$NEW_FIB_GED = $_POST['NEW_FIB_GED_'.$i];
						$NEW_FIB_IMAGES[$NEW_FIB_GED][] = $_POST['NEW_FIB_IMAGES_'.$i];
					}
				}
				for($i=1;$i<=2;$i++) {
					if (isset($_POST['NEW_FIB_PAGE_'.$i])) {
						$NEW_FIB_PAGES[] = $_POST['NEW_FIB_PAGE_'.$i];
					}
				}					
				
				set_module_setting($this->getName(), 'FIB_IMAGES',  serialize($NEW_FIB_IMAGES));
				set_module_setting($this->getName(), 'FIB_PAGES',  	serialize($NEW_FIB_PAGES));
				set_module_setting($this->getName(), 'FIB_RANDOM',  $_POST['NEW_FIB_RANDOM']);
				set_module_setting($this->getName(), 'FIB_TONE',  	$_POST['NEW_FIB_TONE']);
				set_module_setting($this->getName(), 'FIB_SEPIA',  	$_POST['NEW_FIB_SEPIA']);
				set_module_setting($this->getName(), 'FIB_SIZE',  	$_POST['NEW_FIB_SIZE']);					
				
				AddToLog($this->getTitle().' config updated', 'config');
			}
			if (isset($reset)) {				
				WT_DB::prepare(
				"DELETE FROM `##module_setting` WHERE setting_name LIKE 'FIB%'"
			)->execute();					
				
				AddToLog($this->getTitle().' config reset to default values', 'config');
			}
			
			if($medialist=$this->FancyImageBarMedia(true)) {
			
				$FIB_SETTINGS = $this->getSettings();
				$FIB_IMAGES = $FIB_SETTINGS['IMAGES'];
					
				$controller->addInlineJavascript('
					function include_css(css_file) {
						var html_doc = document.getElementsByTagName("head")[0];
						var css = document.createElement("link");
						css.setAttribute("rel", "stylesheet");
						css.setAttribute("type", "text/css");
						css.setAttribute("href", css_file);
						html_doc.appendChild(css);
					}
					include_css("'.WT_MODULES_DIR.$this->getName().'/style.css");
										
					if (jQuery("#image_block li:visible").length == 0) jQuery(".no_images").show();
					
					jQuery("#select_all").click(function(){
						if (jQuery(this).is(":checked") == true) {
							jQuery("#image_block").find(":checkbox").prop("checked", true);
						} else {
							jQuery("#image_block").find(":checkbox").prop("checked", false);
						}
					});
					
					jQuery("#gedcom").change(function() {							
						jQuery("#image_block li").each(function(){
							if(jQuery(this).data("gedcom") == jQuery("#gedcom option:selected").val()) {
								jQuery(this).show();
							}
							else {
								jQuery(this).hide();
							}
						});	
						if (jQuery("#image_block li:visible").length == 0) {
							jQuery(".no_images").show();
							jQuery(".selectbox").hide();
						}
						else {
							jQuery(".no_images").hide();
							jQuery(".selectbox").show();
						}
					});					
						
					// extra options for Sepia Tone	
					jQuery("#tone").change(function() {
						jQuery("#tone option:selected").each(function () {
							if($(this).text() == "Sepia") {				
								$("#sepia").fadeIn(500);	
							} else {
								$("#sepia").fadeOut(500);
							}								
						});
					});					
				'); 
					
				$html = '<div id="fib_config"><h2>'.$this->getTitle().'</h2>
						<form method="post" name="configform" action="'.$this->getConfigLink().'">
							<input type="hidden" name="count" value="'.count($medialist).'" />	
							<div id="selectbar">
								<div class="left">
									<span>'.WT_I18N::translate('Select a tree:').'</span>
									<select id="gedcom">';
										foreach (WT_Tree::getAll() as $tree) {
											if($tree->tree_id == WT_GED_ID) {
												$html .= '<option value="'.$tree->tree_id.'" selected="selected"/>'.$tree->tree_title.'</option>';
											} else { 
												$html .= '<option value="'.$tree->tree_id.'"/>'.$tree->tree_title.'</option>';
											}
										}
				$html .= '			</select>
							</div>
							<div id="buttons">
								<input type="submit" name="update" value="'.WT_I18N::translate('Save').'" />&nbsp;&nbsp;
								<input type="submit" name="reset" value="'.WT_I18N::translate('Reset').'" />
							</div>	
						</div>
						<div class="clearfloat"></div>
						<div id="block_left" class="left">														
							<div class="left">'.WT_I18N::translate('Choose which images you want to show in the Fancy Image Bar').':'.help_link('choose_images', $this->getName()).'</div>
							<div class="selectbox"><span>Select All</span>';
							empty($FIB_IMAGES) ? $html .= '<input id="select_all" type="checkbox" checked="checked"/>' :  $html .= '<input id="select_all" type="checkbox"/>';
				$html .= '</div>
						<div class="clearfloat"></div>
						<h3 class="no_images">'.WT_I18N::translate('No images to display for this tree').'</h3>            				
						<ul id="image_block">';
						// begin looping through the media 
						$i = 0;
										
						foreach ($medialist as $media) {
							if (file_exists($media->getServerFilename())) {
								$image = $this->FancyThumb($media->getServerFilename(), 60, 60);
								
								if(empty($FIB_IMAGES)) $img_checked = ' checked="checked"';
								elseif(!empty($FIB_IMAGES[$media->getGedcomId()]) && is_array($FIB_IMAGES[$media->getGedcomId()]) && in_array($media->getXref(), $FIB_IMAGES[$media->getGedcomId()]))$img_checked = ' checked="checked"';
								else $img_checked = "";
								
								$media->getGedcomId() != WT_GED_ID ? $style = 'style="display:none"' : $style = '';
								
								ob_start();imagejpeg($image,null,100);$image = ob_get_clean();	
								$html .= '	<li data-gedcom= "'.$media->getGedcomId().'"'.$style.'><img src="data:image/jpeg;base64,'.base64_encode($image).'" alt="'.$media->getXref().'" title="'.$media->getXref().'"/><br/>												
												<span>
													<input type="hidden" name="NEW_FIB_GED_'.$i.'" value ="'.$media->getGedcomId().'"/>
													<input type="checkbox" name="NEW_FIB_IMAGES_'.$i.'" value="'.$media->getXref().'"'.$img_checked.'/>	
												</span><br/><br/>
											</li>';
								$i++;
							}
						}								
				$html .='</ul>
						</div>
						<div id="block_right" class="right">
							<h3>'.WT_I18N::translate('Options (for all trees)').':</h3>
							<div id="options">';
							in_array('Homepage', $FIB_SETTINGS['PAGES']) ? $hp_checked = ' checked="checked"' : $hp_checked = "";
							in_array('My page', $FIB_SETTINGS['PAGES']) ? $mp_checked = ' checked="checked"' : $mp_checked = "";
				$html .= '	<p><label>'.WT_I18N::translate('Show Fancy Image Bar on').':</label>
								<input type="checkbox" name="NEW_FIB_PAGE_1" value="Homepage"'.$hp_checked.' />'.WT_I18N::translate('Home page').'
								<input type="checkbox" name="NEW_FIB_PAGE_2" value="My page"'.$mp_checked.' />'.WT_I18N::translate('My page').'
							</p>
							<p><label>'.WT_I18N::translate('Random images').':</label>';
							if($FIB_SETTINGS['RANDOM'] == '1') {
				$html .= '		<input type="radio" name="NEW_FIB_RANDOM" value="1" checked="checked" />'.WT_I18N::translate('yes').'
								<input type="radio" name="NEW_FIB_RANDOM" value="0" />'.WT_I18N::translate('no');
							} else {
				$html .= '		<input type="radio" name="NEW_FIB_RANDOM" value="1" />'.WT_I18N::translate('yes').'
								<input type="radio" name="NEW_FIB_RANDOM" value="0" checked="checked" />'.WT_I18N::translate('no');
							}										
				$html .= '	</p>
							<p><label>'.WT_I18N::translate('Image Tone').':</label>
								<select id="tone" name="NEW_FIB_TONE">';
									if ($FIB_SETTINGS['TONE'] == 'Sepia') {							
				$html .= '				<option value="Sepia" selected="selected">'.WT_I18N::translate('Sepia').'</option>
										<option value="BW">'.WT_I18N::translate('Black and white').'</option>
										<option value="Colors">'.WT_I18N::translate('Original colors').'</option>';
									}
									if ($FIB_SETTINGS['TONE'] == 'BW') {							
				$html .= '				<option value="Sepia">'.WT_I18N::translate('Sepia').'</option>
										<option value="BW" selected="selected">'.WT_I18N::translate('Black and white').'</option>
										<option value="Colors">'.WT_I18N::translate('Original colors').'</option>';
									}
									if ($FIB_SETTINGS['TONE'] == 'Colors') {							
				$html .= '				<option value="Sepia">'.WT_I18N::translate('Sepia').'</option>
										<option value="BW">'.WT_I18N::translate('Black and white').'</option>
										<option value="Colors" selected="selected">'.WT_I18N::translate('Original colors').'</option>';
									}									
				$html .= '		</select>
								</p><p id="sepia">
									<label>'.WT_I18N::translate('Amount of sepia').':</label>
									<input type="text" name="NEW_FIB_SEPIA" size="3" value="'.$FIB_SETTINGS['SEPIA'].'"/>&nbsp;Value between 0 and 100	
								</p><p>
									<label>'.WT_I18N::translate('Cropped image size').':</label>
									<input type="text" name="NEW_FIB_SIZE" size="3" value="'.$FIB_SETTINGS['SIZE'].'"/>&nbsp;px	
								</p>
							</div>										
						</div>					
					</form>
					</div>';
				
				// output
				ob_start();			
				$html .= ob_get_clean();
				echo $html;
			}
			break;
		}
	}
	
	// Extend WT_Module_Menu
	private function GetFancyImageBar(){
		
		if($medialist=$this->FancyImageBarMedia()) {
				
			$FIB_SETTINGS = $this->getSettings();
			$FIB_IMAGES = $FIB_SETTINGS['IMAGES'];
			$width = $height = $FIB_SETTINGS['SIZE'];
					
			// begin looping through the media and write the imagebar
			$srcImages = array();
			foreach ($medialist as $media) {
				if (file_exists($media->getServerFilename())) {
					if (!empty($FIB_IMAGES[$media->getGedcomId()]) && is_array($FIB_IMAGES[$media->getGedcomId()]) && in_array($media->getXref(), $FIB_IMAGES[$media->getGedcomId()])) {
						$srcImages[] = $this->FancyThumb($media->getServerFilename(), $width, $height);
					}
				}
			}	
			
			if(!empty($srcImages)) {
				// be sure the imagebar will be big enough for wider screens
				$newArray = array();
				
				while(count($newArray) <= $FIB_SETTINGS['MAX']){
					$newArray = array_merge($newArray, $srcImages);
				}
				
				// reduce the new array to the desired length (as there might be too many elements in the new array
				$srcImages = array_slice($newArray, 0, $FIB_SETTINGS['MAX']);
				
				$FancyImageBar = $this->CreateFancyImageBar($srcImages, $width, $height, $FIB_SETTINGS['MAX']);
				
				if($FIB_SETTINGS['TONE'] == 'Sepia') {
					$FancyImageBar = $this->FancyImageBarSepia($FancyImageBar, $FIB_SETTINGS['SEPIA']);
				}
				elseif($FIB_SETTINGS['TONE'] == 'BW') {
					$FancyImageBar = $this->FancyImageBarSepia($FancyImageBar, 0);
				}
	
				ob_start();imagejpeg($FancyImageBar,null,100);$FancyImageBar = ob_get_clean();			
				$html = '<div id="fancy_imagebar">
							<img src="data:image/jpeg;base64,'.base64_encode($FancyImageBar).'" />
						</div>';
									
				// output
				return $html;
			}
		}
	}
	
	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}	
	
	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 999;
	}

	// Implement WT_Module_Menu
	public function getMenu() {
		
		// We don't actually have a menu - this is just a convenient "hook" to execute code at the right time during page execution
		global $controller, $ctype;	
		
		if (WT_SCRIPT_NAME === 'index.php') {
						
			$FIB_SETTINGS = $this->getSettings();			
			
			if ($ctype=='gedcom' && in_array('Homepage', $FIB_SETTINGS['PAGES']) || ($ctype=='user' && in_array('My page', $FIB_SETTINGS['PAGES']))) {	
			
				// add js file to set a few theme depending styles
				$controller->addExternalJavascript(WT_MODULES_DIR.$this->getName().'/style.js');		
				
				// put the fancy imagebar in the right position
				$controller->addInlineJavaScript("				
					jQuery('#content').before(jQuery('#fancy_imagebar'));
					setTimeout(function() {
						jQuery('#fancy_imagebar').show();
					}, 250);																					
				");
				
				$html = $this->GetFancyImageBar();
				
				// output
				ob_start();			
				$html .= ob_get_clean();	
				echo $html;
			}		
		}		
		return null;
	}
}