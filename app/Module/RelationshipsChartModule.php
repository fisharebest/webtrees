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

use Aura\Router\RouterContainer;
use Closure;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Algorithm\Dijkstra;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function assert;
use function is_string;
use function redirect;
use function route;
use function view;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleChartInterface, ModuleConfigInterface, RequestHandlerInterface
{
    use ModuleChartTrait;
    use ModuleConfigTrait;

    protected const ROUTE_URL  = '/tree/{tree}/relationships-{ancestors}-{recursion}/{xref}{/xref2}';

    /** It would be more correct to use PHP_INT_MAX, but this isn't friendly in URLs */
    public const UNLIMITED_RECURSION = 99;

    /** By default new trees allow unlimited recursion */
    public const DEFAULT_RECURSION = '99';

    /** By default new trees search for all relationships (not via ancestors) */
    public const DEFAULT_ANCESTORS  = '0';
    public const DEFAULT_PARAMETERS = [
        'ancestors' => self::DEFAULT_ANCESTORS,
        'recursion' => self::DEFAULT_RECURSION,
    ];

    /** @var TreeService */
    private $tree_service;

    /**
     * ModuleController constructor.
     *
     * @param TreeService   $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        $router_container = app(RouterContainer::class);
        assert($router_container instanceof RouterContainer);

        $router_container->getMap()
            ->get(static::class, static::ROUTE_URL, $this)
            ->allows(RequestMethodInterface::METHOD_POST)
            ->tokens([
                'ancestors' => '\d+',
                'recursion' => '\d+',
            ]);
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “RelationshipsChart” module */
        return I18N::translate('A chart displaying relationships between two individuals.');
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return $this->chartMenu($individual);
    }

    /**
     * A main menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu
     */
    public function chartMenu(Individual $individual): Menu
    {
        $gedcomid = $individual->tree()->getUserPreference(Auth::user(), User::PREF_TREE_ACCOUNT_XREF);

        if ($gedcomid !== '' && $gedcomid !== $individual->xref()) {
            return new Menu(
                I18N::translate('Relationship to me'),
                $this->chartUrl($individual, ['xref2' => $gedcomid]),
                $this->chartMenuClass(),
                $this->chartUrlAttributes()
            );
        }

        return new Menu(
            $this->title(),
            $this->chartUrl($individual),
            $this->chartMenuClass(),
            $this->chartUrlAttributes()
        );
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-relationship';
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Relationships');
    }

    /**
     * The URL for a page showing chart options.
     *
     * @param Individual $individual
     * @param mixed[]    $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(static::class, [
                'xref' => $individual->xref(),
                'tree' => $individual->tree()->name(),
            ] + $parameters + self::DEFAULT_PARAMETERS);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $xref2 = $request->getAttribute('xref2') ?? '';

        $ajax      = $request->getQueryParams()['ajax'] ?? '';
        $ancestors = (int) $request->getAttribute('ancestors');
        $recursion = (int) $request->getAttribute('recursion');
        $user      = $request->getAttribute('user');

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $params = (array) $request->getParsedBody();

            return redirect(route(static::class, [
                'ancestors' => $params['ancestors'],
                'recursion' => $params['recursion'],
                'tree'      => $tree->name(),
                'xref'      => $params['xref'],
                'xref2'     => $params['xref2'],
            ]));
        }

        $individual1 = Factory::individual()->make($xref, $tree);
        $individual2 = Factory::individual()->make($xref2, $tree);

        $ancestors_only = (int) $tree->getPreference('RELATIONSHIP_ANCESTORS', static::DEFAULT_ANCESTORS);
        $max_recursion  = (int) $tree->getPreference('RELATIONSHIP_RECURSION', static::DEFAULT_RECURSION);

        $recursion = min($recursion, $max_recursion);

        if ($individual1 instanceof Individual) {
            $individual1 = Auth::checkIndividualAccess($individual1, false, true);
        }

        if ($individual2 instanceof Individual) {
            $individual2 = Auth::checkIndividualAccess($individual2, false, true);
        }

        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        if ($individual1 instanceof Individual && $individual2 instanceof Individual) {
            if ($ajax === '1') {
                return $this->chart($individual1, $individual2, $recursion, $ancestors);
            }

            /* I18N: %s are individual’s names */
            $title    = I18N::translate('Relationships between %1$s and %2$s', $individual1->fullName(), $individual2->fullName());
            $ajax_url = $this->chartUrl($individual1, [
                'ajax'      => true,
                'ancestors' => $ancestors,
                'recursion' => $recursion,
                'xref2'     => $individual2->xref(),
            ]);
        } else {
            $title    = I18N::translate('Relationships');
            $ajax_url = '';
        }

        return $this->viewResponse('modules/relationships-chart/page', [
            'ajax_url'          => $ajax_url,
            'ancestors'         => $ancestors,
            'ancestors_only'    => $ancestors_only,
            'ancestors_options' => $this->ancestorsOptions(),
            'individual1'       => $individual1,
            'individual2'       => $individual2,
            'max_recursion'     => $max_recursion,
            'module'            => $this->name(),
            'recursion'         => $recursion,
            'recursion_options' => $this->recursionOptions($max_recursion),
            'title'             => $title,
            'tree'              => $tree,
        ]);
    }

    /**
     * @param Individual $individual1
     * @param Individual $individual2
     * @param int        $recursion
     * @param int        $ancestors
     *
     * @return ResponseInterface
     */
    public function chart(Individual $individual1, Individual $individual2, int $recursion, int $ancestors): ResponseInterface
    {
        $tree = $individual1->tree();

        $max_recursion = (int) $tree->getPreference('RELATIONSHIP_RECURSION', static::DEFAULT_RECURSION);

        $recursion = min($recursion, $max_recursion);

        $paths = $this->calculateRelationships($individual1, $individual2, $recursion, (bool) $ancestors);

        ob_start();
        if (I18N::direction() === 'ltr') {
            $diagonal1 = asset('css/images/dline.png');
            $diagonal2 = asset('css/images/dline2.png');
        } else {
            $diagonal1 = asset('css/images/dline2.png');
            $diagonal2 = asset('css/images/dline.png');
        }

        $num_paths = 0;
        foreach ($paths as $path) {
            // Extract the relationship names between pairs of individuals
            $relationships = $this->oldStyleRelationshipPath($tree, $path);
            if ($relationships === []) {
                // Cannot see one of the families/individuals, due to privacy;
                continue;
            }
            echo '<h3>', I18N::translate('Relationship: %s', Functions::getRelationshipNameFromPath(implode('', $relationships), $individual1, $individual2)), '</h3>';
            $num_paths++;

            // Use a table/grid for layout.
            $table = [];
            // Current position in the grid.
            $x = 0;
            $y = 0;
            // Extent of the grid.
            $min_y = 0;
            $max_y = 0;
            $max_x = 0;
            // For each node in the path.
            foreach ($path as $n => $xref) {
                if ($n % 2 === 1) {
                    switch ($relationships[$n]) {
                        case 'hus':
                        case 'wif':
                        case 'spo':
                        case 'bro':
                        case 'sis':
                        case 'sib':
                            $table[$x + 1][$y] = '<div style="background:url(' . e(asset('css/images/hline.png')) . ') repeat-x center;  width: 94px; text-align: center"><div class="hline-text" style="height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Factory::individual()->make($path[$n - 1], $tree), Factory::individual()->make($path[$n + 1], $tree)) . '</div><div style="height: 32px;">' . view('icons/arrow-right') . '</div></div>';
                            $x                 += 2;
                            break;
                        case 'son':
                        case 'dau':
                        case 'chi':
                            if ($n > 2 && preg_match('/fat|mot|par/', $relationships[$n - 2])) {
                                $table[$x + 1][$y - 1] = '<div style="background:url(' . $diagonal2 . '); width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: end;">' . Functions::getRelationshipNameFromPath($relationships[$n], Factory::individual()->make($path[$n - 1], $tree), Factory::individual()->make($path[$n + 1], $tree)) . '</div><div style="height: 32px; text-align: start;">' . view('icons/arrow-down') . '</div></div>';
                                $x                     += 2;
                            } else {
                                $table[$x][$y - 1] = '<div style="background:url(' . e('"' . asset('css/images/vline.png') . '"') . ') repeat-y center; height: 64px; text-align: center;"><div class="vline-text" style="display: inline-block; width:50%; line-height: 64px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Factory::individual()->make($path[$n - 1], $tree), Factory::individual()->make($path[$n + 1], $tree)) . '</div><div style="display: inline-block; width:50%; line-height: 64px;">' . view('icons/arrow-down') . '</div></div>';
                            }
                            $y -= 2;
                            break;
                        case 'fat':
                        case 'mot':
                        case 'par':
                            if ($n > 2 && preg_match('/son|dau|chi/', $relationships[$n - 2])) {
                                $table[$x + 1][$y + 1] = '<div style="background:url(' . $diagonal1 . '); background-position: top right; width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: start;">' . Functions::getRelationshipNameFromPath($relationships[$n], Factory::individual()->make($path[$n - 1], $tree), Factory::individual()->make($path[$n + 1], $tree)) . '</div><div style="height: 32px; text-align: end;">' . view('icons/arrow-down') . '</div></div>';
                                $x                     += 2;
                            } else {
                                $table[$x][$y + 1] = '<div style="background:url(' . e('"' . asset('css/images/vline.png') . '"') . ') repeat-y center; height: 64px; text-align:center; "><div class="vline-text" style="display: inline-block; width: 50%; line-height: 64px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Factory::individual()->make($path[$n - 1], $tree), Factory::individual()->make($path[$n + 1], $tree)) . '</div><div style="display: inline-block; width: 50%; line-height: 32px">' . view('icons/arrow-up') . '</div></div>';
                            }
                            $y += 2;
                            break;
                    }
                    $max_x = max($max_x, $x);
                    $min_y = min($min_y, $y);
                    $max_y = max($max_y, $y);
                } else {
                    $individual    = Factory::individual()->make($xref, $tree);
                    $table[$x][$y] = view('chart-box', ['individual' => $individual]);
                }
            }
            echo '<div class="wt-chart wt-chart-relationships">';
            echo '<table style="border-collapse: collapse; margin: 20px 50px;">';
            for ($y = $max_y; $y >= $min_y; --$y) {
                echo '<tr>';
                for ($x = 0; $x <= $max_x; ++$x) {
                    echo '<td style="padding: 0;">';
                    if (isset($table[$x][$y])) {
                        echo $table[$x][$y];
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }

        if (!$num_paths) {
            echo '<p>', I18N::translate('No link between the two individuals could be found.'), '</p>';
        }

        $html = ob_get_clean();

        return response($html);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('modules/relationships-chart/config', [
            'all_trees'         => $this->tree_service->all(),
            'ancestors_options' => $this->ancestorsOptions(),
            'default_ancestors' => self::DEFAULT_ANCESTORS,
            'default_recursion' => self::DEFAULT_RECURSION,
            'recursion_options' => $this->recursionConfigOptions(),
            'title'             => I18N::translate('Chart preferences') . ' — ' . $this->title(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        foreach ($this->tree_service->all() as $tree) {
            $recursion = $params['relationship-recursion-' . $tree->id()] ?? '';
            $ancestors = $params['relationship-ancestors-' . $tree->id()] ?? '';

            $tree->setPreference('RELATIONSHIP_RECURSION', $recursion);
            $tree->setPreference('RELATIONSHIP_ANCESTORS', $ancestors);
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->title()), 'success');

        return redirect($this->getConfigLink());
    }

    /**
     * Possible options for the ancestors option
     *
     * @return string[]
     */
    private function ancestorsOptions(): array
    {
        return [
            0 => I18N::translate('Find any relationship'),
            1 => I18N::translate('Find relationships via ancestors'),
        ];
    }

    /**
     * Possible options for the recursion option
     *
     * @return string[]
     */
    private function recursionConfigOptions(): array
    {
        return [
            0                         => I18N::translate('none'),
            1                         => I18N::number(1),
            2                         => I18N::number(2),
            3                         => I18N::number(3),
            self::UNLIMITED_RECURSION => I18N::translate('unlimited'),
        ];
    }

    /**
     * Calculate the shortest paths - or all paths - between two individuals.
     *
     * @param Individual $individual1
     * @param Individual $individual2
     * @param int        $recursion How many levels of recursion to use
     * @param bool       $ancestor  Restrict to relationships via a common ancestor
     *
     * @return string[][]
     */
    private function calculateRelationships(Individual $individual1, Individual $individual2, $recursion, $ancestor = false): array
    {
        $tree = $individual1->tree();

        $rows = DB::table('link')
            ->where('l_file', '=', $tree->id())
            ->whereIn('l_type', ['FAMS', 'FAMC'])
            ->select(['l_from', 'l_to'])
            ->get();

        // Optionally restrict the graph to the ancestors of the individuals.
        if ($ancestor) {
            $ancestors = $this->allAncestors($individual1->xref(), $individual2->xref(), $tree->id());
            $exclude   = $this->excludeFamilies($individual1->xref(), $individual2->xref(), $tree->id());
        } else {
            $ancestors = [];
            $exclude   = [];
        }

        $graph = [];

        foreach ($rows as $row) {
            if ($ancestors === [] || in_array($row->l_from, $ancestors, true) && !in_array($row->l_to, $exclude, true)) {
                $graph[$row->l_from][$row->l_to] = 1;
                $graph[$row->l_to][$row->l_from] = 1;
            }
        }

        $xref1    = $individual1->xref();
        $xref2    = $individual2->xref();
        $dijkstra = new Dijkstra($graph);
        $paths    = $dijkstra->shortestPaths($xref1, $xref2);

        // Only process each exclusion list once;
        $excluded = [];

        $queue = [];
        foreach ($paths as $path) {
            // Insert the paths into the queue, with an exclusion list.
            $queue[] = [
                'path'    => $path,
                'exclude' => [],
            ];
            // While there are un-extended paths
            for ($next = current($queue); $next !== false; $next = next($queue)) {
                // For each family on the path
                for ($n = count($next['path']) - 2; $n >= 1; $n -= 2) {
                    $exclude = $next['exclude'];
                    if (count($exclude) >= $recursion) {
                        continue;
                    }
                    $exclude[] = $next['path'][$n];
                    sort($exclude);
                    $tmp = implode('-', $exclude);
                    if (in_array($tmp, $excluded, true)) {
                        continue;
                    }

                    $excluded[] = $tmp;
                    // Add any new path to the queue
                    foreach ($dijkstra->shortestPaths($xref1, $xref2, $exclude) as $new_path) {
                        $queue[] = [
                            'path'    => $new_path,
                            'exclude' => $exclude,
                        ];
                    }
                }
            }
        }
        // Extract the paths from the queue.
        $paths = [];
        foreach ($queue as $next) {
            // The Dijkstra library does not use strict types, and converts
            // numeric array keys (XREFs) from strings to integers;
            $path = array_map($this->stringMapper(), $next['path']);

            // Remove duplicates
            $paths[implode('-', $next['path'])] = $path;
        }

        return $paths;
    }

    /**
     * Convert numeric values to strings
     *
     * @return Closure
     */
    private function stringMapper(): Closure
    {
        return static function ($xref) {
            return (string) $xref;
        };
    }

    /**
     * Find all ancestors of a list of individuals
     *
     * @param string $xref1
     * @param string $xref2
     * @param int    $tree_id
     *
     * @return string[]
     */
    private function allAncestors($xref1, $xref2, $tree_id): array
    {
        $ancestors = [
            $xref1,
            $xref2,
        ];

        $queue = [
            $xref1,
            $xref2,
        ];
        while ($queue !== []) {
            $parents = DB::table('link AS l1')
                ->join('link AS l2', static function (JoinClause $join): void {
                    $join
                        ->on('l1.l_to', '=', 'l2.l_to')
                        ->on('l1.l_file', '=', 'l2.l_file');
                })
                ->where('l1.l_file', '=', $tree_id)
                ->where('l1.l_type', '=', 'FAMC')
                ->where('l2.l_type', '=', 'FAMS')
                ->whereIn('l1.l_from', $queue)
                ->pluck('l2.l_from');

            $queue = [];
            foreach ($parents as $parent) {
                if (!in_array($parent, $ancestors, true)) {
                    $ancestors[] = $parent;
                    $queue[]     = $parent;
                }
            }
        }

        return $ancestors;
    }

    /**
     * Find all families of two individuals
     *
     * @param string $xref1
     * @param string $xref2
     * @param int    $tree_id
     *
     * @return string[]
     */
    private function excludeFamilies($xref1, $xref2, $tree_id): array
    {
        return DB::table('link AS l1')
            ->join('link AS l2', static function (JoinClause $join): void {
                $join
                    ->on('l1.l_to', '=', 'l2.l_to')
                    ->on('l1.l_type', '=', 'l2.l_type')
                    ->on('l1.l_file', '=', 'l2.l_file');
            })
            ->where('l1.l_file', '=', $tree_id)
            ->where('l1.l_type', '=', 'FAMS')
            ->where('l1.l_from', '=', $xref1)
            ->where('l2.l_from', '=', $xref2)
            ->pluck('l1.l_to')
            ->all();
    }

    /**
     * Convert a path (list of XREFs) to an "old-style" string of relationships.
     * Return an empty array, if privacy rules prevent us viewing any node.
     *
     * @param Tree     $tree
     * @param string[] $path Alternately Individual / Family
     *
     * @return string[]
     */
    private function oldStyleRelationshipPath(Tree $tree, array $path): array
    {
        $spouse_codes  = [
            'M' => 'hus',
            'F' => 'wif',
            'U' => 'spo',
        ];
        $parent_codes  = [
            'M' => 'fat',
            'F' => 'mot',
            'U' => 'par',
        ];
        $child_codes   = [
            'M' => 'son',
            'F' => 'dau',
            'U' => 'chi',
        ];
        $sibling_codes = [
            'M' => 'bro',
            'F' => 'sis',
            'U' => 'sib',
        ];
        $relationships = [];

        for ($i = 1, $count = count($path); $i < $count; $i += 2) {
            $family = Factory::family()->make($path[$i], $tree);
            $prev   = Factory::individual()->make($path[$i - 1], $tree);
            $next   = Factory::individual()->make($path[$i + 1], $tree);
            if (preg_match('/\n\d (HUSB|WIFE|CHIL) @' . $prev->xref() . '@/', $family->gedcom(), $match)) {
                $rel1 = $match[1];
            } else {
                return [];
            }
            if (preg_match('/\n\d (HUSB|WIFE|CHIL) @' . $next->xref() . '@/', $family->gedcom(), $match)) {
                $rel2 = $match[1];
            } else {
                return [];
            }
            if (($rel1 === 'HUSB' || $rel1 === 'WIFE') && ($rel2 === 'HUSB' || $rel2 === 'WIFE')) {
                $relationships[$i] = $spouse_codes[$next->sex()];
            } elseif (($rel1 === 'HUSB' || $rel1 === 'WIFE') && $rel2 === 'CHIL') {
                $relationships[$i] = $child_codes[$next->sex()];
            } elseif ($rel1 === 'CHIL' && ($rel2 === 'HUSB' || $rel2 === 'WIFE')) {
                $relationships[$i] = $parent_codes[$next->sex()];
            } elseif ($rel1 === 'CHIL' && $rel2 === 'CHIL') {
                $relationships[$i] = $sibling_codes[$next->sex()];
            }
        }

        return $relationships;
    }

    /**
     * Possible options for the recursion option
     *
     * @param int $max_recursion
     *
     * @return string[]
     */
    private function recursionOptions(int $max_recursion): array
    {
        if ($max_recursion === static::UNLIMITED_RECURSION) {
            $text = I18N::translate('Find all possible relationships');
        } else {
            $text = I18N::translate('Find other relationships');
        }

        return [
            '0'            => I18N::translate('Find the closest relationships'),
            $max_recursion => $text,
        ];
    }
}
