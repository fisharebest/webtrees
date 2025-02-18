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

use Closure;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Algorithm\Dijkstra;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\RelationshipService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;
use function asset;
use function count;
use function current;
use function e;
use function implode;
use function in_array;
use function max;
use function min;
use function next;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function redirect;
use function response;
use function route;
use function sort;
use function view;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleChartInterface, ModuleConfigInterface, RequestHandlerInterface
{
    use ModuleChartTrait;
    use ModuleConfigTrait;

    protected const ROUTE_URL = '/tree/{tree}/relationships-{ancestors}-{recursion}/{xref}{/xref2}';

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

    private TreeService $tree_service;

    private RelationshipService $relationship_service;

    /**
     * @param RelationshipService $relationship_service
     * @param TreeService         $tree_service
     */
    public function __construct(RelationshipService $relationship_service, TreeService $tree_service)
    {
        $this->relationship_service = $relationship_service;
        $this->tree_service         = $tree_service;
    }

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
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
        $my_xref = $individual->tree()->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF);

        if ($my_xref !== '' && $my_xref !== $individual->xref()) {
            $my_record = Registry::individualFactory()->make($my_xref, $individual->tree());

            if ($my_record instanceof Individual) {
                return new Menu(
                    I18N::translate('Relationship to me'),
                    $this->chartUrl($my_record, ['xref2' => $individual->xref()]),
                    $this->chartMenuClass(),
                    $this->chartUrlAttributes()
                );
            }
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

    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Relationships');
    }

    /**
     * The URL for a page showing chart options.
     *
     * @param Individual                                $individual
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        $tree           = $individual->tree();
        $ancestors_only = (int) $tree->getPreference('RELATIONSHIP_ANCESTORS', static::DEFAULT_ANCESTORS);
        $max_recursion  = (int) $tree->getPreference('RELATIONSHIP_RECURSION', static::DEFAULT_RECURSION);


        return route(static::class, [
            'xref'      => $individual->xref(),
            'tree'      => $individual->tree()->name(),
            'ancestors' => $ancestors_only,
            'recursion' => $max_recursion,
        ] + $parameters);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree      = Validator::attributes($request)->tree();
        $xref      = Validator::attributes($request)->isXref()->string('xref');
        $xref2     = Validator::attributes($request)->isXref()->string('xref2', '');
        $ajax      = Validator::queryParams($request)->boolean('ajax', false);
        $ancestors = (int) $request->getAttribute('ancestors');
        $recursion = (int) $request->getAttribute('recursion');
        $user      = Validator::attributes($request)->user();

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree'      => $tree->name(),
                'ancestors' => Validator::parsedBody($request)->string('ancestors', ''),
                'recursion' => Validator::parsedBody($request)->string('recursion', ''),
                'xref'      => Validator::parsedBody($request)->string('xref', ''),
                'xref2'     => Validator::parsedBody($request)->string('xref2', ''),
            ]));
        }

        $individual1 = Registry::individualFactory()->make($xref, $tree);
        $individual2 = Registry::individualFactory()->make($xref2, $tree);

        $ancestors_only = (int) $tree->getPreference('RELATIONSHIP_ANCESTORS', static::DEFAULT_ANCESTORS);
        $max_recursion  = (int) $tree->getPreference('RELATIONSHIP_RECURSION', static::DEFAULT_RECURSION);

        $recursion = min($recursion, $max_recursion);

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        if ($individual1 instanceof Individual) {
            $individual1 = Auth::checkIndividualAccess($individual1, false, true);
        }

        if ($individual2 instanceof Individual) {
            $individual2 = Auth::checkIndividualAccess($individual2, false, true);
        }

        if ($individual1 instanceof Individual && $individual2 instanceof Individual) {
            if ($ajax) {
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

            $nodes = Collection::make($path)
                ->map(static function (string $xref, int $key) use ($tree): GedcomRecord {
                    if ($key % 2 === 0) {
                        return Registry::individualFactory()->make($xref, $tree);
                    }

                    return  Registry::familyFactory()->make($xref, $tree);
                });

            $relationship = $this->relationship_service->nameFromPath($nodes->all(), I18N::language());

            echo '<h3>', I18N::translate('Relationship: %s', $relationship), '</h3>';

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
                            $table[$x + 1][$y] = '<div style="background:url(' . e(asset('css/images/hline.png')) . ') repeat-x center;  width: 94px; text-align: center"><div style="height: 32px;">' . $this->relationship_service->legacyNameAlgorithm($relationships[$n], Registry::individualFactory()->make($path[$n - 1], $tree), Registry::individualFactory()->make($path[$n + 1], $tree)) . '</div><div style="height: 32px;">' . view('icons/arrow-right') . '</div></div>';
                            $x += 2;
                            break;
                        case 'son':
                        case 'dau':
                        case 'chi':
                            if ($n > 2 && preg_match('/fat|mot|par/', $relationships[$n - 2])) {
                                $table[$x + 1][$y - 1] = '<div style="background:url(' . $diagonal2 . '); width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: end;">' . $this->relationship_service->legacyNameAlgorithm($relationships[$n], Registry::individualFactory()->make($path[$n - 1], $tree), Registry::individualFactory()->make($path[$n + 1], $tree)) . '</div><div style="height: 32px; text-align: start;">' . view('icons/arrow-down') . '</div></div>';
                                $x += 2;
                            } else {
                                $table[$x][$y - 1] = '<div style="background:url(' . e('"' . asset('css/images/vline.png') . '"') . ') repeat-y center; height: 64px; text-align: center;"><div style="display: inline-block; width:50%; line-height: 64px;">' . $this->relationship_service->legacyNameAlgorithm($relationships[$n], Registry::individualFactory()->make($path[$n - 1], $tree), Registry::individualFactory()->make($path[$n + 1], $tree)) . '</div><div style="display: inline-block; width:50%; line-height: 64px;">' . view('icons/arrow-down') . '</div></div>';
                            }
                            $y -= 2;
                            break;
                        case 'fat':
                        case 'mot':
                        case 'par':
                            if ($n > 2 && preg_match('/son|dau|chi/', $relationships[$n - 2])) {
                                $table[$x + 1][$y + 1] = '<div style="background:url(' . $diagonal1 . '); background-position: top right; width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: start;">' . $this->relationship_service->legacyNameAlgorithm($relationships[$n], Registry::individualFactory()->make($path[$n - 1], $tree), Registry::individualFactory()->make($path[$n + 1], $tree)) . '</div><div style="height: 32px; text-align: end;">' . view('icons/arrow-down') . '</div></div>';
                                $x += 2;
                            } else {
                                $table[$x][$y + 1] = '<div style="background:url(' . e('"' . asset('css/images/vline.png') . '"') . ') repeat-y center; height: 64px; text-align:center; "><div style="display: inline-block; width: 50%; line-height: 64px;">' . $this->relationship_service->legacyNameAlgorithm($relationships[$n], Registry::individualFactory()->make($path[$n - 1], $tree), Registry::individualFactory()->make($path[$n + 1], $tree)) . '</div><div style="display: inline-block; width: 50%; line-height: 32px">' . view('icons/arrow-up') . '</div></div>';
                            }
                            $y += 2;
                            break;
                    }
                    $max_x = max($max_x, $x);
                    $min_y = min($min_y, $y);
                    $max_y = max($max_y, $y);
                } else {
                    $individual    = Registry::individualFactory()->make($xref, $tree);
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
        foreach ($this->tree_service->all() as $tree) {
            $recursion = Validator::parsedBody($request)->integer('relationship-recursion-' . $tree->id());
            $ancestors = Validator::parsedBody($request)->string('relationship-ancestors-' . $tree->id());

            $tree->setPreference('RELATIONSHIP_RECURSION', (string) $recursion);
            $tree->setPreference('RELATIONSHIP_ANCESTORS', $ancestors);
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->title()), 'success');

        return redirect($this->getConfigLink());
    }

    /**
     * Possible options for the ancestors option
     *
     * @return array<int,string>
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
     * @return array<int,string>
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
     * @return array<array<string>>
     */
    private function calculateRelationships(
        Individual $individual1,
        Individual $individual2,
        int $recursion,
        bool $ancestor = false
    ): array {
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
     * @return Closure(int|string):string
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
     * @return array<string>
     */
    private function allAncestors(string $xref1, string $xref2, int $tree_id): array
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
     * @return array<string>
     */
    private function excludeFamilies(string $xref1, string $xref2, int $tree_id): array
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
     * @param Tree          $tree
     * @param array<string> $path Alternately Individual / Family
     *
     * @return array<string>
     */
    private function oldStyleRelationshipPath(Tree $tree, array $path): array
    {
        $spouse_codes = [
            'M' => 'hus',
            'F' => 'wif',
            'U' => 'spo',
        ];
        $parent_codes = [
            'M' => 'fat',
            'F' => 'mot',
            'U' => 'par',
        ];
        $child_codes = [
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
            $family = Registry::familyFactory()->make($path[$i], $tree);
            $prev   = Registry::individualFactory()->make($path[$i - 1], $tree);
            $next   = Registry::individualFactory()->make($path[$i + 1], $tree);
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
                $relationships[$i] = $spouse_codes[$next->sex()] ?? $spouse_codes['U'];
            } elseif (($rel1 === 'HUSB' || $rel1 === 'WIFE') && $rel2 === 'CHIL') {
                $relationships[$i] = $child_codes[$next->sex()] ?? $child_codes['U'];
            } elseif ($rel1 === 'CHIL' && ($rel2 === 'HUSB' || $rel2 === 'WIFE')) {
                $relationships[$i] = $parent_codes[$next->sex()] ?? $parent_codes['U'];
            } elseif ($rel1 === 'CHIL' && $rel2 === 'CHIL') {
                $relationships[$i] = $sibling_codes[$next->sex()] ?? $sibling_codes['U'];
            }
        }

        return $relationships;
    }

    /**
     * Possible options for the recursion option
     *
     * @param int $max_recursion
     *
     * @return array<string>
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
