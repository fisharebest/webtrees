<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class UserMessagesModule
 */
class UserMessagesModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Messages');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Messages” module */ I18N::translate('Communicate directly with other users, using private messages.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		// Block actions
		$action      = Filter::post('action');
		$message_ids = Filter::postArray('message_id');
		if ($action === 'deletemessage') {
			foreach ($message_ids as $message_id) {
				Database::prepare("DELETE FROM `##message` WHERE message_id=?")->execute(array($message_id));
			}
		}
		$block = get_block_setting($block_id, 'block', '1');
		if ($cfg) {
			foreach (array('block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}
		$messages = Database::prepare("SELECT message_id, sender, subject, body, UNIX_TIMESTAMP(created) AS created FROM `##message` WHERE user_id=? ORDER BY message_id DESC")
			->execute(array(Auth::id()))
			->fetchAll();

		$count = count($messages);
		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		$title = I18N::plural('%s message', '%s messages', $count, I18N::number($count));
		$users = array_filter(User::all(), function(User $user) {
			return $user->getUserId() !== Auth::id() && $user->getPreference('verified_by_admin') && $user->getPreference('contactmethod') !== 'none';
		});

		$content = '<form name="messageform" method="post" onsubmit="return confirm(\'' . I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.') . '\');">';
		if ($users) {
			$content .= '<label for="touser">' . I18N::translate('Send a message') . '</label>';
			$content .= '<select id="touser" name="touser">';
			$content .= '<option value="">' . I18N::translate('&lt;select&gt;') . '</option>';
			foreach ($users as $user) {
				$content .= sprintf('<option value="%1$s">%2$s - %1$s</option>', Filter::escapeHtml($user->getUserName()), Filter::escapeHtml($user->getRealName()));
			}
			$content .= '</select>';
			$content .= '<input type="button" value="' . I18N::translate('Send') . '" onclick="message(document.messageform.touser.options[document.messageform.touser.selectedIndex].value, \'messaging2\', \'\'); return false;"><br><br>';
		}
		if ($messages) {
			$content .= '<input type="hidden" name="action" value="deletemessage">';
			$content .= '<table class="list_table"><tr>';
			$content .= '<th class="list_label">' . I18N::translate('Delete') . '<br><a href="#" onclick="jQuery(\'#' . $this->getName() . $block_id . ' :checkbox\').prop(\'checked\', true); return false;">' . I18N::translate('All') . '</a></th>';
			$content .= '<th class="list_label">' . I18N::translate('Subject:') . '</th>';
			$content .= '<th class="list_label">' . I18N::translate('Date sent:') . '</th>';
			$content .= '<th class="list_label">' . I18N::translate('Email address:') . '</th>';
			$content .= '</tr>';
			foreach ($messages as $message) {
				$content .= '<tr>';
				$content .= '<td class="list_value_wrap"><input type="checkbox" id="cb_message' . $message->message_id . '" name="message_id[]" value="' . $message->message_id . '"></td>';
				$content .= '<td class="list_value_wrap"><a href="#" onclick="return expand_layer(\'message' . $message->message_id . '\');"><i id="message' . $message->message_id . '_img" class="icon-plus"></i> <b dir="auto">' . Filter::escapeHtml($message->subject) . '</b></a></td>';
				$content .= '<td class="list_value_wrap">' . format_timestamp($message->created) . '</td>';
				$content .= '<td class="list_value_wrap">';
				$user = User::findByIdentifier($message->sender);
				if ($user) {
					$content .= $user->getRealNameHtml();
					$content .= '  - <span dir="auto">' . $user->getEmail() . '</span>';
				} else {
					$content .= '<a href="mailto:' . Filter::escapeHtml($message->sender) . '">' . Filter::escapeHtml($message->sender) . '</a>';
				}
				$content .= '</td>';
				$content .= '</tr>';
				$content .= '<tr><td class="list_value_wrap" colspan="4"><div id="message' . $message->message_id . '" style="display:none;">';
				$content .= '<div dir="auto" style="white-space: pre-wrap;">' . Filter::expandUrls($message->body) . '</div><br>';
				if (strpos($message->subject, /* I18N: When replying to an email, the subject becomes “RE: <subject>” */ I18N::translate('RE: ')) !== 0) {
					$message->subject = I18N::translate('RE: ') . $message->subject;
				}
				if ($user) {
					$content .= '<a href="#" onclick="reply(\'' . Filter::escapeJs($message->sender) . '\', \'' . Filter::escapeJs($message->subject) . '\'); return false;">' . I18N::translate('Reply') . '</a> | ';
				}
				$content .= '<a href="index.php?action=deletemessage&amp;message_id%5B%5D=' . $message->message_id . '" onclick="return confirm(\'' . I18N::translate('Are you sure you want to delete this message?  It cannot be retrieved later.') . '\');">' . I18N::translate('Delete') . '</a></div></td></tr>';
			}
			$content .= '</table>';
			$content .= '<input type="submit" value="' . I18N::translate('Delete selected messages') . '"><br>';
		}
		$content .= '</form>';

		if ($template) {
			if ($block) {
				$class .= ' small_inner_block';
			}
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return false;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			set_block_setting($block_id, 'block', Filter::postBool('block'));
		}

		$block = get_block_setting($block_id, 'block', '1');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
