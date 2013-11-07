<?php
// Fancy User Messages Module - Version 1.0 - JustCarmen 2013
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
		return /* I18N: Description of the â€œMessagesâ€? module */ WT_I18N::translate('Communicate directly with other users, using private messages.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $controller, $ctype;
		
		require_once WT_ROOT.'includes/functions/functions_edit.php';
		
		// load the module stylesheet
		$content = $this->includeCss(WT_MODULES_DIR.$this->getName().'/style.css');
		
		$controller->addInlineJavascript('						
			// select all			
			jQuery("input[name=select_all]").click(function(){
				if (jQuery(this).is(":checked") == true) {
					jQuery("input[id^=cb-message]").prop("checked", true);
				} else {
					jQuery("input[id^=cb-message]").prop("checked", false);
				}
			});
			
			// open message body
			jQuery("i[id^=icon-message]").click(function(){					
				var message_id = jQuery(this).data("message_id");
				var user_id = jQuery(this).data("user_id");
				if(jQuery(this).hasClass("icon-plus")) {
					if(jQuery("tr[id^=message-body]").length > 0) {
						jQuery("tr[id^=message-body]").hide("slow");
						jQuery("i[id^=icon-message]").removeClass("icon-minus").addClass("icon-plus");	
					}
					jQuery(this).removeClass("icon-plus").addClass("icon-minus");
					if(jQuery("#message-body-" + message_id).length > 0) {
						jQuery("#message-body-" + message_id).show("slow");
					}
					else {						
						if (jQuery("#message-" + message_id).hasClass("even")) var $class = "odd";
						else var $class = "even";
						var url = WT_MODULES_DIR + "'.$this->getName().'/user_message.php?user_id=" + user_id + "&message_id=" + message_id;									
						jQuery.get(url, function(data){	
							jQuery("#message-" + message_id).after("<tr id=\"message-body-" + message_id + "\" class=\"" + $class + "\" style=\"display:none\">" + data + "</tr>");
						}).done(function(){
							jQuery("#message-body-" + message_id).show("slow");
						
						});
					}
				}
				else {
					jQuery("#message-body-" + message_id).hide("slow");
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
		$content.='<form name="messageform" action="index.php?ctype='.$ctype.'" method="get" onsubmit="return confirm(\''.WT_I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.').'\');">';
		
		// header
		if (get_user_count()>1) {
			$content.= 	'<div style="float:left;padding:10px 0">'.WT_I18N::translate('Send message');
			$content.= 	'<select name="touser" style="margin:0 10px">';
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
			// submit button to delete messages
			$content.= '<input type="hidden" name="action" value="deletemessage">';			
			$content.= '<div style="text-align:right;padding:10px 0">';			
			$content.= '<input type="submit" value="'.WT_I18N::translate('Delete Selected Messages').'"></div>';		
		
			//content			
			$content.= '<div class="clearfloat">'.$this->print_user_table($messages).'</div>';				
		}
		// end form
		$content.= '</form>';
		
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
		$aaSorting = "[4,'desc']";
		
		$html = '';
		$controller
			->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
			->addInlineJavascript('	
				var oTable = jQuery("#'.$table_id.'").dataTable({
					"sDom": \'t\',
					"sScrollY": "250px",
					"bScrollCollapse": true,
					"bPaginate": false,
					"bAutoWidth":false,
					"bLengthChange": false,
					"bFilter": true,
					'.WT_I18N::datatablesI18N().',
					"bJQueryUI": true,
					"aaSorting": ['.$aaSorting.'],
					"aoColumns": [
						/* 0-Delete */    		{"bSortable": false, "sClass": "center"},
						/* 1-Subject */  		{"bSortable": false},
						/* 2-Date_send */  		{"iDataSort": 4},
						/* 3-User - email */    {"bSortable": false},
						/* 4-DATE */    		{"bVisible": false}
					],
					"fnDrawCallback": function() {
						if(jQuery(".icon-minus").length > 0) {
							jQuery(".icon-minus").removeClass("icon-minus").addClass("icon-plus");
						}
						var h = jQuery(".dataTables_scrollHead th").outerHeight() - 1;
						var f = jQuery(".dataTables_scrollFoot th").outerHeight() - 1;						
						var b =jQuery(".dataTables_scrollBody .message-table").height();
						jQuery(".dataTables_scrollHead, .dataTables_scrollFoot").removeClass("ui-state-default");					
						if (b > 250) {
							jQuery(".dataTables_scrollHeadInner").prepend("<div class=\"ui-state-default scrollbarBlock\" style=\"top:2px; height:" + h + "px\">");
							jQuery(".dataTables_scrollFootInner").prepend("<div class=\"ui-state-default scrollbarBlock\" style=\"bottom:2px; height:" + f + "px\">");							
						}
						else {
							jQuery(".scrollbarBlock").remove();
						}						
					}
				});					
	
				jQuery("tfoot input").keyup( function () {
					oTable.fnFilter( this.value, jQuery("tfoot input").index(this) );
				} );	
				
				var asInitVals = new Array();
				jQuery("tfoot input").each( function (i) {
					asInitVals[i] = this.value;
				} );
	
				jQuery("tfoot input").focus( function () {
					if ( this.className == "search_init" )
					{
						this.className = "";
						this.value = "";
					}
				} );
				
				jQuery("tfoot input").blur( function (i) {
					if ( this.value == "" )
					{
						this.className = "search_init";
						this.value = asInitVals[jQuery("tfoot input").index(this)];
					}
				} );			
			');
		
		//-- table header		
		$html .= '<table id="' . $table_id . '" class="message-table">';			
		$html .= '<thead><tr>';
		$html .= '<th class="nowrap">'	. WT_I18N::translate('Delete') . '<input type="checkbox" name="select_all" style="vertical-align:middle;margin:0 3px"></th>';
		$html .= '<th>' . str_replace(":", "", WT_I18N::translate('Subject:')) . '</th>';
		$html .= '<th>' . str_replace(":", "", WT_I18N::translate('Date Sent:')) . '</th>';
		$html .= '<th>' . WT_I18N::translate('Email address') . '</th>';
		$html .= '<th>DATE</th>';     //hidden by datatables code
		$html .= '</tr></thead>';
		
		// table footer
		$html .= '<tfoot><tr>';
		$html .= '<th><input type="text" class="search_init" style="display:none"></th>';
		$html .= '<th><input type="text" class="search_init" value="'.WT_I18N::translate('Search').' '.str_replace(":", "", WT_I18N::translate('Subject:')).'" name="search_subject"></th>';
		$html .= '<th><input type="text" class="search_init" value="'.WT_I18N::translate('Search').' '.str_replace(":", "", WT_I18N::translate('Date Sent:')).'" name="search_date_sent"></th>';
		$html .= '<th><input type="text" class="search_init" value="'.WT_I18N::translate('Search').' '. WT_I18N::translate('Email address').'" name="search_email_address"></th>';
		$html .= '<th>&nbsp;</th>';
		$html .= '</tr></tfoot>';

		//-- table body
		$html .= '<tbody>';
		foreach ($messages as $message) {
			$user_id = get_user_id($message->sender);	
				
			$html .= '<tr id="message-'.$message->message_id.'"><td>';
			$html .= '<input type="checkbox" id="cb-message-'.$message->message_id.'" name="message_id[]" value="'.$message->message_id.'">';	
			$html .= '</td>';
			
			//-- Message subject
			$html .= '<td class="wrap">';
			$html .= '<i id="icon-message-'.$message->message_id.'" data-user_id = "'.$user_id.'" data-message_id = "'.$message->message_id.'" class="icon-plus"></i><span dir="auto">'.WT_Filter::escapeHtml($message->subject).'</span>';				
			$html .= "</td>";
			
			//-- Message date/time
			$html .= '<td class="nowrap">' . strip_tags(format_timestamp($message->created)) . "</td>";
			
			//-- User name and email address
			$html .= '<td class="wrap">';			
			if ($user_id) {
				$html .= '<span dir="auto">'.getUserFullName($user_id).'</span> - <span dir="auto">'.getUserEmail($user_id).'</span>';
			} else {
				$html .= '<a href="mailto:'.WT_Filter::escapeHtml($message->sender).'">'.WT_Filter::escapeHtml($message->sender).'</a>';
			}			
			$html .= "</td>";
			//-- change date (sortable) hidden by datatables code
			$html .= "<td>" . $message->created . "</td></tr>";						
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
		return false;
	}
	
	private function includeCss($css) {
		return
			'<script>
				if (document.createStyleSheet) {
					document.createStyleSheet("'.$css.'"); // For Internet Explorer
				} else {
					var newSheet=document.createElement("link");
					newSheet.setAttribute("href","'.$css.'");
					newSheet.setAttribute("type","text/css");
					newSheet.setAttribute("rel","stylesheet");
					document.getElementsByTagName("head")[0].appendChild(newSheet);
				}
			</script>';
	}		
}
