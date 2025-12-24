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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;
use function e;
use function explode;
use function implode;
use function in_array;
use function redirect;
use function strip_tags;
use function trim;

final class FamilyPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly ClipboardService $clipboard_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $slug   = Validator::attributes($request)->string('slug', '');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, false);

        // Redirect to correct xref/slug
        if ($family->xref() !== $xref || Registry::slugFactory()->make($family) !== $slug) {
            return redirect($family->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        $clipboard_facts = $this->clipboard_service->pastableFacts($family);

        $facts = $family->facts([], true)
            ->filter(static fn (Fact $fact): bool => !in_array($fact->tag(), ['FAM:HUSB', 'FAM:WIFE', 'FAM:CHIL'], true));

        return $this->viewResponse('family-page', [
            'can_upload_media' => Auth::canUploadMedia($tree, Auth::user()),
            'clipboard_facts'  => $clipboard_facts,
            'facts'            => $facts,
            'meta_description' => $this->metaDescription($family),
            'meta_robots'      => 'index,follow',
            'record'           => $family,
            'significant'      => $this->significant($family),
            'title'            => $family->fullName(),
            'tree'             => $tree,
        ])->withHeader('Link', '<' . $family->url() . '>; rel="canonical"');
    }

    /**
     * What are the significant elements of this page?
     * The layout will need them to generate URLs for charts and reports.
     *
     * @param Family $family
     *
     * @return object
     */
    private function significant(Family $family): object
    {
        $significant = (object) [
            'family'     => $family,
            'individual' => null,
            'surname'    => '',
        ];

        $individual = $family->spouses()->merge($family->children())->first();

        if ($individual instanceof Individual) {
            $significant->individual = $individual;
            [$significant->surname] = explode(',', $individual->sortName());
        }

        return $significant;
    }

    /**
     * @param Family $family
     *
     * @return string
     */
    private function metaDescription(Family $family): string
    {
        $meta_facts = [
            $family->fullName()
        ];

        foreach ($family->facts(['MARR', 'DIV'], true) as $fact) {
            if ($fact->date()->isOK()) {
                $value = strip_tags($fact->date()->display());
            } else {
                $value = I18N::translate('yes');
            }

            $meta_facts[] = Registry::elementFactory()->make($fact->tag())->labelValue($value, $family->tree());
        }

        if ($family->children()->isNotEmpty()) {
            $child_names = $family->children()
            ->map(static fn (Individual $individual): string => e($individual->getAllNames()[0]['givn']))
            ->filter(static fn (string $x): bool => $x !== Individual::PRAENOMEN_NESCIO)
            ->implode(', ');

            $meta_facts[] = I18N::translate('Children') . ' ' . $child_names;
        }

        $meta_facts = array_map(static fn (string $x): string => strip_tags($x), $meta_facts);
        $meta_facts = array_map(static fn (string $x): string => trim($x), $meta_facts);

        return implode(', ', $meta_facts);
    }
}
