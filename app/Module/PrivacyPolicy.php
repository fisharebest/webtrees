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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function explode;
use function implode;
use function redirect;
use function view;

/**
 * Class PrivacyPolicy - data protection compliance module.
 *
 * Displays a configurable privacy/data-protection page based on the
 * jurisdiction(s) selected by the site administrator.
 */
class PrivacyPolicy extends AbstractModule implements ModuleFooterInterface, ModuleConfigInterface
{
    use ModuleFooterTrait;
    use ModuleConfigTrait;

    private ModuleService $module_service;

    /**
     * All available privacy statements.
     * Each statement has a short, generic, reusable text.
     *
     * @return array<string,array{title:string,text:string,fields:list<string>}>
     */
    public static function allStatements(): array
    {
        return [
            'purpose' => [
                'title'  => I18N::translate('Purpose of processing'),
                'text'   => I18N::translate('This website processes personal data for the purpose of historical and genealogical research.'),
                'fields' => [],
            ],
            'lawful_basis' => [
                'title'  => I18N::translate('Lawful basis'),
                'text'   => I18N::translate('Processing of personal data on this website is based on legitimate interest in historical and genealogical research. This is recognized as historical research under data protection law, and certain rights — including the right to erasure and the right to object — may be limited where exercising those rights would render the research impossible or seriously impair it.'),
                'fields' => [],
            ],
            'data_collected' => [
                'title'  => I18N::translate('Data collected'),
                'text'   => I18N::translate('We collect names, dates, places, family relationships, and other genealogical information. Much of this data is derived from public records such as registers of births, marriages, and deaths. For registered users, we also store email addresses and login credentials.'),
                'fields' => [],
            ],
            'public_records' => [
                'title'  => I18N::translate('Sources of data'),
                'text'   => I18N::translate('Genealogical data on this website is typically obtained from publicly accessible sources, including civil registration records, church records, census returns, published directories, gravestones, and contributions from family members. The data subjects may not have provided this data themselves.'),
                'fields' => [],
            ],
            'cookies' => [
                'title'  => I18N::translate('Cookies'),
                'text'   => I18N::translate('This website uses essential cookies for login sessions and to remember your preferences. These cookies are strictly necessary and do not require consent.'),
                'fields' => [],
            ],
            'analytics' => [
                'title'  => I18N::translate('Tracking and analytics'),
                'text'   => I18N::translate('This website uses third-party services to understand visitor behavior. These services may use cookies or similar tracking technology.'),
                'fields' => [],
            ],
            'third_party_services' => [
                'title'  => I18N::translate('Third-party services'),
                'text'   => I18N::translate('This website uses external services to provide mapping and location features. When you view a map, your browser connects directly to these services, which may receive your IP address and other request data.'),
                'fields' => [],
            ],
            'rights_access' => [
                'title'  => I18N::translate('Right of access and data portability'),
                'text'   => I18N::translate('You have the right to request a copy of the personal data we hold about you. This data can be provided in a machine-readable format (GEDCOM).'),
                'fields' => [],
            ],
            'rights_correction' => [
                'title'  => I18N::translate('Right to correction'),
                'text'   => I18N::translate('You have the right to request that we correct any inaccurate personal data.'),
                'fields' => [],
            ],
            'rights_removal' => [
                'title'  => I18N::translate('Right to erasure, objection, and restriction'),
                'text'   => I18N::translate('You may request that we delete, restrict, or cease processing your personal data. However, where data is processed for historical research purposes or was obtained from publicly accessible sources, these rights may be limited where compliance would render the research impossible or seriously impair it.'),
                'fields' => [],
            ],
            'do_not_sell' => [
                'title'  => I18N::translate('Do not sell or share'),
                'text'   => I18N::translate('We do not sell or share your personal information.'),
                'fields' => [],
            ],
            'controller' => [
                'title'  => I18N::translate('Data controller'),
                'text'   => I18N::translate('The person responsible for this website and for the processing of your personal data is:'),
                'fields' => ['controller_name', 'controller_email', 'controller_address'],
            ],
            'impressum' => [
                'title'  => I18N::translate('Legal notice (Impressum)'),
                'text'   => I18N::translate('This website is operated by the following person, who is also responsible for its content:'),
                'fields' => ['impressum_name', 'impressum_email', 'impressum_address', 'impressum_phone'],
            ],
            'mentions_legales' => [
                'title'  => I18N::translate('Legal notice (Mentions légales)'),
                'text'   => I18N::translate('This website is published by:'),
                'fields' => ['ml_name', 'ml_email', 'ml_address', 'ml_phone', 'ml_hosting'],
            ],
            'dpo' => [
                'title'  => I18N::translate('Data protection officer'),
                'text'   => I18N::translate('Our data protection officer can be contacted at:'),
                'fields' => ['dpo_name', 'dpo_email'],
            ],
            'children' => [
                'title'  => I18N::translate('Children\'s data'),
                'text'   => I18N::translate('This website may contain data about minors, derived from public records such as birth registers, for the purpose of historical and genealogical research. We do not collect personal data directly from children.'),
                'fields' => ['children_age'],
            ],
            'retention' => [
                'title'  => I18N::translate('Data retention'),
                'text'   => I18N::translate('Genealogical data is retained indefinitely, as the purpose of this website is to preserve historical and family-history research. User account data is retained for as long as the account is active.'),
                'fields' => [],
            ],
            'transfers' => [
                'title'  => I18N::translate('International transfers'),
                'text'   => I18N::translate('Your personal data may be transferred to, and processed in, countries other than the country in which you reside. We take appropriate safeguards to protect your data during such transfers.'),
                'fields' => [],
            ],
            'breach' => [
                'title'  => I18N::translate('Breach notification'),
                'text'   => I18N::translate('In the event of a personal data breach that poses a risk to your rights, we will notify the relevant supervisory authority and, where required, the affected individuals.'),
                'fields' => [],
            ],
            'complaint' => [
                'title'  => I18N::translate('Right to complain'),
                'text'   => I18N::translate('You have the right to lodge a complaint with a supervisory authority if you believe your data has been processed unlawfully.'),
                'fields' => ['authority_name', 'authority_url'],
            ],
            'data_localization' => [
                'title'  => I18N::translate('Data storage location'),
                'text'   => I18N::translate('Personal data is stored on servers located in:'),
                'fields' => ['server_country'],
            ],
            'custom' => [
                'title'  => I18N::translate('Custom statement'),
                'text'   => '',
                'fields' => ['custom_title', 'custom_body'],
            ],
        ];
    }

    /**
     * Map of jurisdictions to their required statement keys.
     *
     * @return array<string,array{label:string,statements:list<string>}>
     */
    public static function jurisdictions(): array
    {
        return [
            'DE-AT' => [
                'label'      => I18N::translate('Germany / Austria'),
                'statements' => ['purpose', 'lawful_basis', 'data_collected', 'public_records', 'cookies', 'analytics', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'impressum', 'children', 'retention', 'transfers', 'breach', 'complaint'],
            ],
            'FR' => [
                'label'      => I18N::translate('France'),
                'statements' => ['purpose', 'lawful_basis', 'data_collected', 'public_records', 'cookies', 'analytics', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'mentions_legales', 'children', 'retention', 'transfers', 'breach', 'complaint'],
            ],
            'EU' => [
                'label'      => I18N::translate('European Union / EEA / United Kingdom'),
                'statements' => ['purpose', 'lawful_basis', 'data_collected', 'public_records', 'cookies', 'analytics', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'children', 'retention', 'transfers', 'breach', 'complaint'],
            ],
            'CH' => [
                'label'      => I18N::translate('Switzerland'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'cookies', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'retention', 'transfers', 'breach'],
            ],
            'US-CA' => [
                'label'      => I18N::translate('United States — CA'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'cookies', 'analytics', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'do_not_sell', 'controller', 'children', 'retention'],
            ],
            'US-STATE' => [
                'label'      => I18N::translate('United States — CO, CT, DE, IN, IA, KY, MD, MN, MT, NE, NH, NJ, OR, RI, TN, TX, UT, VA'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'cookies', 'analytics', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'children', 'retention'],
            ],
            'US' => [
                'label'      => I18N::translate('United States'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'cookies', 'analytics', 'third_party_services', 'controller', 'children', 'retention'],
            ],
            'CA' => [
                'label'      => I18N::translate('Canada'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'cookies', 'third_party_services', 'rights_access', 'rights_correction', 'controller', 'retention', 'breach'],
            ],
            'AU-NZ' => [
                'label'      => I18N::translate('Australia / New Zealand'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'third_party_services', 'rights_access', 'rights_correction', 'controller', 'retention', 'transfers', 'breach'],
            ],
            'BR' => [
                'label'      => I18N::translate('Brazil'),
                'statements' => ['purpose', 'lawful_basis', 'data_collected', 'public_records', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'dpo', 'retention', 'transfers', 'breach'],
            ],
            'JP' => [
                'label'      => I18N::translate('Japan'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'transfers', 'breach'],
            ],
            'KR' => [
                'label'      => I18N::translate('South Korea'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'dpo', 'retention', 'transfers', 'breach'],
            ],
            'IN' => [
                'label'      => I18N::translate('India'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'children', 'retention', 'breach'],
            ],
            'CN' => [
                'label'      => I18N::translate('China'),
                'statements' => ['purpose', 'lawful_basis', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'retention', 'transfers', 'breach', 'data_localization'],
            ],
            'SG' => [
                'label'      => I18N::translate('Singapore'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'controller', 'dpo', 'retention', 'transfers', 'breach'],
            ],
            'TH' => [
                'label'      => I18N::translate('Thailand'),
                'statements' => ['purpose', 'lawful_basis', 'data_collected', 'public_records', 'cookies', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'retention', 'transfers', 'breach'],
            ],
            'ZA' => [
                'label'      => I18N::translate('South Africa'),
                'statements' => ['purpose', 'data_collected', 'public_records', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'retention', 'transfers', 'breach', 'complaint'],
            ],
            'LATAM' => [
                'label'      => I18N::translate('Argentina, Colombia, Mexico, Chile, Uruguay, Peru, Israel'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'transfers'],
            ],
            'AF-PH' => [
                'label'      => I18N::translate('Philippines, Kenya, Nigeria, Ghana, Egypt'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'dpo', 'transfers', 'breach'],
            ],
            'RU' => [
                'label'      => I18N::translate('Russia'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'retention', 'transfers', 'breach', 'data_localization'],
            ],
            'SA' => [
                'label'      => I18N::translate('Saudi Arabia'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'retention', 'breach', 'data_localization'],
            ],
            'MEA-SEA' => [
                'label'      => I18N::translate('Turkey, UAE, Indonesia, Malaysia, Vietnam'),
                'statements' => ['purpose', 'data_collected', 'third_party_services', 'rights_access', 'rights_correction', 'rights_removal', 'controller', 'transfers', 'breach'],
            ],
        ];
    }

    /**
     * Dependency injection.
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * How should this module be labelled on tabs, footers, etc.?
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Privacy policy');
    }

    public function description(): string
    {
        /* I18N: Description of the "Privacy policy" module */
        return I18N::translate('Show a privacy/data-protection policy based on your jurisdiction.');
    }

    /**
     * The default position for this footer.  It can be changed in the control panel.
     */
    public function defaultFooterOrder(): int
    {
        return 4;
    }

    /**
     * A footer, to be added at the bottom of every page.
     */
    public function getFooter(ServerRequestInterface $request): string
    {
        $tree = Validator::attributes($request)->treeOptional();

        if ($tree === null) {
            return '';
        }

        $user      = Validator::attributes($request)->user();
        $analytics = $this->analyticsModules($tree, $user);
        $needs_consent = $analytics->filter(
            static fn (ModuleAnalyticsInterface $module): bool => $module->analyticsNeedsConsent()
        );

        return view('modules/privacy-policy/footer', [
            'tree'           => $tree,
            'analytics'      => $analytics,
            'needs_consent'  => $needs_consent,
        ]);
    }

    /**
     * Display the public privacy policy page.
     */
    public function getPageAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        $enabled_keys = $this->enabledStatementKeys();
        $all          = self::allStatements();
        $statements   = [];

        foreach ($enabled_keys as $key) {
            if (isset($all[$key])) {
                $statements[$key] = $all[$key];
            }
        }

        // Override the custom statement title/text with user-supplied values.
        if (isset($statements['custom'])) {
            $custom_title = $this->getPreference('custom_title', '');
            $custom_body  = $this->getPreference('custom_body', '');

            if ($custom_title !== '') {
                $statements['custom']['title'] = $custom_title;
            }
            if ($custom_body !== '') {
                $statements['custom']['text'] = $custom_body;
            }
        }

        return $this->viewResponse('modules/privacy-policy/page', [
            'title'              => I18N::translate('Privacy policy'),
            'tree'               => $tree,
            'statements'         => $statements,
            'controller_name'    => $this->getPreference('controller_name', ''),
            'controller_email'   => $this->getPreference('controller_email', ''),
            'controller_address' => $this->getPreference('controller_address', ''),
            'impressum_name'     => $this->getPreference('impressum_name', ''),
            'impressum_email'    => $this->getPreference('impressum_email', ''),
            'impressum_address'  => $this->getPreference('impressum_address', ''),
            'impressum_phone'    => $this->getPreference('impressum_phone', ''),
            'ml_name'            => $this->getPreference('ml_name', ''),
            'ml_email'           => $this->getPreference('ml_email', ''),
            'ml_address'         => $this->getPreference('ml_address', ''),
            'ml_phone'           => $this->getPreference('ml_phone', ''),
            'ml_hosting'         => $this->getPreference('ml_hosting', ''),
            'dpo_name'           => $this->getPreference('dpo_name', ''),
            'dpo_email'          => $this->getPreference('dpo_email', ''),
            'children_age'       => $this->getPreference('children_age', ''),
            'authority_name'     => $this->getPreference('authority_name', ''),
            'authority_url'      => $this->getPreference('authority_url', ''),
            'server_country'     => $this->getPreference('server_country', ''),
            'analytics'          => $this->analyticsModules($tree, $user),
            'map_providers'      => $this->mapProviderModules($tree, $user),
            'geo_providers'      => $this->geoLocationModules($tree, $user),
            'cookies'            => $request->getCookieParams(),
        ]);
    }

    /**
     * Display the admin configuration form.
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('modules/privacy-policy/admin', [
            'title'              => $this->title(),
            'jurisdictions'      => self::jurisdictions(),
            'all_statements'     => self::allStatements(),
            'jurisdiction'       => $this->getPreference('jurisdiction', ''),
            'enabled'            => $this->enabledStatementKeys(),
            'controller_name'    => $this->getPreference('controller_name', ''),
            'controller_email'   => $this->getPreference('controller_email', ''),
            'controller_address' => $this->getPreference('controller_address', ''),
            'impressum_name'     => $this->getPreference('impressum_name', ''),
            'impressum_email'    => $this->getPreference('impressum_email', ''),
            'impressum_address'  => $this->getPreference('impressum_address', ''),
            'impressum_phone'    => $this->getPreference('impressum_phone', ''),
            'ml_name'            => $this->getPreference('ml_name', ''),
            'ml_email'           => $this->getPreference('ml_email', ''),
            'ml_address'         => $this->getPreference('ml_address', ''),
            'ml_phone'           => $this->getPreference('ml_phone', ''),
            'ml_hosting'         => $this->getPreference('ml_hosting', ''),
            'dpo_name'           => $this->getPreference('dpo_name', ''),
            'dpo_email'          => $this->getPreference('dpo_email', ''),
            'children_age'       => $this->getPreference('children_age', '16'),
            'authority_name'     => $this->getPreference('authority_name', ''),
            'authority_url'      => $this->getPreference('authority_url', ''),
            'server_country'     => $this->getPreference('server_country', ''),
            'custom_title'       => $this->getPreference('custom_title', ''),
            'custom_body'        => $this->getPreference('custom_body', ''),
        ]);
    }

    /**
     * Save admin configuration.
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $jurisdiction = Validator::parsedBody($request)->string('jurisdiction');
        $statements   = Validator::parsedBody($request)->array('statements');

        $this->setPreference('jurisdiction', $jurisdiction);
        $this->setPreference('statements', implode(',', $statements));
        $this->setPreference('controller_name', Validator::parsedBody($request)->string('controller_name'));
        $this->setPreference('controller_email', Validator::parsedBody($request)->string('controller_email'));
        $this->setPreference('controller_address', Validator::parsedBody($request)->string('controller_address'));
        $this->setPreference('impressum_name', Validator::parsedBody($request)->string('impressum_name'));
        $this->setPreference('impressum_email', Validator::parsedBody($request)->string('impressum_email'));
        $this->setPreference('impressum_address', Validator::parsedBody($request)->string('impressum_address'));
        $this->setPreference('impressum_phone', Validator::parsedBody($request)->string('impressum_phone'));
        $this->setPreference('ml_name', Validator::parsedBody($request)->string('ml_name'));
        $this->setPreference('ml_email', Validator::parsedBody($request)->string('ml_email'));
        $this->setPreference('ml_address', Validator::parsedBody($request)->string('ml_address'));
        $this->setPreference('ml_phone', Validator::parsedBody($request)->string('ml_phone'));
        $this->setPreference('ml_hosting', Validator::parsedBody($request)->string('ml_hosting'));
        $this->setPreference('dpo_name', Validator::parsedBody($request)->string('dpo_name'));
        $this->setPreference('dpo_email', Validator::parsedBody($request)->string('dpo_email'));
        $this->setPreference('children_age', Validator::parsedBody($request)->string('children_age'));
        $this->setPreference('authority_name', Validator::parsedBody($request)->string('authority_name'));
        $this->setPreference('authority_url', Validator::parsedBody($request)->string('authority_url'));
        $this->setPreference('server_country', Validator::parsedBody($request)->string('server_country'));
        $this->setPreference('custom_title', Validator::parsedBody($request)->string('custom_title'));
        $this->setPreference('custom_body', Validator::parsedBody($request)->string('custom_body'));

        $message = I18N::translate('The preferences for the module “%s” have been updated.', $this->title());
        FlashMessages::addMessage($message, 'success');

        return redirect($this->getConfigLink());
    }

    /**
     * Get the list of currently enabled statement keys.
     *
     * @return list<string>
     */
    private function enabledStatementKeys(): array
    {
        $stored = $this->getPreference('statements', '');

        if ($stored === '') {
            return [];
        }

        return explode(',', $stored);
    }

    /**
     * Find active analytics modules for a tree.
     *
     * @return Collection<int,ModuleAnalyticsInterface>
     */
    private function analyticsModules(Tree $tree, UserInterface $user): Collection
    {
        return $this->module_service
            ->findByComponent(ModuleAnalyticsInterface::class, $tree, $user)
            ->filter(static fn (ModuleAnalyticsInterface $module): bool => $module->isTracker());
    }

    /**
     * Find active map provider modules for a tree.
     *
     * @return Collection<int,ModuleMapProviderInterface>
     */
    private function mapProviderModules(Tree $tree, UserInterface $user): Collection
    {
        return $this->module_service
            ->findByComponent(ModuleMapProviderInterface::class, $tree, $user);
    }

    /**
     * Find active geocoding modules for a tree.
     *
     * @return Collection<int,ModuleMapGeoLocationInterface>
     */
    private function geoLocationModules(Tree $tree, UserInterface $user): Collection
    {
        return $this->module_service
            ->findByComponent(ModuleMapGeoLocationInterface::class, $tree, $user);
    }
}
