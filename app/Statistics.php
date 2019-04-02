<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees;

use function count;
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
use function in_array;
use ReflectionMethod;

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
     * All public functions are available as keywords - except these ones
     *
     * @var string[]
     */
    private static $public_but_not_allowed = [
        '__construct',
        'embedTags',
        'iso3166',
        'getAllCountries',
        'getAllTagsTable',
        'getAllTagsText',
        'statsPlaces',
        'statsAgeQuery',
        'statsChildrenQuery',
        'statsMarrAgeQuery',
    ];

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
        $examples = [];

        foreach (get_class_methods($this) as $method) {
            $reflection = new ReflectionMethod($this, $method);
            if ($reflection->isPublic() && !in_array($method, self::$public_but_not_allowed, true) && (string) $reflection->getReturnType() !== Builder::class) {
                $examples[$method] = $this->$method();
            }
        }

        ksort($examples);

        $html = '';
        foreach ($examples as $tag => $value) {
            $html .= '<dt>#' . $tag . '#</dt>';
            $html .= '<dd>' . $value . '</dd>';
        }

        return '<dl>' . $html . '</dl>';
    }

    /**
     * Return a string of all supported tags in plain text.
     *
     * @return string
     */
    public function getAllTagsText(): string
    {
        $examples = [];

        foreach (get_class_methods($this) as $method) {
            $reflection = new ReflectionMethod($this, $method);
            if ($reflection->isPublic() && !in_array($method, self::$public_but_not_allowed, true) && (string) $reflection->getReturnType() !== Builder::class) {
                $examples[$method] = $method;
            }
        }

        ksort($examples);

        return implode('<br>', $examples);
    }

    /**
     * Get tags and their parsed results.
     *
     * @param string $text
     *
     * @return string[]
     */
    private function getTags(string $text): array
    {
        $tags    = [];
        $matches = [];

        preg_match_all('/#([^#]+)#/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $params = explode(':', $match[1]);
            $method = array_shift($params);

            if (method_exists($this, $method)) {
                $tags[$match[0]] = $this->$method(...$params);
            }
        }

        return $tags;
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
        if (strpos($text, '#') !== false) {
            $text = strtr($text, $this->getTags($text));
        }

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function gedcomFilename(): string
    {
        return $this->gedcomRepository->gedcomFilename();
    }

    /**
     * @inheritDoc
     */
    public function gedcomId(): int
    {
        return $this->gedcomRepository->gedcomId();
    }

    /**
     * @inheritDoc
     */
    public function gedcomTitle(): string
    {
        return $this->gedcomRepository->gedcomTitle();
    }

    /**
     * @inheritDoc
     */
    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcomRepository->gedcomCreatedSoftware();
    }

    /**
     * @inheritDoc
     */
    public function gedcomCreatedVersion(): string
    {
        return $this->gedcomRepository->gedcomCreatedVersion();
    }

    /**
     * @inheritDoc
     */
    public function gedcomDate(): string
    {
        return $this->gedcomRepository->gedcomDate();
    }

    /**
     * @inheritDoc
     */
    public function gedcomUpdated(): string
    {
        return $this->gedcomRepository->gedcomUpdated();
    }

    /**
     * @inheritDoc
     */
    public function gedcomRootId(): string
    {
        return $this->gedcomRepository->gedcomRootId();
    }

    /**
     * @inheritDoc
     */
    public function totalRecords(): string
    {
        return $this->individualRepository->totalRecords();
    }

    /**
     * @inheritDoc
     */
    public function totalIndividuals(): string
    {
        return $this->individualRepository->totalIndividuals();
    }

    /**
     * @inheritDoc
     */
    public function totalIndisWithSources(): string
    {
        return $this->individualRepository->totalIndisWithSources();
    }

    /**
     * @inheritDoc
     */
    public function chartIndisWithSources(
        string $color_from = null,
        string $color_to = null
    ): string {
        return $this->individualRepository->chartIndisWithSources($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function totalIndividualsPercentage(): string
    {
        return $this->individualRepository->totalIndividualsPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalFamilies(): string
    {
        return $this->individualRepository->totalFamilies();
    }

    /**
     * @inheritDoc
     */
    public function totalFamiliesPercentage(): string
    {
        return $this->individualRepository->totalFamiliesPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalFamsWithSources(): string
    {
        return $this->individualRepository->totalFamsWithSources();
    }

    /**
     * @inheritDoc
     */
    public function chartFamsWithSources(
        string $color_from = null,
        string $color_to = null
    ): string {
        return $this->individualRepository->chartFamsWithSources($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function totalSources(): string
    {
        return $this->individualRepository->totalSources();
    }

    /**
     * @inheritDoc
     */
    public function totalSourcesPercentage(): string
    {
        return $this->individualRepository->totalSourcesPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalNotes(): string
    {
        return $this->individualRepository->totalNotes();
    }

    /**
     * @inheritDoc
     */
    public function totalNotesPercentage(): string
    {
        return $this->individualRepository->totalNotesPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalRepositories(): string
    {
        return $this->individualRepository->totalRepositories();
    }

    /**
     * @inheritDoc
     */
    public function totalRepositoriesPercentage(): string
    {
        return $this->individualRepository->totalRepositoriesPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalSurnames(...$params): string
    {
        return $this->individualRepository->totalSurnames(...$params);
    }

    /**
     * @inheritDoc
     */
    public function totalGivennames(...$params): string
    {
        return $this->individualRepository->totalGivennames(...$params);
    }

    /**
     * @inheritDoc
     */
    public function totalEvents(array $events = []): string
    {
        return $this->eventRepository->totalEvents($events);
    }

    /**
     * @inheritDoc
     */
    public function totalEventsBirth(): string
    {
        return $this->eventRepository->totalEventsBirth();
    }

    /**
     * @inheritDoc
     */
    public function totalBirths(): string
    {
        return $this->eventRepository->totalBirths();
    }

    /**
     * @inheritDoc
     */
    public function totalEventsDeath(): string
    {
        return $this->eventRepository->totalEventsDeath();
    }

    /**
     * @inheritDoc
     */
    public function totalDeaths(): string
    {
        return $this->eventRepository->totalDeaths();
    }

    /**
     * @inheritDoc
     */
    public function totalEventsMarriage(): string
    {
        return $this->eventRepository->totalEventsMarriage();
    }

    /**
     * @inheritDoc
     */
    public function totalMarriages(): string
    {
        return $this->eventRepository->totalMarriages();
    }

    /**
     * @inheritDoc
     */
    public function totalEventsDivorce(): string
    {
        return $this->eventRepository->totalEventsDivorce();
    }

    /**
     * @inheritDoc
     */
    public function totalDivorces(): string
    {
        return $this->eventRepository->totalDivorces();
    }

    /**
     * @inheritDoc
     */
    public function totalEventsOther(): string
    {
        return $this->eventRepository->totalEventsOther();
    }

    /**
     * @inheritDoc
     */
    public function totalSexMales(): string
    {
        return $this->individualRepository->totalSexMales();
    }

    /**
     * @inheritDoc
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->individualRepository->totalSexMalesPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalSexFemales(): string
    {
        return $this->individualRepository->totalSexFemales();
    }

    /**
     * @inheritDoc
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->individualRepository->totalSexFemalesPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalSexUnknown(): string
    {
        return $this->individualRepository->totalSexUnknown();
    }

    /**
     * @inheritDoc
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->individualRepository->totalSexUnknownPercentage();
    }

    /**
     * @inheritDoc
     */
    public function chartSex(
        string $color_female = null,
        string $color_male = null,
        string $color_unknown = null
    ): string {
        return $this->individualRepository->chartSex($color_female, $color_male, $color_unknown);
    }

    /**
     * @inheritDoc
     */
    public function totalLiving(): string
    {
        return $this->individualRepository->totalLiving();
    }

    /**
     * @inheritDoc
     */
    public function totalLivingPercentage(): string
    {
        return $this->individualRepository->totalLivingPercentage();
    }

    /**
     * @inheritDoc
     */
    public function totalDeceased(): string
    {
        return $this->individualRepository->totalDeceased();
    }

    /**
     * @inheritDoc
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->individualRepository->totalDeceasedPercentage();
    }

    /**
     * @inheritDoc
     */
    public function chartMortality(string $color_living = null, string $color_dead = null): string
    {
        return $this->individualRepository->chartMortality($color_living, $color_dead);
    }

    /**
     * @inheritDoc
     */
    public function totalMedia(): string
    {
        return $this->mediaRepository->totalMedia();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaAudio(): string
    {
        return $this->mediaRepository->totalMediaAudio();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaBook(): string
    {
        return $this->mediaRepository->totalMediaBook();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCard(): string
    {
        return $this->mediaRepository->totalMediaCard();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCertificate(): string
    {
        return $this->mediaRepository->totalMediaCertificate();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaCoatOfArms(): string
    {
        return $this->mediaRepository->totalMediaCoatOfArms();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaDocument(): string
    {
        return $this->mediaRepository->totalMediaDocument();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaElectronic(): string
    {
        return $this->mediaRepository->totalMediaElectronic();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaMagazine(): string
    {
        return $this->mediaRepository->totalMediaMagazine();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaManuscript(): string
    {
        return $this->mediaRepository->totalMediaManuscript();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaMap(): string
    {
        return $this->mediaRepository->totalMediaMap();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaFiche(): string
    {
        return $this->mediaRepository->totalMediaFiche();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaFilm(): string
    {
        return $this->mediaRepository->totalMediaFilm();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaNewspaper(): string
    {
        return $this->mediaRepository->totalMediaNewspaper();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaPainting(): string
    {
        return $this->mediaRepository->totalMediaPainting();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaPhoto(): string
    {
        return $this->mediaRepository->totalMediaPhoto();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaTombstone(): string
    {
        return $this->mediaRepository->totalMediaTombstone();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaVideo(): string
    {
        return $this->mediaRepository->totalMediaVideo();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaOther(): string
    {
        return $this->mediaRepository->totalMediaOther();
    }

    /**
     * @inheritDoc
     */
    public function totalMediaUnknown(): string
    {
        return $this->mediaRepository->totalMediaUnknown();
    }

    /**
     * @inheritDoc
     */
    public function chartMedia(string $color_from = null, string $color_to = null): string
    {
        return $this->mediaRepository->chartMedia($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function statsPlaces(string $what = 'ALL', string $fact = '', int $parent = 0, bool $country = false): array
    {
        return $this->placeRepository->statsPlaces($what, $fact, $parent, $country);
    }

    /**
     * @inheritDoc
     */
    public function totalPlaces(): string
    {
        return $this->placeRepository->totalPlaces();
    }

    /**
     * @inheritDoc
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        return $this->placeRepository->chartDistribution($chart_shows, $chart_type, $surname);
    }

    /**
     * @inheritDoc
     */
    public function commonCountriesList(): string
    {
        return $this->placeRepository->commonCountriesList();
    }

    /**
     * @inheritDoc
     */
    public function commonBirthPlacesList(): string
    {
        return $this->placeRepository->commonBirthPlacesList();
    }

    /**
     * @inheritDoc
     */
    public function commonDeathPlacesList(): string
    {
        return $this->placeRepository->commonDeathPlacesList();
    }

    /**
     * @inheritDoc
     */
    public function commonMarriagePlacesList(): string
    {
        return $this->placeRepository->commonMarriagePlacesList();
    }

    /**
     * @inheritDoc
     */
    public function firstBirth(): string
    {
        return $this->familyDatesRepository->firstBirth();
    }

    /**
     * @inheritDoc
     */
    public function firstBirthYear(): string
    {
        return $this->familyDatesRepository->firstBirthYear();
    }

    /**
     * @inheritDoc
     */
    public function firstBirthName(): string
    {
        return $this->familyDatesRepository->firstBirthName();
    }

    /**
     * @inheritDoc
     */
    public function firstBirthPlace(): string
    {
        return $this->familyDatesRepository->firstBirthPlace();
    }

    /**
     * @inheritDoc
     */
    public function lastBirth(): string
    {
        return $this->familyDatesRepository->lastBirth();
    }

    /**
     * @inheritDoc
     */
    public function lastBirthYear(): string
    {
        return $this->familyDatesRepository->lastBirthYear();
    }

    /**
     * @inheritDoc
     */
    public function lastBirthName(): string
    {
        return $this->familyDatesRepository->lastBirthName();
    }

    /**
     * @inheritDoc
     */
    public function lastBirthPlace(): string
    {
        return $this->familyDatesRepository->lastBirthPlace();
    }

    /**
     * @inheritDoc
     */
    public function statsBirthQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsBirthQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsBirthBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsBirthBySexQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsBirth(string $color_from = null, string $color_to = null): string
    {
        return $this->individualRepository->statsBirth($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function firstDeath(): string
    {
        return $this->familyDatesRepository->firstDeath();
    }

    /**
     * @inheritDoc
     */
    public function firstDeathYear(): string
    {
        return $this->familyDatesRepository->firstDeathYear();
    }

    /**
     * @inheritDoc
     */
    public function firstDeathName(): string
    {
        return $this->familyDatesRepository->firstDeathName();
    }

    /**
     * @inheritDoc
     */
    public function firstDeathPlace(): string
    {
        return $this->familyDatesRepository->firstDeathPlace();
    }

    /**
     * @inheritDoc
     */
    public function lastDeath(): string
    {
        return $this->familyDatesRepository->lastDeath();
    }

    /**
     * @inheritDoc
     */
    public function lastDeathYear(): string
    {
        return $this->familyDatesRepository->lastDeathYear();
    }

    /**
     * @inheritDoc
     */
    public function lastDeathName(): string
    {
        return $this->familyDatesRepository->lastDeathName();
    }

    /**
     * @inheritDoc
     */
    public function lastDeathPlace(): string
    {
        return $this->familyDatesRepository->lastDeathPlace();
    }

    /**
     * @inheritDoc
     */
    public function statsDeathQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsDeathQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsDeathBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->individualRepository->statsDeathBySexQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsDeath(string $color_from = null, string $color_to = null): string
    {
        return $this->individualRepository->statsDeath($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function statsAgeQuery(string $related = 'BIRT', string $sex = 'BOTH', int $year1 = -1, int $year2 = -1)
    {
        return $this->individualRepository->statsAgeQuery($related, $sex, $year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsAge(): string
    {
        return $this->individualRepository->statsAge();
    }

    /**
     * @inheritDoc
     */
    public function longestLife(): string
    {
        return $this->individualRepository->longestLife();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeAge(): string
    {
        return $this->individualRepository->longestLifeAge();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeName(): string
    {
        return $this->individualRepository->longestLifeName();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeFemale(): string
    {
        return $this->individualRepository->longestLifeFemale();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeFemaleAge(): string
    {
        return $this->individualRepository->longestLifeFemaleAge();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeFemaleName(): string
    {
        return $this->individualRepository->longestLifeFemaleName();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeMale(): string
    {
        return $this->individualRepository->longestLifeMale();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeMaleAge(): string
    {
        return $this->individualRepository->longestLifeMaleAge();
    }

    /**
     * @inheritDoc
     */
    public function longestLifeMaleName(): string
    {
        return $this->individualRepository->longestLifeMaleName();
    }

    /**
     * @inheritDoc
     */
    public function topTenOldest(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldest((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestList(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestFemale(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemale((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestFemaleList(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemaleList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestMale(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMale((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestMaleList(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMaleList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestAlive((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestListAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestListAlive((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestFemaleAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemaleAlive((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestFemaleListAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestFemaleListAlive((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestMaleAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMaleAlive((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenOldestMaleListAlive(string $total = '10'): string
    {
        return $this->individualRepository->topTenOldestMaleListAlive((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function averageLifespan(bool $show_years = false): string
    {
        return $this->individualRepository->averageLifespan($show_years);
    }

    /**
     * @inheritDoc
     */
    public function averageLifespanFemale(bool $show_years = false): string
    {
        return $this->individualRepository->averageLifespanFemale($show_years);
    }

    /**
     * @inheritDoc
     */
    public function averageLifespanMale(bool $show_years = false): string
    {
        return $this->individualRepository->averageLifespanMale($show_years);
    }

    /**
     * @inheritDoc
     */
    public function firstEvent(): string
    {
        return $this->eventRepository->firstEvent();
    }

    /**
     * @inheritDoc
     */
    public function firstEventYear(): string
    {
        return $this->eventRepository->firstEventYear();
    }

    /**
     * @inheritDoc
     */
    public function firstEventType(): string
    {
        return $this->eventRepository->firstEventType();
    }

    /**
     * @inheritDoc
     */
    public function firstEventName(): string
    {
        return $this->eventRepository->firstEventName();
    }

    /**
     * @inheritDoc
     */
    public function firstEventPlace(): string
    {
        return $this->eventRepository->firstEventPlace();
    }

    /**
     * @inheritDoc
     */
    public function lastEvent(): string
    {
        return $this->eventRepository->lastEvent();
    }

    /**
     * @inheritDoc
     */
    public function lastEventYear(): string
    {
        return $this->eventRepository->lastEventYear();
    }

    /**
     * @inheritDoc
     */
    public function lastEventType(): string
    {
        return $this->eventRepository->lastEventType();
    }

    /**
     * @inheritDoc
     */
    public function lastEventName(): string
    {
        return $this->eventRepository->lastEventName();
    }

    /**
     * @inheritDoc
     */
    public function lastEventPlace(): string
    {
        return $this->eventRepository->lastEventType();
    }

    /**
     * @inheritDoc
     */
    public function firstMarriage(): string
    {
        return $this->familyDatesRepository->firstMarriage();
    }

    /**
     * @inheritDoc
     */
    public function firstMarriageYear(): string
    {
        return $this->familyDatesRepository->firstMarriageYear();
    }

    /**
     * @inheritDoc
     */
    public function firstMarriageName(): string
    {
        return $this->familyDatesRepository->firstMarriageName();
    }

    /**
     * @inheritDoc
     */
    public function firstMarriagePlace(): string
    {
        return $this->familyDatesRepository->firstMarriagePlace();
    }

    /**
     * @inheritDoc
     */
    public function lastMarriage(): string
    {
        return $this->familyDatesRepository->lastMarriage();
    }

    /**
     * @inheritDoc
     */
    public function lastMarriageYear(): string
    {
        return $this->familyDatesRepository->lastMarriageYear();
    }

    /**
     * @inheritDoc
     */
    public function lastMarriageName(): string
    {
        return $this->familyDatesRepository->lastMarriageName();
    }

    /**
     * @inheritDoc
     */
    public function lastMarriagePlace(): string
    {
        return $this->familyDatesRepository->lastMarriagePlace();
    }

    /**
     * @inheritDoc
     */
    public function statsMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->statsMarriageQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsFirstMarriageQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->statsFirstMarriageQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsMarr(string $color_from = null, string $color_to = null): string
    {
        return $this->familyRepository->statsMarr($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function firstDivorce(): string
    {
        return $this->familyDatesRepository->firstDivorce();
    }

    /**
     * @inheritDoc
     */
    public function firstDivorceYear(): string
    {
        return $this->familyDatesRepository->firstDivorceYear();
    }

    /**
     * @inheritDoc
     */
    public function firstDivorceName(): string
    {
        return $this->familyDatesRepository->firstDivorceName();
    }

    /**
     * @inheritDoc
     */
    public function firstDivorcePlace(): string
    {
        return $this->familyDatesRepository->firstDivorcePlace();
    }

    /**
     * @inheritDoc
     */
    public function lastDivorce(): string
    {
        return $this->familyDatesRepository->lastDivorce();
    }

    /**
     * @inheritDoc
     */
    public function lastDivorceYear(): string
    {
        return $this->familyDatesRepository->lastDivorceYear();
    }

    /**
     * @inheritDoc
     */
    public function lastDivorceName(): string
    {
        return $this->familyDatesRepository->lastDivorceName();
    }

    /**
     * @inheritDoc
     */
    public function lastDivorcePlace(): string
    {
        return $this->familyDatesRepository->lastDivorcePlace();
    }

    /**
     * @inheritDoc
     */
    public function statsDiv(string $color_from = null, string $color_to = null): string
    {
        return $this->familyRepository->statsDiv($color_from, $color_to);
    }

    /**
     * @inheritDoc
     */
    public function youngestMarriageFemale(): string
    {
        return $this->familyRepository->youngestMarriageFemale();
    }

    /**
     * @inheritDoc
     */
    public function youngestMarriageFemaleName(): string
    {
        return $this->familyRepository->youngestMarriageFemaleName();
    }

    /**
     * @inheritDoc
     */
    public function youngestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMarriageFemaleAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function oldestMarriageFemale(): string
    {
        return $this->familyRepository->oldestMarriageFemale();
    }

    /**
     * @inheritDoc
     */
    public function oldestMarriageFemaleName(): string
    {
        return $this->familyRepository->oldestMarriageFemaleName();
    }

    /**
     * @inheritDoc
     */
    public function oldestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMarriageFemaleAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function youngestMarriageMale(): string
    {
        return $this->familyRepository->youngestMarriageMale();
    }

    /**
     * @inheritDoc
     */
    public function youngestMarriageMaleName(): string
    {
        return $this->familyRepository->youngestMarriageMaleName();
    }

    /**
     * @inheritDoc
     */
    public function youngestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMarriageMaleAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function oldestMarriageMale(): string
    {
        return $this->familyRepository->oldestMarriageMale();
    }

    /**
     * @inheritDoc
     */
    public function oldestMarriageMaleName(): string
    {
        return $this->familyRepository->oldestMarriageMaleName();
    }

    /**
     * @inheritDoc
     */
    public function oldestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMarriageMaleAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function statsMarrAgeQuery(string $sex, int $year1 = -1, int $year2 = -1): array
    {
        return $this->familyRepository->statsMarrAgeQuery($sex, $year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsMarrAge(): string
    {
        return $this->familyRepository->statsMarrAge();
    }

    /**
     * @inheritDoc
     */
    public function ageBetweenSpousesMF(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesMF((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function ageBetweenSpousesMFList(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesMFList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function ageBetweenSpousesFM(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesFM((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function ageBetweenSpousesFMList(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesFMList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topAgeOfMarriageFamily(): string
    {
        return $this->familyRepository->topAgeOfMarriageFamily();
    }

    /**
     * @inheritDoc
     */
    public function topAgeOfMarriage(): string
    {
        return $this->familyRepository->topAgeOfMarriage();
    }

    /**
     * @inheritDoc
     */
    public function topAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->familyRepository->topAgeOfMarriageFamilies((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->familyRepository->topAgeOfMarriageFamiliesList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function minAgeOfMarriageFamily(): string
    {
        return $this->familyRepository->minAgeOfMarriageFamily();
    }

    /**
     * @inheritDoc
     */
    public function minAgeOfMarriage(): string
    {
        return $this->familyRepository->minAgeOfMarriage();
    }

    /**
     * @inheritDoc
     */
    public function minAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->familyRepository->minAgeOfMarriageFamilies((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function minAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->familyRepository->minAgeOfMarriageFamiliesList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function youngestMother(): string
    {
        return $this->familyRepository->youngestMother();
    }

    /**
     * @inheritDoc
     */
    public function youngestMotherName(): string
    {
        return $this->familyRepository->youngestMotherName();
    }

    /**
     * @inheritDoc
     */
    public function youngestMotherAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMotherAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function oldestMother(): string
    {
        return $this->familyRepository->oldestMother();
    }

    /**
     * @inheritDoc
     */
    public function oldestMotherName(): string
    {
        return $this->familyRepository->oldestMotherName();
    }

    /**
     * @inheritDoc
     */
    public function oldestMotherAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMotherAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function youngestFather(): string
    {
        return $this->familyRepository->youngestFather();
    }

    /**
     * @inheritDoc
     */
    public function youngestFatherName(): string
    {
        return $this->familyRepository->youngestFatherName();
    }

    /**
     * @inheritDoc
     */
    public function youngestFatherAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestFatherAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function oldestFather(): string
    {
        return $this->familyRepository->oldestFather();
    }

    /**
     * @inheritDoc
     */
    public function oldestFatherName(): string
    {
        return $this->familyRepository->oldestFatherName();
    }

    /**
     * @inheritDoc
     */
    public function oldestFatherAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestFatherAge($show_years);
    }

    /**
     * @inheritDoc
     */
    public function totalMarriedMales(): string
    {
        return $this->familyRepository->totalMarriedMales();
    }

    /**
     * @inheritDoc
     */
    public function totalMarriedFemales(): string
    {
        return $this->familyRepository->totalMarriedFemales();
    }

    /**
     * @inheritDoc
     */
    public function monthFirstChildQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->monthFirstChildQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function monthFirstChildBySexQuery(int $year1 = -1, int $year2 = -1): Builder
    {
        return $this->familyRepository->monthFirstChildBySexQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function largestFamily(): string
    {
        return $this->familyRepository->largestFamily();
    }

    /**
     * @inheritDoc
     */
    public function largestFamilySize(): string
    {
        return $this->familyRepository->largestFamilySize();
    }

    /**
     * @inheritDoc
     */
    public function largestFamilyName(): string
    {
        return $this->familyRepository->largestFamilyName();
    }

    /**
     * @inheritDoc
     */
    public function topTenLargestFamily(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestFamily((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenLargestFamilyList(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestFamilyList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function chartLargestFamilies(
        string $color_from = null,
        string $color_to = null,
        string $total = '10'
    ): string {
        return $this->familyRepository->chartLargestFamilies($color_from, $color_to, (int) $total);
    }

    /**
     * @inheritDoc
     */
    public function totalChildren(): string
    {
        return $this->familyRepository->totalChildren();
    }

    /**
     * @inheritDoc
     */
    public function averageChildren(): string
    {
        return $this->familyRepository->averageChildren();
    }

    /**
     * @inheritDoc
     */
    public function statsChildrenQuery(int $year1 = -1, int $year2 = -1): array
    {
        return $this->familyRepository->statsChildrenQuery($year1, $year2);
    }

    /**
     * @inheritDoc
     */
    public function statsChildren(): string
    {
        return $this->familyRepository->statsChildren();
    }

    /**
     * @inheritDoc
     */
    public function topAgeBetweenSiblingsName(string $total = '10'): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsName((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topAgeBetweenSiblings(string $total = '10'): string
    {
        return $this->familyRepository->topAgeBetweenSiblings((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topAgeBetweenSiblingsFullName(string $total = '10'): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsFullName((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topAgeBetweenSiblingsList(string $total = '10', string $one = ''): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsList((int) $total, $one);
    }

    /**
     * @inheritDoc
     */
    public function noChildrenFamilies(): string
    {
        return $this->familyRepository->noChildrenFamilies();
    }

    /**
     * @inheritDoc
     */
    public function noChildrenFamiliesList(string $type = 'list'): string
    {
        return $this->familyRepository->noChildrenFamiliesList($type);
    }

    /**
     * @inheritDoc
     */
    public function chartNoChildrenFamilies(
        string $year1 = '-1',
        string $year2 = '-1'
    ): string {
        return $this->familyRepository->chartNoChildrenFamilies((int) $year1, (int) $year2);
    }

    /**
     * @inheritDoc
     */
    public function topTenLargestGrandFamily(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestGrandFamily((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function topTenLargestGrandFamilyList(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestGrandFamilyList((int) $total);
    }

    /**
     * @inheritDoc
     */
    public function getCommonSurname(): string
    {
        return $this->individualRepository->getCommonSurname();
    }

    /**
     * @inheritDoc
     */
    public function commonSurnames(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->individualRepository->commonSurnames((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @inheritDoc
     */
    public function commonSurnamesTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'rcount'
    ): string {
        return $this->individualRepository->commonSurnamesTotals((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @inheritDoc
     */
    public function commonSurnamesList(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'alpha'
    ): string {
        return $this->individualRepository->commonSurnamesList((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @inheritDoc
     */
    public function commonSurnamesListTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'rcount'
    ): string {
        return $this->individualRepository
            ->commonSurnamesListTotals((int) $threshold, (int) $number_of_surnames, $sorting);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function commonGiven(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGiven((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenFemale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemale((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenFemaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenFemaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenFemaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenFemaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenMale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMale((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenMaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenMaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenMaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenMaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenUnknown(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknown((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenUnknownTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenUnknownList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownList((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenUnknownListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownListTotals((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function commonGivenUnknownTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownTable((int) $threshold, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function chartCommonGiven(
        string $color_from = null,
        string $color_to = null,
        string $maxtoshow = '7'
    ): string {
        return $this->individualRepository->chartCommonGiven($color_from, $color_to, (int) $maxtoshow);
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedIn(): string
    {
        return $this->userRepository->usersLoggedIn();
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInList(): string
    {
        return $this->userRepository->usersLoggedInList();
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInTotal(): int
    {
        return $this->userRepository->usersLoggedInTotal();
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInTotalAnon(): int
    {
        return $this->userRepository->usersLoggedInTotalAnon();
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInTotalVisible(): int
    {
        return $this->userRepository->usersLoggedInTotalVisible();
    }

    /**
     * @inheritDoc
     */
    public function userId(): string
    {
        return $this->userRepository->userId();
    }

    /**
     * @inheritDoc
     */
    public function userName(string $visitor_text = ''): string
    {
        return $this->userRepository->userName();
    }

    /**
     * @inheritDoc
     */
    public function userFullName(): string
    {
        return $this->userRepository->userFullName();
    }

    /**
     * @inheritDoc
     */
    public function totalUsers(): string
    {
        return $this->userRepository->totalUsers();
    }

    /**
     * @inheritDoc
     */
    public function totalAdmins(): string
    {
        return $this->userRepository->totalAdmins();
    }

    /**
     * @inheritDoc
     */
    public function totalNonAdmins(): string
    {
        return $this->userRepository->totalNonAdmins();
    }

    /**
     * @inheritDoc
     */
    public function latestUserId(): string
    {
        return $this->latestUserRepository->latestUserId();
    }

    /**
     * @inheritDoc
     */
    public function latestUserName(): string
    {
        return $this->latestUserRepository->latestUserName();
    }

    /**
     * @inheritDoc
     */
    public function latestUserFullName(): string
    {
        return $this->latestUserRepository->latestUserFullName();
    }

    /**
     * @inheritDoc
     */
    public function latestUserRegDate(string $format = null): string
    {
        return $this->latestUserRepository->latestUserRegDate();
    }

    /**
     * @inheritDoc
     */
    public function latestUserRegTime(string $format = null): string
    {
        return $this->latestUserRepository->latestUserRegTime();
    }

    /**
     * @inheritDoc
     */
    public function latestUserLoggedin(string $yes = null, string $no = null): string
    {
        return $this->latestUserRepository->latestUserLoggedin();
    }

    /**
     * @inheritDoc
     */
    public function contactWebmaster(): string
    {
        return $this->contactRepository->contactWebmaster();
    }

    /**
     * @inheritDoc
     */
    public function contactGedcom(): string
    {
        return $this->contactRepository->contactGedcom();
    }

    /**
     * @inheritDoc
     */
    public function serverDate(): string
    {
        return $this->serverRepository->serverDate();
    }

    /**
     * @inheritDoc
     */
    public function serverTime(): string
    {
        return $this->serverRepository->serverTime();
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function browserDate(): string
    {
        return $this->browserRepository->browserDate();
    }

    /**
     * @inheritDoc
     */
    public function browserTime(): string
    {
        return $this->browserRepository->browserTime();
    }

    /**
     * @inheritDoc
     */
    public function browserTimezone(): string
    {
        return $this->browserRepository->browserTimezone();
    }

    /**
     * @inheritDoc
     */
    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCount($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountUser($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountIndi($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountFam($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountSour($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountRepo($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountNote($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountObje($page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function gedcomFavorites(): string
    {
        return $this->favoritesRepository->gedcomFavorites();
    }

    /**
     * @inheritDoc
     */
    public function userFavorites(): string
    {
        return $this->favoritesRepository->userFavorites();
    }

    /**
     * @inheritDoc
     */
    public function totalGedcomFavorites(): string
    {
        return $this->favoritesRepository->totalGedcomFavorites();
    }

    /**
     * @inheritDoc
     */
    public function totalUserFavorites(): string
    {
        return $this->favoritesRepository->totalUserFavorites();
    }

    /**
     * @inheritDoc
     */
    public function totalUserMessages(): string
    {
        return $this->messageRepository->totalUserMessages();
    }

    /**
     * @inheritDoc
     */
    public function totalUserJournal(): string
    {
        return $this->newsRepository->totalUserJournal();
    }

    /**
     * @inheritDoc
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
            ->filter(function (ModuleInterface $module) use ($block): bool {
                return $module->name() === $block && $module->name() !== 'html';
            })
            ->first();

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

        return $module->getBlock($this->tree, 0, '', $cfg);
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
}
