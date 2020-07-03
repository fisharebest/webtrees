<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Http\RequestHandlers\MessagePage;
use Fisharebest\Webtrees\Http\RequestHandlers\MessageSelect;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function assert;
use function e;
use function route;
use function str_starts_with;
use function view;

/**
 * Class UserMessagesModule
 */
class UserMessagesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * UserMessagesModule constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Messages');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Messages” module */
        return I18N::translate('Communicate directly with other users, using private messages.');
    }

    /**
     * Delete one or messages belonging to a user.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postDeleteMessageAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = (array) $request->getParsedBody();

        $message_ids = $params['message_id'] ?? [];

        DB::table('message')
            ->where('user_id', '=', Auth::id())
            ->whereIn('message_id', $message_ids)
            ->delete();

        if ($request->getQueryParams()['context'] === ModuleBlockInterface::CONTEXT_USER_PAGE) {
            $url = route(UserPage::class, ['tree' => $tree->name()]);
        } else {
            $url = route(TreePage::class, ['tree' => $tree->name()]);
        }

        return redirect($url);
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $context
     * @param string[] $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $messages = DB::table('message')
            ->where('user_id', '=', Auth::id())
            ->orderByDesc('message_id')
            ->get()
            ->map(static function (stdClass $row): stdClass {
                $row->created = Carbon::make($row->created);

                return $row;
            });

        $users = $this->user_service->all()->filter(static function (UserInterface $user) use ($tree): bool {
            $public_tree  = $tree->getPreference('REQUIRE_AUTHENTICATION') !== '1';
            $can_see_tree = $public_tree || Auth::accessLevel($tree, $user) <= Auth::PRIV_USER;

            return
                $user->id() !== Auth::id() &&
                $user->getPreference(User::PREF_IS_ACCOUNT_APPROVED) &&
                $can_see_tree &&
                $user->getPreference(User::PREF_CONTACT_METHOD) !== 'none';
        });

        $content = '';
        if ($users->isNotEmpty()) {
            $url = route(UserPage::class, ['tree' => $tree->name()]);

            $content .= '<form method="post" action="' . e(route(MessageSelect::class, ['tree' => $tree->name()])) . '">';
            $content .= csrf_field();
            $content .= '<input type="hidden" name="url" value="' . e($url) . '">';
            $content .= '<label for="to">' . I18N::translate('Send a message') . '</label>';
            $content .= '<select id="to" name="to" required>';
            $content .= '<option value="">' . I18N::translate('&lt;select&gt;') . '</option>';
            foreach ($users as $user) {
                $content .= sprintf('<option value="%1$s">%2$s - %1$s</option>', e($user->userName()), e($user->realName()));
            }
            $content .= '</select>';
            $content .= '<button type="submit">' . I18N::translate('Send') . '</button><br><br>';
            $content .= '</form>';
        }
        $content .= '<form method="post" action="' . e(route('module', [
                'action'  => 'DeleteMessage',
                'module'  => $this->name(),
                'context' => $context,
                'tree'     => $tree->name(),
            ])) . '" data-confirm="' . I18N::translate('Are you sure you want to delete this message? It cannot be retrieved later.') . '" onsubmit="return confirm(this.dataset.confirm);" id="messageform" name="messageform">';
        $content .= csrf_field();

        if ($messages->isNotEmpty()) {
            $content .= '<div class="table-responsive">';
            $content .= '<table class="table table-sm w-100"><tr>';
            $content .= '<th class="list_label">' . I18N::translate('Delete') . '<br><a href="#" onclick="$(\'#block-' . $block_id . ' :checkbox\').prop(\'checked\', true); return false;">' . I18N::translate('All') . '</a></th>';
            $content .= '<th class="list_label">' . I18N::translate('Subject') . '</th>';
            $content .= '<th class="list_label">' . I18N::translate('Date sent') . '</th>';
            $content .= '<th class="list_label">' . I18N::translate('Email address') . '</th>';
            $content .= '</tr>';
            foreach ($messages as $message) {
                $content .= '<tr>' .
                    '<td class="list_value_wrap center"><input type="checkbox" name="message_id[]" value="' . $message->message_id . '" id="cb_message' . $message->message_id . '"></td>' .
                    '<td class="list_value_wrap">' .
                    '<a href="#message' . $message->message_id . '" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="message' . $message->message_id . '">' .
                    view('icons/expand') .
                    view('icons/collapse') .
                    '<b dir="auto">' . e($message->subject) . '</b>' .
                    '</a></td>' .
                    '<td class="list_value_wrap">' . view('components/datetime', ['timestamp' => $message->created]) . '</td>' .
                    '<td class="list_value_wrap">';

                $user = $this->user_service->findByIdentifier($message->sender);

                if ($user instanceof User) {
                    $content .= '<span dir="auto">' . e($user->realName()) . '</span> - <span dir="auto">' . $user->email() . '</span>';
                } else {
                    $content .= '<a href="mailto:' . e($message->sender) . '">' . e($message->sender) . '</a>';
                }

                $content .= '</td>';
                $content .= '</tr>';
                $content .= '<tr><td class="list_value_wrap" colspan="4"><div id="message' . $message->message_id . '" class="collapse">';
                $content .= '<div dir="auto" style="white-space: pre-wrap;">' . Filter::expandUrls($message->body, $tree) . '</div><br>';

                /* I18N: When replying to an email, the subject becomes “RE: <subject>” */
                if (!str_starts_with($message->subject, I18N::translate('RE: '))) {
                    $message->subject = I18N::translate('RE: ') . $message->subject;
                }

                // If this user still exists, show a reply link.
                if ($user instanceof User) {
                    $reply_url = route(MessagePage::class, [
                        'subject' => $message->subject,
                        'to'      => $user->userName(),
                        'tree'    => $tree->name(),
                        'url'     => route(UserPage::class, ['tree' => $tree->name()]),
                    ]);

                    $content .= '<a class="btn btn-primary" href="' . e($reply_url) . '" title="' . I18N::translate('Reply') . '">' . I18N::translate('Reply') . '</a> ';
                }
                $content .= '<button type="button" class="btn btn-danger" data-confirm="' . I18N::translate('Are you sure you want to delete this message? It cannot be retrieved later.') . '" onclick="if (confirm(this.dataset.confirm)) {$(\'#messageform :checkbox\').prop(\'checked\', false); $(\'#cb_message' . $message->message_id . '\').prop(\'checked\', true); document.messageform.submit();}">' . I18N::translate('Delete') . '</button></div></td></tr>';
            }
            $content .= '</table>';
            $content .= '</div>';
            $content .= '<p><button type="submit">' . I18N::translate('Delete selected messages') . '</button></p>';
        }
        $content .= '</form>';

        if ($context !== self::CONTEXT_EMBED) {
            $count = $messages->count();

            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => I18N::plural('%s message', '%s messages', $count, I18N::number($count)),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
        return false;
    }

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isTreeBlock(): bool
    {
        return false;
    }
}
