<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\User;

class user_messages_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Messages');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Messages” module */ WT_I18N::translate('Communicate directly with other users, using private messages.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype;

		require_once WT_ROOT.'includes/functions/functions_print_facts.php';

		// Block actions
		$action     = WT_Filter::post('action');
		$message_ids = WT_Filter::postArray('message_id');
		if ($action=='deletemessage') {
			foreach ($message_ids as $message_id) {
				WT_DB::prepare("DELETE FROM `##message` WHERE message_id=?")->execute(array($message_id));
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
		$messages = WT_DB::prepare("SELECT message_id, sender, subject, body, UNIX_TIMESTAMP(created) AS created FROM `##message` WHERE user_id=? ORDER BY message_id DESC")
			->execute(array(Auth::id()))
			->fetchAll();

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$title=WT_I18N::plural('%s message', '%s messages',count($messages), WT_I18N::number(count($messages)));
		$content='<form name="messageform" method="post" onsubmit="return confirm(\''.WT_I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.').'\');">';
		if (count(User::all()) > 1) {
			$content.='<br>'.WT_I18N::translate('Send a message')." <select name=\"touser\">";
			$content.='<option value="">' . WT_I18N::translate('&lt;select&gt;') . '</option>';
			foreach (User::all() as $user) {
				if ($user->getUserId() !== Auth::id() && $user->getPreference('verified_by_admin') && $user->getPreference('contactmethod') !== 'none') {
					$content.='<option value="' . WT_Filter::escapeHtml($user->getUserName()) . '">';
					$content.='<span dir="auto">'.WT_Filter::escapeHtml($user->getRealName()).'</span> - <span dir="auto">' . WT_Filter::escapeHtml($user->getUserName()) . '</span>';
					$content.='</option>';
				}
			}
			$content.='</select> <input type="button" value="'.WT_I18N::translate('Send').'" onclick="message(document.messageform.touser.options[document.messageform.touser.selectedIndex].value, \'messaging2\', \'\'); return false;"><br><br>';
		}
		if (count($messages)==0) {
			$content.=WT_I18N::translate('You have no pending messages.')."<br>";
		} else {
			$content.='<input type="hidden" name="action" value="deletemessage">';
			$content.='<table class="list_table"><tr>';
			$content.='<td class="list_label">'.WT_I18N::translate('Delete').'<br><a href="#" onclick="jQuery(\'#' . $this->getName() . $block_id . ' :checkbox\').prop(\'checked\', true); return false;">'.WT_I18N::translate('All').'</a></td>';
			$content.='<td class="list_label">'.WT_I18N::translate('Subject:').'</td>';
			$content.='<td class="list_label">'.WT_I18N::translate('Date sent:').'</td>';
			$content.='<td class="list_label">'.WT_I18N::translate('Email address:').'</td>';
			$content.='</tr>';
			foreach ($messages as $message) {
				$content.='<tr>';
				$content.='<td class="list_value_wrap"><input type="checkbox" id="cb_message'.$message->message_id.'" name="message_id[]" value="'.$message->message_id.'"></td>';
				$content.='<td class="list_value_wrap"><a href="#" onclick="return expand_layer(\'message'.$message->message_id.'\');"><i id="message'.$message->message_id.'_img" class="icon-plus"></i> <b dir="auto">'.WT_Filter::escapeHtml($message->subject).'</b></a></td>';
				$content.='<td class="list_value_wrap">'.format_timestamp($message->created).'</td>';
				$content.='<td class="list_value_wrap">';
				$user = User::findByIdentifier($message->sender);
				if ($user) {
					$content.='<span dir="auto">' . $user->getRealName() . '</span>';
					$content.='  - <span dir="auto">' . $user->getEmail() . '</span>';
				} else {
					$content.='<a href="mailto:'.WT_Filter::escapeHtml($message->sender).'">'.WT_Filter::escapeHtml($message->sender).'</a>';
				}
				$content.='</td>';
				$content.='</tr>';
				$content.='<tr><td class="list_value_wrap" colspan="5"><div id="message'.$message->message_id.'" style="display:none;">';
				$content.='<div dir="auto" style="white-space: pre-wrap;">' . WT_Filter::expandUrls($message->body) . '</div><br>';
				if (strpos($message->subject, /* I18N: When replying to an email, the subject becomes “RE: <subject>” */ WT_I18N::translate('RE: '))!==0) {
					$message->subject= WT_I18N::translate('RE: ').$message->subject;
				}
				if ($user) {
					$content.='<a href="#" onclick="reply(\''.WT_Filter::escapeJs($message->sender).'\', \''.WT_Filter::escapeJs($message->subject).'\'); return false;">'.WT_I18N::translate('Reply').'</a> | ';
				}
				$content.='<a href="index.php?action=deletemessage&amp;message_id[]='.$message->message_id.'" onclick="return confirm(\''.WT_I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.').'\');">'.WT_I18N::translate('Delete').'</a></div></td></tr>';
			}
			$content.='</table>';
			$content.='<input type="submit" value="'.WT_I18N::translate('Delete selected messages').'"><br>';
		}
		$content.='</form>';

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
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
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
