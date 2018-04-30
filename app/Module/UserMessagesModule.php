<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserMessagesModule
 */
class UserMessagesModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Messages');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Messages” module */ I18N::translate('Communicate directly with other users, using private messages.');
	}

	/**
	 * Delete one or messages belonging to a user.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function postDeleteMessageAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$message_ids = (array) $request->get('message_id', []);

		$stmt = Database::prepare("DELETE FROM `##message` WHERE user_id = :user_id AND message_id = :message_id");

		foreach ($message_ids as $message_id) {
			$stmt->execute([
				'message_id' => $message_id,
				'user_id'    => Auth::id(),
			]);
		}

		if ($request->get('ctype') === 'user') {
			$url = route('user-page', ['ged' => $tree->getName()]);
		} else {
			$url = route('tree-page', ['ged' => $tree->getName()]);
		}

		return new RedirectResponse($url);
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = []): string {
		global $ctype, $WT_TREE;

		$messages = Database::prepare("SELECT message_id, sender, subject, body, UNIX_TIMESTAMP(created) AS created FROM `##message` WHERE user_id=? ORDER BY message_id DESC")
			->execute([Auth::id()])
			->fetchAll();

		$count = count($messages);
		$users = array_filter(User::all(), function (User $user) {
			return $user->getUserId() !== Auth::id() && $user->getPreference('verified_by_admin') && $user->getPreference('contactmethod') !== 'none';
		});

		$content = '';
		if (!empty($users)) {
			$url = route('user-page', ['ged' => $WT_TREE->getName()]);
			$content .= '<form onsubmit="return $(&quot;#to&quot;).val() !== &quot;&quot;">';
			$content .= '<input type="hidden" name="route" value="message">';
			$content .= '<input type="hidden" name="ged" value="' . e($WT_TREE->getName()) . '">';
			$content .= '<input type="hidden" name="url" value="' . e($url) . '">';
			$content .= '<label for="to">' . I18N::translate('Send a message') . '</label>';
			$content .= '<select id="to" name="to">';
			$content .= '<option value="">' . I18N::translate('&lt;select&gt;') . '</option>';
			foreach ($users as $user) {
				$content .= sprintf('<option value="%1$s">%2$s - %1$s</option>', e($user->getUserName()), e($user->getRealName()));
			}
			$content .= '</select>';
			$content .= '<button type="submit">' . I18N::translate('Send') . '</button><br><br>';
			$content .= '</form>';
		}
		$content .= '<form id="messageform" name="messageform" method="post" action="' . e(route('module', ['action' => 'DeleteMessage', 'module' => $this->getName(), 'ctype' => $ctype, 'ged' => $WT_TREE->getName()])) . '" data-confirm="' . I18N::translate('Are you sure you want to delete this message? It cannot be retrieved later.') . '" onsubmit="return confirm(this.dataset.confirm);">';
		$content .= csrf_field();

		if (!empty($messages)) {
			$content .= '<table class="list_table w-100"><tr>';
			$content .= '<th class="list_label">' . I18N::translate('Delete') . '<br><a href="#" onclick="$(\'#block-' . $block_id . ' :checkbox\').prop(\'checked\', true); return false;">' . I18N::translate('All') . '</a></th>';
			$content .= '<th class="list_label">' . I18N::translate('Subject') . '</th>';
			$content .= '<th class="list_label">' . I18N::translate('Date sent') . '</th>';
			$content .= '<th class="list_label">' . I18N::translate('Email address') . '</th>';
			$content .= '</tr>';
			foreach ($messages as $message) {
				$content .= '<tr>';
				$content .= '<td class="list_value_wrap center"><input type="checkbox" name="message_id[]" value="' . $message->message_id . '" id="cb_message' . $message->message_id . '"></td>';
				$content .= '<td class="list_value_wrap"><a href="#" onclick="return expand_layer(\'message' . $message->message_id . '\');"><i id="message' . $message->message_id . '_img" class="icon-plus"></i> <b dir="auto">' . e($message->subject) . '</b></a></td>';
				$content .= '<td class="list_value_wrap">' . FunctionsDate::formatTimestamp($message->created + WT_TIMESTAMP_OFFSET) . '</td>';
				$content .= '<td class="list_value_wrap">';
				$user = User::findByIdentifier($message->sender);
				if ($user) {
					$content .= '<span dir="auto">' . e($user->getRealName()) . '</span> - <span dir="auto">' . $user->getEmail() . '</span>';
				} else {
					$content .= '<a href="mailto:' . e($message->sender) . '">' . e($message->sender) . '</a>';
				}
				$content .= '</td>';
				$content .= '</tr>';
				$content .= '<tr><td class="list_value_wrap" colspan="4"><div id="message' . $message->message_id . '" style="display:none;">';
				$content .= '<div dir="auto" style="white-space: pre-wrap;">' . Filter::expandUrls($message->body, $WT_TREE) . '</div><br>';
				if (strpos($message->subject, /* I18N: When replying to an email, the subject becomes “RE: <subject>” */ I18N::translate('RE: ')) !== 0) {
					$message->subject = I18N::translate('RE: ') . $message->subject;
				}

				// If this user still exists, show a reply link.
				if ($user) {
					$reply_url = route('message', ['to' => $user->getUserName(), 'subject' => $message->subject, 'ged' => $WT_TREE->getName()]);
					$content .= '<a class="btn btn-primary" href="' . e($reply_url) . '" title="' . I18N::translate('Reply') . '">' . I18N::translate('Reply') . '</a> ';
				}
				$content .= '<button type="button" class="btn btn-danger" data-confirm="' . I18N::translate('Are you sure you want to delete this message? It cannot be retrieved later.') . '" onclick="if (confirm(this.dataset.confirm)) {$(\'#messageform :checkbox\').prop(\'checked\', false); $(\'#cb_message' . $message->message_id . '\').prop(\'checked\', true); document.messageform.submit();}">' . I18N::translate('Delete') . '</button></div></td></tr>';
			}
			$content .= '</table>';
			$content .= '<p><button type="submit">' . I18N::translate('Delete selected messages') . '</button></p>';
		}
		$content .= '</form>';

		if ($template) {
			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => '',
				'title'      => I18N::plural('%s message', '%s messages', $count, I18N::number($count)),
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock(): bool {
		return false;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id) {
	}
}
