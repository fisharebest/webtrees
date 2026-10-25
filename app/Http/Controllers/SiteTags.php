<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_keys;
use function explode;
use function implode;
use function redirect;
use function route;

final class SiteTags
{
    use ViewResponseTrait;

    public function get(): ResponseInterface
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

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        foreach (array_keys(Gedcom::HIDDEN_TAGS) as $setting) {
            $value = Validator::parsedBody($request)->boolean('HIDE_' . $setting, false);
            Site::setPreference('HIDE_' . $setting, (string) $value);
        }

        $custom_family_tags     = Validator::parsedBody($request)->list('custom_family_tags');
        $custom_individual_tags = Validator::parsedBody($request)->list('custom_individual_tags');
        $custom_gedcom_l_tags   = Validator::parsedBody($request)->boolean('custom_gedcom_l_tags', false);
        $custom_fam_fact        = Validator::parsedBody($request)->boolean('custom_fam_fact', false);
        $custom_fam_nchi        = Validator::parsedBody($request)->boolean('custom_fam_nchi', false);
        $custom_resi_value      = Validator::parsedBody($request)->boolean('custom_resi_value', false);
        $custom_time_tags       = Validator::parsedBody($request)->boolean('custom_time_tags', false);

        Site::setPreference('CUSTOM_FAMILY_TAGS', implode(',', $custom_family_tags));
        Site::setPreference('CUSTOM_INDIVIDUAL_TAGS', implode(',', $custom_individual_tags));
        Site::setPreference('CUSTOM_GEDCOM_L_TAGS', (string) $custom_gedcom_l_tags);
        Site::setPreference('CUSTOM_FAM_FACT', (string) $custom_fam_fact);
        Site::setPreference('CUSTOM_FAM_NCHI', (string) $custom_fam_nchi);
        Site::setPreference('CUSTOM_RESI_VALUE', (string) $custom_resi_value);
        Site::setPreference('CUSTOM_TIME_TAGS', (string) $custom_time_tags);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        return redirect(route(ControlPanel::class));
    }
}
