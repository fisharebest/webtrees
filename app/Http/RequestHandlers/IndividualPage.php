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
use function redirect;
use function route;
use function strtoupper;
use function view;

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

        $name_records = $individual->facts(['NAME'])->map(static function (Fact $fact): string {
            return view('individual-name', ['fact' => $fact]);
        });

        $sex_records = $individual->facts(['SEX'])->map(static function (Fact $fact): string {
            return view('individual-sex', ['fact' => $fact]);
        });

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
