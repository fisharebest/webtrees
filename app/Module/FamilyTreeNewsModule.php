<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;

class FamilyTreeNewsModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private HtmlService $html_service;

    public function __construct(HtmlService $html_service)
    {
        $this->html_service = $html_service;
    }

    public function description(): string
    {
        /* I18N: Description of the “News” module */
        return I18N::translate('Family news and site announcements.');
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
        $articles = DB::table('news')
            ->where('gedcom_id', '=', $tree->id())
            ->orderByDesc('updated')
            ->get()
            ->map(static function (object $row): object {
                $row->updated = Registry::timestampFactory()->fromString($row->updated);

                return $row;
            });

        $content = view('modules/gedcom_news/list', [
            'articles' => $articles,
            'block_id' => $block_id,
            'limit'    => 5,
            'tree'     => $tree,
        ]);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $this->title(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('News');
    }

    public function loadAjax(): bool
    {
        return false;
    }

    public function isUserBlock(): bool
    {
        return false;
    }

    public function isTreeBlock(): bool
    {
        return true;
    }

    public function getEditNewsAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        if (!Auth::isManager($tree)) {
            throw new HttpAccessDeniedException();
        }

        $news_id = Validator::queryParams($request)->integer('news_id', 0);

        $timezone = new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC'));
        $utc      = new DateTimeZone('UTC');

        if ($news_id !== 0) {
            $row = DB::table('news')
                ->where('news_id', '=', $news_id)
                ->where('gedcom_id', '=', $tree->id())
                ->first();

            // Record was deleted before we could read it?
            if ($row === null) {
                throw new HttpNotFoundException(I18N::translate('%s does not exist.', 'news_id:' . $news_id));
            }

            $body    = $row->body;
            $subject = $row->subject;
            $updated = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row->updated, $utc)
                ->setTimezone($timezone);
        } else {
            $body    = '';
            $subject = '';
            $updated = Registry::timestampFactory()->now(Auth::user());
        }

        return $this->viewResponse('modules/gedcom_news/edit', [
            'body'    => $body,
            'news_id' => $news_id,
            'subject' => $subject,
            'title'   => $this->title(),
            'tree'    => $tree,
            'updated' => $updated->format('Y-m-d H:i:s'),
        ]);
    }

    public function postEditNewsAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        if (!Auth::isManager($tree)) {
            throw new HttpAccessDeniedException();
        }

        $news_id = Validator::queryParams($request)->integer('news_id', 0);
        $subject = Validator::parsedBody($request)->string('subject');
        $body    = Validator::parsedBody($request)->string('body');
        $now     = Registry::timestampFactory()->now();

        $subject = $this->html_service->sanitize($subject);
        $body    = $this->html_service->sanitize($body);

        if ($news_id !== 0) {
            $use_current_timestamp = Validator::parsedBody($request)->boolean('use-current-timestamp', false);

            if ($use_current_timestamp) {
                $updated = $now;
            } else {
                $timestamp = Validator::parsedBody($request)->string('timestamp');
                $timezone  = new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC'));
                $utc       = new DateTimeZone('UTC');
                $updated   = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:s', $timestamp, $timezone)
                    ->setTimezone($utc);
            }

            DB::table('news')
                ->where('news_id', '=', $news_id)
                ->where('gedcom_id', '=', $tree->id()) // Check this is our own tree - validates news_id
                ->update([
                    'body'    => $body,
                    'subject' => $subject,
                    'updated' => $updated,
                ]);
        } else {
            DB::table('news')->insert([
                'body'      => $body,
                'subject'   => $subject,
                'gedcom_id' => $tree->id(),
                'updated'   => $now,
            ]);
        }

        $url = route(TreePage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }

    public function postDeleteNewsAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = Validator::attributes($request)->tree();
        $news_id = Validator::queryParams($request)->integer('news_id');

        if (!Auth::isManager($tree)) {
            throw new HttpAccessDeniedException();
        }

        DB::table('news')
            ->where('news_id', '=', $news_id)
            ->where('gedcom_id', '=', $tree->id())
            ->delete();

        $url = route(TreePage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}
