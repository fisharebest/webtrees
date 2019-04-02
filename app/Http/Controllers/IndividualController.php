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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeName;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Controller for the individual page.
 */
class IndividualController extends AbstractBaseController
{
    /** @var ModuleService */
    private $module_service;

    /** @var UserService */
    private $user_service;

    /**
     * IndividualController constructor.
     *
     * @param ModuleService $module_service
     * @param UserService   $user_service
     */
    public function __construct(ModuleService $module_service, UserService $user_service)
    {
        $this->module_service = $module_service;
        $this->user_service   = $user_service;
    }

    /**
     * Show a individual's page.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param ClipboardService       $clipboard_service
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, Tree $tree, ClipboardService $clipboard_service): ResponseInterface
    {
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, false);

        // What is (was) the age of the individual
        $bdate = $individual->getBirthDate();
        $ddate = $individual->getDeathDate();
        if ($bdate->isOK() && !$individual->isDead()) {
            // If living display age
            $age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, new Date(strtoupper(date('d M Y'))))) . ')';
        } elseif ($bdate->isOK() && $ddate->isOK()) {
            // If dead, show age at death
            $age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, $ddate)) . ')';
        } else {
            $age = '';
        }

        // What images are linked to this individual
        $individual_media = [];
        foreach ($individual->facts(['OBJE']) as $fact) {
            $media_object = $fact->target();
            if ($media_object instanceof Media) {
                $individual_media[] = $media_object->firstImageFile();
            }
        }
        $individual_media = array_filter($individual_media);

        $name_records = [];
        foreach ($individual->facts(['NAME']) as $n => $name_fact) {
            $name_records[] = $this->formatNameRecord($tree, $n, $name_fact);
        }

        $sex_records = [];
        foreach ($individual->facts(['SEX']) as $n => $sex_fact) {
            $sex_records[] = $this->formatSexRecord($sex_fact);
        }

        // If this individual is linked to a user account, show the link
        $user_link = '';
        if (Auth::isAdmin()) {
            $users = $this->user_service->findByIndividual($individual);
            foreach ($users as $user) {
                $user_link = ' —  <a href="' . e(route('admin-users', ['filter' => $user->email()])) . '">' . e($user->userName()) . '</a>';
            }
        }

        return $this->viewResponse('individual-page', [
            'age'              => $age,
            'clipboard_facts'  => $clipboard_service->pastableFacts($individual, new Collection()),
            'count_media'      => $this->countFacts($individual, ['OBJE']),
            'count_names'      => $this->countFacts($individual, ['NAME']),
            'count_sex'        => $this->countFacts($individual, ['SEX']),
            'individual'       => $individual,
            'individual_media' => $individual_media,
            'meta_robots'      => 'index,follow',
            'name_records'     => $name_records,
            'sex_records'      => $sex_records,
            'sidebars'         => $this->getSidebars($individual),
            'tabs'             => $this->getTabs($individual),
            'significant'      => $this->significant($individual),
            'title'            => $individual->fullName() . ' ' . $individual->getLifeSpan(),
            'user_link'        => $user_link,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function tab(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $xref        = $request->get('xref', '');
        $record      = Individual::getInstance($xref, $tree);
        $module_name = $request->get('module');
        $module      = $this->module_service->findByName($module_name);

        Auth::checkIndividualAccess($record);
        Auth::checkComponentAccess($module, 'tab', $tree, $user);

        $layout = view('layouts/ajax', [
            'content' => $module->getTabContent($record),
        ]);

        return response($layout);
    }

    /**
     * Count the (non-pending-delete) name records for an individual.
     *
     * @param Individual $individual
     * @param string[]   $tags
     *
     * @return int
     */
    private function countFacts(Individual $individual, array $tags): int
    {
        $count = 0;

        foreach ($individual->facts($tags) as $fact) {
            if (!$fact->isPendingDeletion()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Format a name record
     *
     * @param Tree $tree
     * @param int  $n
     * @param Fact $fact
     *
     * @return string
     */
    private function formatNameRecord(Tree $tree, $n, Fact $fact): string
    {
        $individual = $fact->record();

        // Create a dummy record, so we can extract the formatted NAME value from it.
        $dummy = new Individual(
            'xref',
            "0 @xref@ INDI\n1 DEAT Y\n" . $fact->gedcom(),
            null,
            $individual->tree()
        );
        $dummy->setPrimaryName(0); // Make sure we use the name from "1 NAME"

        $container_class = 'card';
        $content_class   = 'collapse';
        $aria            = 'false';

        if ($n === 0) {
            $content_class = 'collapse show';
            $aria          = 'true';
        }
        if ($fact->isPendingDeletion()) {
            $container_class .= ' old';
        } elseif ($fact->isPendingAddition()) {
            $container_class .= ' new';
        }

        ob_start();
        echo '<dl><dt class="label">', I18N::translate('Name'), '</dt>';
        echo '<dd class="field">', $dummy->fullName(), '</dd>';
        $ct = preg_match_all('/\n2 (\w+) (.*)/', $fact->gedcom(), $nmatch, PREG_SET_ORDER);
        for ($i = 0; $i < $ct; $i++) {
            $tag = $nmatch[$i][1];
            if ($tag != 'SOUR' && $tag != 'NOTE' && $tag != 'SPFX') {
                echo '<dt class="label">', GedcomTag::getLabel($tag, $individual), '</dt>';
                echo '<dd class="field">'; // Before using dir="auto" on this field, note that Gecko treats this as an inline element but WebKit treats it as a block element
                if (isset($nmatch[$i][2])) {
                    $name = e($nmatch[$i][2]);
                    $name = str_replace('/', '', $name);
                    $name = preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $name);
                    switch ($tag) {
                        case 'TYPE':
                            echo GedcomCodeName::getValue($name, $individual);
                            break;
                        case 'SURN':
                            // The SURN field is not necessarily the surname.
                            // Where it is not a substring of the real surname, show it after the real surname.
                            $surname = e($dummy->getAllNames()[0]['surname']);
                            $surns   = preg_replace('/, */', ' ', $nmatch[$i][2]);
                            if (strpos($dummy->getAllNames()[0]['surname'], $surns) !== false) {
                                echo '<span dir="auto">' . $surname . '</span>';
                            } else {
                                echo I18N::translate('%1$s (%2$s)', '<span dir="auto">' . $surname . '</span>', '<span dir="auto">' . $name . '</span>');
                            }
                            break;
                        default:
                            echo '<span dir="auto">' . $name . '</span>';
                            break;
                    }
                }
                echo '</dd>';
                echo '</dl>';
            }
        }
        if (strpos($fact->gedcom(), "\n2 SOUR") !== false) {
            echo '<div id="indi_sour" class="clearfix">', FunctionsPrintFacts::printFactSources($tree, $fact->gedcom(), 2), '</div>';
        }
        if (strpos($fact->gedcom(), "\n2 NOTE") !== false) {
            echo '<div id="indi_note" class="clearfix">', FunctionsPrint::printFactNotes($tree, $fact->gedcom(), 2), '</div>';
        }
        $content = ob_get_clean();

        if ($individual->canEdit() && !$fact->isPendingDeletion()) {
            $edit_links =
                '<a class="btn btn-link" href="#" data-confirm="' . I18N::translate('Are you sure you want to delete this fact?') . '" onclick="return delete_fact(this.dataset.confirm, \'' . e($individual->tree()->name()) . '\', \'' . e($individual->xref()) . '\', \'' . $fact->id() . '\');" title="' . I18N::translate('Delete this name') . '">' . view('icons/delete') . '<span class="sr-only">' . I18N::translate('Delete this name') . '</span></a>' .
                '<a class="btn btn-link" href="' . e(route('edit-name', ['xref' => $individual->xref(), 'fact_id' => $fact->id(), 'ged' => $individual->tree()->name()])) . '" title="' . I18N::translate('Edit the name') . '">' . view('icons/edit') . '<span class="sr-only">' . I18N::translate('Edit the name') . '</span></a>';
        } else {
            $edit_links = '';
        }

        return '
			<div class="' . $container_class . '">
        <div class="card-header" role="tab" id="name-header-' . $n . '">
		        <a data-toggle="collapse" data-parent="#individual-names" href="#name-content-' . $n . '" aria-expanded="' . $aria . '" aria-controls="name-content-' . $n . '">' . $dummy->fullName() . '</a>
		      ' . $edit_links . '
        </div>
		    <div id="name-content-' . $n . '" class="' . $content_class . '" role="tabpanel" aria-labelledby="name-header-' . $n . '">
		      <div class="card-body">' . $content . '</div>
        </div>
      </div>';
    }

    /**
     * print information for a sex record
     *
     * @param Fact $fact
     *
     * @return string
     */
    private function formatSexRecord(Fact $fact): string
    {
        $individual = $fact->record();

        switch ($fact->value()) {
            case 'M':
                $sex = I18N::translate('Male');
                break;
            case 'F':
                $sex = I18N::translate('Female');
                break;
            default:
                $sex = I18N::translateContext('unknown gender', 'Unknown');
                break;
        }

        $container_class = 'card';
        if ($fact->isPendingDeletion()) {
            $container_class .= ' old';
        } elseif ($fact->isPendingAddition()) {
            $container_class .= ' new';
        }

        if ($individual->canEdit() && !$fact->isPendingDeletion()) {
            $edit_links = '<a class="btn btn-link" href="' . e(route('edit-fact', ['xref' => $individual->xref(), 'fact_id' => $fact->id(), 'ged' => $individual->tree()->name()])) . '" title="' . I18N::translate('Edit the gender') . '">' . view('icons/edit') . '<span class="sr-only">' . I18N::translate('Edit the gender') . '</span></a>';
        } else {
            $edit_links = '';
        }

        return '
		<div class="' . $container_class . '">
			<div class="card-header" role="tab" id="name-header-add">
				<div class="card-title mb-0">
					<b>' . I18N::translate('Gender') . '</b> ' . $sex . $edit_links . '
				</div>
			</div>
		</div>';
    }

    /**
     * Which tabs should we show on this individual's page.
     * We don't show empty tabs.
     *
     * @param Individual $individual
     *
     * @return Collection
     * @return ModuleSidebarInterface[]
     */
    public function getSidebars(Individual $individual): Collection
    {
        return $this->module_service->findByComponent(ModuleSidebarInterface::class, $individual->tree(), Auth::user())
            ->filter(static function (ModuleSidebarInterface $sidebar) use ($individual): bool {
                return $sidebar->hasSidebarContent($individual);
            });
    }

    /**
     * Which tabs should we show on this individual's page.
     * We don't show empty tabs.
     *
     * @param Individual $individual
     *
     * @return Collection
     * @return ModuleTabInterface[]
     */
    public function getTabs(Individual $individual): Collection
    {
        return $this->module_service->findByComponent(ModuleTabInterface::class, $individual->tree(), Auth::user())
            ->filter(static function (ModuleTabInterface $tab) use ($individual): bool {
                return $tab->hasTabContent($individual);
            });
    }

    /**
     * What are the significant elements of this page?
     * The layout will need them to generate URLs for charts and reports.
     *
     * @param Individual $individual
     *
     * @return stdClass
     */
    private function significant(Individual $individual): stdClass
    {
        $significant = (object) [
            'family'     => null,
            'individual' => $individual,
            'surname'    => '',
        ];

        [$significant->surname] = explode(',', $individual->sortName());

        foreach ($individual->childFamilies()->merge($individual->spouseFamilies()) as $family) {
            $significant->family = $family;
            break;
        }

        return $significant;
    }
}
