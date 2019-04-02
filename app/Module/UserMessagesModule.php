<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

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
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function postDeleteMessageAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $message_ids = (array) $request->get('message_id', []);

        DB::table('message')
            ->where('user_id', '=', Auth::id())
            ->whereIn('message_id', $message_ids)
            ->delete();

        if ($request->get('ctype') === 'user') {
            $url = route('user-page', ['ged' => $tree->name()]);
        } else {
            $url = route('tree-page', ['ged' => $tree->name()]);
        }

        return redirect($url);
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $ctype
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $ctype = '', array $cfg = []): string
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
                $user->getPreference('verified_by_admin') &&
                $can_see_tree &&
                $user->getPreference('contactmethod') !== 'none';
        });

        $content = '';
        if ($users->isNotEmpty()) {
            $url = route('user-page', ['ged' => $tree->name()]);

            $content .= '<form onsubmit="return $(&quot;#to&quot;).val() !== &quot;&quot;">';
            $content .= '<input type="hidden" name="route" value="message">';
            $content .= '<input type="hidden" name="ged" value="' . e($tree->name()) . '">';
            $content .= '<input type="hidden" name="url" value="' . e($url) . '">';
            $content .= '<label for="to">' . I18N::translate('Send a message') . '</label>';
            $content .= '<select id="to" name="to">';
            $content .= '<option value="">' . I18N::translate('&lt;select&gt;') . '</option>';
            foreach ($users as $user) {
                $content .= sprintf('<option value="%1$s">%2$s - %1$s</option>', e($user->userName()), e($user->realName()));
            }
            $content .= '</select>';
            $content .= '<button type="submit">' . I18N::translate('Send') . '</button><br><br>';
            $content .= '</form>';
        }
        $content .= '<form id="messageform" name="messageform" method="post" action="' . e(route('module', [
                'action' => 'DeleteMessage',
                'module' => $this->name(),
                'ctype'  => $ctype,
                'ged'    => $tree->name(),
            ])) . '" data-confirm="' . I18N::translate('Are you sure you want to delete this message? It cannot be retrieved later.') . '" onsubmit="return confirm(this.dataset.confirm);">';
        $content .= csrf_field();

        if ($messages->isNotEmpty()) {
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
                $content .= '<td class="list_value_wrap">' . view('components/datetime', ['timestamp' => $message->created]) . '</td>';
                $content .= '<td class="list_value_wrap">';

                $user = $this->user_service->findByIdentifier($message->sender);

                if ($user instanceof User) {
                    $content .= '<span dir="auto">' . e($user->realName()) . '</span> - <span dir="auto">' . $user->email() . '</span>';
                } else {
                    $content .= '<a href="mailto:' . e($message->sender) . '">' . e($message->sender) . '</a>';
                }

                $content .= '</td>';
                $content .= '</tr>';
                $content .= '<tr><td class="list_value_wrap" colspan="4"><div id="message' . $message->message_id . '" style="display:none;">';
                $content .= '<div dir="auto" style="white-space: pre-wrap;">' . Filter::expandUrls($message->body, $tree) . '</div><br>';

                /* I18N: When replying to an email, the subject becomes “RE: <subject>” */
                if (strpos($message->subject, I18N::translate('RE: ')) !== 0) {
                    $message->subject = I18N::translate('RE: ') . $message->subject;
                }

                // If this user still exists, show a reply link.
                if ($user) {
                    $reply_url = route('message', [
                        'to'      => $user->userName(),
                        'subject' => $message->subject,
                        'ged'     => $tree->name(),
                    ]);

                    $content .= '<a class="btn btn-primary" href="' . e($reply_url) . '" title="' . I18N::translate('Reply') . '">' . I18N::translate('Reply') . '</a> ';
                }
                $content .= '<button type="button" class="btn btn-danger" data-confirm="' . I18N::translate('Are you sure you want to delete this message? It cannot be retrieved later.') . '" onclick="if (confirm(this.dataset.confirm)) {$(\'#messageform :checkbox\').prop(\'checked\', false); $(\'#cb_message' . $message->message_id . '\').prop(\'checked\', true); document.messageform.submit();}">' . I18N::translate('Delete') . '</button></div></td></tr>';
            }
            $content .= '</table>';
            $content .= '<p><button type="submit">' . I18N::translate('Delete selected messages') . '</button></p>';
        }
        $content .= '</form>';

        if ($ctype !== '') {
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

    /** {@inheritdoc} */
    public function loadAjax(): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function isUserBlock(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isTreeBlock(): bool
    {
        return false;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): void
    {
    }
}
