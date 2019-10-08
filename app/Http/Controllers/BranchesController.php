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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function view;

/**
 * Find all branches of families with a given surname.
 */
class BranchesController extends AbstractBaseController
{
    /** @var ModuleService */
    protected $module_service;

    /**
     * BranchesController constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * A form to request the page parameters.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function page(ServerRequestInterface $request): ResponseInterface
    {
        $module = $request->getAttribute('module');
        $action = $request->getAttribute('action');

        $surname     = $request->getQueryParams()['surname'] ?? '';
        $soundex_std = (bool) ($request->getQueryParams()['soundex_std'] ?? false);
        $soundex_dm  = (bool) ($request->getQueryParams()['soundex_dm'] ?? false);

        if ($surname !== '') {
            /* I18N: %s is a surname */
            $title = I18N::translate('Branches of the %s family', e($surname));
        } else {
            /* I18N: Branches of a family tree */
            $title = I18N::translate('Branches');
        }

        return $this->viewResponse('branches-page', [
            'soundex_dm'  => $soundex_dm,
            'soundex_std' => $soundex_std,
            'surname'     => $surname,
            'title'       => $title,
            'module'      => $module,
            'action'      => $action,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = $request->getAttribute('tree');
        $user        = $request->getAttribute('user');
        $params      = $request->getQueryParams();
        $surname     = $params['surname'];
        $soundex_std = (bool) ($params['soundex_std'] ?? false);
        $soundex_dm  = (bool) ($params['soundex_dm'] ?? false);

        // Highlight direct-line ancestors of this individual.
        $self = Individual::getInstance($tree->getUserPreference($user, 'gedcomid'), $tree);

        if ($surname !== '') {
            $individuals = $this->loadIndividuals($tree, $surname, $soundex_dm, $soundex_std);
        } else {
            $individuals = [];
        }

        if ($self !== null) {
            $ancestors = $this->allAncestors($self);
        } else {
            $ancestors = [];
        }

        // @TODO - convert this to use views
        $html = view('branches-list', [
            'branches' => $this->getPatriarchsHtml($tree, $individuals, $ancestors, $surname, $soundex_dm, $soundex_std),
        ]);

        return response($html);
    }

    /**
     * Find all ancestors of an individual, indexed by the Sosa-Stradonitz number.
     *
     * @param Individual $individual
     *
     * @return Individual[]
     */
    protected function allAncestors(Individual $individual): array
    {
        /** @var Individual[] $ancestors */
        $ancestors = [
            1 => $individual,
        ];

        do {
            $sosa = key($ancestors);

            $family = $ancestors[$sosa]->primaryChildFamily();

            if ($family !== null) {
                if ($family->husband() !== null) {
                    $ancestors[$sosa * 2] = $family->husband();
                }
                if ($family->wife() !== null) {
                    $ancestors[$sosa * 2 + 1] = $family->wife();
                }
            }
        } while (next($ancestors));

        return $ancestors;
    }

    /**
     * Fetch all individuals with a matching surname
     *
     * @param Tree   $tree
     * @param string $surname
     * @param bool   $soundex_dm
     * @param bool   $soundex_std
     *
     * @return Individual[]
     */
    private function loadIndividuals(Tree $tree, string $surname, bool $soundex_dm, bool $soundex_std): array
    {
        $individuals = DB::table('individuals')
            ->join('name', static function (JoinClause $join): void {
                $join
                    ->on('name.n_file', '=', 'individuals.i_file')
                    ->on('name.n_id', '=', 'individuals.i_id');
            })
            ->where('i_file', '=', $tree->id())
            ->where('n_type', '<>', '_MARNM')
            ->where(static function (Builder $query) use ($surname, $soundex_dm, $soundex_std): void {
                $query
                    ->where('n_surn', '=', $surname)
                    ->orWhere('n_surname', '=', $surname);

                if ($soundex_std) {
                    $sdx = Soundex::russell($surname);
                    if ($sdx !== '') {
                        foreach (explode(':', $sdx) as $value) {
                            $query->whereContains('n_soundex_surn_std', $value, 'or');
                        }
                    }
                }

                if ($soundex_dm) {
                    $sdx = Soundex::daitchMokotoff($surname);
                    if ($sdx !== '') {
                        foreach (explode(':', $sdx) as $value) {
                            $query->whereContains('n_soundex_surn_dm', $value, 'or');
                        }
                    }
                }
            })
            ->select(['individuals.*'])
            ->distinct()
            ->get()
            ->map(Individual::rowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->all();

        usort($individuals, Individual::birthDateComparator());

        return $individuals;
    }

    /**
     * For each individual with no ancestors, list their descendants.
     *
     * @param Tree         $tree
     * @param Individual[] $individuals
     * @param Individual[] $ancestors
     * @param string       $surname
     * @param bool         $soundex_dm
     * @param bool         $soundex_std
     *
     * @return string
     */
    public function getPatriarchsHtml(Tree $tree, array $individuals, array $ancestors, string $surname, bool $soundex_dm, bool $soundex_std): string
    {
        $html = '';
        foreach ($individuals as $individual) {
            foreach ($individual->childFamilies() as $family) {
                foreach ($family->spouses() as $parent) {
                    if (in_array($parent, $individuals, true)) {
                        continue 3;
                    }
                }
            }
            $html .= $this->getDescendantsHtml($tree, $individuals, $ancestors, $surname, $soundex_dm, $soundex_std, $individual, null);
        }

        return $html;
    }

    /**
     * Generate a recursive list of descendants of an individual.
     * If parents are specified, we can also show the pedigree (adopted, etc.).
     *
     * @param Tree         $tree
     * @param Individual[] $individuals
     * @param Individual[] $ancestors
     * @param string       $surname
     * @param bool         $soundex_dm
     * @param bool         $soundex_std
     * @param Individual   $individual
     * @param Family|null  $parents
     *
     * @return string
     */
    private function getDescendantsHtml(Tree $tree, array $individuals, array $ancestors, string $surname, bool $soundex_dm, bool $soundex_std, Individual $individual, Family $parents = null): string
    {
        $module = $this->module_service->findByComponent(ModuleChartInterface::class, $tree, Auth::user())->first(static function (ModuleInterface $module) {
            return $module instanceof RelationshipsChartModule;
        });

        // A person has many names. Select the one that matches the searched surname
        $person_name = '';
        foreach ($individual->getAllNames() as $name) {
            [$surn1] = explode(',', $name['sort']);
            if (// one name is a substring of the other
                stripos($surn1, $surname) !== false ||
                stripos($surname, $surn1) !== false ||
                // one name sounds like the other
                $soundex_std && Soundex::compare(Soundex::russell($surn1), Soundex::russell($surname)) ||
                $soundex_dm && Soundex::compare(Soundex::daitchMokotoff($surn1), Soundex::daitchMokotoff($surname))
            ) {
                $person_name = $name['full'];
                break;
            }
        }

        // No matching name? Typically children with a different surname. The branch stops here.
        if (!$person_name) {
            return '<li title="' . strip_tags($individual->fullName()) . '"><small>' . view('icons/sex-' . $individual->sex()) . '…</small></li>';
        }

        // Is this individual one of our ancestors?
        $sosa = array_search($individual, $ancestors, true);
        if (is_int($sosa) && $module instanceof RelationshipsChartModule) {
            $sosa_class = 'search_hit';
            $sosa_html  = '<a class="details1 ' . $individual->getBoxStyle() . '" href="' . e($module->chartUrl($individual, ['xref2' => $individuals[1]->xref()])) . '" rel="nofollow" title="' . I18N::translate('Relationships') . '">' . I18N::number($sosa) . '</a>' . self::sosaGeneration($sosa);
        } else {
            $sosa_class = '';
            $sosa_html  = '';
        }

        // Generate HTML for this individual, and all their descendants
        $indi_html = '<small>' . view('icons/sex-' . $individual->sex()) . '</small><a class="' . $sosa_class . '" href="' . e($individual->url()) . '">' . $person_name . '</a> ' . $individual->getLifeSpan() . $sosa_html;

        // If this is not a birth pedigree (e.g. an adoption), highlight it
        if ($parents) {
            $pedi = '';
            foreach ($individual->facts(['FAMC']) as $fact) {
                if ($fact->target() === $parents) {
                    $pedi = $fact->attribute('PEDI');
                    break;
                }
            }
            if ($pedi !== '' && $pedi !== 'birth') {
                $indi_html = '<span class="red">' . GedcomCodePedi::getValue($pedi, $individual) . '</span> ' . $indi_html;
            }
        }

        // spouses and children
        $spouse_families = $individual->spouseFamilies()
            ->sort(Family::marriageDateComparator());

        if ($spouse_families->isNotEmpty()) {
            $fam_html = '';
            foreach ($spouse_families as $family) {
                $fam_html .= $indi_html; // Repeat the individual details for each spouse.

                $spouse = $family->spouse($individual);
                if ($spouse instanceof Individual) {
                    $sosa = array_search($spouse, $ancestors, true);
                    if (is_int($sosa) && $module instanceof RelationshipsChartModule) {
                        $sosa_class = 'search_hit';
                        $sosa_html  = '<a class="details1 ' . $spouse->getBoxStyle() . '" href="' . e($module->chartUrl($individual, ['xref2' => $individuals[1]->xref()])) . '" rel="nofollow" title="' . I18N::translate('Relationships') . '">' . I18N::number($sosa) . '</a>' . self::sosaGeneration($sosa);
                    } else {
                        $sosa_class = '';
                        $sosa_html  = '';
                    }
                    $marriage_year = $family->getMarriageYear();
                    if ($marriage_year) {
                        $fam_html .= ' <a href="' . e($family->url()) . '" title="' . strip_tags($family->getMarriageDate()->display()) . '"><i class="icon-rings"></i>' . $marriage_year . '</a>';
                    } elseif ($family->facts(['MARR'])->first()) {
                        $fam_html .= ' <a href="' . e($family->url()) . '" title="' . I18N::translate('Marriage') . '"><i class="icon-rings"></i></a>';
                    } else {
                        $fam_html .= ' <a href="' . e($family->url()) . '" title="' . I18N::translate('Not married') . '"><i class="icon-rings"></i></a>';
                    }
                    $fam_html .= ' <small>' . view('icons/sex-' . $spouse->sex()) . '</small><a class="' . $sosa_class . '" href="' . e($spouse->url()) . '">' . $spouse->fullName() . '</a> ' . $spouse->getLifeSpan() . ' ' . $sosa_html;
                }

                $fam_html .= '<ol>';
                foreach ($family->children() as $child) {
                    $fam_html .= $this->getDescendantsHtml($tree, $individuals, $ancestors, $surname, $soundex_dm, $soundex_std, $child, $family);
                }
                $fam_html .= '</ol>';
            }

            return '<li>' . $fam_html . '</li>';
        }

        // No spouses - just show the individual
        return '<li>' . $indi_html . '</li>';
    }

    /**
     * Convert a SOSA number into a generation number. e.g. 8 = great-grandfather = 3 generations
     *
     * @param int $sosa
     *
     * @return string
     */
    private static function sosaGeneration($sosa): string
    {
        $generation = (int) log($sosa, 2) + 1;

        return '<sup title="' . I18N::translate('Generation') . '">' . $generation . '</sup>';
    }
}
