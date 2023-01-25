<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_keys;
use function implode;
use function redirect;
use function route;

/**
 * Edit the tree preferences.
 */
class SiteTagsAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach (array_keys(Gedcom::HIDDEN_TAGS) as $setting) {
            $value = Validator::parsedBody($request)->boolean('HIDE_' . $setting, false);
            Site::setPreference('HIDE_' . $setting, (string) $value);
        }

        $custom_family_tags     = Validator::parsedBody($request)->array('custom_family_tags');
        $custom_individual_tags = Validator::parsedBody($request)->array('custom_individual_tags');
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
