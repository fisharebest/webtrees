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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class HitCountFooterModule - show the number of page hits in the footer.
 */
class HitCountFooterModule extends AbstractModule implements ModuleFooterInterface, MiddlewareInterface
{
    use ModuleFooterTrait;

    // Which pages/routes do we count?
    // For historical reasons, we record the names of the original webtrees script and parameter.
    protected const PAGE_NAMES = [
        'family'     => 'family.php',
        'individual' => 'individual.php',
        'media'      => 'mediaviewer.php',
        'note'       => 'note.php',
        'repository' => 'repo.php',
        'source'     => 'source.php',
        'tree-page'  => 'index.php',
        'user-page'  => 'index.php',
    ];

    /** @var int Count of visits to the current page */
    protected $page_hits = 0;

    /**
     * How should this module be labelled on tabs, footers, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Hit counters');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Hit counters” module */
        return I18N::translate('Count the visits to each page');
    }

    /**
     * The default position for this footer.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultFooterOrder(): int
    {
        return 3;
    }

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @param Tree|null $tree
     *
     * @return string
     */
    public function getFooter(?Tree $tree): string
    {
        if ($this->page_hits === 0) {
            return '';
        }

        $digits = '<span class="odometer">' . I18N::digits($this->page_hits) . '</span>';

        return view('modules/hit-counter/footer', [
            'hit_counter' => I18N::plural('This page has been viewed %s time.', 'This page has been viewed %s times.', $this->page_hits, $digits),
        ]);
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tree = app(Tree::class);

        if ($tree instanceof Tree && $tree->getPreference('SHOW_COUNTER')) {
            $route = $request->get('route');

            $page_name = self::PAGE_NAMES[$route] ?? '';

            switch ($route) {
                case 'family':
                case 'individual':
                case 'media':
                case 'note':
                case 'repository':
                case 'source':
                    $this->page_hits = $this->countHit($tree, $page_name, $request->get('xref', ''));
                    break;

                case 'tree-page':
                    $this->page_hits = $this->countHit($tree, $page_name, 'gedcom:' . $tree->id());
                    break;

                case 'user-page':
                    $user            = app(UserInterface::class);
                    $this->page_hits = $this->countHit($tree, $page_name, 'user:' . $user->id());
                    break;
            }
        }

        return $handler->handle($request);
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
    protected function countHit(Tree $tree, string $page, string $parameter): int
    {
        // Don't increment the counter while we stay on the same page.
        if (
            Session::get('last_gedcom_id') === $tree->id() &&
            Session::get('last_page_name') === $page &&
            Session::get('last_page_parameter') === $parameter
        ) {
            return (int) Session::get('last_count');
        }

        $count = (int) DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->where('page_name', '=', $page)
            ->where('page_parameter', '=', $parameter)
            ->value('page_count');

        $count++;

        DB::table('hit_counter')->updateOrInsert([
            'gedcom_id'      => $tree->id(),
            'page_name'      => $page,
            'page_parameter' => $parameter,
        ], [
            'page_count' => $count,
        ]);

        Session::put('last_gedcom_id', $tree->id());
        Session::put('last_page_name', $page);
        Session::put('last_page_parameter', $parameter);
        Session::put('last_count', $count);

        return $count;
    }
}
