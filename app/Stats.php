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

use Fisharebest\Webtrees\Statistics\Birth;
use Fisharebest\Webtrees\Statistics\Death;
use Fisharebest\Webtrees\Statistics\Google;
use Fisharebest\Webtrees\Statistics\Helper\Country;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\LatestRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\MediaRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\MessageRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\NewsRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\NoteRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\RepositoryRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ServerRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\SexRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\SourceRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\TotalRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\UserRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Repository\LatestRepository;
use Fisharebest\Webtrees\Statistics\Repository\MediaRepository;
use Fisharebest\Webtrees\Statistics\Repository\MessageRepository;
use Fisharebest\Webtrees\Statistics\Repository\NewsRepository;
use Fisharebest\Webtrees\Statistics\Repository\NoteRepository;
use Fisharebest\Webtrees\Statistics\Repository\RepositoryRepository;
use Fisharebest\Webtrees\Statistics\Repository\ServerRepository;
use Fisharebest\Webtrees\Statistics\Repository\SexRepository;
use Fisharebest\Webtrees\Statistics\Repository\SourceRepository;
use Fisharebest\Webtrees\Statistics\Repository\TotalRepository;
use Fisharebest\Webtrees\Statistics\Repository\UserRepository;
use ReflectionMethod;
use const PREG_SET_ORDER;

/**
 * A selection of pre-formatted statistical queries.
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class Stats implements
    GedcomRepositoryInterface,
    EventRepositoryInterface,
    MediaRepositoryInterface,
    SexRepositoryInterface,
    NoteRepositoryInterface,
    SourceRepositoryInterface,
    UserRepositoryInterface,
    ServerRepositoryInterface,
    BrowserRepositoryInterface,
    HitCountRepositoryInterface,
    TotalRepositoryInterface,
    RepositoryRepositoryInterface,
    LatestRepositoryInterface,
    FavoritesRepositoryInterface,
    NewsRepositoryInterface,
    MessageRepositoryInterface,
    ContactRepositoryInterface,
    FamilyDatesRepositoryInterface
{
    /** @var Tree Generate statistics for a specified tree. */
    private $tree;

    /** @var string[] All public functions are available as keywords - except these ones */
    private static $public_but_not_allowed = [
        '__construct',
        'embedTags',
        'iso3166',
        'getAllCountries',
        'getAllTagsTable',
        'getAllTagsText',
        'statsPlaces',
        'statsBirthQuery',
        'statsDeathQuery',
        'statsMarrQuery',
        'statsAgeQuery',
        'monthFirstChildQuery',
        'statsChildrenQuery',
        'statsMarrAgeQuery',
    ];

    /**
     * @var GedcomRepositoryInterface
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
     * @var SourceRepository
     */
    private $sourceRepository;

    /**
     * @var NoteRepositoryInterface
     */
    private $noteRepository;

    /**
     * @var Country
     */
    private $countryHelper;

    /**
     * @var MediaRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var SexRepositoryInterface
     */
    private $sexRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ServerRepositoryInterface
     */
    private $serverRepository;

    /**
     * @var BrowserRepositoryInterface
     */
    private $browserRepository;

    /**
     * @var HitCountRepositoryInterface
     */
    private $hitCountRepository;

    /**
     * @var RepositoryRepositoryInterface
     */
    private $repositoryRepository;

    /**
     * @var LatestRepositoryInterface
     */
    private $latestRepository;

    /**
     * @var FavoritesRepositoryInterface
     */
    private $favoritesRepository;

    /**
     * @var NewsRepositoryInterface
     */
    private $newsRepository;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var ContactRepositoryInterface
     */
    private $contactRepository;

    /**
     * @var FamilyDatesRepositoryInterface
     */
    private $familyDatesRepository;

    /**
     * Create the statistics for a tree.
     *
     * @param Tree $tree Generate statistics for this tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree                  = $tree;
        $this->countryHelper         = new Country();
        $this->gedcomRepository      = new GedcomRepository($tree);
        $this->individualRepository  = new IndividualRepository($tree);
        $this->familyRepository      = new FamilyRepository($tree);
        $this->familyDatesRepository = new FamilyDatesRepository($tree);
        $this->sourceRepository      = new SourceRepository($tree);
        $this->noteRepository        = new NoteRepository($tree);
        $this->mediaRepository       = new MediaRepository($tree);
        $this->eventRepository       = new EventRepository($tree);
        $this->sexRepository         = new SexRepository($tree);
        $this->userRepository        = new UserRepository($tree);
        $this->serverRepository      = new ServerRepository();
        $this->browserRepository     = new BrowserRepository();
        $this->hitCountRepository    = new HitCountRepository($tree);
        $this->repositoryRepository  = new RepositoryRepository($tree);
        $this->latestRepository      = new LatestRepository();
        $this->favoritesRepository   = new FavoritesRepository($tree);
        $this->newsRepository        = new NewsRepository($tree);
        $this->messageRepository     = new MessageRepository();
        $this->contactRepository     = new ContactRepository($tree);
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
            if ($reflection->isPublic() && !\in_array($method, self::$public_but_not_allowed, true)) {
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
            if ($reflection->isPublic() && !\in_array($method, self::$public_but_not_allowed, true)) {
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
     * Get the name used for GEDCOM files and URLs.
     *
     * @return string
     */
    public function gedcomFilename(): string
    {
        return $this->gedcomRepository->gedcomFilename();
    }

    /**
     * Get the internal ID number of the tree.
     *
     * @return int
     */
    public function gedcomId(): int
    {
        return $this->gedcomRepository->gedcomId();
    }

    /**
     * Get the descriptive title of the tree.
     *
     * @return string
     */
    public function gedcomTitle(): string
    {
        return $this->gedcomRepository->gedcomTitle();
    }

    /**
     * Get the software originally used to create the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedSoftware(): string
    {
        return $this->gedcomRepository->gedcomCreatedSoftware();
    }

    /**
     * Get the version of software which created the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedVersion(): string
    {
        return $this->gedcomRepository->gedcomCreatedVersion();
    }

    /**
     * Get the date the GEDCOM file was created.
     *
     * @return string
     */
    public function gedcomDate(): string
    {
        return $this->gedcomRepository->gedcomDate();
    }

    /**
     * When was this tree last updated?
     *
     * @return string
     */
    public function gedcomUpdated(): string
    {
        return $this->gedcomRepository->gedcomUpdated();
    }

    /**
     * What is the significant individual from this tree?
     *
     * @return string
     */
    public function gedcomRootId(): string
    {
        return $this->gedcomRepository->gedcomRootId();
    }

    /**
     * How many GEDCOM records exist in the tree.
     *
     * @return string
     */
    public function totalRecords(): string
    {
        return (new TotalRepository($this->tree))->totalRecords();
    }

    /**
     * How many individuals exist in the tree.
     *
     * @return string
     */
    public function totalIndividuals(): string
    {
        return $this->individualRepository->totalIndividuals();
    }

    /**
     * How many individuals have one or more sources.
     *
     * @return string
     */
    public function totalIndisWithSources(): string
    {
        return $this->individualRepository->totalIndisWithSources();
    }

    /**
     * Create a chart showing individuals with/without sources.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null
    ): string {
        return $this->individualRepository->chartIndisWithSources($size, $color_from, $color_to);
    }

    /**
     * Show the total individuals as a percentage.
     *
     * @return string
     */
    public function totalIndividualsPercentage(): string
    {
        return $this->individualRepository->totalIndividualsPercentage();
    }

    /**
     * Count the total families.
     *
     * @return string
     */
    public function totalFamilies(): string
    {
        return $this->familyRepository->totalFamilies();
    }

    /**
     * Show the total families as a percentage.
     *
     * @return string
     */
    public function totalFamiliesPercentage(): string
    {
        return $this->familyRepository->totalFamiliesPercentage();
    }

    /**
     * Count the families with with source records.
     *
     * @return string
     */
    public function totalFamsWithSources(): string
    {
        return $this->familyRepository->totalFamsWithSources();
    }

    /**
     * Create a chart of individuals with/without sources.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null
    ): string {
        return $this->familyRepository->chartFamsWithSources($size, $color_from, $color_to);
    }

    /**
     * Count the total number of sources.
     *
     * @return string
     */
    public function totalSources(): string
    {
        return $this->sourceRepository->totalSources();
    }

    /**
     * Show the number of sources as a percentage.
     *
     * @return string
     */
    public function totalSourcesPercentage(): string
    {
        return $this->sourceRepository->totalSourcesPercentage();
    }

    /**
     * Count the number of notes.
     *
     * @return string
     */
    public function totalNotes(): string
    {
        return $this->noteRepository->totalNotes();
    }

    /**
     * Show the number of notes as a percentage.
     *
     * @return string
     */
    public function totalNotesPercentage(): string
    {
        return $this->noteRepository->totalNotesPercentage();
    }

    /**
     * Count the number of repositories
     *
     * @return string
     */
    public function totalRepositories(): string
    {
        return $this->repositoryRepository->totalRepositories();
    }

    /**
     * Show the total number of repositories as a percentage.
     *
     * @return string
     */
    public function totalRepositoriesPercentage(): string
    {
        return $this->repositoryRepository->totalRepositoriesPercentage();
    }

    /**
     * Count the surnames.
     *
     * @param string ...$params
     *
     * @return string
     */
    public function totalSurnames(...$params): string
    {
        return $this->individualRepository->totalSurnames(...$params);
    }

    /**
     * Count the number of distinct given names, or count the number of
     * occurrences of a specific name or names.
     *
     * @param string ...$params
     *
     * @return string
     */
    public function totalGivennames(...$params): string
    {
        return $this->individualRepository->totalGivennames(...$params);
    }

    /**
     * Count the number of events (with dates).
     *
     * @param string[] $events
     *
     * @return string
     */
    public function totalEvents(array $events = []): string
    {
        return $this->eventRepository->totalEvents($events);
    }

    /**
     * Count the number of births.
     *
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return $this->eventRepository->totalEventsBirth();
    }

    /**
     * Count the number of births.
     *
     * @return string
     */
    public function totalBirths(): string
    {
        return $this->eventRepository->totalBirths();
    }

    /**
     * Count the number of deaths.
     *
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return $this->eventRepository->totalEventsDeath();
    }

    /**
     * Count the number of deaths.
     *
     * @return string
     */
    public function totalDeaths(): string
    {
        return $this->eventRepository->totalDeaths();
    }

    /**
     * Count the number of marriages.
     *
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return $this->eventRepository->totalEventsMarriage();
    }

    /**
     * Count the number of marriages.
     *
     * @return string
     */
    public function totalMarriages(): string
    {
        return $this->eventRepository->totalMarriages();
    }

    /**
     * Count the number of divorces.
     *
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return $this->eventRepository->totalEventsDivorce();
    }

    /**
     * Count the number of divorces.
     *
     * @return string
     */
    public function totalDivorces(): string
    {
        return $this->eventRepository->totalDivorces();
    }

    /**
     * Count the number of other events.
     *
     * @return string
     */
    public function totalEventsOther(): string
    {
        return $this->eventRepository->totalEventsOther();
    }

    /**
     * Count the number of males.
     *
     * @return string
     */
    public function totalSexMales(): string
    {
        return $this->sexRepository->totalSexMales();
    }

    /**
     * Count the number of males
     *
     * @return string
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->sexRepository->totalSexMalesPercentage();
    }

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemales(): string
    {
        return $this->sexRepository->totalSexFemales();
    }

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->sexRepository->totalSexFemalesPercentage();
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknown(): string
    {
        return $this->sexRepository->totalSexUnknown();
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->sexRepository->totalSexUnknownPercentage();
    }

    /**
     * Generate a chart showing sex distribution.
     *
     * @param string|null $size
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        string $size          = null,
        string $color_female  = null,
        string $color_male    = null,
        string $color_unknown = null
    ): string {
        return $this->sexRepository->chartSex($size, $color_female, $color_male, $color_unknown);
    }

    /**
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLiving(): string
    {
        return $this->individualRepository->totalLiving();
    }

    /**
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLivingPercentage(): string
    {
        return $this->individualRepository->totalLivingPercentage();
    }

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceased(): string
    {
        return $this->individualRepository->totalDeceased();
    }

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->individualRepository->totalDeceasedPercentage();
    }

    /**
     * Create a chart showing mortality.
     *
     * @param string|null $size
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(string $size = null, string $color_living = null, string $color_dead = null): string
    {
        return $this->individualRepository->chartMortality($size, $color_living, $color_dead);
    }

    /**
     * Count the number of media records.
     *
     * @return string
     */
    public function totalMedia(): string
    {
        return $this->mediaRepository->totalMedia();
    }

    /**
     * Count the number of media records with type "audio".
     *
     * @return string
     */
    public function totalMediaAudio(): string
    {
        return $this->mediaRepository->totalMediaAudio();
    }

    /**
     * Count the number of media records with type "book".
     *
     * @return string
     */
    public function totalMediaBook(): string
    {
        return $this->mediaRepository->totalMediaBook();
    }

    /**
     * Count the number of media records with type "card".
     *
     * @return string
     */
    public function totalMediaCard(): string
    {
        return $this->mediaRepository->totalMediaCard();
    }

    /**
     * Count the number of media records with type "certificate".
     *
     * @return string
     */
    public function totalMediaCertificate(): string
    {
        return $this->mediaRepository->totalMediaCertificate();
    }

    /**
     * Count the number of media records with type "coat of arms".
     *
     * @return string
     */
    public function totalMediaCoatOfArms(): string
    {
        return $this->mediaRepository->totalMediaCoatOfArms();
    }

    /**
     * Count the number of media records with type "document".
     *
     * @return string
     */
    public function totalMediaDocument(): string
    {
        return $this->mediaRepository->totalMediaDocument();
    }

    /**
     * Count the number of media records with type "electronic".
     *
     * @return string
     */
    public function totalMediaElectronic(): string
    {
        return $this->mediaRepository->totalMediaElectronic();
    }

    /**
     * Count the number of media records with type "magazine".
     *
     * @return string
     */
    public function totalMediaMagazine(): string
    {
        return $this->mediaRepository->totalMediaMagazine();
    }

    /**
     * Count the number of media records with type "manuscript".
     *
     * @return string
     */
    public function totalMediaManuscript(): string
    {
        return $this->mediaRepository->totalMediaManuscript();
    }

    /**
     * Count the number of media records with type "map".
     *
     * @return string
     */
    public function totalMediaMap(): string
    {
        return $this->mediaRepository->totalMediaMap();
    }

    /**
     * Count the number of media records with type "microfiche".
     *
     * @return string
     */
    public function totalMediaFiche(): string
    {
        return $this->mediaRepository->totalMediaFiche();
    }

    /**
     * Count the number of media records with type "microfilm".
     *
     * @return string
     */
    public function totalMediaFilm(): string
    {
        return $this->mediaRepository->totalMediaFilm();
    }

    /**
     * Count the number of media records with type "newspaper".
     *
     * @return string
     */
    public function totalMediaNewspaper(): string
    {
        return $this->mediaRepository->totalMediaNewspaper();
    }

    /**
     * Count the number of media records with type "painting".
     *
     * @return string
     */
    public function totalMediaPainting(): string
    {
        return $this->mediaRepository->totalMediaPainting();
    }

    /**
     * Count the number of media records with type "photograph".
     *
     * @return string
     */
    public function totalMediaPhoto(): string
    {
        return $this->mediaRepository->totalMediaPhoto();
    }

    /**
     * Count the number of media records with type "tombstone".
     *
     * @return string
     */
    public function totalMediaTombstone(): string
    {
        return $this->mediaRepository->totalMediaTombstone();
    }

    /**
     * Count the number of media records with type "video".
     *
     * @return string
     */
    public function totalMediaVideo(): string
    {
        return $this->mediaRepository->totalMediaVideo();
    }

    /**
     * Count the number of media records with type "other".
     *
     * @return string
     */
    public function totalMediaOther(): string
    {
        return $this->mediaRepository->totalMediaOther();
    }

    /**
     * Count the number of media records with type "unknown".
     *
     * @return string
     */
    public function totalMediaUnknown(): string
    {
        return $this->mediaRepository->totalMediaUnknown();
    }

    /**
     * Create a chart of media types.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return $this->mediaRepository->chartMedia($size, $color_from, $color_to);
    }

    /**
     * Places
     *
     * @param string $what
     * @param string $fact
     * @param int    $parent
     * @param bool   $country
     *
     * @return int[]|stdClass[]
     *
     * @deprecated Use \Fisharebest\Webtrees\Statistics\Places::statsPlaces instead
     */
    public function statsPlaces($what = 'ALL', $fact = '', $parent = 0, $country = false): array
    {
        return (new Places($this->tree))->statsPlaces($what, $fact, $parent, $country);
    }

    /**
     * Count total places.
     *
     * @return int
     */
    private function totalPlacesQuery(): int
    {
        return
            (int) Database::prepare("SELECT COUNT(*) FROM `##places` WHERE p_file=?")
                ->execute([$this->tree->id()])
                ->fetchOne();
    }

    /**
     * Count total places.
     *
     * @return string
     */
    public function totalPlaces(): string
    {
        return I18N::number($this->totalPlacesQuery());
    }

    /**
     * Create a chart showing where events occurred.
     *
     * @param string $chart_shows
     * @param string $chart_type
     * @param string $surname
     *
     * @return string
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type  = '',
        string $surname     = ''
    ) : string {
        return (new Google\ChartDistribution($this->tree))
            ->chartDistribution($chart_shows, $chart_type, $surname);
    }

    /**
     * A list of common countries.
     *
     * @return string
     */
    public function commonCountriesList(): string
    {
        $countries = $this->statsPlaces();

        if (empty($countries)) {
            return '';
        }

        $top10 = [];
        $i     = 1;

        // Get the country names for each language
        $country_names = [];
        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());
            $all_countries = $this->countryHelper->getAllCountries();
            foreach ($all_countries as $country_code => $country_name) {
                $country_names[$country_name] = $country_code;
            }
        }

        I18N::init(WT_LOCALE);

        $all_db_countries = [];
        foreach ($countries as $place) {
            $country = trim($place->country);
            if (array_key_exists($country, $country_names)) {
                if (!isset($all_db_countries[$country_names[$country]][$country])) {
                    $all_db_countries[$country_names[$country]][$country] = (int) $place->tot;
                } else {
                    $all_db_countries[$country_names[$country]][$country] += (int) $place->tot;
                }
            }
        }
        // get all the user’s countries names
        $all_countries = $this->countryHelper->getAllCountries();

        foreach ($all_db_countries as $country_code => $country) {
            foreach ($country as $country_name => $tot) {
                $tmp     = new Place($country_name, $this->tree);

                $top10[] = [
                    'place' => $tmp,
                    'count' => $tot,
                    'name'  => $all_countries[$country_code],
                ];
            }

            if ($i++ === 10) {
                break;
            }
        }

        return view(
            'statistics/other/top10-list',
            [
                'records' => $top10,
            ]
        );
    }

    /**
     * A list of common birth places.
     *
     * @return string
     */
    public function commonBirthPlacesList(): string
    {
        return (string) new BirthPlaces($this->tree);
    }

    /**
     * A list of common death places.
     *
     * @return string
     */
    public function commonDeathPlacesList(): string
    {
        return (string) new DeathPlaces($this->tree);
    }

    /**
     * A list of common marriage places.
     *
     * @return string
     */
    public function commonMarriagePlacesList(): string
    {
        return (string) new MarriagePlaces($this->tree);
    }

    /**
     * Find the earliest birth.
     *
     * @return string
     */
    public function firstBirth(): string
    {
        return $this->familyDatesRepository->firstBirth();
    }

    /**
     * Find the earliest birth year.
     *
     * @return string
     */
    public function firstBirthYear(): string
    {
        return $this->familyDatesRepository->firstBirthYear();
    }

    /**
     * Find the name of the earliest birth.
     *
     * @return string
     */
    public function firstBirthName(): string
    {
        return $this->familyDatesRepository->firstBirthName();
    }

    /**
     * Find the earliest birth place.
     *
     * @return string
     */
    public function firstBirthPlace(): string
    {
        return $this->familyDatesRepository->firstBirthPlace();
    }

    /**
     * Find the latest birth.
     *
     * @return string
     */
    public function lastBirth(): string
    {
        return $this->familyDatesRepository->lastBirth();
    }

    /**
     * Find the latest birth year.
     *
     * @return string
     */
    public function lastBirthYear(): string
    {
        return $this->familyDatesRepository->lastBirthYear();
    }

    /**
     * Find the latest birth name.
     *
     * @return string
     */
    public function lastBirthName(): string
    {
        return $this->familyDatesRepository->lastBirthName();
    }

    /**
     * Find the latest birth place.
     *
     * @return string
     */
    public function lastBirthPlace(): string
    {
        return $this->familyDatesRepository->lastBirthPlace();
    }

    /**
     * Create a chart of birth places.
     *
     * @param bool $simple
     * @param bool $sex
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsBirthQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1): array
    {
        return (new Birth($this->tree))->query($sex, $year1, $year2);
    }

    /**
     * General query on births.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsBirth(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return (new Google\ChartBirth($this->tree))
            ->chartBirth($size, $color_from, $color_to);
    }

    /**
     * Find the earliest death.
     *
     * @return string
     */
    public function firstDeath(): string
    {
        return $this->familyDatesRepository->firstDeath();
    }

    /**
     * Find the earliest death year.
     *
     * @return string
     */
    public function firstDeathYear(): string
    {
        return $this->familyDatesRepository->firstDeathYear();
    }

    /**
     * Find the earliest death name.
     *
     * @return string
     */
    public function firstDeathName(): string
    {
        return $this->familyDatesRepository->firstDeathName();
    }

    /**
     * Find the earliest death place.
     *
     * @return string
     */
    public function firstDeathPlace(): string
    {
        return $this->familyDatesRepository->firstDeathPlace();
    }

    /**
     * Find the latest death.
     *
     * @return string
     */
    public function lastDeath(): string
    {
        return $this->familyDatesRepository->lastDeath();
    }

    /**
     * Find the latest death year.
     *
     * @return string
     */
    public function lastDeathYear(): string
    {
        return $this->familyDatesRepository->lastDeathYear();
    }

    /**
     * Find the latest death name.
     *
     * @return string
     */
    public function lastDeathName(): string
    {
        return $this->familyDatesRepository->lastDeathName();
    }

    /**
     * Find the place of the latest death.
     *
     * @return string
     */
    public function lastDeathPlace(): string
    {
        return $this->familyDatesRepository->lastDeathPlace();
    }

    /**
     * Create a chart of death places.
     *
     * @param bool $simple
     * @param bool $sex
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsDeathQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1): array
    {
        return (new Death($this->tree))->query($sex, $year1, $year2);
    }

    /**
     * General query on deaths.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDeath(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return (new Google\ChartDeath($this->tree))
            ->chartDeath($size, $color_from, $color_to);
    }

    /**
     * Lifespan
     *
     * @param string $type
     * @param string $sex
     *
     * @return string
     */
    private function longlifeQuery($type, $sex): string
    {
        $sex_search = ' 1=1';
        if ($sex === 'F') {
            $sex_search = " i_sex='F'";
        } elseif ($sex === 'M') {
            $sex_search = " i_sex='M'";
        }

        $rows = $this->runSql(
            " SELECT" .
            " death.d_gid AS id," .
            " death.d_julianday2-birth.d_julianday1 AS age" .
            " FROM" .
            " `##dates` AS death," .
            " `##dates` AS birth," .
            " `##individuals` AS indi" .
            " WHERE" .
            " indi.i_id=birth.d_gid AND" .
            " birth.d_gid=death.d_gid AND" .
            " death.d_file={$this->tree->id()} AND" .
            " birth.d_file=death.d_file AND" .
            " birth.d_file=indi.i_file AND" .
            " birth.d_fact='BIRT' AND" .
            " death.d_fact='DEAT' AND" .
            " birth.d_julianday1<>0 AND" .
            " death.d_julianday1>birth.d_julianday2 AND" .
            $sex_search .
            " ORDER BY" .
            " age DESC LIMIT 1"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $row    = $rows[0];
        $person = Individual::getInstance($row->id, $this->tree);
        switch ($type) {
            default:
            case 'full':
                if ($person->canShowName()) {
                    $result = $person->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'age':
                $result = I18N::number((int) ($row->age / 365.25));
                break;
            case 'name':
                $result = '<a href="' . e($person->url()) . '">' . $person->getFullName() . '</a>';
                break;
        }

        return $result;
    }

    /**
     * Find the oldest individuals.
     *
     * @param string $type
     * @param string $sex
     * @param int    $total
     *
     * @return array
     */
    private function topTenOldestQuery(string $type, string $sex, int $total): array
    {
        if ($sex === 'F') {
            $sex_search = " AND i_sex='F' ";
        } elseif ($sex === 'M') {
            $sex_search = " AND i_sex='M' ";
        } else {
            $sex_search = '';
        }

        $rows = $this->runSql(
            "SELECT " .
            " MAX(death.d_julianday2-birth.d_julianday1) AS age, " .
            " death.d_gid AS deathdate " .
            "FROM " .
            " `##dates` AS death, " .
            " `##dates` AS birth, " .
            " `##individuals` AS indi " .
            "WHERE " .
            " indi.i_id=birth.d_gid AND " .
            " birth.d_gid=death.d_gid AND " .
            " death.d_file={$this->tree->id()} AND " .
            " birth.d_file=death.d_file AND " .
            " birth.d_file=indi.i_file AND " .
            " birth.d_fact='BIRT' AND " .
            " death.d_fact='DEAT' AND " .
            " birth.d_julianday1<>0 AND " .
            " death.d_julianday1>birth.d_julianday2 " .
            $sex_search .
            "GROUP BY deathdate " .
            "ORDER BY age DESC " .
            "LIMIT " . $total
        );

        if (!isset($rows[0])) {
            return [];
        }

        $top10 = [];
        foreach ($rows as $row) {
            $person = Individual::getInstance($row->deathdate, $this->tree);
            $age    = $row->age;

            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age .= 'd';
            }

            if ($person->canShow()) {
                $top10[] = [
                    'person' => $person,
                    'age'    => FunctionsDate::getAgeAtEvent($age),
                ];
            }
        }

        // TODO
//        if (I18N::direction() === 'rtl') {
//            $top10 = str_replace([
//                '[',
//                ']',
//                '(',
//                ')',
//                '+',
//            ], [
//                '&rlm;[',
//                '&rlm;]',
//                '&rlm;(',
//                '&rlm;)',
//                '&rlm;+',
//            ], $top10);
//        }

        return $top10;
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string $sex
     * @param int    $total
     *
     * @return array
     */
    private function topTenOldestAliveQuery(string $sex = 'BOTH', int $total = 10): array
    {
        $total = (int) $total;

        // TODO
//        if (!Auth::isMember($this->tree)) {
//            return I18N::translate('This information is private and cannot be shown.');
//        }

        if ($sex === 'F') {
            $sex_search = " AND i_sex='F'";
        } elseif ($sex === 'M') {
            $sex_search = " AND i_sex='M'";
        } else {
            $sex_search = '';
        }

        $rows  = $this->runSql(
            "SELECT" .
            " birth.d_gid AS id," .
            " MIN(birth.d_julianday1) AS age" .
            " FROM" .
            " `##dates` AS birth," .
            " `##individuals` AS indi" .
            " WHERE" .
            " indi.i_id=birth.d_gid AND" .
            " indi.i_gedcom NOT REGEXP '\\n1 (" . implode('|', Gedcom::DEATH_EVENTS) . ")' AND" .
            " birth.d_file={$this->tree->id()} AND" .
            " birth.d_fact='BIRT' AND" .
            " birth.d_file=indi.i_file AND" .
            " birth.d_julianday1<>0" .
            $sex_search .
            " GROUP BY id" .
            " ORDER BY age" .
            " ASC LIMIT " . $total
        );

        $top10 = [];

        foreach ($rows as $row) {
            $person = Individual::getInstance($row->id, $this->tree);
            $age    = (WT_CLIENT_JD - $row->age);

            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age .= 'd';
            }

            $top10[] = [
                'person' => $person,
                'age'    => FunctionsDate::getAgeAtEvent($age),
            ];
        }

        // TODO
//        if (I18N::direction() === 'rtl') {
//            $top10 = str_replace([
//                '[',
//                ']',
//                '(',
//                ')',
//                '+',
//            ], [
//                '&rlm;[',
//                '&rlm;]',
//                '&rlm;(',
//                '&rlm;)',
//                '&rlm;+',
//            ], $top10);
//        }

        return $top10;
    }

    /**
     * Find the average lifespan.
     *
     * @param string $sex
     * @param bool   $show_years
     *
     * @return string
     */
    private function averageLifespanQuery($sex = 'BOTH', $show_years = false): string
    {
        if ($sex === 'F') {
            $sex_search = " AND i_sex='F' ";
        } elseif ($sex === 'M') {
            $sex_search = " AND i_sex='M' ";
        } else {
            $sex_search = '';
        }
        $rows = $this->runSql(
            "SELECT IFNULL(AVG(death.d_julianday2-birth.d_julianday1), 0) AS age" .
            " FROM `##dates` AS death, `##dates` AS birth, `##individuals` AS indi" .
            " WHERE " .
            " indi.i_id=birth.d_gid AND " .
            " birth.d_gid=death.d_gid AND " .
            " death.d_file=" . $this->tree->id() . " AND " .
            " birth.d_file=death.d_file AND " .
            " birth.d_file=indi.i_file AND " .
            " birth.d_fact='BIRT' AND " .
            " death.d_fact='DEAT' AND " .
            " birth.d_julianday1<>0 AND " .
            " death.d_julianday1>birth.d_julianday2 " .
            $sex_search
        );

        $age = $rows[0]->age;
        if ($show_years) {
            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } elseif (!empty($age)) {
                $age .= 'd';
            }

            return FunctionsDate::getAgeAtEvent($age);
        }

        return I18N::number($age / 365.25);
    }

    /**
     * General query on ages.
     *
     * @param bool   $simple
     * @param string $related
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array|string
     */
    public function statsAgeQuery($simple = true, $related = 'BIRT', $sex = 'BOTH', $year1 = -1, $year2 = -1)
    {
        return (new StatisticAge($this->tree))->query($related, $sex, $year1, $year2);
    }

    /**
     * General query on ages.
     *
     * @param string $size
     *
     * @return string
     */
    public function statsAge(string $size = '230x250'): string
    {
        return (new Google\ChartAge($this->tree))->chartAge($size);
    }

    /**
     * Find the longest lived individual.
     *
     * @return string
     */
    public function longestLife(): string
    {
        return $this->longlifeQuery('full', 'BOTH');
    }

    /**
     * Find the age of the longest lived individual.
     *
     * @return string
     */
    public function longestLifeAge(): string
    {
        return $this->longlifeQuery('age', 'BOTH');
    }

    /**
     * Find the name of the longest lived individual.
     *
     * @return string
     */
    public function longestLifeName(): string
    {
        return $this->longlifeQuery('name', 'BOTH');
    }

    /**
     * Find the oldest individuals.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenOldest(string $total = '10'): string
    {
        $records = $this->topTenOldestQuery('nolist', 'BOTH', (int) $total);

        return view(
            'statistics/individuals/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestList(string $total = '10'): string
    {
        $records = $this->topTenOldestQuery('list', 'BOTH', (int) $total);

        return view(
            'statistics/individuals/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestAlive(string $total = '10'): string
    {
        $records = $this->topTenOldestAliveQuery('BOTH', (int) $total);

        return view(
            'statistics/individuals/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestListAlive(string $total = '10'): string
    {
        $records = $this->topTenOldestAliveQuery('BOTH', (int) $total);

        return view(
            'statistics/individuals/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the average lifespan.
     *
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespan($show_years = false): string
    {
        return $this->averageLifespanQuery('BOTH', $show_years);
    }

    /**
     * Find the longest lived female.
     *
     * @return string
     */
    public function longestLifeFemale(): string
    {
        return $this->longlifeQuery('full', 'F');
    }

    /**
     * Find the age of the longest lived female.
     *
     * @return string
     */
    public function longestLifeFemaleAge(): string
    {
        return $this->longlifeQuery('age', 'F');
    }

    /**
     * Find the name of the longest lived female.
     *
     * @return string
     */
    public function longestLifeFemaleName(): string
    {
        return $this->longlifeQuery('name', 'F');
    }

    /**
     * Find the oldest females.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemale(string $total = '10'): string
    {
        $records = $this->topTenOldestQuery('nolist', 'F', (int) $total);

        return view(
            'statistics/individuals/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the oldest living females.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestFemaleList(string $total = '10'): string
    {
        $records = $this->topTenOldestQuery('list', 'F', (int) $total);

        return view(
            'statistics/individuals/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the oldest living females.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestFemaleAlive(string $total = '10'): string
    {
        $records = $this->topTenOldestAliveQuery('F', (int) $total);

        return view(
            'statistics/individuals/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the oldest living females.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestFemaleListAlive(string $total = '10'): string
    {
        $records = $this->topTenOldestAliveQuery('F', (int) $total);

        return view(
            'statistics/individuals/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the average lifespan of females.
     *
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespanFemale($show_years = false): string
    {
        return $this->averageLifespanQuery('F', $show_years);
    }

    /**
     * Find the longest lived male.
     *
     * @return string
     */
    public function longestLifeMale(): string
    {
        return $this->longlifeQuery('full', 'M');
    }

    /**
     * Find the age of the longest lived male.
     *
     * @return string
     */
    public function longestLifeMaleAge(): string
    {
        return $this->longlifeQuery('age', 'M');
    }

    /**
     * Find the name of the longest lived male.
     *
     * @return string
     */
    public function longestLifeMaleName(): string
    {
        return $this->longlifeQuery('name', 'M');
    }

    /**
     * Find the longest lived males.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMale(string $total = '10'): string
    {
        $records = $this->topTenOldestQuery('nolist', 'M', (int) $total);

        return view(
            'statistics/individuals/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the longest lived males.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenOldestMaleList(string $total = '10'): string
    {
        $records = $this->topTenOldestQuery('list', 'M', (int) $total);

        return view(
            'statistics/individuals/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the longest lived living males.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestMaleAlive(string $total = '10'): string
    {
        $records = $this->topTenOldestAliveQuery('M', (int) $total);

        return view(
            'statistics/individuals/top10-nolist',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the longest lived living males.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestMaleListAlive(string $total = '10'): string
    {
        $records = $this->topTenOldestAliveQuery('M', (int) $total);

        return view(
            'statistics/individuals/top10-list',
            [
                'records' => $records,
            ]
        );
    }

    /**
     * Find the average male lifespan.
     *
     * @param bool $show_years
     *
     * @return string
     */
    public function averageLifespanMale($show_years = false): string
    {
        return $this->averageLifespanQuery('M', $show_years);
    }

    /**
     * Find the earliest event.
     *
     * @return string
     */
    public function firstEvent(): string
    {
        return $this->eventRepository->firstEvent();
    }

    /**
     * Find the year of the earliest event.
     *
     * @return string
     */
    public function firstEventYear(): string
    {
        return $this->eventRepository->firstEventYear();
    }

    /**
     * Find the type of the earliest event.
     *
     * @return string
     */
    public function firstEventType(): string
    {
        return $this->eventRepository->firstEventType();
    }

    /**
     * Find the name of the individual with the earliest event.
     *
     * @return string
     */
    public function firstEventName(): string
    {
        return $this->eventRepository->firstEventName();
    }

    /**
     * Find the location of the earliest event.
     *
     * @return string
     */
    public function firstEventPlace(): string
    {
        return $this->eventRepository->firstEventPlace();
    }

    /**
     * Find the latest event.
     *
     * @return string
     */
    public function lastEvent(): string
    {
        return $this->eventRepository->lastEvent();
    }

    /**
     * Find the year of the latest event.
     *
     * @return string
     */
    public function lastEventYear(): string
    {
        return $this->eventRepository->lastEventYear();
    }

    /**
     * Find the type of the latest event.
     *
     * @return string
     */
    public function lastEventType(): string
    {
        return $this->eventRepository->lastEventType();
    }

    /**
     * Find the name of the individual with the latest event.
     *
     * @return string
     */
    public function lastEventName(): string
    {
        return $this->eventRepository->lastEventName();
    }

    /**
     * FInd the location of the latest event.
     *
     * @return string
     */
    public function lastEventPlace(): string
    {
        return $this->eventRepository->lastEventType();
    }

    /**
     * Find the earliest marriage.
     *
     * @return string
     */
    public function firstMarriage(): string
    {
        return $this->familyDatesRepository->firstMarriage();
    }

    /**
     * Find the year of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageYear(): string
    {
        return $this->familyDatesRepository->firstMarriageYear();
    }

    /**
     * Find the names of spouses of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageName(): string
    {
        return $this->familyDatesRepository->firstMarriageName();
    }

    /**
     * Find the place of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriagePlace(): string
    {
        return $this->familyDatesRepository->firstMarriagePlace();
    }

    /**
     * Find the latest marriage.
     *
     * @return string
     */
    public function lastMarriage(): string
    {
        return $this->familyDatesRepository->lastMarriage();
    }

    /**
     * Find the year of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageYear(): string
    {
        return $this->familyDatesRepository->lastMarriageYear();
    }

    /**
     * Find the names of spouses of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageName(): string
    {
        return $this->familyDatesRepository->lastMarriageName();
    }

    /**
     * Find the location of the latest marriage.
     *
     * @return string
     */
    public function lastMarriagePlace(): string
    {
        return $this->familyDatesRepository->lastMarriagePlace();
    }

    /**
     * General query on marriages.
     *
     * @param bool $simple
     * @param bool $first
     * @param int  $year1
     * @param int  $year2
     *
     * @return array
     */
    public function statsMarrQuery($simple = true, $first = false, $year1 = -1, $year2 = -1): array
    {
        return $this->familyRepository->statsMarrQuery($first, $year1, $year2);
    }

    /**
     * General query on marriages.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsMarr(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return (new Google\ChartMarriage($this->tree))
            ->chartMarriage($size, $color_from, $color_to);
    }

    /**
     * Find the earliest divorce.
     *
     * @return string
     */
    public function firstDivorce(): string
    {
        return $this->familyDatesRepository->firstDivorce();
    }

    /**
     * Find the year of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceYear(): string
    {
        return $this->familyDatesRepository->firstDivorceYear();
    }

    /**
     * Find the names of individuals in the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceName(): string
    {
        return $this->familyDatesRepository->firstDivorceName();
    }

    /**
     * Find the location of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorcePlace(): string
    {
        return $this->familyDatesRepository->firstDivorcePlace();
    }

    /**
     * Find the latest divorce.
     *
     * @return string
     */
    public function lastDivorce(): string
    {
        return $this->familyDatesRepository->lastDivorce();
    }

    /**
     * Find the year of the latest divorce.
     *
     * @return string
     */
    public function lastDivorceYear(): string
    {
        return $this->familyDatesRepository->lastDivorceYear();
    }

    /**
     * Find the names of the individuals in the latest divorce.
     *
     * @return string
     */
    public function lastDivorceName(): string
    {
        return $this->familyDatesRepository->lastDivorceName();
    }

    /**
     * Find the location of the latest divorce.
     *
     * @return string
     */
    public function lastDivorcePlace(): string
    {
        return $this->familyDatesRepository->lastDivorcePlace();
    }

    /**
     * General divorce query.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function statsDiv(string $size = null, string $color_from = null, string $color_to = null): string
    {
        return (new Google\ChartDivorce($this->tree))
            ->chartDivorce($size, $color_from, $color_to);
    }

    /**
     * Find the youngest wife.
     *
     * @return string
     */
    public function youngestMarriageFemale(): string
    {
        return $this->familyRepository->youngestMarriageFemale();
    }

    /**
     * Find the name of the youngest wife.
     *
     * @return string
     */
    public function youngestMarriageFemaleName(): string
    {
        return $this->familyRepository->youngestMarriageFemaleName();
    }

    /**
     * Find the age of the youngest wife.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMarriageFemaleAge($show_years);
    }

    /**
     * Find the oldest wife.
     *
     * @return string
     */
    public function oldestMarriageFemale(): string
    {
        return $this->familyRepository->oldestMarriageFemale();
    }

    /**
     * Find the name of the oldest wife.
     *
     * @return string
     */
    public function oldestMarriageFemaleName(): string
    {
        return $this->familyRepository->oldestMarriageFemaleName();
    }

    /**
     * Find the age of the oldest wife.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageFemaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMarriageFemaleAge($show_years);
    }

    /**
     * Find the youngest husband.
     *
     * @return string
     */
    public function youngestMarriageMale(): string
    {
        return $this->familyRepository->youngestMarriageMale();
    }

    /**
     * Find the name of the youngest husband.
     *
     * @return string
     */
    public function youngestMarriageMaleName(): string
    {
        return $this->familyRepository->youngestMarriageMaleName();
    }

    /**
     * Find the age of the youngest husband.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMarriageMaleAge($show_years);
    }

    /**
     * Find the oldest husband.
     *
     * @return string
     */
    public function oldestMarriageMale(): string
    {
        return $this->familyRepository->oldestMarriageMale();
    }

    /**
     * Find the name of the oldest husband.
     *
     * @return string
     */
    public function oldestMarriageMaleName(): string
    {
        return $this->familyRepository->oldestMarriageMaleName();
    }

    /**
     * Find the age of the oldest husband.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMarriageMaleAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMarriageMaleAge($show_years);
    }

    /**
     * General query on ages at marriage.
     *
     * @param bool   $simple
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return array
     */
    public function statsMarrAgeQuery($simple = true, $sex = 'M', $year1 = -1, $year2 = -1): array
    {
        return $this->familyRepository->statsMarrAgeQuery($sex, $year1, $year2);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $size
     *
     * @return string
     */
    public function statsMarrAge(string $size = '200x250'): string
    {
        return $this->familyRepository->statsMarrAge($size);
    }

    /**
     * Find the age between husband and wife.
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMF(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesMF($total);
    }

    /**
     * Find the age between husband and wife.
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesMFList(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesMFList($total);
    }

    /**
     * Find the age between wife and husband..
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFM(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesFM($total);
    }

    /**
     * Find the age between wife and husband..
     *
     * @param string $total
     *
     * @return string
     */
    public function ageBetweenSpousesFMList(string $total = '10'): string
    {
        return $this->familyRepository->ageBetweenSpousesFMList($total);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function topAgeOfMarriageFamily(): string
    {
        return $this->familyRepository->topAgeOfMarriageFamily();
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function topAgeOfMarriage(): string
    {
        return $this->familyRepository->topAgeOfMarriage();
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->familyRepository->topAgeOfMarriageFamilies($total);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function topAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->familyRepository->topAgeOfMarriageFamiliesList($total);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function minAgeOfMarriageFamily(): string
    {
        return $this->familyRepository->minAgeOfMarriageFamily();
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function minAgeOfMarriage(): string
    {
        return $this->familyRepository->minAgeOfMarriage();
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamilies(string $total = '10'): string
    {
        return $this->familyRepository->minAgeOfMarriageFamilies($total);
    }

    /**
     * General query on marriage ages.
     *
     * @param string $total
     *
     * @return string
     */
    public function minAgeOfMarriageFamiliesList(string $total = '10'): string
    {
        return $this->familyRepository->minAgeOfMarriageFamiliesList($total);
    }

    /**
     * Find the youngest mother
     *
     * @return string
     */
    public function youngestMother(): string
    {
        return $this->familyRepository->youngestMother();
    }

    /**
     * Find the name of the youngest mother.
     *
     * @return string
     */
    public function youngestMotherName(): string
    {
        return $this->familyRepository->youngestMotherName();
    }

    /**
     * Find the age of the youngest mother.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestMotherAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestMotherAge($show_years);
    }

    /**
     * Find the oldest mother.
     *
     * @return string
     */
    public function oldestMother(): string
    {
        return $this->familyRepository->oldestMother();
    }

    /**
     * Find the name of the oldest mother.
     *
     * @return string
     */
    public function oldestMotherName(): string
    {
        return $this->familyRepository->oldestMotherName();
    }

    /**
     * Find the age of the oldest mother.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestMotherAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestMotherAge($show_years);
    }

    /**
     * Find the youngest father.
     *
     * @return string
     */
    public function youngestFather(): string
    {
        return $this->familyRepository->youngestFather();
    }

    /**
     * Find the name of the youngest father.
     *
     * @return string
     */
    public function youngestFatherName(): string
    {
        return $this->familyRepository->youngestFatherName();
    }

    /**
     * Find the age of the youngest father.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function youngestFatherAge(string $show_years = ''): string
    {
        return $this->familyRepository->youngestFatherAge($show_years);
    }

    /**
     * Find the oldest father.
     *
     * @return string
     */
    public function oldestFather(): string
    {
        return $this->familyRepository->oldestFather();
    }

    /**
     * Find the name of the oldest father.
     *
     * @return string
     */
    public function oldestFatherName(): string
    {
        return $this->familyRepository->oldestFatherName();
    }

    /**
     * Find the age of the oldest father.
     *
     * @param string $show_years
     *
     * @return string
     */
    public function oldestFatherAge(string $show_years = ''): string
    {
        return $this->familyRepository->oldestFatherAge($show_years);
    }

    /**
     * Number of husbands.
     *
     * @return string
     */
    public function totalMarriedMales(): string
    {
        return $this->familyRepository->totalMarriedMales();
    }

    /**
     * Number of wives.
     *
     * @return string
     */
    public function totalMarriedFemales(): string
    {
        return $this->familyRepository->totalMarriedFemales();
    }

    /**
     * Find the month in the year of the birth of the first child.
     *
     * @param bool $sex
     *
     * @return stdClass[]
     */
    public function monthFirstChildQuery(bool $sex = false): array
    {
        return $this->familyRepository->monthFirstChildQuery($sex);
    }

    /**
     * Find the family with the most children.
     *
     * @return string
     */
    public function largestFamily(): string
    {
        return $this->familyRepository->largestFamily();
    }

    /**
     * Find the number of children in the largest family.
     *
     * @return string
     */
    public function largestFamilySize(): string
    {
        return $this->familyRepository->largestFamilySize();
    }

    /**
     * Find the family with the most children.
     *
     * @return string
     */
    public function largestFamilyName(): string
    {
        return $this->familyRepository->largestFamilyName();
    }

    /**
     * The the families with the most children.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamily(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestFamily($total);
    }

    /**
     * Find the families with the most children.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestFamilyList(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestFamilyList($total);
    }

    /**
     * Create a chart of the largest families.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null,
        string $total      = '10'
    ): string {
        return $this->familyRepository->chartLargestFamilies($size, $color_from, $color_to, $total);
    }

    /**
     * Count the total children.
     *
     * @return string
     */
    public function totalChildren(): string
    {
        return $this->familyRepository->totalChildren();
    }

    /**
     * Find the average number of children in families.
     *
     * @return string
     */
    public function averageChildren(): string
    {
        return $this->familyRepository->averageChildren();
    }

    /**
     * General query on familes/children.
     *
     * @param bool   $simple
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     *
     * @return stdClass[]
     */
    public function statsChildrenQuery($simple = true, $sex = 'BOTH', $year1 = -1, $year2 = -1): array
    {
        return $this->familyRepository->statsChildrenQuery($sex, $year1, $year2);
    }

    /**
     * Genearl query on families/children.
     *
     * @param string $size
     *
     * @return string
     */
    public function statsChildren(string $size = '220x200'): string
    {
        return $this->familyRepository->statsChildren($size);
    }

    /**
     * Find the names of siblings with the widest age gap.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsName(string $total = '10', string $one = ''): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsName($total, $one);
    }

    /**
     * Find the widest age gap between siblings.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblings(string $total = '10', string $one = ''): string
    {
        return $this->familyRepository->topAgeBetweenSiblings($total, $one);
    }

    /**
     * Find the name of siblings with the widest age gap.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsFullName(string $total = '10', string $one = ''): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsFullName($total, $one);
    }

    /**
     * Find the siblings with the widest age gaps.
     *
     * @param string $total
     * @param string $one
     *
     * @return string
     */
    public function topAgeBetweenSiblingsList(string $total = '10', string $one = ''): string
    {
        return $this->familyRepository->topAgeBetweenSiblingsList($total, $one);
    }

    /**
     * Find the families with no children.
     *
     * @return string
     */
    public function noChildrenFamilies(): string
    {
        return $this->familyRepository->noChildrenFamilies();
    }

    /**
     * Find the families with no children.
     *
     * @param string $type
     *
     * @return string
     */
    public function noChildrenFamiliesList($type = 'list'): string
    {
        return $this->familyRepository->noChildrenFamiliesList($type);
    }

    /**
     * Create a chart of children with no families.
     *
     * @param string $size
     * @param string $year1
     * @param string $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(string $size = '220x200', $year1 = '-1', $year2 = '-1'): string
    {
        return $this->familyRepository->chartNoChildrenFamilies($size, $year1, $year2);
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamily(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestGrandFamily($total);
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param string $total
     *
     * @return string
     */
    public function topTenLargestGrandFamilyList(string $total = '10'): string
    {
        return $this->familyRepository->topTenLargestGrandFamilyList($total);
    }

    /**
     * Find common surnames.
     *
     * @return string
     *
     * @deprecated
     */
    public function getCommonSurname(): string
    {
        return $this->individualRepository->getCommonSurname();
    }

    /**
     * Find common surnames.
     *
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
        return $this->individualRepository->commonSurnames($threshold, $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'rcount'
    ): string {
        return $this->individualRepository->commonSurnamesTotals($threshold, $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
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
        return $this->individualRepository->commonSurnamesList($threshold, $number_of_surnames, $sorting);
    }

    /**
     * Find common surnames.
     *
     * @param string $threshold
     * @param string $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    public function commonSurnamesListTotals(
        string $threshold = '1',
        string $number_of_surnames = '10',
        string $sorting = 'rcount'
    ): string {
        return $this->individualRepository->commonSurnamesListTotals($threshold, $number_of_surnames, $sorting);
    }

    /**
     * Create a chart of common surnames.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $number_of_surnames
     *
     * @return string
     */
    public function chartCommonSurnames(
        string $size = null,
        string $color_from = null,
        string $color_to = null,
        string $number_of_surnames = '10'
    ): string {
        return $this->individualRepository->chartCommonSurnames($size, $color_from, $color_to, $number_of_surnames);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGiven(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGiven($threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenList($threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenListTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenTable($threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemale($threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleList($threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleListTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names of females.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenFemaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenFemaleTable($threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMale(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMale($threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleList($threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleListTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names of males.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenMaleTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenMaleTable($threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknown(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknown($threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownList(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownList($threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownListTotals(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownListTotals($threshold, $maxtoshow);
    }

    /**
     * Find common give names of unknown sexes.
     *
     * @param string $threshold
     * @param string $maxtoshow
     *
     * @return string
     */
    public function commonGivenUnknownTable(string $threshold = '1', string $maxtoshow = '10'): string
    {
        return $this->individualRepository->commonGivenUnknownTable($threshold, $maxtoshow);
    }

    /**
     * Create a chart of common given names.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $maxtoshow
     *
     * @return string
     */
    public function chartCommonGiven(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null,
        string $maxtoshow  = '7'
    ): string {
        return $this->individualRepository->chartCommonGiven($size, $color_from, $color_to, $maxtoshow);
    }

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedIn(): string
    {
        return $this->userRepository->usersLoggedIn();
    }

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedInList(): string
    {
        return $this->userRepository->usersLoggedInList();
    }

    /**
     * Who is currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotal(): int
    {
        return $this->userRepository->usersLoggedInTotal();
    }

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalAnon(): int
    {
        return $this->userRepository->usersLoggedInTotalAnon();
    }

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalVisible(): int
    {
        return $this->userRepository->usersLoggedInTotalVisible();
    }

    /**
     * Get the current user's ID.
     *
     * @return string
     */
    public function userId(): string
    {
        return $this->userRepository->userId();
    }

    /**
     * Get the current user's username.
     *
     * @param string $visitor_text
     *
     * @return string
     */
    public function userName(string $visitor_text = ''): string
    {
        return $this->userRepository->userName();
    }

    /**
     * Get the current user's full name.
     *
     * @return string
     */
    public function userFullName(): string
    {
        return $this->userRepository->userFullName();
    }

    /**
     * Count the number of users.
     *
     * @return string
     */
    public function totalUsers(): string
    {
        return $this->userRepository->totalUsers();
    }

    /**
     * Count the number of administrators.
     *
     * @return string
     */
    public function totalAdmins(): string
    {
        return $this->userRepository->totalAdmins();
    }

    /**
     * Count the number of administrators.
     *
     * @return string
     */
    public function totalNonAdmins(): string
    {
        return $this->userRepository->totalNonAdmins();
    }

    /**
     * Get the newest registered user's ID.
     *
     * @return string
     */
    public function latestUserId(): string
    {
        return $this->latestRepository->latestUserId();
    }

    /**
     * Get the newest registered user's username.
     *
     * @return string
     */
    public function latestUserName(): string
    {
        return $this->latestRepository->latestUserName();
    }

    /**
     * Get the newest registered user's real name.
     *
     * @return string
     */
    public function latestUserFullName(): string
    {
        return $this->latestRepository->latestUserFullName();
    }

    /**
     * Get the date of the newest user registration.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegDate(string $format = null): string
    {
        return $this->latestRepository->latestUserRegDate();
    }

    /**
     * Find the timestamp of the latest user to register.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegTime(string $format = null): string
    {
        return $this->latestRepository->latestUserRegTime();
    }

    /**
     * Is the most recently registered user logged in right now?
     *
     * @param string|null $yes
     * @param string|null $no
     *
     * @return string
     */
    public function latestUserLoggedin(string $yes = null, string $no = null): string
    {
        return $this->latestRepository->latestUserLoggedin();
    }

    /**
     * Create a link to contact the webmaster.
     *
     * @return string
     */
    public function contactWebmaster(): string
    {
        return $this->contactRepository->contactWebmaster();
    }

    /**
     * Create a link to contact the genealogy contact.
     *
     * @return string
     */
    public function contactGedcom(): string
    {
        return $this->contactRepository->contactGedcom();
    }

    /**
     * What is the current date on the server?
     *
     * @return string
     */
    public function serverDate(): string
    {
        return $this->serverRepository->serverDate();
    }

    /**
     * What is the current time on the server (in 12 hour clock)?
     *
     * @return string
     */
    public function serverTime(): string
    {
        return $this->serverRepository->serverTime();
    }

    /**
     * What is the current time on the server (in 24 hour clock)?
     *
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
     * What is the client's date.
     *
     * @return string
     */
    public function browserDate(): string
    {
        return $this->browserRepository->browserDate();
    }

    /**
     * What is the client's timestamp.
     *
     * @return string
     */
    public function browserTime(): string
    {
        return $this->browserRepository->browserTime();
    }

    /**
     * What is the browser's tiemzone.
     *
     * @return string
     */
    public function browserTimezone(): string
    {
        return $this->browserRepository->browserTimezone();
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCount($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountUser($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountIndi($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountFam($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountSour($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountRepo($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountNote($page_parameter);
    }

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hitCountRepository->hitCountObje($page_parameter);
    }

    /**
     * Find the favorites for the tree.
     *
     * @return string
     */
    public function gedcomFavorites(): string
    {
        return $this->favoritesRepository->gedcomFavorites();
    }

    /**
     * Find the favorites for the user.
     *
     * @return string
     */
    public function userFavorites(): string
    {
        return $this->favoritesRepository->userFavorites();
    }

    /**
     * Find the number of favorites for the tree.
     *
     * @return string
     */
    public function totalGedcomFavorites(): string
    {
        return $this->favoritesRepository->totalGedcomFavorites();
    }

    /**
     * Find the number of favorites for the user.
     *
     * @return string
     */
    public function totalUserFavorites(): string
    {
        return $this->favoritesRepository->totalUserFavorites();
    }

    /**
     * How many messages in the user's inbox.
     *
     * @return string
     */
    public function totalUserMessages(): string
    {
        return $this->messageRepository->totalUserMessages();
    }

    /**
     * How many blog entries exist for this user.
     *
     * @return string
     */
    public function totalUserJournal(): string
    {
        return $this->newsRepository->totalUserJournal();
    }

    /**
     * How many news items exist for this tree.
     *
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
     * @param string $block_name
     * @param string ...$params
     *
     * @return string
     */
    public function callBlock(string $block_name = '', ...$params): string
    {
        /** @var ModuleBlockInterface $block */
        $block = Module::findByComponent('block', $this->tree, Auth::user())
            ->filter(function (ModuleInterface $block) use ($block_name): bool {
                return $block->name() === $block_name && $block->name() !== 'html';
            })
            ->first();

        if ($block === null) {
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

        return $block->getBlock($this->tree, 0, '', $cfg);
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
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
    }
}
