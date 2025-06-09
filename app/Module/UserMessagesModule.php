<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function route;
use function view;

class UserMessagesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private UserService $user_service;

    /**
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Messages');
    }

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
        $tree        = Validator::attributes($request)->tree();
        $context     = Validator::queryParams($request)->string('context');
        $message_ids = Validator::parsedBody($request)->array('message_id');

        DB::table('message')
            ->where('user_id', '=', Auth::id())
            ->whereIn('message_id', $message_ids)
            ->delete();

        if ($context === ModuleBlockInterface::CONTEXT_USER_PAGE) {
            $url = route(UserPage::class, ['tree' => $tree->name()]);
        } else {
            $url = route(TreePage::class, ['tree' => $tree->name()]);
        }

        return redirect($url);
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $messages = DB::table('message')
            ->where('user_id', '=', Auth::id())
            ->orderByDesc('message_id')
            ->get()
            ->map(static function (object $row): object {
                $row->created = Registry::timestampFactory()->fromString($row->created);

                return $row;
            });

        $users = $this->user_service->all()->filter(static function (UserInterface $user) use ($tree): bool {
            $public_tree  = $tree->getPreference('REQUIRE_AUTHENTICATION') !== '1';
            $can_see_tree = $public_tree || Auth::accessLevel($tree, $user) <= Auth::PRIV_USER;

            return
                $user->id() !== Auth::id() &&
                $user->getPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED) &&
                $can_see_tree &&
                $user->getPreference(UserInterface::PREF_CONTACT_METHOD) !== MessageService::CONTACT_METHOD_NONE;
        });

        $content = view('modules/user-messages/user-messages', [
            'block_id'     => $block_id,
            'context'      => $context,
            'messages'     => $messages,
            'module'       => $this,
            'tree'         => $tree,
            'user_service' => $this->user_service,
            'users'        => $users,
        ]);

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
