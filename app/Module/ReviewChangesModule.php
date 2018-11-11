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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ReviewChangesModule
 */
class ReviewChangesModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Pending changes');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Pending changes” module */
        return I18N::translate('A list of changes that need to be reviewed by a moderator, and email notifications.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param bool     $template
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, bool $template = true, array $cfg = []): string
    {
        global $ctype;

        $sendmail = $this->getBlockSetting($block_id, 'sendmail', '1');
        $days     = $this->getBlockSetting($block_id, 'days', '1');

        extract($cfg, EXTR_OVERWRITE);

        $changes = Database::prepare(
            "SELECT 1" .
            " FROM `##change`" .
            " WHERE status='pending'" .
            " LIMIT 1"
        )->fetchOne();

        if ($changes === '1' && $sendmail === '1') {
            // There are pending changes - tell moderators/managers/administrators about them.
            if (WT_TIMESTAMP - (int) Site::getPreference('LAST_CHANGE_EMAIL') > (60 * 60 * 24 * $days)) {
                // Which users have pending changes?
                foreach (User::all() as $user) {
                    if ($user->getPreference('contactmethod') !== 'none') {
                        foreach (Tree::getAll() as $tmp_tree) {
                            if ($tmp_tree->hasPendingEdit() && Auth::isManager($tmp_tree, $user)) {
                                I18N::init($user->getPreference('language'));

                                $sender = new User(
                                    (object) [
                                        'user_id'   => null,
                                        'user_name' => '',
                                        'real_name' => $tmp_tree->getTitle(),
                                        'email'     => $tmp_tree->getPreference('WEBTREES_EMAIL'),
                                    ]
                                );

                                Mail::send(
                                    $sender,
                                    $user,
                                    $sender,
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
                                I18N::init(WT_LOCALE);
                            }
                        }
                    }
                }
                Site::setPreference('LAST_CHANGE_EMAIL', (string) WT_TIMESTAMP);
            }
        }
        if (Auth::isEditor($tree) && $tree->hasPendingEdit()) {
            $content = '';
            if (Auth::isModerator($tree)) {
                $content .= '<a href="' . e(route('show-pending', ['ged' => $tree->name()])) . '">' . I18N::translate('There are pending changes for you to moderate.') . '</a><br>';
            }
            if ($sendmail === '1') {
                $last_email_timestamp = (int) Site::getPreference('LAST_CHANGE_EMAIL');
                $content .= I18N::translate('Last email reminder was sent ') . FunctionsDate::formatTimestamp($last_email_timestamp) . '<br>';
                $content .= I18N::translate('Next email reminder will be sent after ') . FunctionsDate::formatTimestamp($last_email_timestamp + 60 * 60 * 24 * $days) . '<br><br>';
            }
            $content .= '<ul>';
            $changes = Database::prepare(
                "SELECT xref" .
                " FROM  `##change`" .
                " WHERE status='pending'" .
                " AND   gedcom_id=?" .
                " GROUP BY xref"
            )->execute([$tree->id()])->fetchAll();
            foreach ($changes as $change) {
                $record = GedcomRecord::getInstance($change->xref, $tree);
                if ($record->canShow()) {
                    $content .= '<li><a href="' . e($record->url()) . '">' . $record->getFullName() . '</a></li>';
                }
            }
            $content .= '</ul>';

            if ($template) {
                if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                    $config_url = route('tree-page-block-edit', [
                        'block_id' => $block_id,
                        'ged'      => $tree->name(),
                    ]);
                } elseif ($ctype === 'user' && Auth::check()) {
                    $config_url = route('user-page-block-edit', [
                        'block_id' => $block_id,
                        'ged'      => $tree->name(),
                    ]);
                } else {
                    $config_url = '';
                }

                return view('modules/block-template', [
                    'block'      => str_replace('_', '-', $this->getName()),
                    'id'         => $block_id,
                    'config_url' => $config_url,
                    'title'      => $this->getTitle(),
                    'content'    => $content,
                ]);
            }

            return $content;
        }

        return '';
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
    public function isGedcomBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
    {
        $this->setBlockSetting($block_id, 'days', $request->get('num', '1'));
        $this->setBlockSetting($block_id, 'sendmail', $request->get('sendmail', ''));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id)
    {
        $sendmail = $this->getBlockSetting($block_id, 'sendmail', '1');
        $days     = $this->getBlockSetting($block_id, 'days', '1');

        echo view('modules/review_changes/config', [
            'days'     => $days,
            'sendmail' => $sendmail,
        ]);
    }
}
