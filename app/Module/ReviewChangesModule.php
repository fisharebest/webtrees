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
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChanges;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ReviewChangesModule
 */
class ReviewChangesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /** @var EmailService */
    private $email_service;

    /** @var UserService */
    private $user_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * ReviewChangesModule constructor.
     *
     * @param EmailService $email_service
     * @param TreeService  $tree_service
     * @param UserService  $user_service
     */
    public function __construct(
        EmailService $email_service,
        TreeService $tree_service,
        UserService $user_service
    ) {
        $this->email_service = $email_service;
        $this->tree_service  = $tree_service;
        $this->user_service  = $user_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Pending changes');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Pending changes” module */
        return I18N::translate('A list of changes that need to be reviewed by a moderator, and email notifications.');
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
        $old_language = I18N::languageTag();

        $sendmail = (bool) $this->getBlockSetting($block_id, 'sendmail', '1');
        $days     = (int) $this->getBlockSetting($block_id, 'days', '1');

        extract($config, EXTR_OVERWRITE);

        $changes_exist = DB::table('change')
            ->where('status', 'pending')
            ->exists();

        if ($changes_exist && $sendmail) {
            $last_email_timestamp = Carbon::createFromTimestamp((int) Site::getPreference('LAST_CHANGE_EMAIL'));
            $next_email_timestamp = $last_email_timestamp->addDays($days);

            // There are pending changes - tell moderators/managers/administrators about them.
            if ($next_email_timestamp < Carbon::now()) {
                // Which users have pending changes?
                foreach ($this->user_service->all() as $user) {
                    if ($user->getPreference(User::PREF_CONTACT_METHOD) !== 'none') {
                        foreach ($this->tree_service->all() as $tmp_tree) {
                            if ($tmp_tree->hasPendingEdit() && Auth::isManager($tmp_tree, $user)) {
                                I18N::init($user->getPreference(User::PREF_LANGUAGE));

                                $this->email_service->send(
                                    new SiteUser(),
                                    $user,
                                    new TreeUser($tmp_tree),
                                    I18N::translate('Pending changes'),
                                    view('emails/pending-changes-text', [
                                        'tree' => $tmp_tree,
                                        'user' => $user,
                                    ]),
                                    view('emails/pending-changes-html', [
                                        'tree' => $tmp_tree,
                                        'user' => $user,
                                    ])
                                );
                            }
                        }
                    }
                }
                I18N::init($old_language);
                Site::setPreference('LAST_CHANGE_EMAIL', (string) Carbon::now()->unix());
            }
        }
        if (Auth::isEditor($tree) && $tree->hasPendingEdit()) {
            $content = '';
            if (Auth::isModerator($tree)) {
                $content .= '<a href="' . e(route(PendingChanges::class, ['tree' => $tree->name()])) . '">' . I18N::translate('There are pending changes for you to moderate.') . '</a><br>';
            }
            if ($sendmail) {
                $last_email_timestamp = Carbon::createFromTimestamp((int) Site::getPreference('LAST_CHANGE_EMAIL'));
                $next_email_timestamp = $last_email_timestamp->copy()->addDays($days);

                $content .= I18N::translate('Last email reminder was sent ') . view('components/datetime', ['timestamp' => $last_email_timestamp]) . '<br>';
                $content .= I18N::translate('Next email reminder will be sent after ') . view('components/datetime', ['timestamp' => $next_email_timestamp]) . '<br><br>';
            }
            $content .= '<ul>';

            $changes = DB::table('change')
                ->where('gedcom_id', '=', $tree->id())
                ->where('status', '=', 'pending')
                ->select(['xref'])
                ->get();

            foreach ($changes as $change) {
                $record = Factory::gedcomRecord()->make($change->xref, $tree);
                if ($record->canShow()) {
                    $content .= '<li><a href="' . e($record->url()) . '">' . $record->fullName() . '</a></li>';
                }
            }
            $content .= '</ul>';

            if ($context !== self::CONTEXT_EMBED) {
                return view('modules/block-template', [
                    'block'      => Str::kebab($this->name()),
                    'id'         => $block_id,
                    'config_url' => $this->configUrl($tree, $context, $block_id),
                    'title'      => $this->title(),
                    'content'    => $content,
                ]);
            }

            return $content;
        }

        return '';
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
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $params = (array) $request->getParsedBody();

        $this->setBlockSetting($block_id, 'days', $params['days']);
        $this->setBlockSetting($block_id, 'sendmail', $params['sendmail']);
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        $sendmail = $this->getBlockSetting($block_id, 'sendmail', '1');
        $days     = $this->getBlockSetting($block_id, 'days', '1');

        return view('modules/review_changes/config', [
            'days'     => $days,
            'sendmail' => $sendmail,
        ]);
    }
}
