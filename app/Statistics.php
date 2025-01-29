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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics\Repository\BrowserRepository;
use Fisharebest\Webtrees\Statistics\Repository\ContactRepository;
use Fisharebest\Webtrees\Statistics\Repository\EventRepository;
use Fisharebest\Webtrees\Statistics\Repository\FamilyDatesRepository;
use Fisharebest\Webtrees\Statistics\Repository\FamilyRepository;
use Fisharebest\Webtrees\Statistics\Repository\FavoritesRepository;
use Fisharebest\Webtrees\Statistics\Repository\GedcomRepository;
use Fisharebest\Webtrees\Statistics\Repository\HitCountRepository;
use Fisharebest\Webtrees\Statistics\Repository\IndividualRepository;
use Fisharebest\Webtrees\Statistics\Repository\LatestUserRepository;
use Fisharebest\Webtrees\Statistics\Repository\MediaRepository;
use Fisharebest\Webtrees\Statistics\Repository\MessageRepository;
use Fisharebest\Webtrees\Statistics\Repository\NewsRepository;
use Fisharebest\Webtrees\Statistics\Repository\PlaceRepository;
use Fisharebest\Webtrees\Statistics\Repository\ServerRepository;
use Fisharebest\Webtrees\Statistics\Repository\UserRepository;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Statistics\Service\CountryService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

use function count;
use function in_array;
use function str_contains;

/**
 * A selection of pre-formatted statistical queries.
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class Statistics
{
    private Tree $tree;

    private GedcomRepository $gedcom_repository;

    private IndividualRepository $individual_repository;

    private FamilyRepository $family_repository;

    private MediaRepository $media_repository;

    private EventRepository $event_repository;

    private UserRepository $user_repository;

    private ServerRepository $server_repository;

    private BrowserRepository $browser_repository;

    private HitCountRepository $hit_count_repository;

    private LatestUserRepository $latest_user_repository;

    private FavoritesRepository $favorites_repository;

    private NewsRepository $news_repository;

    private MessageRepository $message_repository;

    private ContactRepository $contact_repository;

    private FamilyDatesRepository $family_dates_repository;

    private PlaceRepository $place_repository;

    private ModuleService $module_service;

    public function __construct(
        CenturyService $century_service,
        ColorService $color_service,
        CountryService $country_service,
        ModuleService $module_service,
        Tree $tree,
        UserService $user_service
    ) {
        $this->tree                    = $tree;
        $this->gedcom_repository       = new GedcomRepository($tree);
        $this->individual_repository   = new IndividualRepository($century_service, $color_service, $tree);
        $this->family_repository       = new FamilyRepository($century_service, $color_service, $tree);
        $this->family_dates_repository = new FamilyDatesRepository($tree);
        $this->media_repository        = new MediaRepository($color_service, $tree);
        $this->event_repository        = new EventRepository($tree);
        $this->user_repository         = new UserRepository($tree, $user_service);
        $this->server_repository       = new ServerRepository();
        $this->browser_repository      = new BrowserRepository();
        $this->hit_count_repository    = new HitCountRepository($tree, $user_service);
        $this->latest_user_repository  = new LatestUserRepository($user_service);
        $this->favorites_repository    = new FavoritesRepository($tree, $module_service);
        $this->news_repository         = new NewsRepository($tree);
        $this->message_repository      = new MessageRepository();
        $this->contact_repository      = new ContactRepository($tree, $user_service);
        $this->place_repository        = new PlaceRepository($tree, $country_service, $this->individual_repository);
        $this->module_service          = $module_service;
    }

    public function getAllTagsTable(): string
    {
        try {
            $class = new ReflectionClass($this);

            $public_methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

            $examples = Collection::make($public_methods)
                ->filter(static fn (ReflectionMethod $method): bool => !in_array($method->getName(), ['embedTags', 'getAllTagsTable'], true))
                ->filter(static function (ReflectionMethod $method): bool {
                    $type = $method->getReturnType();

                    return $type instanceof ReflectionNamedType && $type->getName() === 'string';
                })
                ->sort(static fn (ReflectionMethod $x, ReflectionMethod $y): int => $x->getName() <=> $y->getName())
                ->map(function (ReflectionMethod $method): string {
                    $tag = $method->getName();

                    return '<dt>#' . $tag . '#</dt><dd>' . $this->$tag() . '</dd>';
                });

            return '<dl>' . $examples->implode('') . '</dl>';
        } catch (ReflectionException $ex) {
            return $ex->getMessage();
        }
    }

    public function embedTags(string $text): string
    {
        if (str_contains($text, '#')) {
            $text = strtr($text, $this->getTags($text));
        }

        return $text;
    }

    public function gedcomFilename(): string
    {
        return $this->gedcom_repository->gedcomFilename();
    }

    public function gedcomId(): int
    {
        return $this->gedcom_repository->gedcomId();
    }

    public function gedcomTitle(): string
    {
        return $this->gedcom_repository->gedcomTitle();
    }

    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcom_repository->gedcomCreatedSoftware();
    }

    public function gedcomCreatedVersion(): string
    {
        return $this->gedcom_repository->gedcomCreatedVersion();
    }

    public function gedcomDate(): string
    {
        return $this->gedcom_repository->gedcomDate();
    }

    public function gedcomUpdated(): string
    {
        return $this->gedcom_repository->gedcomUpdated();
    }

    public function gedcomRootId(): string
    {
        return $this->gedcom_repository->gedcomRootId();
    }

    public function totalRecords(): string
    {
        return $this->individual_repository->totalRecords();
    }

    public function totalIndividuals(): string
    {
        return $this->individual_repository->totalIndividuals();
    }

    public function totalIndisWithSources(): string
    {
        return $this->individual_repository->totalIndisWithSources();
    }

    public function totalIndisWithSourcesPercentage(): string
    {
        return $this->individual_repository->totalIndisWithSourcesPercentage();
    }

    public function chartIndisWithSources(
        string|null $color_from = null,
        string|null $color_to = null
    ): string {
        return $this->individual_repository->chartIndisWithSources($color_from, $color_to);
    }

    public function totalIndividualsPercentage(): string
    {
        return $this->individual_repository->totalIndividualsPercentage();
    }

    public function totalFamilies(): string
    {
        return $this->individual_repository->totalFamilies();
    }

    public function totalFamiliesPercentage(): string
    {
        return $this->individual_repository->totalFamiliesPercentage();
    }

    public function totalFamsWithSources(): string
    {
        return $this->individual_repository->totalFamsWithSources();
    }

    public function totalFamsWithSourcesPercentage(): string
    {
        return $this->individual_repository->totalFamsWithSourcesPercentage();
    }

    public function chartFamsWithSources(
        string|null $color_from = null,
        string|null $color_to = null
    ): string {
        return $this->individual_repository->chartFamsWithSources($color_from, $color_to);
    }

    public function totalSources(): string
    {
        return $this->individual_repository->totalSources();
    }

    public function totalSourcesPercentage(): string
    {
        return $this->individual_repository->totalSourcesPercentage();
    }

    public function totalNotes(): string
    {
        return $this->individual_repository->totalNotes();
    }

    public function totalNotesPercentage(): string
    {
        return $this->individual_repository->totalNotesPercentage();
    }

    public function totalRepositories(): string
    {
        return $this->individual_repository->totalRepositories();
    }

    public function totalRepositoriesPercentage(): string
    {
        return $this->individual_repository->totalRepositoriesPercentage();
    }

    /**
     * @param string ...$params
     */
    public function totalSurnames(...$params): string
    {
        return $this->individual_repository->totalSurnames(...$params);
    }

    /**
     * @param string ...$params
     */
    public function totalGivennames(...$params): string
    {
        return $this->individual_repository->totalGivennames(...$params);
    }

    /**
     * @param array<string> $events
     */
    public function totalEvents(array $events = []): string
    {
        return $this->event_repository->totalEvents($events);
    }

    public function totalEventsBirth(): string
    {
        return $this->event_repository->totalEventsBirth();
    }

    public function totalBirths(): string
    {
        return $this->event_repository->totalBirths();
    }

    public function totalEventsDeath(): string
    {
        return $this->event_repository->totalEventsDeath();
    }

    public function totalDeaths(): string
    {
        return $this->event_repository->totalDeaths();
    }

    public function totalEventsMarriage(): string
    {
        return $this->event_repository->totalEventsMarriage();
    }

    public function totalMarriages(): string
    {
        return $this->event_repository->totalMarriages();
    }

    public function totalEventsDivorce(): string
    {
        return $this->event_repository->totalEventsDivorce();
    }

    public function totalDivorces(): string
    {
        return $this->event_repository->totalDivorces();
    }

    public function totalEventsOther(): string
    {
        return $this->event_repository->totalEventsOther();
    }

    public function totalSexMales(): string
    {
        return $this->individual_repository->totalSexMales();
    }

    public function totalSexMalesPercentage(): string
    {
        return $this->individual_repository->totalSexMalesPercentage();
    }

    public function totalSexFemales(): string
    {
        return $this->individual_repository->totalSexFemales();
    }

    public function totalSexFemalesPercentage(): string
    {
        return $this->individual_repository->totalSexFemalesPercentage();
    }

    public function totalSexUnknown(): string
    {
        return $this->individual_repository->totalSexUnknown();
    }

    public function totalSexUnknownPercentage(): string
    {
        return $this->individual_repository->totalSexUnknownPercentage();
    }

    public function chartSex(
        string|null $color_female = null,
        string|null $color_male = null,
        string|null $color_unknown = null
    ): string {
        return $this->individual_repository->chartSex($color_female, $color_male, $color_unknown);
    }

    public function totalLiving(): string
    {
        return $this->individual_repository->totalLiving();
    }

    public function totalLivingPercentage(): string
    {
        return $this->individual_repository->totalLivingPercentage();
    }

    public function totalDeceased(): string
    {
        return $this->individual_repository->totalDeceased();
    }

    public function totalDeceasedPercentage(): string
    {
        return $this->individual_repository->totalDeceasedPercentage();
    }

    public function chartMortality(string|null $color_living = null, string|null $color_dead = null): string
    {
        return $this->individual_repository->chartMortality($color_living, $color_dead);
    }

    public function totalMedia(): string
    {
        return $this->media_repository->totalMedia();
    }

    public function totalMediaAudio(): string
    {
        return $this->media_repository->totalMediaAudio();
    }

    public function totalMediaBook(): string
    {
        return $this->media_repository->totalMediaBook();
    }

    public function totalMediaCard(): string
    {
        return $this->media_repository->totalMediaCard();
    }

    public function totalMediaCertificate(): string
    {
        return $this->media_repository->totalMediaCertificate();
    }

    public function totalMediaCoatOfArms(): string
    {
        return $this->media_repository->totalMediaCoatOfArms();
    }

    public function totalMediaDocument(): string
    {
        return $this->media_repository->totalMediaDocument();
    }

    public function totalMediaElectronic(): string
    {
        return $this->media_repository->totalMediaElectronic();
    }

    public function totalMediaMagazine(): string
    {
        return $this->media_repository->totalMediaMagazine();
    }

    public function totalMediaManuscript(): string
    {
        return $this->media_repository->totalMediaManuscript();
    }

    public function totalMediaMap(): string
    {
        return $this->media_repository->totalMediaMap();
    }

    public function totalMediaFiche(): string
    {
        return $this->media_repository->totalMediaFiche();
    }

    public function totalMediaFilm(): string
    {
        return $this->media_repository->totalMediaFilm();
    }

    public function totalMediaNewspaper(): string
    {
        return $this->media_repository->totalMediaNewspaper();
    }

    public function totalMediaPainting(): string
    {
        return $this->media_repository->totalMediaPainting();
    }

    public function totalMediaPhoto(): string
    {
        return $this->media_repository->totalMediaPhoto();
    }

    public function totalMediaTombstone(): string
    {
        return $this->media_repository->totalMediaTombstone();
    }

    public function totalMediaVideo(): string
    {
        return $this->media_repository->totalMediaVideo();
    }

    public function totalMediaOther(): string
    {
        return $this->media_repository->totalMediaOther();
    }

    public function totalMediaUnknown(): string
    {
        return $this->media_repository->totalMediaUnknown();
    }

    public function chartMedia(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->media_repository->chartMedia($color_from, $color_to);
    }

    public function totalPlaces(): string
    {
        return $this->place_repository->totalPlaces();
    }

    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        return $this->place_repository->chartDistribution($chart_shows, $chart_type, $surname);
    }

    public function commonCountriesList(): string
    {
        return $this->place_repository->commonCountriesList();
    }

    public function commonBirthPlacesList(): string
    {
        return $this->place_repository->commonBirthPlacesList();
    }

    public function commonDeathPlacesList(): string
    {
        return $this->place_repository->commonDeathPlacesList();
    }

    public function commonMarriagePlacesList(): string
    {
        return $this->place_repository->commonMarriagePlacesList();
    }

    public function firstBirth(): string
    {
        return $this->family_dates_repository->firstBirth();
    }

    public function firstBirthYear(): string
    {
        return $this->family_dates_repository->firstBirthYear();
    }

    public function firstBirthName(): string
    {
        return $this->family_dates_repository->firstBirthName();
    }

    public function firstBirthPlace(): string
    {
        return $this->family_dates_repository->firstBirthPlace();
    }

    public function lastBirth(): string
    {
        return $this->family_dates_repository->lastBirth();
    }

    public function lastBirthYear(): string
    {
        return $this->family_dates_repository->lastBirthYear();
    }

    public function lastBirthName(): string
    {
        return $this->family_dates_repository->lastBirthName();
    }

    public function lastBirthPlace(): string
    {
        return $this->family_dates_repository->lastBirthPlace();
    }

    public function statsBirthQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsBirthQuery($year1, $year2);
    }

    public function statsBirthBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsBirthBySexQuery($year1, $year2);
    }

    public function statsBirth(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->individual_repository->statsBirth($color_from, $color_to);
    }

    public function firstDeath(): string
    {
        return $this->family_dates_repository->firstDeath();
    }

    public function firstDeathYear(): string
    {
        return $this->family_dates_repository->firstDeathYear();
    }

    public function firstDeathName(): string
    {
        return $this->family_dates_repository->firstDeathName();
    }

    public function firstDeathPlace(): string
    {
        return $this->family_dates_repository->firstDeathPlace();
    }

    public function lastDeath(): string
    {
        return $this->family_dates_repository->lastDeath();
    }

    public function lastDeathYear(): string
    {
        return $this->family_dates_repository->lastDeathYear();
    }

    public function lastDeathName(): string
    {
        return $this->family_dates_repository->lastDeathName();
    }

    public function lastDeathPlace(): string
    {
        return $this->family_dates_repository->lastDeathPlace();
    }

    public function statsDeathQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsDeathQuery($year1, $year2);
    }

    public function statsDeathBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsDeathBySexQuery($year1, $year2);
    }

    public function statsDeath(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->individual_repository->statsDeath($color_from, $color_to);
    }

    /**
     * General query on ages.
     *
     * @return array<object{days:int}>
     */
    public function statsAgeQuery(string $related = 'BIRT', string $sex = 'BOTH', int $year1 = -1, int $year2 = -1): array
    {
        return $this->individual_repository->statsAgeQuery($related, $sex, $year1, $year2);
    }

    public function statsAge(): string
    {
        return $this->individual_repository->statsAge();
    }

    public function longestLife(): string
    {
        return $this->individual_repository->longestLife();
    }

    public function longestLifeAge(): string
    {
        return $this->individual_repository->longestLifeAge();
    }

    public function longestLifeName(): string
    {
        return $this->individual_repository->longestLifeName();
    }

    public function longestLifeFemale(): string
    {
        return $this->individual_repository->longestLifeFemale();
    }

    public function longestLifeFemaleAge(): string
    {
        return $this->individual_repository->longestLifeFemaleAge();
    }

    public function longestLifeFemaleName(): string
    {
        return $this->individual_repository->longestLifeFemaleName();
    }

    public function longestLifeMale(): string
    {
        return $this->individual_repository->longestLifeMale();
    }

    public function longestLifeMaleAge(): string
    {
        return $this->individual_repository->longestLifeMaleAge();
    }

    public function longestLifeMaleName(): string
    {
        return $this->individual_repository->longestLifeMaleName();
    }

    public function topTenOldest(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldest((int) $total);
    }

    public function topTenOldestList(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestList((int) $total);
    }

    public function topTenOldestFemale(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemale((int) $total);
    }

    public function topTenOldestFemaleList(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemaleList((int) $total);
    }

    public function topTenOldestMale(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMale((int) $total);
    }

    public function topTenOldestMaleList(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMaleList((int) $total);
    }

    public function topTenOldestAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestAlive((int) $total);
    }

    public function topTenOldestListAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestListAlive((int) $total);
    }

    public function topTenOldestFemaleAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemaleAlive((int) $total);
    }

    public function topTenOldestFemaleListAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemaleListAlive((int) $total);
    }

    public function topTenOldestMaleAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMaleAlive((int) $total);
    }

    public function topTenOldestMaleListAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMaleListAlive((int) $total);
    }

    public function averageLifespan(string $show_years = ''): string
    {
        return $this->individual_repository->averageLifespan((bool) $show_years);
    }

    public function averageLifespanFemale(string $show_years = ''): string
    {
        return $this->individual_repository->averageLifespanFemale((bool) $show_years);
    }

    public function averageLifespanMale(string $show_years = ''): string
    {
        return $this->individual_repository->averageLifespanMale((bool) $show_years);
    }

    public function firstEvent(): string
    {
        return $this->event_repository->firstEvent();
    }

    public function firstEventYear(): string
    {
        return $this->event_repository->firstEventYear();
    }

    public function firstEventType(): string
    {
        return $this->event_repository->firstEventType();
    }

    public function firstEventName(): string
    {
        return $this->event_repository->firstEventName();
    }

    public function firstEventPlace(): string
    {
        return $this->event_repository->firstEventPlace();
    }

    public function lastEvent(): string
    {
        return $this->event_repository->lastEvent();
    }

    public function lastEventYear(): string
    {
        return $this->event_repository->lastEventYear();
    }

    public function lastEventType(): string
    {
        return $this->event_repository->lastEventType();
    }

    public function lastEventName(): string
    {
        return $this->event_repository->lastEventName();
    }

    public function lastEventPlace(): string
    {
        return $this->event_repository->lastEventPlace();
    }

    public function firstMarriage(): string
    {
        return $this->family_dates_repository->firstMarriage();
    }

    public function firstMarriageYear(): string
    {
        return $this->family_dates_repository->firstMarriageYear();
    }

    public function firstMarriageName(): string
    {
        return $this->family_dates_repository->firstMarriageName();
    }

    public function firstMarriagePlace(): string
    {
        return $this->family_dates_repository->firstMarriagePlace();
    }

    public function lastMarriage(): string
    {
        return $this->family_dates_repository->lastMarriage();
    }

    public function lastMarriageYear(): string
    {
        return $this->family_dates_repository->lastMarriageYear();
    }

    public function lastMarriageName(): string
    {
        return $this->family_dates_repository->lastMarriageName();
    }

    public function lastMarriagePlace(): string
    {
        return $this->family_dates_repository->lastMarriagePlace();
    }

    public function statsMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->statsMarriageQuery($year1, $year2);
    }

    public function statsFirstMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->statsFirstMarriageQuery($year1, $year2);
    }

    public function statsMarr(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->family_repository->statsMarr($color_from, $color_to);
    }

    public function firstDivorce(): string
    {
        return $this->family_dates_repository->firstDivorce();
    }

    public function firstDivorceYear(): string
    {
        return $this->family_dates_repository->firstDivorceYear();
    }

    public function firstDivorceName(): string
    {
        return $this->family_dates_repository->firstDivorceName();
    }

    public function firstDivorcePlace(): string
    {
        return $this->family_dates_repository->firstDivorcePlace();
    }

    public function lastDivorce(): string
    {
        return $this->family_dates_repository->lastDivorce();
    }

    public function lastDivorceYear(): string
    {
        return $this->family_dates_repository->lastDivorceYear();
    }

    public function lastDivorceName(): string
    {
        return $this->family_dates_repository->lastDivorceName();
    }

    public function lastDivorcePlace(): string
    {
        return $this->family_dates_repository->lastDivorcePlace();
    }

    public function statsDiv(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->family_repository->statsDiv($color_from, $color_to);
    }

    public function youngestMarriageFemale(): string
    {
        return $this->family_repository->youngestMarriageFemale();
    }

    public function youngestMarriageFemaleName(): string
    {
        return $this->family_repository->youngestMarriageFemaleName();
    }

    public function youngestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestMarriageFemaleAge($show_years);
    }

    public function oldestMarriageFemale(): string
    {
        return $this->family_repository->oldestMarriageFemale();
    }

    public function oldestMarriageFemaleName(): string
    {
        return $this->family_repository->oldestMarriageFemaleName();
    }

    public function oldestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestMarriageFemaleAge($show_years);
    }

    public function youngestMarriageMale(): string
    {
        return $this->family_repository->youngestMarriageMale();
    }

    public function youngestMarriageMaleName(): string
    {
        return $this->family_repository->youngestMarriageMaleName();
    }

    public function youngestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestMarriageMaleAge($show_years);
    }

    public function oldestMarriageMale(): string
    {
        return $this->family_repository->oldestMarriageMale();
    }

    public function oldestMarriageMaleName(): string
    {
        return $this->family_repository->oldestMarriageMaleName();
    }

    public function oldestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestMarriageMaleAge($show_years);
    }

    /**
     * @return array<object{f_id:string,d_gid:string,age:int}>
     */
    public function statsMarrAgeQuery(string $sex, int $year1 = -1, int $year2 = -1): array
    {
        return $this->family_repository->statsMarrAgeQuery($sex, $year1, $year2);
    }

    public function statsMarrAge(): string
    {
        return $this->family_repository->statsMarrAge();
    }

    public function ageBetweenSpousesMF(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesMF((int) $total);
    }

    public function ageBetweenSpousesMFList(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesMFList((int) $total);
    }

    public function ageBetweenSpousesFM(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesFM((int) $total);
    }

    public function ageBetweenSpousesFMList(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesFMList((int) $total);
    }

    public function topAgeOfMarriageFamily(): string
    {
        return $this->family_repository->topAgeOfMarriageFamily();
    }

    public function topAgeOfMarriage(): string
    {
        return $this->family_repository->topAgeOfMarriage();
    }

    public function topAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->family_repository->topAgeOfMarriageFamilies((int) $total);
    }

    public function topAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->family_repository->topAgeOfMarriageFamiliesList((int) $total);
    }

    public function minAgeOfMarriageFamily(): string
    {
        return $this->family_repository->minAgeOfMarriageFamily();
    }

    public function minAgeOfMarriage(): string
    {
        return $this->family_repository->minAgeOfMarriage();
    }

    public function minAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->family_repository->minAgeOfMarriageFamilies((int) $total);
    }

    public function minAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->family_repository->minAgeOfMarriageFamiliesList((int) $total);
    }

    public function youngestMother(): string
    {
        return $this->family_repository->youngestMother();
    }

    public function youngestMotherName(): string
    {
        return $this->family_repository->youngestMotherName();
    }

    public function youngestMotherAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestMotherAge($show_years);
    }

    public function oldestMother(): string
    {
        return $this->family_repository->oldestMother();
    }

    public function oldestMotherName(): string
    {
        return $this->family_repository->oldestMotherName();
    }

    public function oldestMotherAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestMotherAge($show_years);
    }

    public function youngestFather(): string
    {
        return $this->family_repository->youngestFather();
    }

    public function youngestFatherName(): string
    {
        return $this->family_repository->youngestFatherName();
    }

    public function youngestFatherAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestFatherAge($show_years);
    }

    public function oldestFather(): string
    {
        return $this->family_repository->oldestFather();
    }

    public function oldestFatherName(): string
    {
        return $this->family_repository->oldestFatherName();
    }

    public function oldestFatherAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestFatherAge($show_years);
    }

    public function totalMarriedMales(): string
    {
        return $this->family_repository->totalMarriedMales();
    }

    public function totalMarriedFemales(): string
    {
        return $this->family_repository->totalMarriedFemales();
    }

    public function monthFirstChildQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->monthFirstChildQuery($year1, $year2);
    }

    public function monthFirstChildBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->monthFirstChildBySexQuery($year1, $year2);
    }

    public function largestFamily(): string
    {
        return $this->family_repository->largestFamily();
    }

    public function largestFamilySize(): string
    {
        return $this->family_repository->largestFamilySize();
    }

    public function largestFamilyName(): string
    {
        return $this->family_repository->largestFamilyName();
    }

    public function topTenLargestFamily(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestFamily((int) $total);
    }

    public function topTenLargestFamilyList(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestFamilyList((int) $total);
    }

    public function chartLargestFamilies(
        string|null $color_from = null,
        string|null $color_to = null,
        string $total = '10'
    ): string {
        return $this->family_repository->chartLargestFamilies($color_from, $color_to, (int) $total);
    }

    public function totalChildren(): string
    {
        return $this->family_repository->totalChildren();
    }

    public function averageChildren(): string
    {
        return $this->family_repository->averageChildren();
    }

    /**
     * @return array<object{f_numchil:int,total:int}>
     */
    public function statsChildrenQuery(int $year1 = -1, int $year2 = -1): array
    {
        return $this->family_repository->statsChildrenQuery($year1, $year2);
    }

    public function statsChildren(): string
    {
        return $this->family_repository->statsChildren();
    }

    public function topAgeBetweenSiblingsName(string $total = '10'): string
    {
        return $this->family_repository->topAgeBetweenSiblingsName((int) $total);
    }

    public function topAgeBetweenSiblings(string $total = '10'): string
    {
        return $this->family_repository->topAgeBetweenSiblings((int) $total);
    }

    public function topAgeBetweenSiblingsFullName(string $total = '10'): string
    {
        return $this->family_repository->topAgeBetweenSiblingsFullName((int) $total);
    }

    public function topAgeBetweenSiblingsList(string $total = '10', string $one = ''): string
    {
        return $this->family_repository->topAgeBetweenSiblingsList((int) $total, $one);
    }

    public function noChildrenFamilies(): string
    {
        return $this->family_repository->noChildrenFamilies();
    }

    public function noChildrenFamiliesList(string $type = 'list'): string
    {
        return $this->family_repository->noChildrenFamiliesList($type);
    }

    public function chartNoChildrenFamilies(
        string $year1 = '-1',
        string $year2 = '-1'
    ): string {
        return $this->family_repository->chartNoChildrenFamilies((int) $year1, (int) $year2);
    }

    public function topTenLargestGrandFamily(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestGrandFamily((int) $total);
    }

    public function topTenLargestGrandFamilyList(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestGrandFamilyList((int) $total);
    }

    public function getCommonSurname(): string
    {
        return $this->individual_repository->getCommonSurname();
    }

    public function commonSurnames(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->individual_repository->commonSurnames((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    public function commonSurnamesTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'count'
    ): string {
        return $this->individual_repository->commonSurnamesTotals((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    public function commonSurnamesList(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->individual_repository->commonSurnamesList((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    public function commonSurnamesListTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'count'
    ): string {
        return $this->individual_repository
            ->commonSurnamesListTotals((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    public function chartCommonSurnames(
        string|null $color_from = null,
        string|null $color_to = null,
        string $number_of_surnames = '10'
    ): string {
        return $this->individual_repository
            ->chartCommonSurnames($color_from, $color_to, (int) $number_of_surnames);
    }

    public function commonGiven(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGiven((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenList((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenListTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenTable((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenFemale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemale((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenFemaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenFemaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleList((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenFemaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenFemaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleTable((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenMale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMale((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenMaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenMaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleList((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenMaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenMaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleTable((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenUnknown(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknown((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenUnknownTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenUnknownList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownList((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenUnknownListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownListTotals((int) $threshold, (int) $maxtoshow);
    }

    public function commonGivenUnknownTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownTable((int) $threshold, (int) $maxtoshow);
    }

    public function chartCommonGiven(
        string|null $color_from = null,
        string|null $color_to = null,
        string $maxtoshow = '7'
    ): string {
        return $this->individual_repository->chartCommonGiven($color_from, $color_to, (int) $maxtoshow);
    }

    public function usersLoggedIn(): string
    {
        return $this->user_repository->usersLoggedIn();
    }

    public function usersLoggedInList(): string
    {
        return $this->user_repository->usersLoggedInList();
    }

    public function usersLoggedInTotal(): int
    {
        return $this->user_repository->usersLoggedInTotal();
    }

    public function usersLoggedInTotalAnon(): int
    {
        return $this->user_repository->usersLoggedInTotalAnon();
    }

    public function usersLoggedInTotalVisible(): int
    {
        return $this->user_repository->usersLoggedInTotalVisible();
    }

    public function userId(): string
    {
        return $this->user_repository->userId();
    }

    public function userName(string $visitor_text = ''): string
    {
        return $this->user_repository->userName($visitor_text);
    }

    public function userFullName(): string
    {
        return $this->user_repository->userFullName();
    }

    public function totalUsers(): string
    {
        return $this->user_repository->totalUsers();
    }

    public function totalAdmins(): string
    {
        return $this->user_repository->totalAdmins();
    }

    public function totalNonAdmins(): string
    {
        return $this->user_repository->totalNonAdmins();
    }

    public function latestUserId(): string
    {
        return $this->latest_user_repository->latestUserId();
    }

    public function latestUserName(): string
    {
        return $this->latest_user_repository->latestUserName();
    }

    public function latestUserFullName(): string
    {
        return $this->latest_user_repository->latestUserFullName();
    }

    public function latestUserRegDate(string|null $format = null): string
    {
        return $this->latest_user_repository->latestUserRegDate($format);
    }

    public function latestUserRegTime(string|null $format = null): string
    {
        return $this->latest_user_repository->latestUserRegTime($format);
    }

    public function latestUserLoggedin(string|null $yes = null, string|null $no = null): string
    {
        return $this->latest_user_repository->latestUserLoggedin($yes, $no);
    }

    public function contactWebmaster(): string
    {
        return $this->contact_repository->contactWebmaster();
    }

    public function contactGedcom(): string
    {
        return $this->contact_repository->contactGedcom();
    }

    public function serverDate(): string
    {
        return $this->server_repository->serverDate();
    }

    public function serverTime(): string
    {
        return $this->server_repository->serverTime();
    }

    public function serverTime24(): string
    {
        return $this->server_repository->serverTime24();
    }

    public function serverTimezone(): string
    {
        return $this->server_repository->serverTimezone();
    }

    public function browserDate(): string
    {
        return $this->browser_repository->browserDate();
    }

    public function browserTime(): string
    {
        return $this->browser_repository->browserTime();
    }

    public function browserTimezone(): string
    {
        return $this->browser_repository->browserTimezone();
    }

    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCount($page_parameter);
    }

    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountUser($page_parameter);
    }

    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountIndi($page_parameter);
    }

    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountFam($page_parameter);
    }

    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountSour($page_parameter);
    }

    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountRepo($page_parameter);
    }

    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountNote($page_parameter);
    }

    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountObje($page_parameter);
    }

    public function gedcomFavorites(): string
    {
        return $this->favorites_repository->gedcomFavorites();
    }

    public function userFavorites(): string
    {
        return $this->favorites_repository->userFavorites();
    }

    public function totalGedcomFavorites(): string
    {
        return $this->favorites_repository->totalGedcomFavorites();
    }

    public function totalUserFavorites(): string
    {
        return $this->favorites_repository->totalUserFavorites();
    }

    public function totalUserMessages(): string
    {
        return $this->message_repository->totalUserMessages();
    }

    public function totalUserJournal(): string
    {
        return $this->news_repository->totalUserJournal();
    }

    public function totalGedcomNews(): string
    {
        return $this->news_repository->totalGedcomNews();
    }

    /**
     * Create any of the other blocks.
     * Use as #callBlock:block_name#
     *
     * @param string ...$params
     */
    public function callBlock(string $block = '', ...$params): string
    {
        $module = $this->module_service
            ->findByComponent(ModuleBlockInterface::class, $this->tree, Auth::user())
            ->first(static fn (ModuleInterface $module): bool => $module->name() === $block && $module->name() !== 'html');

        if ($module === null) {
            return '';
        }

        // Build the config array
        $cfg = [];
        foreach ($params as $config) {
            $bits = explode('=', $config);

            if (count($bits) < 2) {
                continue;
            }

            $v       = array_shift($bits);
            $cfg[$v] = implode('=', $bits);
        }

        return $module->getBlock($this->tree, 0, ModuleBlockInterface::CONTEXT_EMBED, $cfg);
    }

    public function webtreesVersion(): string
    {
        return Webtrees::VERSION;
    }

    /**
     * @return array<string,string>
     */
    private function getTags(string $text): array
    {
        $tags    = [];
        $matches = [];

        preg_match_all('/#([^#\n]+)(?=#)/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $params = explode(':', $match[1]);
            $method = array_shift($params);

            if (method_exists($this, $method)) {
                $tags[$match[0] . '#'] = $this->$method(...$params);
            }
        }

        return $tags;
    }
}
