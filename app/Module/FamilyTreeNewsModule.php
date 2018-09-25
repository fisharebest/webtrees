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
 * Class FamilyTreeNewsModule
 */
class FamilyTreeNewsModule extends AbstractModule implements ModuleBlockInterface
{
    // How to update the database schema for this module
    const SCHEMA_TARGET_VERSION   = 3;
    const SCHEMA_SETTING_NAME     = 'NB_SCHEMA_VERSION';
    const SCHEMA_MIGRATION_PREFIX = '\Fisharebest\Webtrees\Module\FamilyTreeNews\Schema';

    /**
     * Create a new module.
     *
     * @param string $directory Where is this module installed
     */
    public function __construct($directory)
    {
        parent::__construct($directory);

        // Create/update the database tables.
        Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);
    }

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('News');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “News” module */
        return I18N::translate('Family news and site announcements.');
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
            "SELECT news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) + :offset AS updated, subject, body FROM `##news` WHERE gedcom_id = :tree_id ORDER BY updated DESC"
        )->execute([
            'offset'  => WT_TIMESTAMP_OFFSET,
            'tree_id' => $tree->getTreeId(),
        ])->fetchAll();

        $content = view('modules/gedcom_news/list', [
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
        return false;
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
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getEditNewsAction(Request $request, Tree $tree): Response
    {
        if (!Auth::isManager($tree)) {
            throw new AccessDeniedHttpException();
        }

        $news_id = $request->get('news_id');

        if ($news_id > 0) {
            $row = Database::prepare(
                "SELECT subject, body FROM `##news` WHERE news_id = :news_id AND gedcom_id = :tree_id"
            )->execute([
                'news_id' => $news_id,
                'tree_id' => $tree->getTreeId(),
            ])->fetchOneRow();
        } else {
            $row = (object) [
                'body'    => '',
                'subject' => '',
            ];
        }

        $title = I18N::translate('Add/edit a journal/news entry');

        return $this->viewResponse('modules/gedcom_news/edit', [
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
    public function postEditNewsAction(Request $request, Tree $tree): RedirectResponse
    {
        if (!Auth::isManager($tree)) {
            throw new AccessDeniedHttpException();
        }

        $news_id = $request->get('news_id');
        $subject = $request->get('subject');
        $body    = $request->get('body');

        if ($news_id > 0) {
            Database::prepare(
                "UPDATE `##news` SET subject = :subject, body = :body, updated = CURRENT_TIMESTAMP" .
                " WHERE news_id = :news_id AND gedcom_id = :tree_id"
            )->execute([
                'subject' => $subject,
                'body'    => $body,
                'news_id' => $news_id,
                'tree_id' => $tree->getTreeId(),
            ]);
        } else {
            Database::prepare(
                "INSERT INTO `##news` (gedcom_id, subject, body, updated) VALUES (:tree_id, :subject ,:body, CURRENT_TIMESTAMP)"
            )->execute([
                'body'    => $body,
                'subject' => $subject,
                'tree_id' => $tree->getTreeId(),
            ]);
        }

        $url = route('tree-page', [
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
    public function postDeleteNewsAction(Request $request, Tree $tree): RedirectResponse
    {
        $news_id = $request->get('news_id');

        if (!Auth::isManager($tree)) {
            throw new AccessDeniedHttpException();
        }

        Database::prepare(
            "DELETE FROM `##news` WHERE news_id = :news_id AND gedcom_id = :tree_id"
        )->execute([
            'news_id' => $news_id,
            'tree_id' => $tree->getTreeId(),
        ]);

        $url = route('tree-page', [
            'ged' => $tree->getName(),
        ]);

        return new RedirectResponse($url);
    }
}
