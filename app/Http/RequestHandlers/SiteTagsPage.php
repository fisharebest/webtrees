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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;

final class SiteTagsPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $custom_family_tags      = explode(',', Site::getPreference('CUSTOM_FAMILY_TAGS'));
        $custom_individual_tags  = explode(',', Site::getPreference('CUSTOM_INDIVIDUAL_TAGS'));

        $all_family_tags = new Collection(Gedcom::CUSTOM_FAMILY_TAGS);
        $all_individual_tags = new Collection(Gedcom::CUSTOM_INDIVIDUAL_TAGS);

        $all_family_tags = $all_family_tags->mapWithKeys(
            static fn (string $tag): array => [$tag => Registry::elementFactory()->make('FAM:' . $tag)->label() . ' - ' . $tag]
        );

        $all_individual_tags = $all_individual_tags->mapWithKeys(
            static fn (string $tag): array => [$tag => Registry::elementFactory()->make('INDI:' . $tag)->label() . ' - ' . $tag]
        );

        $custom_gedcom_l_tags = (bool) Site::getPreference('CUSTOM_GEDCOM_L_TAGS');

        // GEDCOM 7 extensions
        $custom_fam_fact      = (bool) Site::getPreference('CUSTOM_FAM_FACT');
        $custom_fam_nchi      = (bool) Site::getPreference('CUSTOM_FAM_NCHI');
        $custom_resi_value    = (bool) Site::getPreference('CUSTOM_RESI_VALUE');
        $custom_time_tags     = (bool) Site::getPreference('CUSTOM_TIME_TAGS');

        return $this->viewResponse('admin/tags', [
            'all_family_tags'        => $all_family_tags->sort()->all(),
            'all_individual_tags'    => $all_individual_tags->sort()->all(),
            'custom_family_tags'     => $custom_family_tags,
            'custom_gedcom_l_tags'   => $custom_gedcom_l_tags,
            'custom_individual_tags' => $custom_individual_tags,
            'custom_fam_fact'        => $custom_fam_fact,
            'custom_fam_nchi'        => $custom_fam_nchi,
            'custom_resi_value'      => $custom_resi_value,
            'custom_time_tags'       => $custom_time_tags,
            'element_factory'        => Registry::elementFactory(),
            'title'                  => I18N::translate('GEDCOM tags'),
        ]);
    }
}
