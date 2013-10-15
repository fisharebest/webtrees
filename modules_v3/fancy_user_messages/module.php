<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_user_messages_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */ 'Fancy User Messages';
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Messages” module */ WT_I18N::translate('Communicate directly with other users, using private messages.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $controller, $ctype;
		
		$controller->addInlineJavascript('
			jQuery("i[id^=message]").click(function(){					
				var message_id = jQuery(this).data("message_id");
				var user_id = jQuery(this).data("user_id");
				if(jQuery(this).hasClass("icon-plus")) {
					jQuery(this).removeClass("icon-plus").addClass("icon-minus");
					jQuery(this).parents("tr").each(function(){
						var curRow = jQuery(this);
						if(curRow.hasClass("even")) var $class = "odd";
						else var $class = "even";
						curRow.parent().find("tr").not(curRow).hide();	
						var url = WT_MODULES_DIR + "'.$this->getName().'/user_message.php?user_id=" + user_id + "&message_id=" + message_id;									
						jQuery.get(url, function(data){	
							curRow.after("<tr id=\"message-body-" + message_id + "\" class=\"" + $class + "\">" + data + "</tr>");
						});							
					});
				}
				else {
					jQuery("#message-body-" + message_id).remove();
					jQuery(this).parents("tbody").find("tr").show();
					jQuery(this).removeClass("icon-minus").addClass("icon-plus");					
				}
			});		
		');

		// Block actions
		$action     = WT_Filter::get('action');
		$message_id = WT_Filter::getArray('message_id');
		if ($action=='deletemessage') {
			foreach ($message_id as $msg_id) {
				deleteMessage($msg_id);
			}
		}
				
		$block=get_block_setting($block_id, 'block', true);
		if ($cfg) {
			foreach (array('block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}
		$messages = getUserMessages(WT_USER_ID);
		
		$title=WT_I18N::plural('%s message', '%s messages',count($messages), WT_I18N::number(count($messages)));
		
		// start form
		$content='<form name="messageform" action="index.php?ctype='.$ctype.'" method="get" onsubmit="return confirm(\''.WT_I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.').'\');">';
		
		// header
		if (get_user_count()>1) {
			$content.= 	'<div style="float:left">'.WT_I18N::translate('Send Message');
			$content .= '<select name="touser" style="margin:0 10px">';
			$content.=		'<option value="">' . WT_I18N::translate('&lt;select&gt;') . '</option>';
							foreach (get_all_users() as $user_id=>$user_name) {
								if ($user_id!=WT_USER_ID && get_user_setting($user_id, 'verified_by_admin') && get_user_setting($user_id, 'contactmethod')!='none') {
			$content.=				'<option value="'.$user_name.'">';
			$content.=					WT_Filter::escapeHtml(getUserFullName($user_id));
			$content.=				'</option>';
								}
							}
			$content.=	'</select>';
			$content.=	'<input type="button" value="'.WT_I18N::translate('Send').'" onclick="message(document.messageform.touser.options[document.messageform.touser.selectedIndex].value, \'messaging2\', \'\'); return false;"></div>';
		}
		
		if (count($messages)==0) {
			$content.= '<div>'.WT_I18N::translate('You have no pending messages.').'</div>';
		} else {
			// link to select all messages at once
			$content.= '<input type="hidden" name="action" value="deletemessage">';			
			$content .= '<div style="text-align:right;padding:10px"><a href="#" onclick="jQuery(\'.'.$this->getName().'_block :checkbox\').attr(\'checked\',\'checked\'); return false;">'.WT_I18N::translate('Select all').'</a></div>';
		
			//content			
			$content .= '<div class="clearfloat">'.$this->print_user_table($messages).'</div>';
			
			// submit button to delete messages
			$content .= '<input type="submit" value="'.WT_I18N::translate('Delete Selected Messages').'">';			
		}
		// end form
		$content .= '</form>';
		
		// template
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		
		if ($template) {
			if ($block) {
				require WT_THEME_DIR.'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR.'templates/block_main_temp.php';
			}
		} else {
			return $content;
		}
	}
	
	// print a table of uses messages - code retrieved from print_recent_changes function (functions_print_lists line 1526)
	private function print_user_table($messages) {
		global $controller;
		
		$table_id = "ID" . (int)(microtime() * 1000000); // create a unique ID
		
		$html = '';
		$controller
			->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
			->addInlineJavascript('				
				jQuery("#'.$table_id.'").dataTable({
					"sDom": \'t\',
					"bPaginate": false,
					"bAutoWidth":false,
					"bLengthChange": false,
					"bFilter": false,
					'.WT_I18N::datatablesI18N().',
					"bJQueryUI": true,
					"aoColumns": [
						/* 0-Delete */    		{"bSortable": false, "sClass": "center"},
						/* 1-Subject */  		{"bSortable": false},
						/* 2-Date_send */  		{"bSortable": false},
						/* 3-User - email */    {"bSortable": false}
					]
				});				
			');
	
		//-- table header
		$html .= '<table id="' . $table_id . '" class="width100">';
		$html .= '<thead><tr>';
		$html .= '<th>'	. WT_I18N::translate('Delete') . '</th>';
		$html .= '<th>' . WT_I18N::translate('Subject:') . '</th>';
		$html .= '<th>' . WT_I18N::translate('Date Sent:') . '</th>';
		$html .= '<th>' . WT_I18N::translate('Email Address:') . '</th>';
		$html .= '</tr></thead><tbody>';

		//-- table body
		foreach ($messages as $message) {
			$user_id = get_user_id($message->sender);	
				
			$html .= '<tr><td>';
			$html .= '<input type="checkbox" id="cb_message'.$message->message_id.'" name="message_id[]" value="'.$message->message_id.'">';	
			$html .= '</td>';
			
			//-- Message subject
			$html .= '<td class="wrap">';
			$html .= '<i id="message'.$message->message_id.'_img" data-user_id = "'.$user_id.'" data-message_id = "'.$message->message_id.'" class="icon-plus"></i>'.WT_Filter::escapeHtml($message->subject);				
			$html .= "</td>";
			
			//-- Message date/time
			$html .= "<td class='nowrap'>" . format_timestamp($message->created) . "</td>";
			
			//-- User name and email address
			$html .= "<td class='wrap'>";			
			if ($user_id) {
				$html .= '<span dir="auto">'.getUserFullName($user_id).'</span> - <span dir="auto">'.getUserEmail($user_id).'</span>';
			} else {
				$html .= '<a href="mailto:'.WT_Filter::escapeHtml($message->sender).'">'.WT_Filter::escapeHtml($message->sender).'</a>';
			}			
			$html .= "</td></tr>";						
		}	
		
		$html .= '</tbody></table>';
		return $html;
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save')) {
			set_block_setting($block_id, 'block',  WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
