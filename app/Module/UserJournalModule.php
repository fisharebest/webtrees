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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class UserJournalModule
 */
class UserJournalModule extends AbstractModule implements ModuleBlockInterface
{
    /**
     * Create a new module.
     *
     * @param string $directory Where is this module installed
     */
    public function __construct($directory)
    {
        parent::__construct($directory);

        // Create/update the database tables.
        Database::updateSchema('\Fisharebest\Webtrees\Module\FamilyTreeNews\Schema', 'NB_SCHEMA_VERSION', 3);
    }

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Journal');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “Journal” module */
        return I18N::translate('A private area to record notes or keep a journal.');
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
        $articles = Database::prepare(
            "SELECT news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) + :offset AS updated, subject, body FROM `##news` WHERE user_id = :user_id ORDER BY updated DESC"
        )->execute([
            'offset'  => WT_TIMESTAMP_OFFSET,
            'user_id' => Auth::id(),
        ])->fetchAll();

        $content = view('modules/user_blog/list', [
            'articles' => $articles,
            'block_id' => $block_id,
            'limit'    => 5,
        ]);

        if ($template) {
            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->getName()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $this->getTitle(),
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
    public function isGedcomBlock(): bool
    {
        return false;
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
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getEditJournalAction(Request $request): Response
    {
        if (!Auth::check()) {
            throw new AccessDeniedHttpException();
        }

        $news_id = $request->get('news_id');

        if ($news_id > 0) {
            $row = Database::prepare(
                "SELECT subject, body FROM `##news` WHERE news_id = :news_id AND user_id = :user_id"
            )->execute([
                'news_id' => $news_id,
                'user_id' => Auth::id(),
            ])->fetchOneRow();
        } else {
            $row = (object) [
                'body'    => '',
                'subject' => '',
            ];
        }

        $title = I18N::translate('Add/edit a journal/news entry');

        return $this->viewResponse('modules/user_blog/edit', [
            'body'    => $row->body,
            'news_id' => $news_id,
            'subject' => $row->subject,
            'title'   => $title,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postEditJournalAction(Request $request, Tree $tree): RedirectResponse
    {
        if (!Auth::check()) {
            throw new AccessDeniedHttpException();
        }

        $news_id = $request->get('news_id');
        $subject = $request->get('subject');
        $body    = $request->get('body');

        if ($news_id > 0) {
            Database::prepare(
                "UPDATE `##news` SET subject = :subject, body = :body, updated = CURRENT_TIMESTAMP" .
                " WHERE news_id = :news_id AND user_id = :user_id"
            )->execute([
                'subject' => $subject,
                'body'    => $body,
                'news_id' => $news_id,
                'user_id' => Auth::id(),
            ]);
        } else {
            Database::prepare(
                "INSERT INTO `##news` (user_id, subject, body, updated) VALUES (:user_id, :subject ,:body, CURRENT_TIMESTAMP)"
            )->execute([
                'body'    => $body,
                'subject' => $subject,
                'user_id' => Auth::id(),
            ]);
        }

        $url = route('user-page', [
            'ged' => $tree->getName(),
        ]);

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postDeleteJournalAction(Request $request, Tree $tree): RedirectResponse
    {
        $news_id = $request->get('news_id');

        if (!Auth::check()) {
            throw new AccessDeniedHttpException();
        }

        Database::prepare(
            "DELETE FROM `##news` WHERE news_id = :news_id AND user_id = :user_id"
        )->execute([
            'news_id' => $news_id,
            'user_id' => Auth::id(),
        ]);

        $url = route('user-page', [
            'ged' => $tree->getName(),
        ]);

        return new RedirectResponse($url);
    }
}
