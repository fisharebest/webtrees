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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeName;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function array_map;
use function assert;
use function date;
use function e;
use function explode;
use function implode;
use function is_string;
use function ob_get_clean;
use function ob_start;
use function preg_match_all;
use function preg_replace;
use function redirect;
use function route;
use function str_replace;
use function strpos;
use function strtoupper;
use function view;

use const PREG_SET_ORDER;

/**
 * Show an individual's page.
 */
class IndividualPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var ClipboardService */
    private $clipboard_service;

    /** @var ModuleService */
    private $module_service;

    /** @var UserService */
    private $user_service;

    /**
     * IndividualPage constructor.
     *
     * @param ClipboardService $clipboard_service
     * @param ModuleService    $module_service
     * @param UserService      $user_service
     */
    public function __construct(ClipboardService $clipboard_service, ModuleService $module_service, UserService $user_service)
    {
        $this->clipboard_service = $clipboard_service;
        $this->module_service    = $module_service;
        $this->user_service      = $user_service;
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

        $individual = Factory::individual()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual);

        // Redirect to correct xref/slug
        if ($individual->xref() !== $xref || $request->getAttribute('slug') !== $individual->slug()) {
            return redirect($individual->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        // What is (was) the age of the individual
        $bdate = $individual->getBirthDate();
        $ddate = $individual->getDeathDate();

        if ($individual->isDead()) {
            // If dead, show age at death
            $age = (new Age($bdate, $ddate))->ageAtEvent(false);
        } else {
            // If living, show age today
            $today = strtoupper(date('d M Y'));
            $age   = (new Age($bdate, new Date($today)))->ageAtEvent(true);
        }

        // What images are linked to this individual
        $individual_media = new Collection();
        foreach ($individual->facts(['OBJE']) as $fact) {
            $media_object = $fact->target();
            if ($media_object instanceof Media) {
                $media_file = $media_object->firstImageFile();
                if ($media_file instanceof MediaFile) {
                    $individual_media->add($media_file);
                }
            }
        }

        $name_records = new Collection();
        foreach ($individual->facts(['NAME']) as $n => $name_fact) {
            $name_records->add($this->formatNameRecord($tree, $n, $name_fact));
        }

        $sex_records = new Collection();
        foreach ($individual->facts(['SEX']) as $n => $sex_fact) {
            $sex_records->add($this->formatSexRecord($sex_fact));
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
            'clipboard_facts'  => $this->clipboard_service->pastableFacts($individual, new Collection()),
            'individual'       => $individual,
            'individual_media' => $individual_media,
            'meta_description' => $this->metaDescription($individual),
            'meta_robots'      => 'index,follow',
            'name_records'     => $name_records,
            'sex_records'      => $sex_records,
            'sidebars'         => $this->getSidebars($individual),
            'tabs'             => $this->getTabs($individual),
            'significant'      => $this->significant($individual),
            'title'            => $individual->fullName() . ' ' . $individual->lifespan(),
            'tree'             => $tree,
            'user_link'        => $user_link,
        ]);
    }

    /**
     * @param Individual $individual
     *
     * @return string
     */
    private function metaDescription(Individual $individual): string
    {
        $meta_facts = [];

        $birth_date  = $individual->getBirthDate();
        $birth_place = $individual->getBirthPlace();

        if ($birth_date->isOK() || $birth_place->id() !== 0) {
            $meta_facts[] = I18N::translate('Birth') . ' ' .
                $birth_date->display(false, null, false) . ' ' .
                $birth_place->placeName();
        }

        $death_date  = $individual->getDeathDate();
        $death_place = $individual->getDeathPlace();

        if ($death_date->isOK() || $death_place->id() !== 0) {
            $meta_facts[] = I18N::translate('Death') . ' ' .
                $death_date->display(false, null, false) . ' ' .
                $death_place->placeName();
        }

        foreach ($individual->childFamilies() as $family) {
            $meta_facts[] = I18N::translate('Parents') . ' ' . $family->fullName();
        }

        foreach ($individual->spouseFamilies() as $family) {
            $spouse = $family->spouse($individual);
            if ($spouse instanceof Individual) {
                $meta_facts[] = I18N::translate('Spouse') . ' ' . $spouse->fullName();
            }

            $child_names = $family->children()->map(static function (Individual $individual): string {
                return e($individual->getAllNames()[0]['givn']);
            })->implode(', ');


            if ($child_names !== '') {
                $meta_facts[] = I18N::translate('Children') . ' ' . $child_names;
            }
        }

        $meta_facts = array_map('strip_tags', $meta_facts);
        $meta_facts = array_map('trim', $meta_facts);

        return implode(', ', $meta_facts);
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
        $dummy = Factory::individual()->new(
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
            $container_class .= ' wt-old';
        } elseif ($fact->isPendingAddition()) {
            $container_class .= ' wt-new';
        }

        ob_start();
        echo '<dl class="row mb-0"><dt class="col-md-4 col-lg-3">', I18N::translate('Name'), '</dt>';
        echo '<dd class="col-md-8 col-lg-9">', $dummy->fullName(), '</dd>';
        $ct = preg_match_all('/\n2 (\w+) (.*)/', $fact->gedcom(), $nmatch, PREG_SET_ORDER);
        for ($i = 0; $i < $ct; $i++) {
            $tag = $nmatch[$i][1];
            if ($tag !== 'SOUR' && $tag !== 'NOTE' && $tag !== 'SPFX') {
                echo '<dt class="col-md-4 col-lg-3">', GedcomTag::getLabel($tag), '</dt>';
                echo '<dd class="col-md-8 col-lg-9">'; // Before using dir="auto" on this field, note that Gecko treats this as an inline element but WebKit treats it as a block element
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
            }
        }
        echo '</dl>';
        if (strpos($fact->gedcom(), "\n2 SOUR") !== false) {
            echo '<div id="indi_sour" class="clearfix">', FunctionsPrintFacts::printFactSources($tree, $fact->gedcom(), 2), '</div>';
        }
        if (strpos($fact->gedcom(), "\n2 NOTE") !== false) {
            echo '<div id="indi_note" class="clearfix">', FunctionsPrint::printFactNotes($tree, $fact->gedcom(), 2), '</div>';
        }
        $content = ob_get_clean();

        if ($fact->canEdit()) {
            $edit_links =
                '<a class="btn btn-link" href="#" data-confirm="' . I18N::translate('Are you sure you want to delete this fact?') . '" data-post-url="' . e(route(DeleteFact::class, ['tree' => $individual->tree()->name(), 'xref' => $individual->xref(), 'fact_id' => $fact->id()])) . '" title="' . I18N::translate('Delete this name') . '">' . view('icons/delete') . '<span class="sr-only">' . I18N::translate('Delete this name') . '</span></a>' .
                '<a class="btn btn-link" href="' . e(route(EditName::class, ['xref' => $individual->xref(), 'fact_id' => $fact->id(), 'tree' => $individual->tree()->name()])) . '" title="' . I18N::translate('Edit the name') . '">' . view('icons/edit') . '<span class="sr-only">' . I18N::translate('Edit the name') . '</span></a>';
        } else {
            $edit_links = '';
        }

        return
            '<div class="' . $container_class . '">' .
            '<div class="card-header" role="tab" id="name-header-' . $n . '">' .
            '<a data-toggle="collapse" href="#name-content-' . $n . '" aria-expanded="' . $aria . '" aria-controls="name-content-' . $n . '">' .
            //view('icons/expand') .
            //view('icons/collapse') .
            $dummy->fullName() .
            '</a>' .
            $edit_links .
            '</div>' .
            '<div id="name-content-' . $n . '" class="' . $content_class . '" data-parent="#individual-names" aria-labelledby="name-header-' . $n . '">' .
            '<div class="card-body">' . $content . '</div>' .
            '</div>' .
            '</div>';
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
            $container_class .= ' wt-old';
        } elseif ($fact->isPendingAddition()) {
            $container_class .= ' wt-new';
        }

        if ($fact->canEdit()) {
            $edit_links = '<a class="btn btn-link" href="' . e(route(EditFactPage::class, ['xref' => $individual->xref(), 'fact_id' => $fact->id(), 'tree' => $individual->tree()->name()])) . '" title="' . I18N::translate('Edit the gender') . '">' . view('icons/edit') . '<span class="sr-only">' . I18N::translate('Edit the gender') . '</span></a>';
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
     * @return Collection<ModuleSidebarInterface>
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
     * @return Collection<ModuleTabInterface>
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
        [$surname] = explode(',', $individual->sortName());

        $family = $individual->childFamilies()->merge($individual->spouseFamilies())->first();

        return (object) [
            'family'     => $family,
            'individual' => $individual,
            'surname'    => $surname,
        ];
    }
}
