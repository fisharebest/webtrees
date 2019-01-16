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

namespace Fisharebest\Webtrees\Http\Middleware;

use Closure;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\View;
use Illuminate\Database\Capsule\Manager as DB;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware to count requests for particular pages.
 * For historical reasons, we record the names of the original webtrees script and parameter.
 */
class PageHitCounter implements MiddlewareInterface
{
    // Which pages/routes do we count?
    private const PAGE_NAMES = [
        'family'     => 'family.php',
        'individual' => 'individual.php',
        'media'      => 'mediaviewer.php',
        'note'       => 'note.php',
        'repository' => 'repo.php',
        'source'     => 'source.php',
        'tree-page'  => 'index.php',
        'user-page'  => 'index.php',
    ];

    /** @var Tree|null */
    private $tree;

    /** @var User */
    private $user;

    /**
     * PageHitCounter constructor.
     *
     * @param User  $user
     * @param ?Tree $tree
     */
    public function __construct(User $user, ?Tree $tree)
    {
        $this->tree = $tree;
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        $page_hits = 0;

        if ($this->tree !== null && $this->tree->getPreference('SHOW_COUNTER')) {
            $route = $request->get('route');

            $page_name = self::PAGE_NAMES[$route] ?? '';

            switch ($route) {
                case 'family':
                case 'individual':
                case 'media':
                case 'note':
                case 'repository':
                case 'source':
                    $page_hits = $this->countHit($this->tree, $page_name, $request->get('xref', ''));
                    break;

                case 'tree-page':
                    $page_hits = $this->countHit($this->tree, $page_name, 'gedcom:' . $this->tree->id());
                    break;

                case 'user-page':
                    $page_hits = $this->countHit($this->tree, $page_name, 'user:' . $this->user->getUserId());
                    break;
            }
        }

        // Make the count available to the layout.
        View::share('page_hits', $page_hits);

        return $next($request);
    }

    /**
     * Increment the page count.
     *
     * @param Tree   $tree
     * @param string $page
     * @param string $parameter
     *
     * @return int
     */
    private function countHit(Tree $tree, $page, $parameter): int
    {
        $gedcom_id = $tree->id();

        // Don't increment the counter while we stay on the same page.
        if (
            Session::get('last_gedcom_id') === $gedcom_id &&
            Session::get('last_page_name') === $page &&
            Session::get('last_page_parameter') === $parameter
        ) {
            return Session::get('last_count');
        }

        $count = $this->getCount($tree, $page, $parameter);

        $count++;

        DB::table('hit_counter')->updateOrInsert([
            'gedcom_id'      => $tree->id(),
            'page_name'      => $page,
            'page_parameter' => $parameter,
        ], [
            'page_count' => $count,
        ]);

        Session::put('last_gedcom_id', $gedcom_id);
        Session::put('last_page_name', $page);
        Session::put('last_page_parameter', $parameter);
        Session::put('last_count', $count);

        return $count;
    }

    /**
     * How many times has a page been viewed
     *
     * @param Tree   $tree
     * @param string $page
     * @param string $parameter
     *
     * @return int
     */
    public function getCount(Tree $tree, $page, $parameter): int
    {
        return (int) DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->where('page_name', '=', $page)
            ->where('page_parameter', '=', $parameter)
            ->value('page_count');
    }
}
