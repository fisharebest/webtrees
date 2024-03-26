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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\BrowserRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ContactRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\EventRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\FamilyDatesRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\FavoritesRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\GedcomRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\HitCountRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\IndividualRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\LatestUserRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\MediaRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\MessageRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\NewsRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\PlaceRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ServerRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\UserRepositoryInterface;
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
use stdClass;

use function count;
use function in_array;
use function str_contains;

/**
 * A selection of pre-formatted statistical queries.
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class Statistics implements
    GedcomRepositoryInterface,
    IndividualRepositoryInterface,
    EventRepositoryInterface,
    MediaRepositoryInterface,
    UserRepositoryInterface,
    ServerRepositoryInterface,
    BrowserRepositoryInterface,
    HitCountRepositoryInterface,
    LatestUserRepositoryInterface,
    FavoritesRepositoryInterface,
    NewsRepositoryInterface,
    MessageRepositoryInterface,
    ContactRepositoryInterface,
    FamilyDatesRepositoryInterface,
    PlaceRepositoryInterface
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

    /**
     * Create the statistics for a tree.
     *
     * @param CenturyService $century_service
     * @param ColorService   $color_service
     * @param CountryService $country_service
     * @param ModuleService  $module_service
     * @param Tree           $tree Generate statistics for this tree
     * @param UserService    $user_service
     */
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

    /**
     * Return a string of all supported tags and an example of its output in table row form.
     *
     * @return string
     */
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

    /**
     * Embed tags in text
     *
     * @param string $text
     *
     * @return string
     */
    public function embedTags(string $text): string
    {
        if (str_contains($text, '#')) {
            $text = strtr($text, $this->getTags($text));
        }

        return $text;
    }

    /**
     * @return string
     */
    public function gedcomFilename(): string
    {
        return $this->gedcom_repository->gedcomFilename();
    }

    /**
     * @return int
     */
    public function gedcomId(): int
    {
        return $this->gedcom_repository->gedcomId();
    }

    /**
     * @return string
     */
    public function gedcomTitle(): string
    {
        return $this->gedcom_repository->gedcomTitle();
    }

    /**
     * @return string
     */
    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcom_repository->gedcomCreatedSoftware();
    }

    /**
     * @return string
     */
    public function gedcomCreatedVersion(): string
    {
        return $this->gedcom_repository->gedcomCreatedVersion();
    }

    /**
     * @return string
     */
    public function gedcomDate(): string
    {
        return $this->gedcom_repository->gedcomDate();
    }

    /**
     * @return string
     */
    public function gedcomUpdated(): string
    {
        return $this->gedcom_repository->gedcomUpdated();
    }

    /**
     * @return string
     */
    public function gedcomRootId(): string
    {
        return $this->gedcom_repository->gedcomRootId();
    }

    /**
     * @return string
     */
    public function totalRecords(): string
    {
        return $this->individual_repository->totalRecords();
    }

    /**
     * @return string
     */
    public function totalIndividuals(): string
    {
        return $this->individual_repository->totalIndividuals();
    }

    /**
     * @return string
     */
    public function totalIndisWithSources(): string
    {
        return $this->individual_repository->totalIndisWithSources();
    }

    /**
     * @return string
     */
    public function totalIndisWithSourcesPercentage(): string
    {
        return $this->individual_repository->totalIndisWithSourcesPercentage();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(
        string|null $color_from = null,
        string|null $color_to = null
    ): string {
        return $this->individual_repository->chartIndisWithSources($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function totalIndividualsPercentage(): string
    {
        return $this->individual_repository->totalIndividualsPercentage();
    }

    /**
     * @return string
     */
    public function totalFamilies(): string
    {
        return $this->individual_repository->totalFamilies();
    }

    /**
     * @return string
     */
    public function totalFamiliesPercentage(): string
    {
        return $this->individual_repository->totalFamiliesPercentage();
    }

    /**
     * @return string
     */
    public function totalFamsWithSources(): string
    {
        return $this->individual_repository->totalFamsWithSources();
    }

    /**
     * @return string
     */
    public function totalFamsWithSourcesPercentage(): string
    {
        return $this->individual_repository->totalFamsWithSourcesPercentage();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(
        string|null $color_from = null,
        string|null $color_to = null
    ): string {
        return $this->individual_repository->chartFamsWithSources($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function totalSources(): string
    {
        return $this->individual_repository->totalSources();
    }

    /**
     * @return string
     */
    public function totalSourcesPercentage(): string
    {
        return $this->individual_repository->totalSourcesPercentage();
    }

    /**
     * @return string
     */
    public function totalNotes(): string
    {
        return $this->individual_repository->totalNotes();
    }

    /**
     * @return string
     */
    public function totalNotesPercentage(): string
    {
        return $this->individual_repository->totalNotesPercentage();
    }

    /**
     * @return string
     */
    public function totalRepositories(): string
    {
        return $this->individual_repository->totalRepositories();
    }

    /**
     * @return string
     */
    public function totalRepositoriesPercentage(): string
    {
        return $this->individual_repository->totalRepositoriesPercentage();
    }

    /**
     * @param array<string> ...$params
     *
     * @return string
     */
    public function totalSurnames(...$params): string
    {
        return $this->individual_repository->totalSurnames(...$params);
    }

    /**
     * @param array<string> ...$params
     *
     * @return string
     */
    public function totalGivennames(...$params): string
    {
        return $this->individual_repository->totalGivennames(...$params);
    }

    /**
     * @param array<string> $events
     *
     * @return string
     */
    public function totalEvents(array $events = []): string
    {
        return $this->event_repository->totalEvents($events);
    }

    /**
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return $this->event_repository->totalEventsBirth();
    }

    /**
     * @return string
     */
    public function totalBirths(): string
    {
        return $this->event_repository->totalBirths();
    }

    /**
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return $this->event_repository->totalEventsDeath();
    }

    /**
     * @return string
     */
    public function totalDeaths(): string
    {
        return $this->event_repository->totalDeaths();
    }

    /**
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return $this->event_repository->totalEventsMarriage();
    }

    /**
     * @return string
     */
    public function totalMarriages(): string
    {
        return $this->event_repository->totalMarriages();
    }

    /**
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return $this->event_repository->totalEventsDivorce();
    }

    /**
     * @return string
     */
    public function totalDivorces(): string
    {
        return $this->event_repository->totalDivorces();
    }

    /**
     * @return string
     */
    public function totalEventsOther(): string
    {
        return $this->event_repository->totalEventsOther();
    }

    /**
     * @return string
     */
    public function totalSexMales(): string
    {
        return $this->individual_repository->totalSexMales();
    }

    /**
     * @return string
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->individual_repository->totalSexMalesPercentage();
    }

    /**
     * @return string
     */
    public function totalSexFemales(): string
    {
        return $this->individual_repository->totalSexFemales();
    }

    /**
     * @return string
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->individual_repository->totalSexFemalesPercentage();
    }

    /**
     * @return string
     */
    public function totalSexUnknown(): string
    {
        return $this->individual_repository->totalSexUnknown();
    }

    /**
     * @return string
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->individual_repository->totalSexUnknownPercentage();
    }

    /**
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        string|null $color_female = null,
        string|null $color_male = null,
        string|null $color_unknown = null
    ): string {
        return $this->individual_repository->chartSex($color_female, $color_male, $color_unknown);
    }

    /**
     * @return string
     */
    public function totalLiving(): string
    {
        return $this->individual_repository->totalLiving();
    }

    /**
     * @return string
     */
    public function totalLivingPercentage(): string
    {
        return $this->individual_repository->totalLivingPercentage();
    }

    /**
     * @return string
     */
    public function totalDeceased(): string
    {
        return $this->individual_repository->totalDeceased();
    }

    /**
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->individual_repository->totalDeceasedPercentage();
    }

    /**
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(string|null $color_living = null, string|null $color_dead = null): string
    {
        return $this->individual_repository->chartMortality($color_living, $color_dead);
    }

    /**
     * @return string
     */
    public function totalMedia(): string
    {
        return $this->media_repository->totalMedia();
    }

    /**
     * @return string
     */
    public function totalMediaAudio(): string
    {
        return $this->media_repository->totalMediaAudio();
    }

    /**
     * @return string
     */
    public function totalMediaBook(): string
    {
        return $this->media_repository->totalMediaBook();
    }

    /**
     * @return string
     */
    public function totalMediaCard(): string
    {
        return $this->media_repository->totalMediaCard();
    }

    /**
     * @return string
     */
    public function totalMediaCertificate(): string
    {
        return $this->media_repository->totalMediaCertificate();
    }

    /**
     * @return string
     */
    public function totalMediaCoatOfArms(): string
    {
        return $this->media_repository->totalMediaCoatOfArms();
    }

    /**
     * @return string
     */
    public function totalMediaDocument(): string
    {
        return $this->media_repository->totalMediaDocument();
    }

    /**
     * @return string
     */
    public function totalMediaElectronic(): string
    {
        return $this->media_repository->totalMediaElectronic();
    }

    /**
     * @return string
     */
    public function totalMediaMagazine(): string
    {
        return $this->media_repository->totalMediaMagazine();
    }

    /**
     * @return string
     */
    public function totalMediaManuscript(): string
    {
        return $this->media_repository->totalMediaManuscript();
    }

    /**
     * @return string
     */
    public function totalMediaMap(): string
    {
        return $this->media_repository->totalMediaMap();
    }

    /**
     * @return string
     */
    public function totalMediaFiche(): string
    {
        return $this->media_repository->totalMediaFiche();
    }

    /**
     * @return string
     */
    public function totalMediaFilm(): string
    {
        return $this->media_repository->totalMediaFilm();
    }

    /**
     * @return string
     */
    public function totalMediaNewspaper(): string
    {
        return $this->media_repository->totalMediaNewspaper();
    }

    /**
     * @return string
     */
    public function totalMediaPainting(): string
    {
        return $this->media_repository->totalMediaPainting();
    }

    /**
     * @return string
     */
    public function totalMediaPhoto(): string
    {
        return $this->media_repository->totalMediaPhoto();
    }

    /**
     * @return string
     */
    public function totalMediaTombstone(): string
    {
        return $this->media_repository->totalMediaTombstone();
    }

    /**
     * @return string
     */
    public function totalMediaVideo(): string
    {
        return $this->media_repository->totalMediaVideo();
    }

    /**
     * @return string
     */
    public function totalMediaOther(): string
    {
        return $this->media_repository->totalMediaOther();
    }

    /**
     * @return string
     */
    public function totalMediaUnknown(): string
    {
        return $this->media_repository->totalMediaUnknown();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->media_repository->chartMedia($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function totalPlaces(): string
    {
        return $this->place_repository->totalPlaces();
    }

    /**
     * @param string $chart_shows
     * @param string $chart_type
     * @param string $surname
     *
     * @return string
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        return $this->place_repository->chartDistribution($chart_shows, $chart_type, $surname);
    }

    /**
     * @return string
     */
    public function commonCountriesList(): string
    {
        return $this->place_repository->commonCountriesList();
    }

    /**
     * @return string
     */
    public function commonBirthPlacesList(): string
    {
        return $this->place_repository->commonBirthPlacesList();
    }

    /**
     * @return string
     */
    public function commonDeathPlacesList(): string
    {
        return $this->place_repository->commonDeathPlacesList();
    }

    /**
     * @return string
     */
    public function commonMarriagePlacesList(): string
    {
        return $this->place_repository->commonMarriagePlacesList();
    }

    /**
     * @return string
     */
    public function firstBirth(): string
    {
        return $this->family_dates_repository->firstBirth();
    }

    /**
     * @return string
     */
    public function firstBirthYear(): string
    {
        return $this->family_dates_repository->firstBirthYear();
    }

    /**
     * @return string
     */
    public function firstBirthName(): string
    {
        return $this->family_dates_repository->firstBirthName();
    }

    /**
     * @return string
     */
    public function firstBirthPlace(): string
    {
        return $this->family_dates_repository->firstBirthPlace();
    }

    /**
     * @return string
     */
    public function lastBirth(): string
    {
        return $this->family_dates_repository->lastBirth();
    }

    /**
     * @return string
     */
    public function lastBirthYear(): string
    {
        return $this->family_dates_repository->lastBirthYear();
    }

    /**
     * @return string
     */
    public function lastBirthName(): string
    {
        return $this->family_dates_repository->lastBirthName();
    }

    /**
     * @return string
     */
    public function lastBirthPlace(): string
    {
        return $this->family_dates_repository->lastBirthPlace();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsBirthQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsBirthQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsBirthBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsBirthBySexQuery($year1, $year2);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsBirth(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->individual_repository->statsBirth($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function firstDeath(): string
    {
        return $this->family_dates_repository->firstDeath();
    }

    /**
     * @return string
     */
    public function firstDeathYear(): string
    {
        return $this->family_dates_repository->firstDeathYear();
    }

    /**
     * @return string
     */
    public function firstDeathName(): string
    {
        return $this->family_dates_repository->firstDeathName();
    }

    /**
     * @return string
     */
    public function firstDeathPlace(): string
    {
        return $this->family_dates_repository->firstDeathPlace();
    }

    /**
     * @return string
     */
    public function lastDeath(): string
    {
        return $this->family_dates_repository->lastDeath();
    }

    /**
     * @return string
     */
    public function lastDeathYear(): string
    {
        return $this->family_dates_repository->lastDeathYear();
    }

    /**
     * @return string
     */
    public function lastDeathName(): string
    {
        return $this->family_dates_repository->lastDeathName();
    }

    /**
     * @return string
     */
    public function lastDeathPlace(): string
    {
        return $this->family_dates_repository->lastDeathPlace();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsDeathQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsDeathQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsDeathBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individual_repository->statsDeathBySexQuery($year1, $year2);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDeath(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->individual_repository->statsDeath($color_from, $color_to);
    }

    /**
     * General query on ages.
     *
     * @param string $related
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array<array<stdClass>>
     */
    public function statsAgeQuery(string $related = 'BIRT', string $sex = 'BOTH', int $year1 = -1, int $year2 = -1): array
    {
        return $this->individual_repository->statsAgeQuery($related, $sex, $year1, $year2);
    }

    /**
     * @return string
     */
    public function statsAge(): string
    {
        return $this->individual_repository->statsAge();
    }

    /**
     * @return string
     */
    public function longestLife(): string
    {
        return $this->individual_repository->longestLife();
    }

    /**
     * @return string
     */
    public function longestLifeAge(): string
    {
        return $this->individual_repository->longestLifeAge();
    }

    /**
     * @return string
     */
    public function longestLifeName(): string
    {
        return $this->individual_repository->longestLifeName();
    }

    /**
     * @return string
     */
    public function longestLifeFemale(): string
    {
        return $this->individual_repository->longestLifeFemale();
    }

    /**
     * @return string
     */
    public function longestLifeFemaleAge(): string
    {
        return $this->individual_repository->longestLifeFemaleAge();
    }

    /**
     * @return string
     */
    public function longestLifeFemaleName(): string
    {
        return $this->individual_repository->longestLifeFemaleName();
    }

    /**
     * @return string
     */
    public function longestLifeMale(): string
    {
        return $this->individual_repository->longestLifeMale();
    }

    /**
     * @return string
     */
    public function longestLifeMaleAge(): string
    {
        return $this->individual_repository->longestLifeMaleAge();
    }

    /**
     * @return string
     */
    public function longestLifeMaleName(): string
    {
        return $this->individual_repository->longestLifeMaleName();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldest(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldest((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestList(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemale(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemale((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleList(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemaleList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMale(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMale((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleList(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMaleList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestListAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestListAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemaleAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleListAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestFemaleListAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMaleAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleListAlive(string $total = '10'): string
    {
        return $this->individual_repository->topTenOldestMaleListAlive((int) $total);
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function averageLifespan(string $show_years = ''): string
    {
        return $this->individual_repository->averageLifespan((bool) $show_years);
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function averageLifespanFemale(string $show_years = ''): string
    {
        return $this->individual_repository->averageLifespanFemale((bool) $show_years);
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function averageLifespanMale(string $show_years = ''): string
    {
        return $this->individual_repository->averageLifespanMale((bool) $show_years);
    }

    /**
     * @return string
     */
    public function firstEvent(): string
    {
        return $this->event_repository->firstEvent();
    }

    /**
     * @return string
     */
    public function firstEventYear(): string
    {
        return $this->event_repository->firstEventYear();
    }

    /**
     * @return string
     */
    public function firstEventType(): string
    {
        return $this->event_repository->firstEventType();
    }

    /**
     * @return string
     */
    public function firstEventName(): string
    {
        return $this->event_repository->firstEventName();
    }

    /**
     * @return string
     */
    public function firstEventPlace(): string
    {
        return $this->event_repository->firstEventPlace();
    }

    /**
     * @return string
     */
    public function lastEvent(): string
    {
        return $this->event_repository->lastEvent();
    }

    /**
     * @return string
     */
    public function lastEventYear(): string
    {
        return $this->event_repository->lastEventYear();
    }

    /**
     * @return string
     */
    public function lastEventType(): string
    {
        return $this->event_repository->lastEventType();
    }

    /**
     * @return string
     */
    public function lastEventName(): string
    {
        return $this->event_repository->lastEventName();
    }

    /**
     * @return string
     */
    public function lastEventPlace(): string
    {
        return $this->event_repository->lastEventPlace();
    }

    /**
     * @return string
     */
    public function firstMarriage(): string
    {
        return $this->family_dates_repository->firstMarriage();
    }

    /**
     * @return string
     */
    public function firstMarriageYear(): string
    {
        return $this->family_dates_repository->firstMarriageYear();
    }

    /**
     * @return string
     */
    public function firstMarriageName(): string
    {
        return $this->family_dates_repository->firstMarriageName();
    }

    /**
     * @return string
     */
    public function firstMarriagePlace(): string
    {
        return $this->family_dates_repository->firstMarriagePlace();
    }

    /**
     * @return string
     */
    public function lastMarriage(): string
    {
        return $this->family_dates_repository->lastMarriage();
    }

    /**
     * @return string
     */
    public function lastMarriageYear(): string
    {
        return $this->family_dates_repository->lastMarriageYear();
    }

    /**
     * @return string
     */
    public function lastMarriageName(): string
    {
        return $this->family_dates_repository->lastMarriageName();
    }

    /**
     * @return string
     */
    public function lastMarriagePlace(): string
    {
        return $this->family_dates_repository->lastMarriagePlace();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->statsMarriageQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsFirstMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->statsFirstMarriageQuery($year1, $year2);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsMarr(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->family_repository->statsMarr($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function firstDivorce(): string
    {
        return $this->family_dates_repository->firstDivorce();
    }

    /**
     * @return string
     */
    public function firstDivorceYear(): string
    {
        return $this->family_dates_repository->firstDivorceYear();
    }

    /**
     * @return string
     */
    public function firstDivorceName(): string
    {
        return $this->family_dates_repository->firstDivorceName();
    }

    /**
     * @return string
     */
    public function firstDivorcePlace(): string
    {
        return $this->family_dates_repository->firstDivorcePlace();
    }

    /**
     * @return string
     */
    public function lastDivorce(): string
    {
        return $this->family_dates_repository->lastDivorce();
    }

    /**
     * @return string
     */
    public function lastDivorceYear(): string
    {
        return $this->family_dates_repository->lastDivorceYear();
    }

    /**
     * @return string
     */
    public function lastDivorceName(): string
    {
        return $this->family_dates_repository->lastDivorceName();
    }

    /**
     * @return string
     */
    public function lastDivorcePlace(): string
    {
        return $this->family_dates_repository->lastDivorcePlace();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDiv(string|null $color_from = null, string|null $color_to = null): string
    {
        return $this->family_repository->statsDiv($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function youngestMarriageFemale(): string
    {
        return $this->family_repository->youngestMarriageFemale();
    }

    /**
     * @return string
     */
    public function youngestMarriageFemaleName(): string
    {
        return $this->family_repository->youngestMarriageFemaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestMarriageFemaleAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestMarriageFemale(): string
    {
        return $this->family_repository->oldestMarriageFemale();
    }

    /**
     * @return string
     */
    public function oldestMarriageFemaleName(): string
    {
        return $this->family_repository->oldestMarriageFemaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestMarriageFemaleAge($show_years);
    }

    /**
     * @return string
     */
    public function youngestMarriageMale(): string
    {
        return $this->family_repository->youngestMarriageMale();
    }

    /**
     * @return string
     */
    public function youngestMarriageMaleName(): string
    {
        return $this->family_repository->youngestMarriageMaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestMarriageMaleAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestMarriageMale(): string
    {
        return $this->family_repository->oldestMarriageMale();
    }

    /**
     * @return string
     */
    public function oldestMarriageMaleName(): string
    {
        return $this->family_repository->oldestMarriageMaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestMarriageMaleAge($show_years);
    }

    /**
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array<stdClass>
     */
    public function statsMarrAgeQuery(string $sex, int $year1 = -1, int $year2 = -1): array
    {
        return $this->family_repository->statsMarrAgeQuery($sex, $year1, $year2);
    }

    /**
     * @return string
     */
    public function statsMarrAge(): string
    {
        return $this->family_repository->statsMarrAge();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMF(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesMF((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMFList(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesMFList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFM(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesFM((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFMList(string $total = '10'): string
    {
        return $this->family_repository->ageBetweenSpousesFMList((int) $total);
    }

    /**
     * @return string
     */
    public function topAgeOfMarriageFamily(): string
    {
        return $this->family_repository->topAgeOfMarriageFamily();
    }

    /**
     * @return string
     */
    public function topAgeOfMarriage(): string
    {
        return $this->family_repository->topAgeOfMarriage();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->family_repository->topAgeOfMarriageFamilies((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->family_repository->topAgeOfMarriageFamiliesList((int) $total);
    }

    /**
     * @return string
     */
    public function minAgeOfMarriageFamily(): string
    {
        return $this->family_repository->minAgeOfMarriageFamily();
    }

    /**
     * @return string
     */
    public function minAgeOfMarriage(): string
    {
        return $this->family_repository->minAgeOfMarriage();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->family_repository->minAgeOfMarriageFamilies((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->family_repository->minAgeOfMarriageFamiliesList((int) $total);
    }

    /**
     * @return string
     */
    public function youngestMother(): string
    {
        return $this->family_repository->youngestMother();
    }

    /**
     * @return string
     */
    public function youngestMotherName(): string
    {
        return $this->family_repository->youngestMotherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMotherAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestMotherAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestMother(): string
    {
        return $this->family_repository->oldestMother();
    }

    /**
     * @return string
     */
    public function oldestMotherName(): string
    {
        return $this->family_repository->oldestMotherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMotherAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestMotherAge($show_years);
    }

    /**
     * @return string
     */
    public function youngestFather(): string
    {
        return $this->family_repository->youngestFather();
    }

    /**
     * @return string
     */
    public function youngestFatherName(): string
    {
        return $this->family_repository->youngestFatherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestFatherAge(string $show_years = ''): string
    {
        return $this->family_repository->youngestFatherAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestFather(): string
    {
        return $this->family_repository->oldestFather();
    }

    /**
     * @return string
     */
    public function oldestFatherName(): string
    {
        return $this->family_repository->oldestFatherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestFatherAge(string $show_years = ''): string
    {
        return $this->family_repository->oldestFatherAge($show_years);
    }

    /**
     * @return string
     */
    public function totalMarriedMales(): string
    {
        return $this->family_repository->totalMarriedMales();
    }

    /**
     * @return string
     */
    public function totalMarriedFemales(): string
    {
        return $this->family_repository->totalMarriedFemales();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function monthFirstChildQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->monthFirstChildQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function monthFirstChildBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->family_repository->monthFirstChildBySexQuery($year1, $year2);
    }

    /**
     * @return string
     */
    public function largestFamily(): string
    {
        return $this->family_repository->largestFamily();
    }

    /**
     * @return string
     */
    public function largestFamilySize(): string
    {
        return $this->family_repository->largestFamilySize();
    }

    /**
     * @return string
     */
    public function largestFamilyName(): string
    {
        return $this->family_repository->largestFamilyName();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamily(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestFamily((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamilyList(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestFamilyList((int) $total);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string|null $color_from = null,
        string|null $color_to = null,
        string $total = '10'
    ): string {
        return $this->family_repository->chartLargestFamilies($color_from, $color_to, (int) $total);
    }

    /**
     * @return string
     */
    public function totalChildren(): string
    {
        return $this->family_repository->totalChildren();
    }

    /**
     * @return string
     */
    public function averageChildren(): string
    {
        return $this->family_repository->averageChildren();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return array<stdClass>
     */
    public function statsChildrenQuery(int $year1 = -1, int $year2 = -1): array
    {
        return $this->family_repository->statsChildrenQuery($year1, $year2);
    }

    /**
     * @return string
     */
    public function statsChildren(): string
    {
        return $this->family_repository->statsChildren();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeBetweenSiblingsName(string $total = '10'): string
    {
        return $this->family_repository->topAgeBetweenSiblingsName((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeBetweenSiblings(string $total = '10'): string
    {
        return $this->family_repository->topAgeBetweenSiblings((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeBetweenSiblingsFullName(string $total = '10'): string
    {
        return $this->family_repository->topAgeBetweenSiblingsFullName((int) $total);
    }

    /**
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsList(string $total = '10', string $one = ''): string
    {
        return $this->family_repository->topAgeBetweenSiblingsList((int) $total, $one);
    }

    /**
     * @return string
     */
    public function noChildrenFamilies(): string
    {
        return $this->family_repository->noChildrenFamilies();
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function noChildrenFamiliesList(string $type = 'list'): string
    {
        return $this->family_repository->noChildrenFamiliesList($type);
    }

    /**
     * @param string $year1
     * @param string $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(
        string $year1 = '-1',
        string $year2 = '-1'
    ): string {
        return $this->family_repository->chartNoChildrenFamilies((int) $year1, (int) $year2);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamily(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestGrandFamily((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamilyList(string $total = '10'): string
    {
        return $this->family_repository->topTenLargestGrandFamilyList((int) $total);
    }

    /**
     * @return string
     */
    public function getCommonSurname(): string
    {
        return $this->individual_repository->getCommonSurname();
    }

    /**
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnames(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->individual_repository->commonSurnames((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'count'
    ): string {
        return $this->individual_repository->commonSurnamesTotals((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesList(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->individual_repository->commonSurnamesList((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesListTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'count'
    ): string {
        return $this->individual_repository
            ->commonSurnamesListTotals((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $number_of_surnames
     *
     * @return string
     */
    public function chartCommonSurnames(
        string|null $color_from = null,
        string|null $color_to = null,
        string $number_of_surnames = '10'
    ): string {
        return $this->individual_repository
            ->chartCommonSurnames($color_from, $color_to, (int) $number_of_surnames);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGiven(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGiven((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemale((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenFemaleTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMale((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenMaleTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknown(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknown((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individual_repository->commonGivenUnknownTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $maxtoshow
     *
     * @return string
     */
    public function chartCommonGiven(
        string|null $color_from = null,
        string|null $color_to = null,
        string $maxtoshow = '7'
    ): string {
        return $this->individual_repository->chartCommonGiven($color_from, $color_to, (int) $maxtoshow);
    }

    /**
     * @return string
     */
    public function usersLoggedIn(): string
    {
        return $this->user_repository->usersLoggedIn();
    }

    /**
     * @return string
     */
    public function usersLoggedInList(): string
    {
        return $this->user_repository->usersLoggedInList();
    }

    /**
     * @return int
     */
    public function usersLoggedInTotal(): int
    {
        return $this->user_repository->usersLoggedInTotal();
    }

    /**
     * @return int
     */
    public function usersLoggedInTotalAnon(): int
    {
        return $this->user_repository->usersLoggedInTotalAnon();
    }

    /**
     * @return int
     */
    public function usersLoggedInTotalVisible(): int
    {
        return $this->user_repository->usersLoggedInTotalVisible();
    }

    /**
     * @return string
     */
    public function userId(): string
    {
        return $this->user_repository->userId();
    }

    /**
     * @param string $visitor_text
     *
     * @return string
     */
    public function userName(string $visitor_text = ''): string
    {
        return $this->user_repository->userName($visitor_text);
    }

    /**
     * @return string
     */
    public function userFullName(): string
    {
        return $this->user_repository->userFullName();
    }

    /**
     * @return string
     */
    public function totalUsers(): string
    {
        return $this->user_repository->totalUsers();
    }

    /**
     * @return string
     */
    public function totalAdmins(): string
    {
        return $this->user_repository->totalAdmins();
    }

    /**
     * @return string
     */
    public function totalNonAdmins(): string
    {
        return $this->user_repository->totalNonAdmins();
    }

    /**
     * @return string
     */
    public function latestUserId(): string
    {
        return $this->latest_user_repository->latestUserId();
    }

    /**
     * @return string
     */
    public function latestUserName(): string
    {
        return $this->latest_user_repository->latestUserName();
    }

    /**
     * @return string
     */
    public function latestUserFullName(): string
    {
        return $this->latest_user_repository->latestUserFullName();
    }

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegDate(string|null $format = null): string
    {
        return $this->latest_user_repository->latestUserRegDate($format);
    }

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegTime(string|null $format = null): string
    {
        return $this->latest_user_repository->latestUserRegTime($format);
    }

    /**
     * @param string|null $yes
     * @param string|null $no
     *
     * @return string
     */
    public function latestUserLoggedin(string|null $yes = null, string|null $no = null): string
    {
        return $this->latest_user_repository->latestUserLoggedin($yes, $no);
    }

    /**
     * @return string
     */
    public function contactWebmaster(): string
    {
        return $this->contact_repository->contactWebmaster();
    }

    /**
     * @return string
     */
    public function contactGedcom(): string
    {
        return $this->contact_repository->contactGedcom();
    }

    /**
     * @return string
     */
    public function serverDate(): string
    {
        return $this->server_repository->serverDate();
    }

    /**
     * @return string
     */
    public function serverTime(): string
    {
        return $this->server_repository->serverTime();
    }

    /**
     * @return string
     */
    public function serverTime24(): string
    {
        return $this->server_repository->serverTime24();
    }

    /**
     * What is the timezone of the server.
     *
     * @return string
     */
    public function serverTimezone(): string
    {
        return $this->server_repository->serverTimezone();
    }

    /**
     * @return string
     */
    public function browserDate(): string
    {
        return $this->browser_repository->browserDate();
    }

    /**
     * @return string
     */
    public function browserTime(): string
    {
        return $this->browser_repository->browserTime();
    }

    /**
     * @return string
     */
    public function browserTimezone(): string
    {
        return $this->browser_repository->browserTimezone();
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCount($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountUser($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountIndi($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountFam($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountSour($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountRepo($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountNote($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hit_count_repository->hitCountObje($page_parameter);
    }

    /**
     * @return string
     */
    public function gedcomFavorites(): string
    {
        return $this->favorites_repository->gedcomFavorites();
    }

    /**
     * @return string
     */
    public function userFavorites(): string
    {
        return $this->favorites_repository->userFavorites();
    }

    /**
     * @return string
     */
    public function totalGedcomFavorites(): string
    {
        return $this->favorites_repository->totalGedcomFavorites();
    }

    /**
     * @return string
     */
    public function totalUserFavorites(): string
    {
        return $this->favorites_repository->totalUserFavorites();
    }

    /**
     * @return string
     */
    public function totalUserMessages(): string
    {
        return $this->message_repository->totalUserMessages();
    }

    /**
     * @return string
     */
    public function totalUserJournal(): string
    {
        return $this->news_repository->totalUserJournal();
    }

    /**
     * @return string
     */
    public function totalGedcomNews(): string
    {
        return $this->news_repository->totalGedcomNews();
    }

    /**
     * Create any of the other blocks.
     * Use as #callBlock:block_name#
     *
     * @param string $block
     * @param string ...$params
     *
     * @return string|null
     */
    public function callBlock(string $block = '', ...$params): string|null
    {
        /** @var ModuleBlockInterface|null $module */
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

    /**
     * What is the current version of webtrees.
     *
     * @return string
     */
    public function webtreesVersion(): string
    {
        return Webtrees::VERSION;
    }

    /**
     * Get tags and their parsed results.
     *
     * @param string $text
     *
     * @return array<string>
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
