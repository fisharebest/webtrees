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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class FamilyTreeNewsModule
 */
class FamilyTreeNewsModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “News” module */
        return I18N::translate('Family news and site announcements.');
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
        $articles = DB::table('news')
            ->where('gedcom_id', '=', $tree->id())
            ->orderByDesc('updated')
            ->get()
            ->map(static function (stdClass $row): stdClass {
                $row->updated = Carbon::make($row->updated);

                return $row;
            });

        $content = view('modules/gedcom_news/list', [
            'articles' => $articles,
            'block_id' => $block_id,
            'limit'    => 5,
        ]);

        if ($ctype !== '') {
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
        return I18N::translate('News');
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

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function getEditNewsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        if (!Auth::isManager($tree)) {
            throw new AccessDeniedHttpException();
        }

        $news_id = $request->get('news_id');

        if ($news_id > 0) {
            $row = DB::table('news')
                ->where('news_id', '=', $news_id)
                ->where('gedcom_id', '=', $tree->id())
                ->first();
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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function postEditNewsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        if (!Auth::isManager($tree)) {
            throw new AccessDeniedHttpException();
        }

        $news_id = $request->get('news_id');
        $subject = $request->get('subject');
        $body    = $request->get('body');

        if ($news_id > 0) {
            DB::table('news')
                ->where('news_id', '=', $news_id)
                ->where('gedcom_id', '=', $tree->id())
                ->update([
                    'body'    => $body,
                    'subject' => $subject,
                ]);
        } else {
            DB::table('news')->insert([
                'body'      => $body,
                'subject'   => $subject,
                'gedcom_id' => $tree->id(),
            ]);
        }

        $url = route('tree-page', [
            'ged' => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function postDeleteNewsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $news_id = $request->get('news_id');

        if (!Auth::isManager($tree)) {
            throw new AccessDeniedHttpException();
        }

        DB::table('news')
            ->where('news_id', '=', $news_id)
            ->where('gedcom_id', '=', $tree->id())
            ->delete();

        $url = route('tree-page', [
            'ged' => $tree->name(),
        ]);

        return redirect($url);
    }
}
