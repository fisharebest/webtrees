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
use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function assert;

/**
 * Class UserJournalModule
 */
class UserJournalModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /** @var HtmlService */
    private $html_service;

    /**
     * HtmlBlockModule constructor.
     *
     * @param HtmlService $html_service
     */
    public function __construct(HtmlService $html_service)
    {
        $this->html_service = $html_service;
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Journal” module */
        return I18N::translate('A private area to record notes or keep a journal.');
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
        $articles = DB::table('news')
            ->where('user_id', '=', Auth::id())
            ->orderByDesc('updated')
            ->get()
            ->map(static function (stdClass $row): stdClass {
                $row->updated = Carbon::make($row->updated);

                return $row;
            });

        $content = view('modules/user_blog/list', [
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

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Journal');
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

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getEditJournalAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        if (!Auth::check()) {
            throw new HttpAccessDeniedException();
        }

        $news_id = $request->getQueryParams()['news_id'] ?? '';

        if ($news_id !== '') {
            $row = DB::table('news')
                ->where('news_id', '=', $news_id)
                ->where('user_id', '=', Auth::id())
                ->first();
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
            'tree'    => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postEditJournalAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        if (!Auth::check()) {
            throw new HttpAccessDeniedException();
        }

        $params = (array) $request->getParsedBody();

        $news_id = $request->getQueryParams()['news_id'] ?? '';
        $subject = $params['subject'];
        $body    = $params['body'];

        $subject = $this->html_service->sanitize($subject);
        $body    = $this->html_service->sanitize($body);

        if ($news_id !== '') {
            DB::table('news')
                ->where('news_id', '=', $news_id)
                ->where('user_id', '=', Auth::id())
                ->update([
                    'body'    => $body,
                    'subject' => $subject,
                    'updated' => new Expression('updated'), // See issue #3208
                ]);
        } else {
            DB::table('news')->insert([
                'body'    => $body,
                'subject' => $subject,
                'user_id' => Auth::id(),
            ]);
        }

        $url = route(UserPage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postDeleteJournalAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $news_id = $request->getQueryParams()['news_id'];

        DB::table('news')
            ->where('news_id', '=', $news_id)
            ->where('user_id', '=', Auth::id())
            ->delete();

        $url = route(UserPage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}
