<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionType;
use stdClass;

use function call_user_func;
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
    /**
     * Generate statistics for a specified tree.
     *
     * @var Tree
     */
    private $tree;
    /**
     * @var GedcomRepository
     */
    private $gedcomRepository;

    /**
     * @var IndividualRepository
     */
    private $individualRepository;

    /**
     * @var FamilyRepository
     */
    private $familyRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var BrowserRepository
     */
    private $browserRepository;

    /**
     * @var HitCountRepository
     */
    private $hitCountRepository;

    /**
     * @var LatestUserRepository
     */
    private $latestUserRepository;

    /**
     * @var FavoritesRepository
     */
    private $favoritesRepository;

    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @var FamilyDatesRepository
     */
    private $familyDatesRepository;

    /**
     * @var PlaceRepository
     */
    private $placeRepository;

    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * Create the statistics for a tree.
     *
     * @param ModuleService $module_service
     * @param Tree          $tree Generate statistics for this tree
     * @param UserService   $user_service
     */
    public function __construct(
        ModuleService $module_service,
        Tree $tree,
        UserService $user_service
    ) {
        $this->tree                  = $tree;
        $this->gedcomRepository      = new GedcomRepository($tree);
        $this->individualRepository  = new IndividualRepository($tree);
        $this->familyRepository      = new FamilyRepository($tree);
        $this->familyDatesRepository = new FamilyDatesRepository($tree);
        $this->mediaRepository       = new MediaRepository($tree);
        $this->eventRepository       = new EventRepository($tree);
        $this->userRepository        = new UserRepository($tree, $user_service);
        $this->serverRepository      = new ServerRepository();
        $this->browserRepository     = new BrowserRepository();
        $this->hitCountRepository    = new HitCountRepository($tree, $user_service);
        $this->latestUserRepository  = new LatestUserRepository($user_service);
        $this->favoritesRepository   = new FavoritesRepository($tree, $module_service);
        $this->newsRepository        = new NewsRepository($tree);
        $this->messageRepository     = new MessageRepository();
        $this->contactRepository     = new ContactRepository($tree, $user_service);
        $this->placeRepository       = new PlaceRepository($tree);
        $this->module_service        = $module_service;
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
                ->filter(static function (ReflectionMethod $method): bool {
                    return !in_array($method->getName(), ['embedTags', 'getAllTagsTable'], true);
                })
                ->filter(static function (ReflectionMethod $method): bool {
                    $type = $method->getReturnType();

                    return $type instanceof ReflectionType && $type->getName() === 'string';
                })
                ->sort(static function (ReflectionMethod $x, ReflectionMethod $y): int {
                    return $x->getName() <=> $y->getName();
                })
                ->map(function (ReflectionMethod $method): string {
                    $tag = $method->getName();

                    return '<dt>#' . $tag . '#</dt><dd>' . call_user_func([$this, $tag]) . '</dd>';
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
        return $this->gedcomRepository->gedcomFilename();
    }

    /**
     * @return int
     */
    public function gedcomId(): int
    {
        return $this->gedcomRepository->gedcomId();
    }

    /**
     * @return string
     */
    public function gedcomTitle(): string
    {
        return $this->gedcomRepository->gedcomTitle();
    }

    /**
     * @return string
     */
    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcomRepository->gedcomCreatedSoftware();
    }

    /**
     * @return string
     */
    public function gedcomCreatedVersion(): string
    {
        return $this->gedcomRepository->gedcomCreatedVersion();
    }

    /**
     * @return string
     */
    public function gedcomDate(): string
    {
        return $this->gedcomRepository->gedcomDate();
    }

    /**
     * @return string
     */
    public function gedcomUpdated(): string
    {
        return $this->gedcomRepository->gedcomUpdated();
    }

    /**
     * @return string
     */
    public function gedcomRootId(): string
    {
        return $this->gedcomRepository->gedcomRootId();
    }

    /**
     * @return string
     */
    public function totalRecords(): string
    {
        return $this->individualRepository->totalRecords();
    }

    /**
     * @return string
     */
    public function totalIndividuals(): string
    {
        return $this->individualRepository->totalIndividuals();
    }

    /**
     * @return string
     */
    public function totalIndisWithSources(): string
    {
        return $this->individualRepository->totalIndisWithSources();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(
        string $color_from = null,
        string $color_to = null
    ): string {
        return $this->individualRepository->chartIndisWithSources($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function totalIndividualsPercentage(): string
    {
        return $this->individualRepository->totalIndividualsPercentage();
    }

    /**
     * @return string
     */
    public function totalFamilies(): string
    {
        return $this->individualRepository->totalFamilies();
    }

    /**
     * @return string
     */
    public function totalFamiliesPercentage(): string
    {
        return $this->individualRepository->totalFamiliesPercentage();
    }

    /**
     * @return string
     */
    public function totalFamsWithSources(): string
    {
        return $this->individualRepository->totalFamsWithSources();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(
        string $color_from = null,
        string $color_to = null
    ): string {
        return $this->individualRepository->chartFamsWithSources($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function totalSources(): string
    {
        return $this->individualRepository->totalSources();
    }

    /**
     * @return string
     */
    public function totalSourcesPercentage(): string
    {
        return $this->individualRepository->totalSourcesPercentage();
    }

    /**
     * @return string
     */
    public function totalNotes(): string
    {
        return $this->individualRepository->totalNotes();
    }

    /**
     * @return string
     */
    public function totalNotesPercentage(): string
    {
        return $this->individualRepository->totalNotesPercentage();
    }

    /**
     * @return string
     */
    public function totalRepositories(): string
    {
        return $this->individualRepository->totalRepositories();
    }

    /**
     * @return string
     */
    public function totalRepositoriesPercentage(): string
    {
        return $this->individualRepository->totalRepositoriesPercentage();
    }

    /**
     * @param string[] ...$params
     *
     * @return string
     */
    public function totalSurnames(...$params): string
    {
        return $this->individualRepository->totalSurnames(...$params);
    }

    /**
     * @param string[] ...$params
     *
     * @return string
     */
    public function totalGivennames(...$params): string
    {
        return $this->individualRepository->totalGivennames(...$params);
    }

    /**
     * @param string[] $events
     *
     * @return string
     */
    public function totalEvents(array $events = []): string
    {
        return $this->eventRepository->totalEvents($events);
    }

    /**
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return $this->eventRepository->totalEventsBirth();
    }

    /**
     * @return string
     */
    public function totalBirths(): string
    {
        return $this->eventRepository->totalBirths();
    }

    /**
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return $this->eventRepository->totalEventsDeath();
    }

    /**
     * @return string
     */
    public function totalDeaths(): string
    {
        return $this->eventRepository->totalDeaths();
    }

    /**
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return $this->eventRepository->totalEventsMarriage();
    }

    /**
     * @return string
     */
    public function totalMarriages(): string
    {
        return $this->eventRepository->totalMarriages();
    }

    /**
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return $this->eventRepository->totalEventsDivorce();
    }

    /**
     * @return string
     */
    public function totalDivorces(): string
    {
        return $this->eventRepository->totalDivorces();
    }

    /**
     * @return string
     */
    public function totalEventsOther(): string
    {
        return $this->eventRepository->totalEventsOther();
    }

    /**
     * @return string
     */
    public function totalSexMales(): string
    {
        return $this->individualRepository->totalSexMales();
    }

    /**
     * @return string
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->individualRepository->totalSexMalesPercentage();
    }

    /**
     * @return string
     */
    public function totalSexFemales(): string
    {
        return $this->individualRepository->totalSexFemales();
    }

    /**
     * @return string
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->individualRepository->totalSexFemalesPercentage();
    }

    /**
     * @return string
     */
    public function totalSexUnknown(): string
    {
        return $this->individualRepository->totalSexUnknown();
    }

    /**
     * @return string
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->individualRepository->totalSexUnknownPercentage();
    }

    /**
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        string $color_female = null,
        string $color_male = null,
        string $color_unknown = null
    ): string {
        return $this->individualRepository->chartSex($color_female, $color_male, $color_unknown);
    }

    /**
     * @return string
     */
    public function totalLiving(): string
    {
        return $this->individualRepository->totalLiving();
    }

    /**
     * @return string
     */
    public function totalLivingPercentage(): string
    {
        return $this->individualRepository->totalLivingPercentage();
    }

    /**
     * @return string
     */
    public function totalDeceased(): string
    {
        return $this->individualRepository->totalDeceased();
    }

    /**
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->individualRepository->totalDeceasedPercentage();
    }

    /**
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(string $color_living = null, string $color_dead = null): string
    {
        return $this->individualRepository->chartMortality($color_living, $color_dead);
    }

    /**
     * @return string
     */
    public function totalMedia(): string
    {
        return $this->mediaRepository->totalMedia();
    }

    /**
     * @return string
     */
    public function totalMediaAudio(): string
    {
        return $this->mediaRepository->totalMediaAudio();
    }

    /**
     * @return string
     */
    public function totalMediaBook(): string
    {
        return $this->mediaRepository->totalMediaBook();
    }

    /**
     * @return string
     */
    public function totalMediaCard(): string
    {
        return $this->mediaRepository->totalMediaCard();
    }

    /**
     * @return string
     */
    public function totalMediaCertificate(): string
    {
        return $this->mediaRepository->totalMediaCertificate();
    }

    /**
     * @return string
     */
    public function totalMediaCoatOfArms(): string
    {
        return $this->mediaRepository->totalMediaCoatOfArms();
    }

    /**
     * @return string
     */
    public function totalMediaDocument(): string
    {
        return $this->mediaRepository->totalMediaDocument();
    }

    /**
     * @return string
     */
    public function totalMediaElectronic(): string
    {
        return $this->mediaRepository->totalMediaElectronic();
    }

    /**
     * @return string
     */
    public function totalMediaMagazine(): string
    {
        return $this->mediaRepository->totalMediaMagazine();
    }

    /**
     * @return string
     */
    public function totalMediaManuscript(): string
    {
        return $this->mediaRepository->totalMediaManuscript();
    }

    /**
     * @return string
     */
    public function totalMediaMap(): string
    {
        return $this->mediaRepository->totalMediaMap();
    }

    /**
     * @return string
     */
    public function totalMediaFiche(): string
    {
        return $this->mediaRepository->totalMediaFiche();
    }

    /**
     * @return string
     */
    public function totalMediaFilm(): string
    {
        return $this->mediaRepository->totalMediaFilm();
    }

    /**
     * @return string
     */
    public function totalMediaNewspaper(): string
    {
        return $this->mediaRepository->totalMediaNewspaper();
    }

    /**
     * @return string
     */
    public function totalMediaPainting(): string
    {
        return $this->mediaRepository->totalMediaPainting();
    }

    /**
     * @return string
     */
    public function totalMediaPhoto(): string
    {
        return $this->mediaRepository->totalMediaPhoto();
    }

    /**
     * @return string
     */
    public function totalMediaTombstone(): string
    {
        return $this->mediaRepository->totalMediaTombstone();
    }

    /**
     * @return string
     */
    public function totalMediaVideo(): string
    {
        return $this->mediaRepository->totalMediaVideo();
    }

    /**
     * @return string
     */
    public function totalMediaOther(): string
    {
        return $this->mediaRepository->totalMediaOther();
    }

    /**
     * @return string
     */
    public function totalMediaUnknown(): string
    {
        return $this->mediaRepository->totalMediaUnknown();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(string $color_from = null, string $color_to = null): string
    {
        return $this->mediaRepository->chartMedia($color_from, $color_to);
    }

    /**
     * @param string $what
     * @param string $fact
     * @param int    $parent
     * @param bool   $country
     *
     * @return stdClass[]
     */
    public function statsPlaces(string $what = 'ALL', string $fact = '', int $parent = 0, bool $country = false): array
    {
        return $this->placeRepository->statsPlaces($what, $fact, $parent, $country);
    }

    /**
     * @return string
     */
    public function totalPlaces(): string
    {
        return $this->placeRepository->totalPlaces();
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
        return $this->placeRepository->chartDistribution($chart_shows, $chart_type, $surname);
    }

    /**
     * @return string
     */
    public function commonCountriesList(): string
    {
        return $this->placeRepository->commonCountriesList();
    }

    /**
     * @return string
     */
    public function commonBirthPlacesList(): string
    {
        return $this->placeRepository->commonBirthPlacesList();
    }

    /**
     * @return string
     */
    public function commonDeathPlacesList(): string
    {
        return $this->placeRepository->commonDeathPlacesList();
    }

    /**
     * @return string
     */
    public function commonMarriagePlacesList(): string
    {
        return $this->placeRepository->commonMarriagePlacesList();
    }

    /**
     * @return string
     */
    public function firstBirth(): string
    {
        return $this->familyDatesRepository->firstBirth();
    }

    /**
     * @return string
     */
    public function firstBirthYear(): string
    {
        return $this->familyDatesRepository->firstBirthYear();
    }

    /**
     * @return string
     */
    public function firstBirthName(): string
    {
        return $this->familyDatesRepository->firstBirthName();
    }

    /**
     * @return string
     */
    public function firstBirthPlace(): string
    {
        return $this->familyDatesRepository->firstBirthPlace();
    }

    /**
     * @return string
     */
    public function lastBirth(): string
    {
        return $this->familyDatesRepository->lastBirth();
    }

    /**
     * @return string
     */
    public function lastBirthYear(): string
    {
        return $this->familyDatesRepository->lastBirthYear();
    }

    /**
     * @return string
     */
    public function lastBirthName(): string
    {
        return $this->familyDatesRepository->lastBirthName();
    }

    /**
     * @return string
     */
    public function lastBirthPlace(): string
    {
        return $this->familyDatesRepository->lastBirthPlace();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsBirthQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsBirthQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsBirthBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsBirthBySexQuery($year1, $year2);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsBirth(string $color_from = null, string $color_to = null): string
    {
        return $this->individualRepository->statsBirth($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function firstDeath(): string
    {
        return $this->familyDatesRepository->firstDeath();
    }

    /**
     * @return string
     */
    public function firstDeathYear(): string
    {
        return $this->familyDatesRepository->firstDeathYear();
    }

    /**
     * @return string
     */
    public function firstDeathName(): string
    {
        return $this->familyDatesRepository->firstDeathName();
    }

    /**
     * @return string
     */
    public function firstDeathPlace(): string
    {
        return $this->familyDatesRepository->firstDeathPlace();
    }

    /**
     * @return string
     */
    public function lastDeath(): string
    {
        return $this->familyDatesRepository->lastDeath();
    }

    /**
     * @return string
     */
    public function lastDeathYear(): string
    {
        return $this->familyDatesRepository->lastDeathYear();
    }

    /**
     * @return string
     */
    public function lastDeathName(): string
    {
        return $this->familyDatesRepository->lastDeathName();
    }

    /**
     * @return string
     */
    public function lastDeathPlace(): string
    {
        return $this->familyDatesRepository->lastDeathPlace();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsDeathQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsDeathQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsDeathBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsDeathBySexQuery($year1, $year2);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDeath(string $color_from = null, string $color_to = null): string
    {
        return $this->individualRepository->statsDeath($color_from, $color_to);
    }

    /**
     * General query on ages.
     *
     * @param string $related
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array|string
     */
    public function statsAgeQuery(string $related = 'BIRT', string $sex = 'BOTH', int $year1 = -1, int $year2 = -1)
    {
        return $this->individualRepository->statsAgeQuery($related, $sex, $year1, $year2);
    }

    /**
     * @return string
     */
    public function statsAge(): string
    {
        return $this->individualRepository->statsAge();
    }

    /**
     * @return string
     */
    public function longestLife(): string
    {
        return $this->individualRepository->longestLife();
    }

    /**
     * @return string
     */
    public function longestLifeAge(): string
    {
        return $this->individualRepository->longestLifeAge();
    }

    /**
     * @return string
     */
    public function longestLifeName(): string
    {
        return $this->individualRepository->longestLifeName();
    }

    /**
     * @return string
     */
    public function longestLifeFemale(): string
    {
        return $this->individualRepository->longestLifeFemale();
    }

    /**
     * @return string
     */
    public function longestLifeFemaleAge(): string
    {
        return $this->individualRepository->longestLifeFemaleAge();
    }

    /**
     * @return string
     */
    public function longestLifeFemaleName(): string
    {
        return $this->individualRepository->longestLifeFemaleName();
    }

    /**
     * @return string
     */
    public function longestLifeMale(): string
    {
        return $this->individualRepository->longestLifeMale();
    }

    /**
     * @return string
     */
    public function longestLifeMaleAge(): string
    {
        return $this->individualRepository->longestLifeMaleAge();
    }

    /**
     * @return string
     */
    public function longestLifeMaleName(): string
    {
        return $this->individualRepository->longestLifeMaleName();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldest(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldest((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestList(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemale(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemale((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleList(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemaleList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMale(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMale((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleList(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMaleList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestListAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestListAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemaleAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleListAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemaleListAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMaleAlive((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleListAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMaleListAlive((int) $total);
    }

    /**
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespan(bool $show_years = false): string
    {
        return $this->individualRepository->averageLifespan($show_years);
    }

    /**
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespanFemale(bool $show_years = false): string
    {
        return $this->individualRepository->averageLifespanFemale($show_years);
    }

    /**
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespanMale(bool $show_years = false): string
    {
        return $this->individualRepository->averageLifespanMale($show_years);
    }

    /**
     * @return string
     */
    public function firstEvent(): string
    {
        return $this->eventRepository->firstEvent();
    }

    /**
     * @return string
     */
    public function firstEventYear(): string
    {
        return $this->eventRepository->firstEventYear();
    }

    /**
     * @return string
     */
    public function firstEventType(): string
    {
        return $this->eventRepository->firstEventType();
    }

    /**
     * @return string
     */
    public function firstEventName(): string
    {
        return $this->eventRepository->firstEventName();
    }

    /**
     * @return string
     */
    public function firstEventPlace(): string
    {
        return $this->eventRepository->firstEventPlace();
    }

    /**
     * @return string
     */
    public function lastEvent(): string
    {
        return $this->eventRepository->lastEvent();
    }

    /**
     * @return string
     */
    public function lastEventYear(): string
    {
        return $this->eventRepository->lastEventYear();
    }

    /**
     * @return string
     */
    public function lastEventType(): string
    {
        return $this->eventRepository->lastEventType();
    }

    /**
     * @return string
     */
    public function lastEventName(): string
    {
        return $this->eventRepository->lastEventName();
    }

    /**
     * @return string
     */
    public function lastEventPlace(): string
    {
        return $this->eventRepository->lastEventType();
    }

    /**
     * @return string
     */
    public function firstMarriage(): string
    {
        return $this->familyDatesRepository->firstMarriage();
    }

    /**
     * @return string
     */
    public function firstMarriageYear(): string
    {
        return $this->familyDatesRepository->firstMarriageYear();
    }

    /**
     * @return string
     */
    public function firstMarriageName(): string
    {
        return $this->familyDatesRepository->firstMarriageName();
    }

    /**
     * @return string
     */
    public function firstMarriagePlace(): string
    {
        return $this->familyDatesRepository->firstMarriagePlace();
    }

    /**
     * @return string
     */
    public function lastMarriage(): string
    {
        return $this->familyDatesRepository->lastMarriage();
    }

    /**
     * @return string
     */
    public function lastMarriageYear(): string
    {
        return $this->familyDatesRepository->lastMarriageYear();
    }

    /**
     * @return string
     */
    public function lastMarriageName(): string
    {
        return $this->familyDatesRepository->lastMarriageName();
    }

    /**
     * @return string
     */
    public function lastMarriagePlace(): string
    {
        return $this->familyDatesRepository->lastMarriagePlace();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->statsMarriageQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function statsFirstMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->statsFirstMarriageQuery($year1, $year2);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsMarr(string $color_from = null, string $color_to = null): string
    {
        return $this->familyRepository->statsMarr($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function firstDivorce(): string
    {
        return $this->familyDatesRepository->firstDivorce();
    }

    /**
     * @return string
     */
    public function firstDivorceYear(): string
    {
        return $this->familyDatesRepository->firstDivorceYear();
    }

    /**
     * @return string
     */
    public function firstDivorceName(): string
    {
        return $this->familyDatesRepository->firstDivorceName();
    }

    /**
     * @return string
     */
    public function firstDivorcePlace(): string
    {
        return $this->familyDatesRepository->firstDivorcePlace();
    }

    /**
     * @return string
     */
    public function lastDivorce(): string
    {
        return $this->familyDatesRepository->lastDivorce();
    }

    /**
     * @return string
     */
    public function lastDivorceYear(): string
    {
        return $this->familyDatesRepository->lastDivorceYear();
    }

    /**
     * @return string
     */
    public function lastDivorceName(): string
    {
        return $this->familyDatesRepository->lastDivorceName();
    }

    /**
     * @return string
     */
    public function lastDivorcePlace(): string
    {
        return $this->familyDatesRepository->lastDivorcePlace();
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDiv(string $color_from = null, string $color_to = null): string
    {
        return $this->familyRepository->statsDiv($color_from, $color_to);
    }

    /**
     * @return string
     */
    public function youngestMarriageFemale(): string
    {
        return $this->familyRepository->youngestMarriageFemale();
    }

    /**
     * @return string
     */
    public function youngestMarriageFemaleName(): string
    {
        return $this->familyRepository->youngestMarriageFemaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMarriageFemaleAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestMarriageFemale(): string
    {
        return $this->familyRepository->oldestMarriageFemale();
    }

    /**
     * @return string
     */
    public function oldestMarriageFemaleName(): string
    {
        return $this->familyRepository->oldestMarriageFemaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMarriageFemaleAge($show_years);
    }

    /**
     * @return string
     */
    public function youngestMarriageMale(): string
    {
        return $this->familyRepository->youngestMarriageMale();
    }

    /**
     * @return string
     */
    public function youngestMarriageMaleName(): string
    {
        return $this->familyRepository->youngestMarriageMaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMarriageMaleAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestMarriageMale(): string
    {
        return $this->familyRepository->oldestMarriageMale();
    }

    /**
     * @return string
     */
    public function oldestMarriageMaleName(): string
    {
        return $this->familyRepository->oldestMarriageMaleName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMarriageMaleAge($show_years);
    }

    /**
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array
     */
    public function statsMarrAgeQuery(string $sex, int $year1 = -1, int $year2 = -1): array
    {
        return $this->familyRepository->statsMarrAgeQuery($sex, $year1, $year2);
    }

    /**
     * @return string
     */
    public function statsMarrAge(): string
    {
        return $this->familyRepository->statsMarrAge();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMF(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesMF((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMFList(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesMFList((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFM(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesFM((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFMList(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesFMList((int) $total);
    }

    /**
     * @return string
     */
    public function topAgeOfMarriageFamily(): string
    {
        return $this->familyRepository->topAgeOfMarriageFamily();
    }

    /**
     * @return string
     */
    public function topAgeOfMarriage(): string
    {
        return $this->familyRepository->topAgeOfMarriage();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->familyRepository->topAgeOfMarriageFamilies((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->familyRepository->topAgeOfMarriageFamiliesList((int) $total);
    }

    /**
     * @return string
     */
    public function minAgeOfMarriageFamily(): string
    {
        return $this->familyRepository->minAgeOfMarriageFamily();
    }

    /**
     * @return string
     */
    public function minAgeOfMarriage(): string
    {
        return $this->familyRepository->minAgeOfMarriage();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->familyRepository->minAgeOfMarriageFamilies((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->familyRepository->minAgeOfMarriageFamiliesList((int) $total);
    }

    /**
     * @return string
     */
    public function youngestMother(): string
    {
        return $this->familyRepository->youngestMother();
    }

    /**
     * @return string
     */
    public function youngestMotherName(): string
    {
        return $this->familyRepository->youngestMotherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMotherAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMotherAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestMother(): string
    {
        return $this->familyRepository->oldestMother();
    }

    /**
     * @return string
     */
    public function oldestMotherName(): string
    {
        return $this->familyRepository->oldestMotherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMotherAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMotherAge($show_years);
    }

    /**
     * @return string
     */
    public function youngestFather(): string
    {
        return $this->familyRepository->youngestFather();
    }

    /**
     * @return string
     */
    public function youngestFatherName(): string
    {
        return $this->familyRepository->youngestFatherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function youngestFatherAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestFatherAge($show_years);
    }

    /**
     * @return string
     */
    public function oldestFather(): string
    {
        return $this->familyRepository->oldestFather();
    }

    /**
     * @return string
     */
    public function oldestFatherName(): string
    {
        return $this->familyRepository->oldestFatherName();
    }

    /**
     * @param string $show_years
     *
     * @return string
     */
    public function oldestFatherAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestFatherAge($show_years);
    }

    /**
     * @return string
     */
    public function totalMarriedMales(): string
    {
        return $this->familyRepository->totalMarriedMales();
    }

    /**
     * @return string
     */
    public function totalMarriedFemales(): string
    {
        return $this->familyRepository->totalMarriedFemales();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function monthFirstChildQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->monthFirstChildQuery($year1, $year2);
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return Builder
     */
    public function monthFirstChildBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->monthFirstChildBySexQuery($year1, $year2);
    }

    /**
     * @return string
     */
    public function largestFamily(): string
    {
        return $this->familyRepository->largestFamily();
    }

    /**
     * @return string
     */
    public function largestFamilySize(): string
    {
        return $this->familyRepository->largestFamilySize();
    }

    /**
     * @return string
     */
    public function largestFamilyName(): string
    {
        return $this->familyRepository->largestFamilyName();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamily(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestFamily((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamilyList(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestFamilyList((int) $total);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string $color_from = null,
        string $color_to = null,
        string $total = '10'
    ): string {
        return $this->familyRepository->chartLargestFamilies($color_from, $color_to, (int) $total);
    }

    /**
     * @return string
     */
    public function totalChildren(): string
    {
        return $this->familyRepository->totalChildren();
    }

    /**
     * @return string
     */
    public function averageChildren(): string
    {
        return $this->familyRepository->averageChildren();
    }

    /**
     * @param int $year1
     * @param int $year2
     *
     * @return array
     */
    public function statsChildrenQuery(int $year1 = -1, int $year2 = -1): array
    {
        return $this->familyRepository->statsChildrenQuery($year1, $year2);
    }

    /**
     * @return string
     */
    public function statsChildren(): string
    {
        return $this->familyRepository->statsChildren();
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeBetweenSiblingsName(string $total = '10'): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsName((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeBetweenSiblings(string $total = '10'): string
    {
        return $this->familyRepository->topAgeBetweenSiblings((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topAgeBetweenSiblingsFullName(string $total = '10'): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsFullName((int) $total);
    }

    /**
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsList(string $total = '10', string $one = ''): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsList((int) $total, $one);
    }

    /**
     * @return string
     */
    public function noChildrenFamilies(): string
    {
        return $this->familyRepository->noChildrenFamilies();
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function noChildrenFamiliesList(string $type = 'list'): string
    {
        return $this->familyRepository->noChildrenFamiliesList($type);
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
        return $this->familyRepository->chartNoChildrenFamilies((int) $year1, (int) $year2);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamily(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestGrandFamily((int) $total);
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamilyList(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestGrandFamilyList((int) $total);
    }

    /**
     * @return string
     */
    public function getCommonSurname(): string
    {
        return $this->individualRepository->getCommonSurname();
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
        return $this->individualRepository->commonSurnames((int) $threshold, (int) $number_of_surnames, $sorting);
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
        return $this->individualRepository->commonSurnamesTotals((int) $threshold, (int) $number_of_surnames, $sorting);
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
        return $this->individualRepository->commonSurnamesList((int) $threshold, (int) $number_of_surnames, $sorting);
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
        return $this->individualRepository
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
        string $color_from = null,
        string $color_to = null,
        string $number_of_surnames = '10'
    ): string {
        return $this->individualRepository
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
        return $this->individualRepository->commonGiven((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemale((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMale((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknown(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknown((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $maxtoshow
     *
     * @return string
     */
    public function chartCommonGiven(
        string $color_from = null,
        string $color_to = null,
        string $maxtoshow = '7'
    ): string {
        return $this->individualRepository->chartCommonGiven($color_from, $color_to, (int) $maxtoshow);
    }

    /**
     * @return string
     */
    public function usersLoggedIn(): string
    {
        return $this->userRepository->usersLoggedIn();
    }

    /**
     * @return string
     */
    public function usersLoggedInList(): string
    {
        return $this->userRepository->usersLoggedInList();
    }

    /**
     * @return int
     */
    public function usersLoggedInTotal(): int
    {
        return $this->userRepository->usersLoggedInTotal();
    }

    /**
     * @return int
     */
    public function usersLoggedInTotalAnon(): int
    {
        return $this->userRepository->usersLoggedInTotalAnon();
    }

    /**
     * @return int
     */
    public function usersLoggedInTotalVisible(): int
    {
        return $this->userRepository->usersLoggedInTotalVisible();
    }

    /**
     * @return string
     */
    public function userId(): string
    {
        return $this->userRepository->userId();
    }

    /**
     * @param string $visitor_text
     *
     * @return string
     */
    public function userName(string $visitor_text = ''): string
    {
        return $this->userRepository->userName($visitor_text);
    }

    /**
     * @return string
     */
    public function userFullName(): string
    {
        return $this->userRepository->userFullName();
    }

    /**
     * @return string
     */
    public function totalUsers(): string
    {
        return $this->userRepository->totalUsers();
    }

    /**
     * @return string
     */
    public function totalAdmins(): string
    {
        return $this->userRepository->totalAdmins();
    }

    /**
     * @return string
     */
    public function totalNonAdmins(): string
    {
        return $this->userRepository->totalNonAdmins();
    }

    /**
     * @return string
     */
    public function latestUserId(): string
    {
        return $this->latestUserRepository->latestUserId();
    }

    /**
     * @return string
     */
    public function latestUserName(): string
    {
        return $this->latestUserRepository->latestUserName();
    }

    /**
     * @return string
     */
    public function latestUserFullName(): string
    {
        return $this->latestUserRepository->latestUserFullName();
    }

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegDate(string $format = null): string
    {
        return $this->latestUserRepository->latestUserRegDate($format);
    }

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegTime(string $format = null): string
    {
        return $this->latestUserRepository->latestUserRegTime($format);
    }

    /**
     * @param string|null $yes
     * @param string|null $no
     *
     * @return string
     */
    public function latestUserLoggedin(string $yes = null, string $no = null): string
    {
        return $this->latestUserRepository->latestUserLoggedin($yes, $no);
    }

    /**
     * @return string
     */
    public function contactWebmaster(): string
    {
        return $this->contactRepository->contactWebmaster();
    }

    /**
     * @return string
     */
    public function contactGedcom(): string
    {
        return $this->contactRepository->contactGedcom();
    }

    /**
     * @return string
     */
    public function serverDate(): string
    {
        return $this->serverRepository->serverDate();
    }

    /**
     * @return string
     */
    public function serverTime(): string
    {
        return $this->serverRepository->serverTime();
    }

    /**
     * @return string
     */
    public function serverTime24(): string
    {
        return $this->serverRepository->serverTime24();
    }

    /**
     * What is the timezone of the server.
     *
     * @return string
     */
    public function serverTimezone(): string
    {
        return $this->serverRepository->serverTimezone();
    }

    /**
     * @return string
     */
    public function browserDate(): string
    {
        return $this->browserRepository->browserDate();
    }

    /**
     * @return string
     */
    public function browserTime(): string
    {
        return $this->browserRepository->browserTime();
    }

    /**
     * @return string
     */
    public function browserTimezone(): string
    {
        return $this->browserRepository->browserTimezone();
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCount($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountUser($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountIndi($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountFam($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountSour($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountRepo($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountNote($page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountObje($page_parameter);
    }

    /**
     * @return string
     */
    public function gedcomFavorites(): string
    {
        return $this->favoritesRepository->gedcomFavorites();
    }

    /**
     * @return string
     */
    public function userFavorites(): string
    {
        return $this->favoritesRepository->userFavorites();
    }

    /**
     * @return string
     */
    public function totalGedcomFavorites(): string
    {
        return $this->favoritesRepository->totalGedcomFavorites();
    }

    /**
     * @return string
     */
    public function totalUserFavorites(): string
    {
        return $this->favoritesRepository->totalUserFavorites();
    }

    /**
     * @return string
     */
    public function totalUserMessages(): string
    {
        return $this->messageRepository->totalUserMessages();
    }

    /**
     * @return string
     */
    public function totalUserJournal(): string
    {
        return $this->newsRepository->totalUserJournal();
    }

    /**
     * @return string
     */
    public function totalGedcomNews(): string
    {
        return $this->newsRepository->totalGedcomNews();
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
    public function callBlock(string $block = '', ...$params): ?string
    {
        /** @var ModuleBlockInterface|null $module */
        $module = $this->module_service
            ->findByComponent(ModuleBlockInterface::class, $this->tree, Auth::user())
            ->first(static function (ModuleInterface $module) use ($block): bool {
                return $module->name() === $block && $module->name() !== 'html';
            });

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
                $tags[$match[0] . '#'] = call_user_func([$this, $method], ...$params);
            }
        }

        return $tags;
    }
}
