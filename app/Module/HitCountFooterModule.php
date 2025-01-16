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

use Fisharebest\Webtrees\Http\RequestHandlers\FamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\IndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\NotePage;
use Fisharebest\Webtrees\Http\RequestHandlers\RepositoryPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SourcePage;
use Fisharebest\Webtrees\Http\RequestHandlers\SubmitterPage;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
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

    // Which routes do we count?
    // For historical reasons, we record the names of the original webtrees script and parameter.
    protected const PAGE_NAMES = [
        FamilyPage::class     => 'family.php',
        IndividualPage::class => 'individual.php',
        MediaPage::class      => 'mediaviewer.php',
        NotePage::class       => 'note.php',
        RepositoryPage::class => 'repo.php',
        SourcePage::class     => 'source.php',
        SubmitterPage::class  => 'submitter',
        TreePage::class       => 'index.php',
        UserPage::class       => 'index.php',
    ];

    // Count of visits to the current page
    protected int $page_hits = 0;

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
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getFooter(ServerRequestInterface $request): string
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
        $route = Validator::attributes($request)->route();
        $tree  = Validator::attributes($request)->treeOptional();
        $user  = Validator::attributes($request)->user();

        if ($tree instanceof Tree && $tree->getPreference('SHOW_COUNTER')) {
            $page_name = self::PAGE_NAMES[$route->name] ?? '';

            switch ($route->name) {
                case FamilyPage::class:
                case IndividualPage::class:
                case MediaPage::class:
                case NotePage::class:
                case RepositoryPage::class:
                case SourcePage::class:
                case SubmitterPage::class:
                    $xref = Validator::attributes($request)->isXref()->string('xref');
                    $this->page_hits = $this->countHit($tree, $page_name, $xref);
                    break;

                case TreePage::class:
                    $this->page_hits = $this->countHit($tree, $page_name, 'gedcom:' . $tree->id());
                    break;

                case UserPage::class:
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
            Session::get('last_page_name') === $page &&
            Session::get('last_page_parameter') === $parameter &&
            Session::get('last_gedcom_id') === $tree->id()
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
