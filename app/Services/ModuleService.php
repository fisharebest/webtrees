<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Closure;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AhnentafelReportModule;
use Fisharebest\Webtrees\Module\AlbumModule;
use Fisharebest\Webtrees\Module\AncestorsChartModule;
use Fisharebest\Webtrees\Module\AustrianHistoricEvents;
use Fisharebest\Webtrees\Module\AustrianPresidents;
use Fisharebest\Webtrees\Module\BingMaps;
use Fisharebest\Webtrees\Module\BingWebmasterToolsModule;
use Fisharebest\Webtrees\Module\BirthDeathMarriageReportModule;
use Fisharebest\Webtrees\Module\BirthReportModule;
use Fisharebest\Webtrees\Module\BranchesListModule;
use Fisharebest\Webtrees\Module\BritishMonarchs;
use Fisharebest\Webtrees\Module\BritishPrimeMinisters;
use Fisharebest\Webtrees\Module\BritishSocialHistory;
use Fisharebest\Webtrees\Module\CalendarMenuModule;
use Fisharebest\Webtrees\Module\CemeteryReportModule;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Module\ChangeReportModule;
use Fisharebest\Webtrees\Module\ChartsBlockModule;
use Fisharebest\Webtrees\Module\ChartsMenuModule;
use Fisharebest\Webtrees\Module\CheckForNewVersion;
use Fisharebest\Webtrees\Module\CkeditorModule;
use Fisharebest\Webtrees\Module\ClippingsCartModule;
use Fisharebest\Webtrees\Module\CloudsTheme;
use Fisharebest\Webtrees\Module\ColorsTheme;
use Fisharebest\Webtrees\Module\CompactTreeChartModule;
use Fisharebest\Webtrees\Module\ContactsFooterModule;
use Fisharebest\Webtrees\Module\CustomCssJsModule;
use Fisharebest\Webtrees\Module\CzechMonarchsAndPresidents;
use Fisharebest\Webtrees\Module\DeathReportModule;
use Fisharebest\Webtrees\Module\DescendancyChartModule;
use Fisharebest\Webtrees\Module\DescendancyModule;
use Fisharebest\Webtrees\Module\DescendancyReportModule;
use Fisharebest\Webtrees\Module\DutchMonarchs;
use Fisharebest\Webtrees\Module\DutchPrimeMinisters;
use Fisharebest\Webtrees\Module\EsriMaps;
use Fisharebest\Webtrees\Module\FabTheme;
use Fisharebest\Webtrees\Module\FactSourcesReportModule;
use Fisharebest\Webtrees\Module\FamilyBookChartModule;
use Fisharebest\Webtrees\Module\FamilyGroupReportModule;
use Fisharebest\Webtrees\Module\FamilyListModule;
use Fisharebest\Webtrees\Module\FamilyNavigatorModule;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\FamilyTreeNewsModule;
use Fisharebest\Webtrees\Module\FamilyTreeStatisticsModule;
use Fisharebest\Webtrees\Module\FanChartModule;
use Fisharebest\Webtrees\Module\FixCemeteryTag;
use Fisharebest\Webtrees\Module\FixDuplicateLinks;
use Fisharebest\Webtrees\Module\FixMissingDeaths;
use Fisharebest\Webtrees\Module\FixNameSlashesAndSpaces;
use Fisharebest\Webtrees\Module\FixNameTags;
use Fisharebest\Webtrees\Module\FixPlaceNames;
use Fisharebest\Webtrees\Module\FixPrimaryTag;
use Fisharebest\Webtrees\Module\FixSearchAndReplace;
use Fisharebest\Webtrees\Module\FixWtObjeSortTag;
use Fisharebest\Webtrees\Module\FrenchHistory;
use Fisharebest\Webtrees\Module\FrequentlyAskedQuestionsModule;
use Fisharebest\Webtrees\Module\GeonamesAutocomplete;
use Fisharebest\Webtrees\Module\GoogleAnalyticsModule;
use Fisharebest\Webtrees\Module\GoogleMaps;
use Fisharebest\Webtrees\Module\GoogleWebmasterToolsModule;
use Fisharebest\Webtrees\Module\HereMaps;
use Fisharebest\Webtrees\Module\HitCountFooterModule;
use Fisharebest\Webtrees\Module\HourglassChartModule;
use Fisharebest\Webtrees\Module\HtmlBlockModule;
use Fisharebest\Webtrees\Module\IndividualFactsTabModule;
use Fisharebest\Webtrees\Module\IndividualFamiliesReportModule;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Module\IndividualMetadataModule;
use Fisharebest\Webtrees\Module\IndividualReportModule;
use Fisharebest\Webtrees\Module\InteractiveTreeModule;
use Fisharebest\Webtrees\Module\LanguageAfrikaans;
use Fisharebest\Webtrees\Module\LanguageAlbanian;
use Fisharebest\Webtrees\Module\LanguageArabic;
use Fisharebest\Webtrees\Module\LanguageBosnian;
use Fisharebest\Webtrees\Module\LanguageBulgarian;
use Fisharebest\Webtrees\Module\LanguageCatalan;
use Fisharebest\Webtrees\Module\LanguageChineseSimplified;
use Fisharebest\Webtrees\Module\LanguageChineseTraditional;
use Fisharebest\Webtrees\Module\LanguageCroatian;
use Fisharebest\Webtrees\Module\LanguageCzech;
use Fisharebest\Webtrees\Module\LanguageDanish;
use Fisharebest\Webtrees\Module\LanguageDivehi;
use Fisharebest\Webtrees\Module\LanguageDutch;
use Fisharebest\Webtrees\Module\LanguageEnglishAustralia;
use Fisharebest\Webtrees\Module\LanguageEnglishGreatBritain;
use Fisharebest\Webtrees\Module\LanguageEnglishUnitedStates;
use Fisharebest\Webtrees\Module\LanguageEstonian;
use Fisharebest\Webtrees\Module\LanguageFaroese;
use Fisharebest\Webtrees\Module\LanguageFarsi;
use Fisharebest\Webtrees\Module\LanguageFinnish;
use Fisharebest\Webtrees\Module\LanguageFrench;
use Fisharebest\Webtrees\Module\LanguageFrenchCanada;
use Fisharebest\Webtrees\Module\LanguageGalician;
use Fisharebest\Webtrees\Module\LanguageGeorgian;
use Fisharebest\Webtrees\Module\LanguageGerman;
use Fisharebest\Webtrees\Module\LanguageGreek;
use Fisharebest\Webtrees\Module\LanguageHebrew;
use Fisharebest\Webtrees\Module\LanguageHindi;
use Fisharebest\Webtrees\Module\LanguageHungarian;
use Fisharebest\Webtrees\Module\LanguageIcelandic;
use Fisharebest\Webtrees\Module\LanguageIndonesian;
use Fisharebest\Webtrees\Module\LanguageItalian;
use Fisharebest\Webtrees\Module\LanguageJapanese;
use Fisharebest\Webtrees\Module\LanguageJavanese;
use Fisharebest\Webtrees\Module\LanguageKazhak;
use Fisharebest\Webtrees\Module\LanguageKorean;
use Fisharebest\Webtrees\Module\LanguageKurdish;
use Fisharebest\Webtrees\Module\LanguageLatvian;
use Fisharebest\Webtrees\Module\LanguageLingala;
use Fisharebest\Webtrees\Module\LanguageLithuanian;
use Fisharebest\Webtrees\Module\LanguageMalay;
use Fisharebest\Webtrees\Module\LanguageMaori;
use Fisharebest\Webtrees\Module\LanguageMarathi;
use Fisharebest\Webtrees\Module\LanguageNepalese;
use Fisharebest\Webtrees\Module\LanguageNorwegianBokmal;
use Fisharebest\Webtrees\Module\LanguageNorwegianNynorsk;
use Fisharebest\Webtrees\Module\LanguageOccitan;
use Fisharebest\Webtrees\Module\LanguagePolish;
use Fisharebest\Webtrees\Module\LanguagePortuguese;
use Fisharebest\Webtrees\Module\LanguagePortugueseBrazil;
use Fisharebest\Webtrees\Module\LanguageRomanian;
use Fisharebest\Webtrees\Module\LanguageRussian;
use Fisharebest\Webtrees\Module\LanguageSerbian;
use Fisharebest\Webtrees\Module\LanguageSerbianLatin;
use Fisharebest\Webtrees\Module\LanguageSlovakian;
use Fisharebest\Webtrees\Module\LanguageSlovenian;
use Fisharebest\Webtrees\Module\LanguageSpanish;
use Fisharebest\Webtrees\Module\LanguageSundanese;
use Fisharebest\Webtrees\Module\LanguageSwahili;
use Fisharebest\Webtrees\Module\LanguageSwedish;
use Fisharebest\Webtrees\Module\LanguageTagalog;
use Fisharebest\Webtrees\Module\LanguageTamil;
use Fisharebest\Webtrees\Module\LanguageTatar;
use Fisharebest\Webtrees\Module\LanguageThai;
use Fisharebest\Webtrees\Module\LanguageTurkish;
use Fisharebest\Webtrees\Module\LanguageUkranian;
use Fisharebest\Webtrees\Module\LanguageUrdu;
use Fisharebest\Webtrees\Module\LanguageVietnamese;
use Fisharebest\Webtrees\Module\LanguageWelsh;
use Fisharebest\Webtrees\Module\LanguageYiddish;
use Fisharebest\Webtrees\Module\LifespansChartModule;
use Fisharebest\Webtrees\Module\ListsMenuModule;
use Fisharebest\Webtrees\Module\LocationListModule;
use Fisharebest\Webtrees\Module\LoggedInUsersModule;
use Fisharebest\Webtrees\Module\LoginBlockModule;
use Fisharebest\Webtrees\Module\LowCountriesRulers;
use Fisharebest\Webtrees\Module\MapBox;
use Fisharebest\Webtrees\Module\MapGeoLocationGeonames;
use Fisharebest\Webtrees\Module\MapGeoLocationNominatim;
use Fisharebest\Webtrees\Module\MapGeoLocationOpenRouteService;
use Fisharebest\Webtrees\Module\MapLinkBing;
use Fisharebest\Webtrees\Module\MapLinkGoogle;
use Fisharebest\Webtrees\Module\MapLinkOpenStreetMap;
use Fisharebest\Webtrees\Module\MarriageReportModule;
use Fisharebest\Webtrees\Module\MatomoAnalyticsModule;
use Fisharebest\Webtrees\Module\MediaListModule;
use Fisharebest\Webtrees\Module\MediaTabModule;
use Fisharebest\Webtrees\Module\MinimalTheme;
use Fisharebest\Webtrees\Module\MissingFactsReportModule;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\ModuleMapAutocompleteInterface;
use Fisharebest\Webtrees\Module\ModuleMapGeoLocationInterface;
use Fisharebest\Webtrees\Module\ModuleMapLinkInterface;
use Fisharebest\Webtrees\Module\ModuleMapProviderInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleShareInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\NoteListModule;
use Fisharebest\Webtrees\Module\NotesTabModule;
use Fisharebest\Webtrees\Module\OccupationReportModule;
use Fisharebest\Webtrees\Module\OnThisDayModule;
use Fisharebest\Webtrees\Module\OpenRouteServiceAutocomplete;
use Fisharebest\Webtrees\Module\OpenStreetMap;
use Fisharebest\Webtrees\Module\OrdnanceSurveyHistoricMaps;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\Module\PedigreeMapModule;
use Fisharebest\Webtrees\Module\PedigreeReportModule;
use Fisharebest\Webtrees\Module\PlaceHierarchyListModule;
use Fisharebest\Webtrees\Module\PlacesModule;
use Fisharebest\Webtrees\Module\PoweredByWebtreesModule;
use Fisharebest\Webtrees\Module\PrivacyPolicy;
use Fisharebest\Webtrees\Module\RecentChangesModule;
use Fisharebest\Webtrees\Module\RedirectLegacyUrlsModule;
use Fisharebest\Webtrees\Module\RelatedIndividualsReportModule;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Module\RelativesTabModule;
use Fisharebest\Webtrees\Module\ReportsMenuModule;
use Fisharebest\Webtrees\Module\RepositoryListModule;
use Fisharebest\Webtrees\Module\ResearchTaskModule;
use Fisharebest\Webtrees\Module\ReviewChangesModule;
use Fisharebest\Webtrees\Module\SearchMenuModule;
use Fisharebest\Webtrees\Module\ShareAnniversaryModule;
use Fisharebest\Webtrees\Module\ShareUrlModule;
use Fisharebest\Webtrees\Module\SiteMapModule;
use Fisharebest\Webtrees\Module\SlideShowModule;
use Fisharebest\Webtrees\Module\SourceListModule;
use Fisharebest\Webtrees\Module\SourcesTabModule;
use Fisharebest\Webtrees\Module\StatcounterModule;
use Fisharebest\Webtrees\Module\StatisticsChartModule;
use Fisharebest\Webtrees\Module\StoriesModule;
use Fisharebest\Webtrees\Module\SubmitterListModule;
use Fisharebest\Webtrees\Module\ThemeSelectModule;
use Fisharebest\Webtrees\Module\TimelineChartModule;
use Fisharebest\Webtrees\Module\TopGivenNamesModule;
use Fisharebest\Webtrees\Module\TopPageViewsModule;
use Fisharebest\Webtrees\Module\TopSurnamesModule;
use Fisharebest\Webtrees\Module\TreesMenuModule;
use Fisharebest\Webtrees\Module\UpcomingAnniversariesModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Module\UserJournalModule;
use Fisharebest\Webtrees\Module\UserMessagesModule;
use Fisharebest\Webtrees\Module\UserWelcomeModule;
use Fisharebest\Webtrees\Module\USPresidents;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Module\WelcomeBlockModule;
use Fisharebest\Webtrees\Module\XeneaTheme;
use Fisharebest\Webtrees\Module\YahrzeitModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Throwable;

use function app;
use function assert;
use function basename;
use function dirname;
use function glob;
use function is_object;
use function str_contains;
use function strlen;

use const GLOB_NOSORT;

/**
 * Functions for managing and maintaining modules.
 */
class ModuleService
{
    // Components are pieces of user-facing functionality, are managed together in the control panel.
    private const COMPONENTS = [
        ModuleAnalyticsInterface::class,
        ModuleBlockInterface::class,
        ModuleChartInterface::class,
        ModuleDataFixInterface::class,
        ModuleFooterInterface::class,
        ModuleHistoricEventsInterface::class,
        ModuleLanguageInterface::class,
        ModuleListInterface::class,
        ModuleMapAutocompleteInterface::class,
        ModuleMapLinkInterface::class,
        ModuleMapProviderInterface::class,
        ModuleMapGeoLocationInterface::class,
        ModuleMenuInterface::class,
        ModuleReportInterface::class,
        ModuleShareInterface::class,
        ModuleSidebarInterface::class,
        ModuleTabInterface::class,
        ModuleThemeInterface::class,
    ];

    // Components that have access levels.
    private const COMPONENTS_WITH_ACCESS = [
        ModuleBlockInterface::class,
        ModuleChartInterface::class,
        ModuleListInterface::class,
        ModuleMenuInterface::class,
        ModuleReportInterface::class,
        ModuleSidebarInterface::class,
        ModuleTabInterface::class,
    ];

    // Components that are displayed in a particular order
    private const COMPONENTS_WITH_SORT = [
        ModuleFooterInterface::class,
        ModuleMenuInterface::class,
        ModuleSidebarInterface::class,
        ModuleTabInterface::class,
    ];

    // Array keys are module names, and should match module names from earlier versions of webtrees.
    private const CORE_MODULES = [
        'GEDFact_assistant'       => CensusAssistantModule::class,
        'ahnentafel_report'       => AhnentafelReportModule::class,
        'ancestors_chart'         => AncestorsChartModule::class,
        'austrian-history'        => AustrianHistoricEvents::class,
        'austrian-presidents'     => AustrianPresidents::class,
        'bdm_report'              => BirthDeathMarriageReportModule::class,
        'bing-maps'               => BingMaps::class,
        'bing-webmaster-tools'    => BingWebmasterToolsModule::class,
        'birth_report'            => BirthReportModule::class,
        'branches_list'           => BranchesListModule::class,
        'british-monarchs'        => BritishMonarchs::class,
        'british-prime-ministers' => BritishPrimeMinisters::class,
        'british-social-history'  => BritishSocialHistory::class,
        'calendar-menu'           => CalendarMenuModule::class,
        'cemetery_report'         => CemeteryReportModule::class,
        'change_report'           => ChangeReportModule::class,
        'charts'                  => ChartsBlockModule::class,
        'charts-menu'             => ChartsMenuModule::class,
        'check-for-new-version'   => CheckForNewVersion::class,
        'ckeditor'                => CkeditorModule::class,
        'clippings'               => ClippingsCartModule::class,
        'clouds'                  => CloudsTheme::class,
        'colors'                  => ColorsTheme::class,
        'compact-chart'           => CompactTreeChartModule::class,
        'contact-links'           => ContactsFooterModule::class,
        'czech-leaders'           => CzechMonarchsAndPresidents::class,
        'custom-css-js'           => CustomCssJsModule::class,
        'death_report'            => DeathReportModule::class,
        'descendancy'             => DescendancyModule::class,
        'descendancy_chart'       => DescendancyChartModule::class,
        'descendancy_report'      => DescendancyReportModule::class,
        'dutch_monarchs'          => DutchMonarchs::class,
        'dutch_prime_ministers'   => DutchPrimeMinisters::class,
        'esri-maps'               => EsriMaps::class,
        'extra_info'              => IndividualMetadataModule::class,
        'fab'                     => FabTheme::class,
        'fact_sources'            => FactSourcesReportModule::class,
        'family_book_chart'       => FamilyBookChartModule::class,
        'family_group_report'     => FamilyGroupReportModule::class,
        'family_list'             => FamilyListModule::class,
        'family_nav'              => FamilyNavigatorModule::class,
        'fan_chart'               => FanChartModule::class,
        'faq'                     => FrequentlyAskedQuestionsModule::class,
        'french-history'          => FrenchHistory::class,
        'fix-add-death'           => FixMissingDeaths::class,
        'fix-ceme-tag'            => FixCemeteryTag::class,
        'fix-duplicate-links'     => FixDuplicateLinks::class,
        'fix-name-slashes-spaces' => FixNameSlashesAndSpaces::class,
        'fix-name-tags'           => FixNameTags::class,
        'fix-place-names'         => FixPlaceNames::class,
        'fix-prim-tag'            => FixPrimaryTag::class,
        'fix-search-and-replace'  => FixSearchAndReplace::class,
        'fix-wt-obje-sort'        => FixWtObjeSortTag::class,
        'gedcom_block'            => WelcomeBlockModule::class,
        'gedcom_favorites'        => FamilyTreeFavoritesModule::class,
        'gedcom_news'             => FamilyTreeNewsModule::class,
        'gedcom_stats'            => FamilyTreeStatisticsModule::class,
        'geonames'                => GeonamesAutocomplete::class,
        'google-analytics'        => GoogleAnalyticsModule::class,
        'google-maps'             => GoogleMaps::class,
        'google-webmaster-tools'  => GoogleWebmasterToolsModule::class,
        'here-maps'               => HereMaps::class,
        'hit-counter'             => HitCountFooterModule::class,
        'hourglass_chart'         => HourglassChartModule::class,
        'html'                    => HtmlBlockModule::class,
        'individual_ext_report'   => IndividualFamiliesReportModule::class,
        'individual_list'         => IndividualListModule::class,
        'individual_report'       => IndividualReportModule::class,
        'language-af'             => LanguageAfrikaans::class,
        'language-ar'             => LanguageArabic::class,
        'language-bg'             => LanguageBulgarian::class,
        'language-bs'             => LanguageBosnian::class,
        'language-ca'             => LanguageCatalan::class,
        'language-cs'             => LanguageCzech::class,
        'language-cy'             => LanguageWelsh::class,
        'language-da'             => LanguageDanish::class,
        'language-de'             => LanguageGerman::class,
        'language-dv'             => LanguageDivehi::class,
        'language-el'             => LanguageGreek::class,
        'language-en-AU'          => LanguageEnglishAustralia::class,
        'language-en-GB'          => LanguageEnglishGreatBritain::class,
        'language-en-US'          => LanguageEnglishUnitedStates::class,
        'language-es'             => LanguageSpanish::class,
        'language-et'             => LanguageEstonian::class,
        'language-fa'             => LanguageFarsi::class,
        'language-fi'             => LanguageFinnish::class,
        'language-fo'             => LanguageFaroese::class,
        'language-fr'             => LanguageFrench::class,
        'language-fr-CA'          => LanguageFrenchCanada::class,
        'language-gl'             => LanguageGalician::class,
        'language-he'             => LanguageHebrew::class,
        'language-hi'             => LanguageHindi::class,
        'language-hr'             => LanguageCroatian::class,
        'language-hu'             => LanguageHungarian::class,
        'language-id'             => LanguageIndonesian::class,
        'language-is'             => LanguageIcelandic::class,
        'language-it'             => LanguageItalian::class,
        'language-ja'             => LanguageJapanese::class,
        'language-jv'             => LanguageJavanese::class,
        'language-ka'             => LanguageGeorgian::class,
        'language-kk'             => LanguageKazhak::class,
        'language-ko'             => LanguageKorean::class,
        'language-ku'             => LanguageKurdish::class,
        'language-ln'             => LanguageLingala::class,
        'language-lt'             => LanguageLithuanian::class,
        'language-lv'             => LanguageLatvian::class,
        'language-mi'             => LanguageMaori::class,
        'language-mr'             => LanguageMarathi::class,
        'language-ms'             => LanguageMalay::class,
        'language-nb'             => LanguageNorwegianBokmal::class,
        'language-ne'             => LanguageNepalese::class,
        'language-nl'             => LanguageDutch::class,
        'language-nn'             => LanguageNorwegianNynorsk::class,
        'language-oc'             => LanguageOccitan::class,
        'language-pl'             => LanguagePolish::class,
        'language-pt'             => LanguagePortuguese::class,
        'language-pt-BR'          => LanguagePortugueseBrazil::class,
        'language-ro'             => LanguageRomanian::class,
        'language-ru'             => LanguageRussian::class,
        'language-sk'             => LanguageSlovakian::class,
        'language-sl'             => LanguageSlovenian::class,
        'language-sq'             => LanguageAlbanian::class,
        'language-sr'             => LanguageSerbian::class,
        'language-sr-Latn'        => LanguageSerbianLatin::class,
        'language-su'             => LanguageSundanese::class,
        'language-sv'             => LanguageSwedish::class,
        'language-sw'             => LanguageSwahili::class,
        'language-ta'             => LanguageTamil::class,
        'language-th'             => LanguageThai::class,
        'language-tl'             => LanguageTagalog::class,
        'language-tr'             => LanguageTurkish::class,
        'language-tt'             => LanguageTatar::class,
        'language-uk'             => LanguageUkranian::class,
        'language-ur'             => LanguageUrdu::class,
        'language-vi'             => LanguageVietnamese::class,
        'language-yi'             => LanguageYiddish::class,
        'language-zh-Hans'        => LanguageChineseSimplified::class,
        'language-zh-Hant'        => LanguageChineseTraditional::class,
        'legacy-urls'             => RedirectLegacyUrlsModule::class,
        'lifespans_chart'         => LifespansChartModule::class,
        'lightbox'                => AlbumModule::class,
        'lists-menu'              => ListsMenuModule::class,
        'location_list'           => LocationListModule::class,
        'logged_in'               => LoggedInUsersModule::class,
        'login_block'             => LoginBlockModule::class,
        'low_countries_rulers'    => LowCountriesRulers::class,
        'map-link-bing'           => MapLinkBing::class,
        'map-link-google'         => MapLinkGoogle::class,
        'map-link-openstreetmap'  => MapLinkOpenStreetMap::class,
        'map-location-geonames'   => MapGeoLocationGeonames::class,
        'map-location-nominatim'  => MapGeoLocationNominatim::class,
        'map-location-ors'        => MapGeoLocationOpenRouteService::class,
        'mapbox'                  => MapBox::class,
        'marriage_report'         => MarriageReportModule::class,
        'matomo-analytics'        => MatomoAnalyticsModule::class,
        'media'                   => MediaTabModule::class,
        'media_list'              => MediaListModule::class,
        'minimal'                 => MinimalTheme::class,
        'missing_facts_report'    => MissingFactsReportModule::class,
        'notes'                   => NotesTabModule::class,
        'note_list'               => NoteListModule::class,
        'occupation_report'       => OccupationReportModule::class,
        'openrouteservice'        => OpenRouteServiceAutocomplete::class,
        'openstreetmap'           => OpenStreetMap::class,
        'osgb-historic'           => OrdnanceSurveyHistoricMaps::class,
        'pedigree-map'            => PedigreeMapModule::class,
        'pedigree_chart'          => PedigreeChartModule::class,
        'pedigree_report'         => PedigreeReportModule::class,
        'personal_facts'          => IndividualFactsTabModule::class,
        'places'                  => PlacesModule::class,
        'places_list'             => PlaceHierarchyListModule::class,
        'powered-by-webtrees'     => PoweredByWebtreesModule::class,
        'privacy-policy'          => PrivacyPolicy::class,
        'random_media'            => SlideShowModule::class,
        'recent_changes'          => RecentChangesModule::class,
        'relationships_chart'     => RelationshipsChartModule::class,
        'relative_ext_report'     => RelatedIndividualsReportModule::class,
        'relatives'               => RelativesTabModule::class,
        'reports-menu'            => ReportsMenuModule::class,
        'repository_list'         => RepositoryListModule::class,
        'review_changes'          => ReviewChangesModule::class,
        'search-menu'             => SearchMenuModule::class,
        'share-anniversary'       => ShareAnniversaryModule::class,
        'share-url'               => ShareUrlModule::class,
        'sitemap'                 => SiteMapModule::class,
        'source_list'             => SourceListModule::class,
        'sources_tab'             => SourcesTabModule::class,
        'statcounter'             => StatcounterModule::class,
        'statistics_chart'        => StatisticsChartModule::class,
        'stories'                 => StoriesModule::class,
        'submitter_list'          => SubmitterListModule::class,
        'theme_select'            => ThemeSelectModule::class,
        'timeline_chart'          => TimelineChartModule::class,
        'todays_events'           => OnThisDayModule::class,
        'todo'                    => ResearchTaskModule::class,
        'top10_givnnames'         => TopGivenNamesModule::class,
        'top10_pageviews'         => TopPageViewsModule::class,
        'top10_surnames'          => TopSurnamesModule::class,
        'tree'                    => InteractiveTreeModule::class,
        'trees-menu'              => TreesMenuModule::class,
        'upcoming_events'         => UpcomingAnniversariesModule::class,
        'us-presidents'           => USPresidents::class,
        'user_blog'               => UserJournalModule::class,
        'user_favorites'          => UserFavoritesModule::class,
        'user_messages'           => UserMessagesModule::class,
        'user_welcome'            => UserWelcomeModule::class,
        'webtrees'                => WebtreesTheme::class,
        'xenea'                   => XeneaTheme::class,
        'yahrzeit'                => YahrzeitModule::class,
    ];

    /**
     * A function to convert modules into their titles - to create option lists, etc.
     *
     * @return Closure
     */
    public function titleMapper(): Closure
    {
        return static function (ModuleInterface $module): string {
            return $module->title();
        };
    }

    /**
     * Modules which (a) provide a specific function and (b) we have permission to see.
     *
     * @template T
     * @param class-string<T> $interface
     * @param Tree            $tree
     * @param UserInterface   $user
     *
     * @return Collection<string,T&ModuleInterface>
     */
    public function findByComponent(string $interface, Tree $tree, UserInterface $user): Collection
    {
        return $this->findByInterface($interface, false, true)
            ->filter(static function (ModuleInterface $module) use ($interface, $tree, $user): bool {
                return $module->accessLevel($tree, $interface) >= Auth::accessLevel($tree, $user);
            });
    }

    /**
     * All modules which provide a specific function.
     *
     * @template T
     * @param class-string<T> $interface
     * @param bool            $include_disabled
     * @param bool            $sort
     *
     * @return Collection<string,T&ModuleInterface>
     */
    public function findByInterface(string $interface, bool $include_disabled = false, bool $sort = false): Collection
    {
        /** @var Collection<string,T&ModuleInterface> $modules */
        $modules = $this->all($include_disabled)
            ->filter($this->interfaceFilter($interface));

        switch ($interface) {
            case ModuleFooterInterface::class:
                /** @var Collection<string,T&ModuleInterface> */
                return $modules->sort($this->footerComparator());

            case ModuleMenuInterface::class:
                /** @var Collection<string,T&ModuleInterface> */
                return $modules->sort($this->menuComparator());

            case ModuleSidebarInterface::class:
                /** @var Collection<string,T&ModuleInterface> */
                return $modules->sort($this->sidebarComparator());

            case ModuleTabInterface::class:
                /** @var Collection<string,T&ModuleInterface> */
                return $modules->sort($this->tabComparator());

            default:
                if ($sort) {
                    /** @var Collection<string,T&ModuleInterface> */
                    return $modules->sort($this->moduleComparator());
                }

                return $modules;
        }
    }

    /**
     * All modules.
     *
     * @param bool $include_disabled
     *
     * @return Collection<string,ModuleInterface>
     */
    public function all(bool $include_disabled = false): Collection
    {
        return Registry::cache()->array()->remember('all-modules', function (): Collection {
            // Modules have a default status, order etc.
            // We can override these from database settings.
            $module_info = DB::table('module')
                ->get()
                ->mapWithKeys(static function (object $row): array {
                    return [$row->module_name => $row];
                });

            return $this->coreModules()
                ->merge($this->customModules())
                ->map(static function (ModuleInterface $module) use ($module_info): ModuleInterface {
                    $info = $module_info->get($module->name());

                    if (is_object($info)) {
                        $module->setEnabled($info->status === 'enabled');

                        if ($module instanceof ModuleFooterInterface && $info->footer_order !== null) {
                            $module->setFooterOrder((int) $info->footer_order);
                        }

                        if ($module instanceof ModuleMenuInterface && $info->menu_order !== null) {
                            $module->setMenuOrder((int) $info->menu_order);
                        }

                        if ($module instanceof ModuleSidebarInterface && $info->sidebar_order !== null) {
                            $module->setSidebarOrder((int) $info->sidebar_order);
                        }

                        if ($module instanceof ModuleTabInterface && $info->tab_order !== null) {
                            $module->setTabOrder((int) $info->tab_order);
                        }
                    } else {
                        $module->setEnabled($module->isEnabledByDefault());

                        DB::table('module')->insert([
                            'module_name' => $module->name(),
                            'status'      => $module->isEnabled() ? 'enabled' : 'disabled',
                        ]);
                    }

                    return $module;
                });
        })->filter($this->enabledFilter($include_disabled));
    }

    /**
     * All core modules in the system.
     *
     * @return Collection<string,ModuleInterface>
     */
    private function coreModules(): Collection
    {
        return Collection::make(self::CORE_MODULES)
            ->map(static function (string $class, string $name): ModuleInterface {
                $module = app($class);
                assert($module instanceof ModuleInterface);

                $module->setName($name);

                return $module;
            });
    }

    /**
     * All custom modules in the system.  Custom modules are defined in modules_v4/
     *
     * @return Collection<string,ModuleCustomInterface>
     */
    private function customModules(): Collection
    {
        $pattern   = Webtrees::MODULES_DIR . '*/module.php';
        $filenames = glob($pattern, GLOB_NOSORT);

        return Collection::make($filenames)
            ->filter(static function (string $filename): bool {
                // Special characters will break PHP variable names.
                // This also allows us to ignore modules called "foo.example" and "foo.disable"
                $module_name = basename(dirname($filename));

                foreach (['.', ' ', '[', ']'] as $character) {
                    if (str_contains($module_name, $character)) {
                        return false;
                    }
                }

                return strlen($module_name) <= 30;
            })
            ->map(static function (string $filename): ?ModuleCustomInterface {
                $module = self::load($filename);

                if ($module instanceof ModuleCustomInterface) {
                    $module->setName('_' . basename(dirname($filename)) . '_');

                    return $module;
                }

                return null;
            })
            ->filter()
            ->mapWithKeys(static function (ModuleCustomInterface $module): array {
                return [$module->name() => $module];
            });
    }

    /**
     * Load a custom module in a static scope, to prevent it from modifying local or object variables.
     *
     * @param string $filename
     *
     * @return ModuleInterface|null
     */
    private static function load(string $filename): ?ModuleInterface
    {
        try {
            return include $filename;
        } catch (Throwable $exception) {
            $module_name = basename(dirname($filename));
            $message     = 'Fatal error in module: ' . $module_name . '<br>' . $exception;
            FlashMessages::addMessage($message, 'danger');
        }

        return null;
    }

    /**
     * A function filter modules by enabled/disabled
     *
     * @param bool $include_disabled
     *
     * @return Closure(ModuleInterface): bool
     */
    private function enabledFilter(bool $include_disabled): Closure
    {
        return static function (ModuleInterface $module) use ($include_disabled): bool {
            return $include_disabled || $module->isEnabled();
        };
    }

    /**
     * A function filter modules by type
     *
     * @param class-string $interface
     *
     * @return Closure(ModuleInterface): bool
     */
    private function interfaceFilter(string $interface): Closure
    {
        return static function (ModuleInterface $module) use ($interface): bool {
            return $module instanceof $interface;
        };
    }

    /**
     * A function to sort footers
     *
     * @return Closure(ModuleFooterInterface, ModuleFooterInterface): int
     */
    private function footerComparator(): Closure
    {
        return static function (ModuleFooterInterface $x, ModuleFooterInterface $y): int {
            return $x->getFooterOrder() <=> $y->getFooterOrder();
        };
    }

    /**
     * A function to sort menus
     *
     * @return Closure(ModuleMenuInterface, ModuleMenuInterface): int
     */
    private function menuComparator(): Closure
    {
        return static function (ModuleMenuInterface $x, ModuleMenuInterface $y): int {
            return $x->getMenuOrder() <=> $y->getMenuOrder();
        };
    }

    /**
     * A function to sort menus
     *
     * @return Closure(ModuleSidebarInterface, ModuleSidebarInterface): int
     */
    private function sidebarComparator(): Closure
    {
        return static function (ModuleSidebarInterface $x, ModuleSidebarInterface $y): int {
            return $x->getSidebarOrder() <=> $y->getSidebarOrder();
        };
    }

    /**
     * A function to sort menus
     *
     * @return Closure(ModuleTabInterface, ModuleTabInterface): int
     */
    private function tabComparator(): Closure
    {
        return static function (ModuleTabInterface $x, ModuleTabInterface $y): int {
            return $x->getTabOrder() <=> $y->getTabOrder();
        };
    }

    /**
     * A function to sort modules by name.
     *
     * Languages have a "sortable" name, so that "British English" sorts as "English, British".
     * This provides a more natural order in the language menu.
     *
     * @return Closure(ModuleInterface, ModuleInterface): int
     */
    private function moduleComparator(): Closure
    {
        return static function (ModuleInterface $x, ModuleInterface $y): int {
            $title1 = $x instanceof ModuleLanguageInterface ? $x->locale()->endonymSortable() : $x->title();
            $title2 = $y instanceof ModuleLanguageInterface ? $y->locale()->endonymSortable() : $y->title();

            return I18N::comparator()($title1, $title2);
        };
    }

    /**
     * During setup, we'll need access to some languages.
     *
     * @return Collection<string,ModuleLanguageInterface>
     */
    public function setupLanguages(): Collection
    {
        return $this->coreModules()
            ->filter(static function (ModuleInterface $module): bool {
                return $module instanceof ModuleLanguageInterface && $module->isEnabledByDefault();
            })
            ->sort(static function (ModuleLanguageInterface $x, ModuleLanguageInterface $y): int {
                return $x->locale()->endonymSortable() <=> $y->locale()->endonymSortable();
            });
    }

    /**
     * Find a specified module, if it is currently active.
     *
     * @param string $module_name
     * @param bool   $include_disabled
     *
     * @return ModuleInterface|null
     */
    public function findByName(string $module_name, bool $include_disabled = false): ?ModuleInterface
    {
        return $this->all($include_disabled)
            ->first(static function (ModuleInterface $module) use ($module_name): bool {
                return $module->name() === $module_name;
            });
    }

    /**
     * Configuration settings are available through the various "module component" pages.
     * For modules that do not provide a component, we need to list them separately.
     *
     * @param bool $include_disabled
     *
     * @return Collection<string,ModuleInterface>
     */
    public function otherModules(bool $include_disabled = false): Collection
    {
        return $this->findByInterface(ModuleInterface::class, $include_disabled, true)
            ->filter(static function (ModuleInterface $module): bool {
                foreach (self::COMPONENTS as $interface) {
                    if ($module instanceof $interface) {
                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * Generate a list of module names which exist in the database but not on disk.
     *
     * @return Collection<int,string>
     */
    public function deletedModules(): Collection
    {
        $database_modules = DB::table('module')->pluck('module_name');

        $disk_modules = $this->all(true)
            ->map(static function (ModuleInterface $module): string {
                return $module->name();
            });

        return $database_modules->diff($disk_modules);
    }

    /**
     * Boot all the modules.
     *
     * @param ModuleThemeInterface $current_theme
     */
    public function bootModules(ModuleThemeInterface $current_theme): void
    {
        foreach ($this->all() as $module) {
            // Only bootstrap the current theme.
            if ($module instanceof ModuleThemeInterface && $module !== $current_theme) {
                continue;
            }

            $module->boot();
        }
    }

    /**
     * @return Collection<int,string>
     */
    public function componentsWithAccess(): Collection
    {
        return new Collection(self::COMPONENTS_WITH_ACCESS);
    }

    /**
     * @return Collection<int,string>
     */
    public function componentsWithOrder(): Collection
    {
        return new Collection(self::COMPONENTS_WITH_SORT);
    }
}
