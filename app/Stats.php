<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\Http\Middleware\PageHitCounter;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use PDOException;
use stdClass;
use const PREG_SET_ORDER;

/**
 * A selection of pre-formatted statistical queries.
 *
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class Stats
{
    // Used in Google charts
    const GOOGLE_CHART_ENCODING = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';

    /** @var Tree Generate statistics for a specified tree. */
    private $tree;

    /** @var string[] All public functions are available as keywords - except these ones */
    private $public_but_not_allowed = [
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

    /** @var string[] List of GEDCOM media types */
    private $media_types = [
        'audio',
        'book',
        'card',
        'certificate',
        'coat',
        'document',
        'electronic',
        'magazine',
        'manuscript',
        'map',
        'fiche',
        'film',
        'newspaper',
        'painting',
        'photo',
        'tombstone',
        'video',
        'other',
    ];

    /**
     * Create the statistics for a tree.
     *
     * @param Tree $tree Generate statistics for this tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
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
            $reflection = new \ReflectionMethod($this, $method);
            if ($reflection->isPublic() && !in_array($method, $this->public_but_not_allowed)) {
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
            $reflection = new \ReflectionMethod($this, $method);
            if ($reflection->isPublic() && !in_array($method, $this->public_but_not_allowed)) {
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
        $tags = [];

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
        return $this->tree->name();
    }

    /**
     * Get the internal ID number of the tree.
     *
     * @return int
     */
    public function gedcomId(): int
    {
        return $this->tree->id();
    }

    /**
     * Get the descriptive title of the tree.
     *
     * @return string
     */
    public function gedcomTitle(): string
    {
        return e($this->tree->title());
    }

    /**
     * Get information from the GEDCOM's HEAD record.
     *
     * @return string[]
     */
    private function gedcomHead(): array
    {
        $title   = '';
        $version = '';
        $source  = '';

        $head = GedcomRecord::getInstance('HEAD', $this->tree);
        $sour = $head->getFirstFact('SOUR');
        if ($sour !== null) {
            $source  = $sour->value();
            $title   = $sour->attribute('NAME');
            $version = $sour->attribute('VERS');
        }

        return [
            $title,
            $version,
            $source,
        ];
    }

    /**
     * Get the software originally used to create the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedSoftware(): string
    {
        $head = $this->gedcomHead();

        return $head[0];
    }

    /**
     * Get the version of software which created the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedVersion(): string
    {
        $head = $this->gedcomHead();
        // fix broken version string in Family Tree Maker
        if (strstr($head[1], 'Family Tree Maker ')) {
            $p       = strpos($head[1], '(') + 1;
            $p2      = strpos($head[1], ')');
            $head[1] = substr($head[1], $p, ($p2 - $p));
        }
        // Fix EasyTree version
        if ($head[2] == 'EasyTree') {
            $head[1] = substr($head[1], 1);
        }

        return $head[1];
    }

    /**
     * Get the date the GEDCOM file was created.
     *
     * @return string
     */
    public function gedcomDate(): string
    {
        $head = GedcomRecord::getInstance('HEAD', $this->tree);
        $fact = $head->getFirstFact('DATE');
        if ($fact) {
            $date = new Date($fact->value());

            return $date->display();
        }

        return '';
    }

    /**
     * When was this tree last updated?
     *
     * @return string
     */
    public function gedcomUpdated(): string
    {
        $row = Database::prepare(
            "SELECT d_year, d_month, d_day FROM `##dates` WHERE d_julianday1 = (SELECT MAX(d_julianday1) FROM `##dates` WHERE d_file =? AND d_fact='CHAN') LIMIT 1"
        )->execute([$this->tree->id()])->fetchOneRow();
        if ($row) {
            $date = new Date("{$row->d_day} {$row->d_month} {$row->d_year}");

            return $date->display();
        }

        return $this->gedcomDate();
    }

    /**
     * What is the significant individual from this tree?
     *
     * @return string
     */
    public function gedcomRootId(): string
    {
        return $this->tree->getPreference('PEDIGREE_ROOT_ID');
    }

    /**
     * Convert totals into percentages.
     *
     * @param int    $total
     * @param string $type
     *
     * @return string
     */
    private function getPercentage(int $total, string $type): string
    {
        switch ($type) {
            case 'individual':
                $type = $this->totalIndividualsQuery();
                break;
            case 'family':
                $type = $this->totalFamiliesQuery();
                break;
            case 'source':
                $type = $this->totalSourcesQuery();
                break;
            case 'note':
                $type = $this->totalNotesQuery();
                break;
            case 'all':
            default:
                $type = $this->totalIndividualsQuery() + $this->totalFamiliesQuery() + $this->totalSourcesQuery();
                break;
        }

        return I18N::percentage($total / $type, 1);
    }

    /**
     * How many GEDCOM records exist in the tree.
     *
     * @return string
     */
    public function totalRecords(): string
    {
        return I18N::number($this->totalIndividualsQuery() + $this->totalFamiliesQuery() + $this->totalSourcesQuery());
    }

    /**
     * How many individuals exist in the tree.
     *
     * @return int
     */
    private function totalIndividualsQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * How many individuals exist in the tree.
     *
     * @return string
     */
    public function totalIndividuals(): string
    {
        return I18N::number($this->totalIndividualsQuery());
    }

    /**
     * How many individuals have one or more sources.
     *
     * @return int
     */
    private function totalIndisWithSourcesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(DISTINCT i_id)" .
            " FROM `##individuals` JOIN `##link` ON i_id = l_from AND i_file = l_file" .
            " WHERE l_file = :tree_id AND l_type = 'SOUR'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * How many individuals have one or more sources.
     *
     * @return string
     */
    public function totalIndisWithSources(): string
    {
        return I18N::number($this->totalIndisWithSourcesQuery());
    }

    /**
     * Create a chart showing individuals with/without sources.
     *
     * @param string|null $size        // Optional parameter, set from tag
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

        $sizes    = explode('x', $size);
        $tot_indi = $this->totalIndividualsQuery();
        if ($tot_indi == 0) {
            return '';
        }

        $tot_sindi_per = $this->totalIndisWithSourcesQuery() / $tot_indi;
        $with          = (int) (100 * $tot_sindi_per);
        $chd           = $this->arrayToExtendedEncoding([100 - $with, $with]);
        $chl           = I18N::translate('Without sources') . ' - ' . I18N::percentage(1 - $tot_sindi_per, 1) . '|' . I18N::translate('With sources') . ' - ' . I18N::percentage($tot_sindi_per, 1);
        $chart_title   = I18N::translate('Individuals with sources');

        return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:' . $chd . '&amp;chs=' . $size . '&amp;chco=' . $color_from . ',' . $color_to . '&amp;chf=bg,s,ffffff00&amp;chl=' . rawurlencode($chl) . '" width="' . $sizes[0] . '" height="' . $sizes[1] . '" alt="' . $chart_title . '" title="' . $chart_title . '">';
    }

    /**
     * Show the total individuals as a percentage.
     *
     * @return string
     */
    public function totalIndividualsPercentage(): string
    {
        return $this->getPercentage($this->totalIndividualsQuery(), 'all');
    }

    /**
     * Count the total families.
     *
     * @return int
     */
    private function totalFamiliesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##families` WHERE f_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the total families.
     *
     * @return string
     */
    public function totalFamilies(): string
    {
        return I18N::number($this->totalFamiliesQuery());
    }

    /**
     * Count the families with source records.
     *
     * @return int
     */
    private function totalFamsWithSourcesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(DISTINCT f_id)" .
            " FROM `##families` JOIN `##link` ON f_id = l_from AND f_file = l_file" .
            " WHERE l_file = :tree_id AND l_type = 'SOUR'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the families with with source records.
     *
     * @return string
     */
    public function totalFamsWithSources(): string
    {
        return I18N::number($this->totalFamsWithSourcesQuery());
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
    public function chartFamsWithSources(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

        $sizes   = explode('x', $size);
        $tot_fam = $this->totalFamiliesQuery();
        if ($tot_fam == 0) {
            return '';
        }

        $tot_sfam_per = $this->totalFamsWithSourcesQuery() / $tot_fam;
        $with          = (int) (100 * $tot_sfam_per);
        $chd           = $this->arrayToExtendedEncoding([100 - $with, $with]);
        $chl           = I18N::translate('Without sources') . ' - ' . I18N::percentage(1 - $tot_sfam_per, 1) . '|' . I18N::translate('With sources') . ' - ' . I18N::percentage($tot_sfam_per, 1);
        $chart_title   = I18N::translate('Families with sources');

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
    }

    /**
     * Show the total families as a percentage.
     *
     * @return string
     */
    public function totalFamiliesPercentage(): string
    {
        return $this->getPercentage($this->totalFamiliesQuery(), 'all');
    }

    /**
     * Count the total number of sources.
     *
     * @return int
     */
    private function totalSourcesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##sources` WHERE s_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the total number of sources.
     *
     * @return string
     */
    public function totalSources(): string
    {
        return I18N::number($this->totalSourcesQuery());
    }

    /**
     * Show the number of sources as a percentage.
     *
     * @return string
     */
    public function totalSourcesPercentage(): string
    {
        return $this->getPercentage($this->totalSourcesQuery(), 'all');
    }

    /**
     * Count the number of notes.
     *
     * @return int
     */
    private function totalNotesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##other` WHERE o_type='NOTE' AND o_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of notes.
     *
     * @return string
     */
    public function totalNotes(): string
    {
        return I18N::number($this->totalNotesQuery());
    }

    /**
     * Show the number of notes as a percentage.
     *
     * @return string
     */
    public function totalNotesPercentage(): string
    {
        return $this->getPercentage($this->totalNotesQuery(), 'all');
    }

    /**
     * Count the number of repositories.
     *
     * @return int
     */
    private function totalRepositoriesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##other` WHERE o_type='REPO' AND o_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of repositories
     *
     * @return string
     */
    public function totalRepositories(): string
    {
        return I18N::number($this->totalRepositoriesQuery());
    }

    /**
     * Show the total number of repositories as a percentage.
     *
     * @return string
     */
    public function totalRepositoriesPercentage(): string
    {
        return $this->getPercentage($this->totalRepositoriesQuery(), 'all');
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
        if ($params) {
            $opt      = 'IN (' . implode(',', array_fill(0, count($params), '?')) . ')';
            $distinct = '';
        } else {
            $opt      = "IS NOT NULL";
            $distinct = 'DISTINCT';
        }
        $params[] = $this->tree->id();

        $total = (int) Database::prepare(
            "SELECT COUNT({$distinct} n_surn COLLATE '" . I18N::collation() . "')" .
            " FROM `##name`" .
            " WHERE n_surn COLLATE '" . I18N::collation() . "' {$opt} AND n_file=?"
        )->execute(
            $params
        )->fetchOne();

        return I18N::number($total);
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
        if ($params) {
            $qs       = implode(',', array_fill(0, count($params), '?'));
            $params[] = $this->tree->id();
            $total    = (int) Database::prepare(
                "SELECT COUNT( n_givn) FROM `##name` WHERE n_givn IN ({$qs}) AND n_file=?"
            )->execute(
                $params
            )->fetchOne();
        } else {
            $total = (int) Database::prepare(
                "SELECT COUNT(DISTINCT n_givn) FROM `##name` WHERE n_givn IS NOT NULL AND n_file=?"
            )->execute([
                $this->tree->id(),
            ])->fetchOne();
        }

        return I18N::number($total);
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
        $sql  = "SELECT COUNT(*) AS tot FROM `##dates` WHERE d_file=?";
        $vars = [$this->tree->id()];

        $no_types = [
            'HEAD',
            'CHAN',
        ];
        if ($events) {
            $types = [];
            foreach ($events as $type) {
                if (substr($type, 0, 1) == '!') {
                    $no_types[] = substr($type, 1);
                } else {
                    $types[] = $type;
                }
            }
            if ($types) {
                $sql .= ' AND d_fact IN (' . implode(', ', array_fill(0, count($types), '?')) . ')';
                $vars = array_merge($vars, $types);
            }
        }
        $sql .= ' AND d_fact NOT IN (' . implode(', ', array_fill(0, count($no_types), '?')) . ')';
        $vars = array_merge($vars, $no_types);

        $n = (int) Database::prepare($sql)->execute($vars)->fetchOne();

        return I18N::number($n);
    }

    /**
     * Count the number of births.
     *
     * @return string
     */
    public function totalEventsBirth(): string
    {
        return $this->totalEvents(Gedcom::BIRTH_EVENTS);
    }

    /**
     * Count the number of births.
     *
     * @return string
     */
    public function totalBirths(): string
    {
        return $this->totalEvents(['BIRT']);
    }

    /**
     * Count the number of deaths.
     *
     * @return string
     */
    public function totalEventsDeath(): string
    {
        return $this->totalEvents(Gedcom::DEATH_EVENTS);
    }

    /**
     * Count the number of deaths.
     *
     * @return string
     */
    public function totalDeaths(): string
    {
        return $this->totalEvents(['DEAT']);
    }

    /**
     * Count the number of marriages.
     *
     * @return string
     */
    public function totalEventsMarriage(): string
    {
        return $this->totalEvents(Gedcom::MARRIAGE_EVENTS);
    }

    /**
     * Count the number of marriages.
     *
     * @return string
     */
    public function totalMarriages(): string
    {
        return $this->totalEvents(['MARR']);
    }

    /**
     * Count the number of divorces.
     *
     * @return string
     */
    public function totalEventsDivorce(): string
    {
        return $this->totalEvents(Gedcom::DIVORCE_EVENTS);
    }

    /**
     * Count the number of divorces.
     *
     * @return string
     */
    public function totalDivorces(): string
    {
        return $this->totalEvents(['DIV']);
    }

    /**
     * Count the number of other events.
     *
     * @return string
     */
    public function totalEventsOther(): string
    {
        $facts    = array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS);
        $no_facts = [];
        foreach ($facts as $fact) {
            $fact       = '!' . str_replace('\'', '', $fact);
            $no_facts[] = $fact;
        }

        return $this->totalEvents($no_facts);
    }

    /**
     * Count the number of males.
     *
     * @return int
     */
    private function totalSexMalesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_sex = 'M'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of males.
     *
     * @return string
     */
    public function totalSexMales(): string
    {
        return I18N::number($this->totalSexMalesQuery());
    }

    /**
     * Count the number of males
     *
     * @return string
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->getPercentage($this->totalSexMalesQuery(), 'individual');
    }

    /**
     * Count the number of females.
     *
     * @return int
     */
    private function totalSexFemalesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_sex = 'F'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemales(): string
    {
        return I18N::number($this->totalSexFemalesQuery());
    }

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->getPercentage($this->totalSexFemalesQuery(), 'individual');
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return int
     */
    private function totalSexUnknownQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_sex = 'U'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknown(): string
    {
        return I18N::number($this->totalSexUnknownQuery());
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->getPercentage($this->totalSexUnknownQuery(), 'individual');
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
    public function chartSex(string $size = null, string $color_female = null, string $color_male = null, string $color_unknown = null): string
    {
        $WT_STATS_S_CHART_X = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y = Theme::theme()->parameter('stats-small-chart-y');

        $size          = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_female  = $color_female ?? 'ffd1dc';
        $color_male    = $color_male ?? '84beff';
        $color_unknown = $color_unknown ?? '777777';

        $sizes = explode('x', $size);
        // Raw data - for calculation
        $tot_f = $this->totalSexFemalesQuery();
        $tot_m = $this->totalSexMalesQuery();
        $tot_u = $this->totalSexUnknownQuery();
        $tot   = $tot_f + $tot_m + $tot_u;
        // I18N data - for display
        $per_f = $this->totalSexFemalesPercentage();
        $per_m = $this->totalSexMalesPercentage();
        $per_u = $this->totalSexUnknownPercentage();
        if ($tot == 0) {
            return '';
        }

        if ($tot_u > 0) {
            $chd = $this->arrayToExtendedEncoding([
                intdiv(4095 * $tot_u, $tot),
                intdiv(4095 * $tot_f, $tot),
                intdiv(4095 * $tot_m, $tot),
            ]);
            $chl =
                I18N::translateContext('unknown people', 'Unknown') . ' - ' . $per_u . '|' .
                I18N::translate('Females') . ' - ' . $per_f . '|' .
                I18N::translate('Males') . ' - ' . $per_m;
            $chart_title =
                I18N::translate('Males') . ' - ' . $per_m . I18N::$list_separator .
                I18N::translate('Females') . ' - ' . $per_f . I18N::$list_separator .
                I18N::translateContext('unknown people', 'Unknown') . ' - ' . $per_u;

            return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
        }

        $chd = $this->arrayToExtendedEncoding([
            intdiv(4095 * $tot_f, $tot),
            intdiv(4095 * $tot_m, $tot),
        ]);
        $chl         =
            I18N::translate('Females') . ' - ' . $per_f . '|' .
            I18N::translate('Males') . ' - ' . $per_m;
        $chart_title = I18N::translate('Males') . ' - ' . $per_m . I18N::$list_separator .
                   I18N::translate('Females') . ' - ' . $per_f;

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
    }

    /**
     * Count the number of living individuals.
     *
     * The totalLiving/totalDeceased queries assume that every dead person will
     * have a DEAT record. It will not include individuals who were born more
     * than MAX_ALIVE_AGE years ago, and who have no DEAT record.
     * A good reason to run the “Add missing DEAT records” batch-update!
     *
     * @return int
     */
    private function totalLivingQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom NOT REGEXP '\\n1 (" . implode('|', Gedcom::DEATH_EVENTS) . ")'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLiving(): string
    {
        return I18N::number($this->totalLivingQuery());
    }

    /**
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLivingPercentage(): string
    {
        return $this->getPercentage($this->totalLivingQuery(), 'individual');
    }

    /**
     * Count the number of dead individuals.
     *
     * @return int
     */
    private function totalDeceasedQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom REGEXP '\\n1 (" . implode('|', Gedcom::DEATH_EVENTS) . ")'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceased(): string
    {
        return I18N::number($this->totalDeceasedQuery());
    }

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->getPercentage($this->totalDeceasedQuery(), 'individual');
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
        $WT_STATS_S_CHART_X = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y = Theme::theme()->parameter('stats-small-chart-y');

        $size         = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_living = $color_living ?? 'ffffff';
        $color_dead   = $color_dead ?? 'cccccc';

        $sizes = explode('x', $size);
        // Raw data - for calculation
        $tot_l = $this->totalLivingQuery();
        $tot_d = $this->totalDeceasedQuery();
        $tot   = $tot_l + $tot_d;
        // I18N data - for display
        $per_l = $this->totalLivingPercentage();
        $per_d = $this->totalDeceasedPercentage();
        if ($tot == 0) {
            return '';
        }

        $chd = $this->arrayToExtendedEncoding([
            intdiv(4095 * $tot_l, $tot),
            intdiv(4095 * $tot_d, $tot),
        ]);
        $chl         =
            I18N::translate('Living') . ' - ' . $per_l . '|' .
            I18N::translate('Dead') . ' - ' . $per_d . '|';
        $chart_title = I18N::translate('Living') . ' - ' . $per_l . I18N::$list_separator .
                   I18N::translate('Dead') . ' - ' . $per_d;

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
    }

    /**
     * Count the number of users.
     *
     * @return string
     */
    public function totalUsers(): string
    {
        $total = count(User::all());

        return I18N::number($total);
    }

    /**
     * Count the number of administrators.
     *
     * @return string
     */
    public function totalAdmins(): string
    {
        return I18N::number(count(User::administrators()));
    }

    /**
     * Count the number of administrators.
     *
     * @return string
     */
    public function totalNonAdmins(): string
    {
        return I18N::number(count(User::all()) - count(User::administrators()));
    }

    /**
     * Count the number of media records with a given type.
     *
     * @param string $type
     *
     * @return int
     */
    private function totalMediaType($type): int
    {
        if (!in_array($type, $this->media_types) && $type != 'all' && $type != 'unknown') {
            return 0;
        }
        $sql  = "SELECT COUNT(*) AS tot FROM `##media` WHERE m_file=?";
        $vars = [$this->tree->id()];

        if ($type != 'all') {
            if ($type == 'unknown') {
                // There has to be a better way then this :(
                foreach ($this->media_types as $t) {
                    $sql .= " AND (m_gedcom NOT LIKE ? AND m_gedcom NOT LIKE ?)";
                    $vars[] = "%3 TYPE {$t}%";
                    $vars[] = "%1 _TYPE {$t}%";
                }
            } else {
                $sql .= " AND (m_gedcom LIKE ? OR m_gedcom LIKE ?)";
                $vars[] = "%3 TYPE {$type}%";
                $vars[] = "%1 _TYPE {$type}%";
            }
        }

        return (int) Database::prepare($sql)->execute($vars)->fetchOne();
    }

    /**
     * Count the number of media records.
     *
     * @return string
     */
    public function totalMedia(): string
    {
        return I18N::number($this->totalMediaType('all'));
    }

    /**
     * Count the number of media records with type "audio".
     *
     * @return string
     */
    public function totalMediaAudio(): string
    {
        return I18N::number($this->totalMediaType('audio'));
    }

    /**
     * Count the number of media records with type "book".
     *
     * @return string
     */
    public function totalMediaBook(): string
    {
        return I18N::number($this->totalMediaType('book'));
    }

    /**
     * Count the number of media records with type "card".
     *
     * @return string
     */
    public function totalMediaCard(): string
    {
        return I18N::number($this->totalMediaType('card'));
    }

    /**
     * Count the number of media records with type "certificate".
     *
     * @return string
     */
    public function totalMediaCertificate(): string
    {
        return I18N::number($this->totalMediaType('certificate'));
    }

    /**
     * Count the number of media records with type "coat of arms".
     *
     * @return string
     */
    public function totalMediaCoatOfArms(): string
    {
        return I18N::number($this->totalMediaType('coat'));
    }

    /**
     * Count the number of media records with type "document".
     *
     * @return string
     */
    public function totalMediaDocument(): string
    {
        return I18N::number($this->totalMediaType('document'));
    }

    /**
     * Count the number of media records with type "electronic".
     *
     * @return string
     */
    public function totalMediaElectronic(): string
    {
        return I18N::number($this->totalMediaType('electronic'));
    }

    /**
     * Count the number of media records with type "magazine".
     *
     * @return string
     */
    public function totalMediaMagazine(): string
    {
        return I18N::number($this->totalMediaType('magazine'));
    }

    /**
     * Count the number of media records with type "manuscript".
     *
     * @return string
     */
    public function totalMediaManuscript(): string
    {
        return I18N::number($this->totalMediaType('manuscript'));
    }

    /**
     * Count the number of media records with type "map".
     *
     * @return string
     */
    public function totalMediaMap(): string
    {
        return I18N::number($this->totalMediaType('map'));
    }

    /**
     * Count the number of media records with type "microfiche".
     *
     * @return string
     */
    public function totalMediaFiche(): string
    {
        return I18N::number($this->totalMediaType('fiche'));
    }

    /**
     * Count the number of media records with type "microfilm".
     *
     * @return string
     */
    public function totalMediaFilm(): string
    {
        return I18N::number($this->totalMediaType('film'));
    }

    /**
     * Count the number of media records with type "newspaper".
     *
     * @return string
     */
    public function totalMediaNewspaper(): string
    {
        return I18N::number($this->totalMediaType('newspaper'));
    }

    /**
     * Count the number of media records with type "painting".
     *
     * @return string
     */
    public function totalMediaPainting(): string
    {
        return I18N::number($this->totalMediaType('painting'));
    }

    /**
     * Count the number of media records with type "photograph".
     *
     * @return string
     */
    public function totalMediaPhoto(): string
    {
        return I18N::number($this->totalMediaType('photo'));
    }

    /**
     * Count the number of media records with type "tombstone".
     *
     * @return string
     */
    public function totalMediaTombstone(): string
    {
        return I18N::number($this->totalMediaType('tombstone'));
    }

    /**
     * Count the number of media records with type "video".
     *
     * @return string
     */
    public function totalMediaVideo(): string
    {
        return I18N::number($this->totalMediaType('video'));
    }

    /**
     * Count the number of media records with type "other".
     *
     * @return string
     */
    public function totalMediaOther(): string
    {
        return I18N::number($this->totalMediaType('other'));
    }

    /**
     * Count the number of media records with type "unknown".
     *
     * @return string
     */
    public function totalMediaUnknown(): string
    {
        return I18N::number($this->totalMediaType('unknown'));
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
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

        $sizes = explode('x', $size);
        $tot   = $this->totalMediaType('all');
        // Beware divide by zero
        if ($tot == 0) {
            return I18N::translate('None');
        }
        // Build a table listing only the media types actually present in the GEDCOM
        $mediaCounts = [];
        $mediaTypes  = '';
        $chart_title = '';
        $c           = 0;
        $max         = 0;
        $media       = [];
        foreach ($this->media_types as $type) {
            $count = $this->totalMediaType($type);
            if ($count > 0) {
                $media[$type] = $count;
                if ($count > $max) {
                    $max = $count;
                }
                $c += $count;
            }
        }
        $count = $this->totalMediaType('unknown');
        if ($count > 0) {
            $media['unknown'] = $tot - $c;
            if ($tot - $c > $max) {
                $max = $count;
            }
        }
        if (($max / $tot) > 0.6 && count($media) > 10) {
            arsort($media);
            $media = array_slice($media, 0, 10);
            $c     = $tot;
            foreach ($media as $cm) {
                $c -= $cm;
            }
            if (isset($media['other'])) {
                $media['other'] += $c;
            } else {
                $media['other'] = $c;
            }
        }
        asort($media);
        foreach ($media as $type => $count) {
            $mediaCounts[] = intdiv(100 * $count, $tot);
            $mediaTypes    .= GedcomTag::getFileFormTypeValue($type) . ' - ' . I18N::number($count) . '|';
            $chart_title   .= GedcomTag::getFileFormTypeValue($type) . ' (' . $count . '), ';
        }
        $chart_title = substr($chart_title, 0, -2);
        $chd         = $this->arrayToExtendedEncoding($mediaCounts);
        $chl         = substr($mediaTypes, 0, -1);

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
    }

    /**
     * Birth and Death
     *
     * @param string $type
     * @param string $life_dir
     * @param string $birth_death
     *
     * @return string
     */
    private function mortalityQuery($type, $life_dir, $birth_death): string
    {
        if ($birth_death == 'MARR') {
            $query_field = "'MARR'";
        } elseif ($birth_death == 'DIV') {
            $query_field = "'DIV'";
        } elseif ($birth_death == 'BIRT') {
            $query_field = "'BIRT'";
        } else {
            $query_field = "'DEAT'";
        }
        if ($life_dir == 'ASC') {
            $dmod = 'MIN';
        } else {
            $dmod = 'MAX';
        }
        $rows = $this->runSql(
            "SELECT d_year, d_type, d_fact, d_gid" .
            " FROM `##dates`" .
            " WHERE d_file={$this->tree->id()} AND d_fact IN ({$query_field}) AND d_julianday1=(" .
            " SELECT {$dmod}( d_julianday1 )" .
            " FROM `##dates`" .
            " WHERE d_file={$this->tree->id()} AND d_fact IN ({$query_field}) AND d_julianday1<>0 )" .
            " LIMIT 1"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $row    = $rows[0];
        $record = GedcomRecord::getInstance($row->d_gid, $this->tree);
        switch ($type) {
            default:
            case 'full':
                if ($record->canShow()) {
                    $result = $record->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'year':
                if ($row->d_year < 0) {
                    $row->d_year = abs($row->d_year) . ' B.C.';
                }
                $date   = new Date($row->d_type . ' ' . $row->d_year);
                $result = $date->display();
                break;
            case 'name':
                $result = '<a href="' . e($record->url()) . '">' . $record->getFullName() . '</a>';
                break;
            case 'place':
                $fact = GedcomRecord::getInstance($row->d_gid, $this->tree)->getFirstFact($row->d_fact);
                if ($fact) {
                    $result = FunctionsPrint::formatFactPlace($fact, true, true, true);
                } else {
                    $result = I18N::translate('Private');
                }
                break;
        }

        return $result;
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
     */
    public function statsPlaces($what = 'ALL', $fact = '', $parent = 0, $country = false): array
    {
        if ($fact) {
            if ($what == 'INDI') {
                $rows = Database::prepare(
                    "SELECT i_gedcom AS ged FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom LIKE '%\n2 PLAC %'"
                )->execute([
                    'tree_id' => $this->tree->id(),
                ])->fetchAll();
            } elseif ($what == 'FAM') {
                $rows = Database::prepare(
                    "SELECT f_gedcom AS ged FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\n2 PLAC %'"
                )->execute([
                    'tree_id' => $this->tree->id(),
                ])->fetchAll();
            }
            $placelist = [];
            foreach ($rows as $row) {
                if (preg_match('/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC (.+)/', $row->ged, $match)) {
                    if ($country) {
                        $tmp   = explode(Place::GEDCOM_SEPARATOR, $match[1]);
                        $place = end($tmp);
                    } else {
                        $place = $match[1];
                    }
                    if (!isset($placelist[$place])) {
                        $placelist[$place] = 1;
                    } else {
                        $placelist[$place]++;
                    }
                }
            }

            return $placelist;
        }

        if ($parent > 0) {
            // used by placehierarchy googlemap module
            if ($what == 'INDI') {
                $join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
            } elseif ($what == 'FAM') {
                $join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
            } else {
                $join = "";
            }
            $rows = $this->runSql(
                " SELECT" .
                " p_place AS place," .
                " COUNT(*) AS tot" .
                " FROM" .
                " `##places`" .
                " JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
                $join .
                " WHERE" .
                " p_id={$parent} AND" .
                " p_file={$this->tree->id()}" .
                " GROUP BY place"
            );

            return $rows;
        }

        if ($what == 'INDI') {
            $join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
        } elseif ($what == 'FAM') {
            $join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
        } else {
            $join = "";
        }
        $rows = $this->runSql(
            " SELECT" .
            " p_place AS country," .
            " COUNT(*) AS tot" .
            " FROM" .
            " `##places`" .
            " JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
            $join .
            " WHERE" .
            " p_file={$this->tree->id()}" .
            " AND p_parent_id='0'" .
            " GROUP BY country ORDER BY tot DESC, country ASC"
        );

        return $rows;
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
    public function chartDistribution(string $chart_shows = 'world', string $chart_type = '', string $surname = ''): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_CHART_COLOR3 = Theme::theme()->parameter('distribution-chart-low-values');
        $WT_STATS_MAP_X        = Theme::theme()->parameter('distribution-chart-x');
        $WT_STATS_MAP_Y        = Theme::theme()->parameter('distribution-chart-y');

        if ($this->totalPlacesQuery() == 0) {
            return '';
        }
        // Get the country names for each language
        $country_to_iso3166 = [];
        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());
            $countries = $this->getAllCountries();
            foreach ($this->iso3166() as $three => $two) {
                $country_to_iso3166[$three]             = $two;
                $country_to_iso3166[$countries[$three]] = $two;
            }
        }
        I18N::init(WT_LOCALE);
        switch ($chart_type) {
            case 'surname_distribution_chart':
                if ($surname == '') {
                    $surname = $this->getCommonSurname();
                }
                $chart_title = I18N::translate('Surname distribution chart') . ': ' . $surname;
                // Count how many people are events in each country
                $surn_countries = [];

                $rows = Database::prepare(
                    "SELECT i_gedcom" .
                    " FROM `##individuals`" .
                    " JOIN `##name` ON n_id = i_id AND n_file = i_file" .
                    " WHERE n_file = :tree_id" .
                    " AND n_surn COLLATE :collate = :surname"
                )->execute([
                    'tree_id' => $this->tree->id(),
                    'collate' => I18N::collation(),
                    'surname' => $surname,
                ])->fetchAll();

                foreach ($rows as $row) {
                    if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $row->i_gedcom, $matches)) {
                        // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
                        foreach ($matches[1] as $country) {
                            if (array_key_exists($country, $country_to_iso3166)) {
                                if (array_key_exists($country_to_iso3166[$country], $surn_countries)) {
                                    $surn_countries[$country_to_iso3166[$country]]++;
                                } else {
                                    $surn_countries[$country_to_iso3166[$country]] = 1;
                                }
                            }
                        }
                    }
                }
                break;
            case 'birth_distribution_chart':
                $chart_title = I18N::translate('Birth by country');
                // Count how many people were born in each country
                $surn_countries = [];
                $b_countries    = $this->statsPlaces('INDI', 'BIRT', 0, true);
                foreach ($b_countries as $place => $count) {
                    $country = $place;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $count;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $count;
                        }
                    }
                }
                break;
            case 'death_distribution_chart':
                $chart_title = I18N::translate('Death by country');
                // Count how many people were death in each country
                $surn_countries = [];
                $d_countries    = $this->statsPlaces('INDI', 'DEAT', 0, true);
                foreach ($d_countries as $place => $count) {
                    $country = $place;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $count;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $count;
                        }
                    }
                }
                break;
            case 'marriage_distribution_chart':
                $chart_title = I18N::translate('Marriage by country');
                // Count how many families got marriage in each country
                $surn_countries = [];
                $m_countries    = $this->statsPlaces('FAM');
                // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
                foreach ($m_countries as $place) {
                    $country = $place->country;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $place->tot;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $place->tot;
                        }
                    }
                }
                break;
            case 'indi_distribution_chart':
            default:
                $chart_title = I18N::translate('Individual distribution chart');
                // Count how many people have events in each country
                $surn_countries = [];
                $a_countries    = $this->statsPlaces('INDI');
                // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
                foreach ($a_countries as $place) {
                    $country = $place->country;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $place->tot;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $place->tot;
                        }
                    }
                }
                break;
        }
        $chart_url = 'https://chart.googleapis.com/chart?cht=t&amp;chtm=' . $chart_shows;
        $chart_url .= '&amp;chco=' . $WT_STATS_CHART_COLOR1 . ',' . $WT_STATS_CHART_COLOR3 . ',' . $WT_STATS_CHART_COLOR2; // country colours
        $chart_url .= '&amp;chf=bg,s,ECF5FF'; // sea colour
        $chart_url .= '&amp;chs=' . $WT_STATS_MAP_X . 'x' . $WT_STATS_MAP_Y;
        $chart_url .= '&amp;chld=' . implode('', array_keys($surn_countries)) . '&amp;chd=s:';
        foreach ($surn_countries as $count) {
            $chart_url .= substr(self::GOOGLE_CHART_ENCODING, (int) ($count / max($surn_countries) * 61), 1);
        }
        $chart = '<div id="google_charts" class="center">';
        $chart .= '<p>' . $chart_title . '</p>';
        $chart .= '<div><img src="' . $chart_url . '" alt="' . $chart_title . '" title="' . $chart_title . '" class="gchart" /><br>';
        $chart .= '<table class="center"><tr>';
        $chart .= '<td bgcolor="#' . $WT_STATS_CHART_COLOR2 . '" width="12"></td><td>' . I18N::translate('Highest population') . '</td>';
        $chart .= '<td bgcolor="#' . $WT_STATS_CHART_COLOR3 . '" width="12"></td><td>' . I18N::translate('Lowest population') . '</td>';
        $chart .= '<td bgcolor="#' . $WT_STATS_CHART_COLOR1 . '" width="12"></td><td>' . I18N::translate('Nobody at all') . '</td>';
        $chart .= '</tr></table></div></div>';

        return $chart;
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
            $all_countries = $this->getAllCountries();
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
        $all_countries = $this->getAllCountries();
        foreach ($all_db_countries as $country_code => $country) {
            $top10[] = '<li>';
            foreach ($country as $country_name => $tot) {
                $tmp     = new Place($country_name, $this->tree);
                $place   = '<a href="' . e($tmp->url()) . '" class="list_item">' . $all_countries[$country_code] . '</a>';
                $top10[] .= $place . ' - ' . I18N::number($tot);
            }
            $top10[] .= '</li>';
            if ($i++ == 10) {
                break;
            }
        }
        $top10 = implode('', $top10);

        return '<ul>' . $top10 . '</ul>';
    }

    /**
     * A list of common birth places.
     *
     * @return string
     */
    public function commonBirthPlacesList(): string
    {
        $places = $this->statsPlaces('INDI', 'BIRT');
        $top10  = [];
        $i      = 1;
        arsort($places);
        foreach ($places as $place => $count) {
            $tmp     = new Place($place, $this->tree);
            $place   = '<a href="' . e($tmp->url()) . '" class="list_item">' . $tmp->getFullName() . '</a>';
            $top10[] = '<li>' . $place . ' - ' . I18N::number($count) . '</li>';
            if ($i++ == 10) {
                break;
            }
        }
        $top10 = implode('', $top10);

        return '<ul>' . $top10 . '</ul>';
    }

    /**
     * A list of common death places.
     *
     * @return string
     */
    public function commonDeathPlacesList(): string
    {
        $places = $this->statsPlaces('INDI', 'DEAT');
        $top10  = [];
        $i      = 1;
        arsort($places);
        foreach ($places as $place => $count) {
            $tmp     = new Place($place, $this->tree);
            $place   = '<a href="' . e($tmp->url()) . '" class="list_item">' . $tmp->getFullName() . '</a>';
            $top10[] = '<li>' . $place . ' - ' . I18N::number($count) . '</li>';
            if ($i++ == 10) {
                break;
            }
        }
        $top10 = implode('', $top10);

        return '<ul>' . $top10 . '</ul>';
    }

    /**
     * A list of common marriage places.
     *
     * @return string
     */
    public function commonMarriagePlacesList(): string
    {
        $places = $this->statsPlaces('FAM', 'MARR');
        $top10  = [];
        $i      = 1;
        arsort($places);
        foreach ($places as $place => $count) {
            $tmp     = new Place($place, $this->tree);
            $place   = '<a href="' . e($tmp->url()) . '" class="list_item">' . $tmp->getFullName() . '</a>';
            $top10[] = '<li>' . $place . ' - ' . I18N::number($count) . '</li>';
            if ($i++ == 10) {
                break;
            }
        }
        $top10 = implode('', $top10);

        return '<ul>' . $top10 . '</ul>';
    }

    /**
     * Create a chart of birth places.
     *
     * @param bool        $simple
     * @param bool        $sex
     * @param int         $year1
     * @param int         $year2
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return array|string
     */
    public function statsBirthQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1, string $size = null, string $color_from = null, string $color_to = null)
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        if ($simple) {
            $sql =
                "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` " .
                "WHERE " .
                "d_file = {$this->tree->id()} AND " .
                "d_year <> 0 AND " .
                "d_fact='BIRT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        } elseif ($sex) {
            $sql =
                "SELECT d_month, i_sex, COUNT(*) AS total FROM `##dates` " .
                "JOIN `##individuals` ON d_file = i_file AND d_gid = i_id " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='BIRT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total FROM `##dates` " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='BIRT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        }
        if ($year1 >= 0 && $year2 >= 0) {
            $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
        }
        if ($simple) {
            $sql .= " GROUP BY century ORDER BY century";
        } else {
            $sql .= " GROUP BY d_month";
            if ($sex) {
                $sql .= ", i_sex";
            }
        }
        $rows = $this->runSql($sql);
        if ($simple) {
            $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
            $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
            $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

            $sizes = explode('x', $size);
            $tot   = 0;
            foreach ($rows as $values) {
                $values->total = (int) $values->total;
                $tot += $values->total;
            }
            // Beware divide by zero
            if ($tot == 0) {
                return '';
            }
            $centuries = '';
            $counts    = [];
            foreach ($rows as $values) {
                $counts[] = intdiv(100 * $values->total, $tot);
                $centuries .= $this->centuryName($values->century) . ' - ' . I18N::number($values->total) . '|';
            }
            $chd = $this->arrayToExtendedEncoding($counts);
            $chl = rawurlencode(substr($centuries, 0, -1));

            return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Births by century') . '" title="' . I18N::translate('Births by century') . '" />';
        }

        return $rows;
    }

    /**
     * Create a chart of death places.
     *
     * @param bool        $simple
     * @param bool        $sex
     * @param int         $year1
     * @param int         $year2
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return array|string
     */
    public function statsDeathQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1, string $size = null, string $color_from = null, string $color_to = null)
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        if ($simple) {
            $sql =
                "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                'd_year<>0 AND ' .
                "d_fact='DEAT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        } elseif ($sex) {
            $sql =
                "SELECT d_month, i_sex, COUNT(*) AS total FROM `##dates` " .
                "JOIN `##individuals` ON d_file = i_file AND d_gid = i_id " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='DEAT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total FROM `##dates` " .
                "WHERE " .
                "d_file={$this->tree->id()} AND " .
                "d_fact='DEAT' AND " .
                "d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
        }
        if ($year1 >= 0 && $year2 >= 0) {
            $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
        }
        if ($simple) {
            $sql .= " GROUP BY century ORDER BY century";
        } else {
            $sql .= " GROUP BY d_month";
            if ($sex) {
                $sql .= ", i_sex";
            }
        }
        $rows = $this->runSql($sql);
        if ($simple) {
            $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
            $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
            $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

            $sizes = explode('x', $size);
            $tot   = 0;
            foreach ($rows as $values) {
                $values->total = (int) $values->total;
                $tot += $values->total;
            }
            // Beware divide by zero
            if ($tot == 0) {
                return '';
            }
            $centuries = '';
            $counts    = [];
            foreach ($rows as $values) {
                $counts[] = intdiv(100 * $values->total, $tot);
                $centuries .= $this->centuryName($values->century) . ' - ' . I18N::number($values->total) . '|';
            }
            $chd = $this->arrayToExtendedEncoding($counts);
            $chl = rawurlencode(substr($centuries, 0, -1));

            return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Deaths by century') . '" title="' . I18N::translate('Deaths by century') . '" />';
        }

        return $rows;
    }

    /**
     * Find the earliest birth.
     *
     * @return string
     */
    public function firstBirth(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'BIRT');
    }

    /**
     * Find the earliest birth year.
     *
     * @return string
     */
    public function firstBirthYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'BIRT');
    }

    /**
     * Find the name of the earliest birth.
     *
     * @return string
     */
    public function firstBirthName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'BIRT');
    }

    /**
     * Find the earliest birth place.
     *
     * @return string
     */
    public function firstBirthPlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'BIRT');
    }

    /**
     * Find the latest birth.
     *
     * @return string
     */
    public function lastBirth(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'BIRT');
    }

    /**
     * Find the latest birth year.
     *
     * @return string
     */
    public function lastBirthYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'BIRT');
    }

    /**
     * Find the latest birth name.
     *
     * @return string
     */
    public function lastBirthName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'BIRT');
    }

    /**
     * Find the latest birth place.
     *
     * @return string
     */
    public function lastBirthPlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'BIRT');
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
        return $this->statsBirthQuery(true, false, -1, -1, $size, $color_from, $color_to);
    }

    /**
     * Find the earliest death.
     *
     * @return string
     */
    public function firstDeath(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'DEAT');
    }

    /**
     * Find the earliest death year.
     *
     * @return string
     */
    public function firstDeathYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'DEAT');
    }

    /**
     * Find the earliest death name.
     *
     * @return string
     */
    public function firstDeathName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'DEAT');
    }

    /**
     * Find the earliest death place.
     *
     * @return string
     */
    public function firstDeathPlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'DEAT');
    }

    /**
     * Find the latest death.
     *
     * @return string
     */
    public function lastDeath(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'DEAT');
    }

    /**
     * Find the latest death year.
     *
     * @return string
     */
    public function lastDeathYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'DEAT');
    }

    /**
     * Find the latest death name.
     *
     * @return string
     */
    public function lastDeathName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'DEAT');
    }

    /**
     * Find the place of the latest death.
     *
     * @return string
     */
    public function lastDeathPlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'DEAT');
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
        return $this->statsDeathQuery(true, false, -1, -1, $size, $color_from, $color_to);
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
        if ($sex == 'F') {
            $sex_search = " i_sex='F'";
        } elseif ($sex == 'M') {
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
     * @return string
     */
    private function topTenOldestQuery(string $type, string $sex, int $total): string
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
            return '';
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
                $age = $age . 'd';
            }
            $age = FunctionsDate::getAgeAtEvent($age);
            if ($person->canShow()) {
                if ($type == 'list') {
                    $top10[] = '<li><a href="' . e($person->url()) . '">' . $person->getFullName() . '</a> (' . $age . ')' . '</li>';
                } else {
                    $top10[] = '<a href="' . e($person->url()) . '">' . $person->getFullName() . '</a> (' . $age . ')';
                }
            }
        }
        if ($type == 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode(' ', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type == 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string $type
     * @param string $sex
     * @param string $total
     *
     * @return string
     */
    private function topTenOldestAliveQuery($type = 'list', $sex = 'BOTH', $total = '10'): string
    {
        $total = (int) $total;

        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }
        if ($sex == 'F') {
            $sex_search = " AND i_sex='F'";
        } elseif ($sex == 'M') {
            $sex_search = " AND i_sex='M'";
        } else {
            $sex_search = '';
        }

        $rows = $this->runSql(
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
                $age = $age . 'd';
            }
            $age = FunctionsDate::getAgeAtEvent($age);
            if ($type === 'list') {
                $top10[] = '<li><a href="' . e($person->url()) . '">' . $person->getFullName() . '</a> (' . $age . ')' . '</li>';
            } else {
                $top10[] = '<a href="' . e($person->url()) . '">' . $person->getFullName() . '</a> (' . $age . ')';
            }
        }
        if ($type === 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

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
                $age = $age . 'd';
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
     * @param string $size
     *
     * @return array|string
     */
    public function statsAgeQuery($simple = true, $related = 'BIRT', $sex = 'BOTH', $year1 = -1, $year2 = -1, $size = '230x250')
    {
        if ($simple) {
            $sizes = explode('x', $size);
            $rows  = $this->runSql(
                "SELECT" .
                " ROUND(AVG(death.d_julianday2-birth.d_julianday1)/365.25,1) AS age," .
                " FLOOR(death.d_year/100+1) AS century," .
                " i_sex AS sex" .
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
                " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
                " death.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
                " death.d_julianday1>birth.d_julianday2" .
                " GROUP BY century, sex ORDER BY century, sex"
            );
            if (empty($rows)) {
                return '';
            }
            $chxl    = '0:|';
            $countsm = '';
            $countsf = '';
            $countsa = '';
            $out     = [];
            foreach ($rows as $values) {
                $out[$values->century][$values->sex] = $values->age;
            }
            foreach ($out as $century => $values) {
                if ($sizes[0] < 980) {
                    $sizes[0] += 50;
                }
                $chxl .= $this->centuryName($century) . '|';

                $female_age  = $values['F'] ?? 0;
                $male_age    = $values['M'] ?? 0;
                $average_age = $female_age + $male_age;

                if ($female_age > 0 && $male_age > 0) {
                    $average_age = $average_age / 2.0;
                }

                $countsf .= (string) $female_age . ',';
                $countsm .= (string) $male_age . ',';
                $countsa .= (string) $average_age . ',';
            }

            $countsm = substr($countsm, 0, -1);
            $countsf = substr($countsf, 0, -1);
            $countsa = substr($countsa, 0, -1);
            $chd     = 't2:' . $countsm . '|' . $countsf . '|' . $countsa;
            $decades = '';
            for ($i = 0; $i <= 100; $i += 10) {
                $decades .= '|' . I18N::number($i);
            }
            $chxl  .= '1:||' . I18N::translate('century') . '|2:' . $decades . '|3:||' . I18N::translate('Age') . '|';
            $title = I18N::translate('Average age related to death century');
            if (count($rows) > 6 || mb_strlen($title) < 30) {
                $chtt = $title;
            } else {
                $offset  = 0;
                $counter = [];
                while ($offset = strpos($title, ' ', $offset + 1)) {
                    $counter[] = $offset;
                }
                $half = intdiv(count($counter), 2);
                $chtt = substr_replace($title, '|', $counter[$half], 1);
            }

            return '<img src="' . "https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|N*f1*,000000,0,-1,11,1|N*f1*,000000,1,-1,11,1&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=" . rawurlencode($chtt) . "&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . '&amp;chdl=' . rawurlencode(I18N::translate('Males') . '|' . I18N::translate('Females') . '|' . I18N::translate('Average age at death')) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Average age related to death century') . '" title="' . I18N::translate('Average age related to death century') . '" />';
        }

        $sex_search = '';
        $years      = '';
        if ($sex == 'F') {
            $sex_search = " AND i_sex='F'";
        } elseif ($sex == 'M') {
            $sex_search = " AND i_sex='M'";
        }
        if ($year1 >= 0 && $year2 >= 0) {
            if ($related == 'BIRT') {
                $years = " AND birth.d_year BETWEEN '{$year1}' AND '{$year2}'";
            } elseif ($related == 'DEAT') {
                $years = " AND death.d_year BETWEEN '{$year1}' AND '{$year2}'";
            }
        }
        $rows = $this->runSql(
            "SELECT" .
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
            " birth.d_julianday1 <> 0 AND" .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
            " death.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
            " death.d_julianday1>birth.d_julianday2" .
            $years .
            $sex_search .
            " ORDER BY age DESC"
        );

        return $rows;
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
        return $this->statsAgeQuery(true, 'BIRT', 'BOTH', -1, -1, $size);
    }

    /**
     * Find the lognest lived individual.
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
        return $this->topTenOldestQuery('nolist', 'BOTH', (int) $total);
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
        return $this->topTenOldestQuery('list', 'BOTH', (int) $total);
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestAlive(string $total = null): string
    {
        return $this->topTenOldestAliveQuery('nolist', 'BOTH', $total);
    }

    /**
     * Find the oldest living individuals.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestListAlive(string $total = null): string
    {
        return $this->topTenOldestAliveQuery('list', 'BOTH', $total);
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
        return $this->topTenOldestQuery('nolist', 'F', (int) $total);
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
        return $this->topTenOldestQuery('list', 'F', (int) $total);
    }

    /**
     * Find the oldest living females.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestFemaleAlive(string $total = null): string
    {
        return $this->topTenOldestAliveQuery('nolist', 'F', $total);
    }

    /**
     * Find the oldest living females.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestFemaleListAlive(string $total = null): string
    {
        return $this->topTenOldestAliveQuery('list', 'F', $total);
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
        return $this->topTenOldestQuery('nolist', 'M', (int) $total);
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
        return $this->topTenOldestQuery('list', 'M', (int) $total);
    }

    /**
     * Find the longest lived living males.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestMaleAlive(string $total = null): string
    {
        return $this->topTenOldestAliveQuery('nolist', 'M', $total);
    }

    /**
     * Find the longest lived living males.
     *
     * @param string|null $total
     *
     * @return string
     */
    public function topTenOldestMaleListAlive(string $total = null): string
    {
        return $this->topTenOldestAliveQuery('list', 'M', $total);
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
     * Events
     *
     * @param string   $type
     * @param string   $direction
     * @param string[] $facts
     *
     * @return string
     */
    private function eventQuery(string $type, string $direction, array $facts): string
    {
        $eventTypes = [
            'BIRT' => I18N::translate('birth'),
            'DEAT' => I18N::translate('death'),
            'MARR' => I18N::translate('marriage'),
            'ADOP' => I18N::translate('adoption'),
            'BURI' => I18N::translate('burial'),
            'CENS' => I18N::translate('census added'),
        ];

        $fact_query = "IN ('" . implode(',', $facts) . "')";

        if ($direction != 'ASC') {
            $direction = 'DESC';
        }
        $rows = $this->runSql(
            ' SELECT' .
            ' d_gid AS id,' .
            ' d_year AS year,' .
            ' d_fact AS fact,' .
            ' d_type AS type' .
            ' FROM' .
            " `##dates`" .
            ' WHERE' .
            " d_file={$this->tree->id()} AND" .
            " d_gid<>'HEAD' AND" .
            " d_fact {$fact_query} AND" .
            ' d_julianday1<>0' .
            ' ORDER BY' .
            " d_julianday1 {$direction}, d_type LIMIT 1"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $row    = $rows[0];
        $record = GedcomRecord::getInstance($row->id, $this->tree);
        switch ($type) {
            default:
            case 'full':
                if ($record->canShow()) {
                    $result = $record->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'year':
                $date   = new Date($row->type . ' ' . $row->year);
                $result = $date->display();
                break;
            case 'type':
                if (isset($eventTypes[$row->fact])) {
                    $result = $eventTypes[$row->fact];
                } else {
                    $result = GedcomTag::getLabel($row->fact);
                }
                break;
            case 'name':
                $result = '<a href="' . e($record->url()) . '">' . $record->getFullName() . '</a>';
                break;
            case 'place':
                $fact = $record->getFirstFact($row->fact);
                if ($fact) {
                    $result = FunctionsPrint::formatFactPlace($fact, true, true, true);
                } else {
                    $result = I18N::translate('Private');
                }
                break;
        }

        return $result;
    }

    /**
     * Find the earliest event.
     *
     * @return string
     */
    public function firstEvent(): string
    {
        return $this->eventQuery('full', 'ASC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the year of the earliest event.
     *
     * @return string
     */
    public function firstEventYear(): string
    {
        return $this->eventQuery('year', 'ASC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the type of the earliest event.
     *
     * @return string
     */
    public function firstEventType(): string
    {
        return $this->eventQuery('type', 'ASC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the name of the individual with the earliest event.
     *
     * @return string
     */
    public function firstEventName(): string
    {
        return $this->eventQuery('name', 'ASC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the location of the earliest event.
     *
     * @return string
     */
    public function firstEventPlace(): string
    {
        return $this->eventQuery('place', 'ASC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the latest event.
     *
     * @return string
     */
    public function lastEvent(): string
    {
        return $this->eventQuery('full', 'DESC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the year of the latest event.
     *
     * @return string
     */
    public function lastEventYear(): string
    {
        return $this->eventQuery('year', 'DESC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the type of the latest event.
     *
     * @return string
     */
    public function lastEventType(): string
    {
        return $this->eventQuery('type', 'DESC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Find the name of the individual with the latest event.
     *
     * @return string
     */
    public function lastEventName(): string
    {
        return $this->eventQuery('name', 'DESC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * FInd the location of the latest event.
     *
     * @return string
     */
    public function lastEventPlace(): string
    {
        return $this->eventQuery('place', 'DESC', array_merge(Gedcom::BIRTH_EVENTS, Gedcom::MARRIAGE_EVENTS, Gedcom::DIVORCE_EVENTS, Gedcom::DEATH_EVENTS));
    }

    /**
     * Query the database for marriage tags.
     *
     * @param string $type
     * @param string $age_dir
     * @param string $sex
     * @param bool   $show_years
     *
     * @return string
     */
    private function marriageQuery(string $type, string $age_dir, string $sex, bool $show_years): string
    {
        if ($sex == 'F') {
            $sex_field = 'f_wife';
        } else {
            $sex_field = 'f_husb';
        }
        if ($age_dir != 'ASC') {
            $age_dir = 'DESC';
        }
        $rows = $this->runSql(
            " SELECT fam.f_id AS famid, fam.{$sex_field}, married.d_julianday2-birth.d_julianday1 AS age, indi.i_id AS i_id" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS birth ON birth.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->id()}" .
            " WHERE" .
            " birth.d_gid = indi.i_id AND" .
            " married.d_gid = fam.f_id AND" .
            " indi.i_id = fam.{$sex_field} AND" .
            " fam.f_file = {$this->tree->id()} AND" .
            " birth.d_fact = 'BIRT' AND" .
            " married.d_fact = 'MARR' AND" .
            " birth.d_julianday1 <> 0 AND" .
            " married.d_julianday2 > birth.d_julianday1 AND" .
            " i_sex='{$sex}'" .
            " ORDER BY" .
            " married.d_julianday2-birth.d_julianday1 {$age_dir} LIMIT 1"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $row = $rows[0];
        if (isset($row->famid)) {
            $family = Family::getInstance($row->famid, $this->tree);
        }
        if (isset($row->i_id)) {
            $person = Individual::getInstance($row->i_id, $this->tree);
        }
        switch ($type) {
            default:
            case 'full':
                if ($family->canShow()) {
                    $result = $family->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'name':
                $result = '<a href="' . e($family->url()) . '">' . $person->getFullName() . '</a>';
                break;
            case 'age':
                $age = $row->age;
                if ($show_years) {
                    if ((int) ($age / 365.25) > 0) {
                        $age = (int) ($age / 365.25) . 'y';
                    } elseif ((int) ($age / 30.4375) > 0) {
                        $age = (int) ($age / 30.4375) . 'm';
                    } else {
                        $age = $age . 'd';
                    }
                    $result = FunctionsDate::getAgeAtEvent($age);
                } else {
                    $result = I18N::number((int) ($age / 365.25));
                }
                break;
        }

        return $result;
    }

    /**
     * General query on age at marriage.
     *
     * @param string $type
     * @param string $age_dir
     * @param int    $total
     *
     * @return string
     */
    private function ageOfMarriageQuery($type, $age_dir, int $total): string
    {
        $total = (int) $total;

        if ($age_dir != 'ASC') {
            $age_dir = 'DESC';
        }
        $hrows = $this->runSql(
            " SELECT DISTINCT fam.f_id AS family, MIN(husbdeath.d_julianday2-married.d_julianday1) AS age" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS husbdeath ON husbdeath.d_file = {$this->tree->id()}" .
            " WHERE" .
            " fam.f_file = {$this->tree->id()} AND" .
            " husbdeath.d_gid = fam.f_husb AND" .
            " husbdeath.d_fact = 'DEAT' AND" .
            " married.d_gid = fam.f_id AND" .
            " married.d_fact = 'MARR' AND" .
            " married.d_julianday1 < husbdeath.d_julianday2 AND" .
            " married.d_julianday1 <> 0" .
            " GROUP BY family" .
            " ORDER BY age {$age_dir}"
        );
        $wrows = $this->runSql(
            " SELECT DISTINCT fam.f_id AS family, MIN(wifedeath.d_julianday2-married.d_julianday1) AS age" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS wifedeath ON wifedeath.d_file = {$this->tree->id()}" .
            " WHERE" .
            " fam.f_file = {$this->tree->id()} AND" .
            " wifedeath.d_gid = fam.f_wife AND" .
            " wifedeath.d_fact = 'DEAT' AND" .
            " married.d_gid = fam.f_id AND" .
            " married.d_fact = 'MARR' AND" .
            " married.d_julianday1 < wifedeath.d_julianday2 AND" .
            " married.d_julianday1 <> 0" .
            " GROUP BY family" .
            " ORDER BY age {$age_dir}"
        );
        $drows = $this->runSql(
            " SELECT DISTINCT fam.f_id AS family, MIN(divorced.d_julianday2-married.d_julianday1) AS age" .
            " FROM `##families` AS fam" .
            " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS divorced ON divorced.d_file = {$this->tree->id()}" .
            " WHERE" .
            " fam.f_file = {$this->tree->id()} AND" .
            " married.d_gid = fam.f_id AND" .
            " married.d_fact = 'MARR' AND" .
            " divorced.d_gid = fam.f_id AND" .
            " divorced.d_fact IN ('DIV', 'ANUL', '_SEPR', '_DETS') AND" .
            " married.d_julianday1 < divorced.d_julianday2 AND" .
            " married.d_julianday1 <> 0" .
            " GROUP BY family" .
            " ORDER BY age {$age_dir}"
        );
        $rows = [];
        foreach ($drows as $family) {
            $rows[$family->family] = $family->age;
        }
        foreach ($hrows as $family) {
            if (!isset($rows[$family->family])) {
                $rows[$family->family] = $family->age;
            }
        }
        foreach ($wrows as $family) {
            if (!isset($rows[$family->family])) {
                $rows[$family->family] = $family->age;
            } elseif ($rows[$family->family] > $family->age) {
                $rows[$family->family] = $family->age;
            }
        }
        if ($age_dir === 'DESC') {
            arsort($rows);
        } else {
            asort($rows);
        }
        $top10 = [];
        $i     = 0;
        foreach ($rows as $fam => $age) {
            $family = Family::getInstance($fam, $this->tree);
            if ($type === 'name') {
                return $family->formatList();
            }
            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age = $age . 'd';
            }
            $age = FunctionsDate::getAgeAtEvent($age);
            if ($type === 'age') {
                return $age;
            }
            $husb = $family->getHusband();
            $wife = $family->getWife();
            if ($husb && $wife && ($husb->getAllDeathDates() && $wife->getAllDeathDates() || !$husb->isDead() || !$wife->isDead())) {
                if ($family->canShow()) {
                    if ($type === 'list') {
                        $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> (' . $age . ')' . '</li>';
                    } else {
                        $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> (' . $age . ')';
                    }
                }
                if (++$i === $total) {
                    break;
                }
            }
        }
        if ($type === 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
    }

    /**
     * Find the ages between spouses.
     *
     * @param string $type
     * @param string $age_dir
     * @param int    $total
     *
     * @return string
     */
    private function ageBetweenSpousesQuery(string $type, string $age_dir, int $total): string
    {
        $total = (int) $total;

        if ($age_dir === 'DESC') {
            $sql =
                "SELECT f_id AS xref, MIN(wife.d_julianday2-husb.d_julianday1) AS age" .
                " FROM `##families`" .
                " JOIN `##dates` AS wife ON wife.d_gid = f_wife AND wife.d_file = f_file" .
                " JOIN `##dates` AS husb ON husb.d_gid = f_husb AND husb.d_file = f_file" .
                " WHERE f_file = :tree_id" .
                " AND husb.d_fact = 'BIRT'" .
                " AND wife.d_fact = 'BIRT'" .
                " AND wife.d_julianday2 >= husb.d_julianday1 AND husb.d_julianday1 <> 0" .
                " GROUP BY xref" .
                " ORDER BY age DESC" .
                " LIMIT :limit";
        } else {
            $sql =
                "SELECT f_id AS xref, MIN(husb.d_julianday2-wife.d_julianday1) AS age" .
                " FROM `##families`" .
                " JOIN `##dates` AS wife ON wife.d_gid = f_wife AND wife.d_file = f_file" .
                " JOIN `##dates` AS husb ON husb.d_gid = f_husb AND husb.d_file = f_file" .
                " WHERE f_file = :tree_id" .
                " AND husb.d_fact = 'BIRT'" .
                " AND wife.d_fact = 'BIRT'" .
                " AND husb.d_julianday2 >= wife.d_julianday1 AND wife.d_julianday1 <> 0" .
                " GROUP BY xref" .
                " ORDER BY age DESC" .
                " LIMIT :limit";
        }
        $rows = Database::prepare(
            $sql
        )->execute([
            'tree_id' => $this->tree->id(),
            'limit'   => $total,
        ])->fetchAll();

        $top10 = [];
        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->xref, $this->tree);
            if ($fam->age < 0) {
                break;
            }
            $age = $fam->age;
            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age = $age . 'd';
            }
            $age = FunctionsDate::getAgeAtEvent($age);
            if ($family->canShow()) {
                if ($type === 'list') {
                    $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> (' . $age . ')' . '</li>';
                } else {
                    $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> (' . $age . ')';
                }
            }
        }
        if ($type === 'list') {
            $top10 = implode('', $top10);
            if ($top10) {
                $top10 = '<ul>' . $top10 . '</ul>';
            }
        } else {
            $top10 = implode(' ', $top10);
        }

        return $top10;
    }

    /**
     * General query on parents.
     *
     * @param string $type
     * @param string $age_dir
     * @param string $sex
     * @param bool   $show_years
     *
     * @return string
     */
    private function parentsQuery(string $type, string $age_dir, string $sex, bool $show_years): string
    {
        if ($sex == 'F') {
            $sex_field = 'WIFE';
        } else {
            $sex_field = 'HUSB';
        }
        if ($age_dir != 'ASC') {
            $age_dir = 'DESC';
        }
        $rows = $this->runSql(
            " SELECT" .
            " parentfamily.l_to AS id," .
            " childbirth.d_julianday2-birth.d_julianday1 AS age" .
            " FROM `##link` AS parentfamily" .
            " JOIN `##link` AS childfamily ON childfamily.l_file = {$this->tree->id()}" .
            " JOIN `##dates` AS birth ON birth.d_file = {$this->tree->id()}" .
            " JOIN `##dates` AS childbirth ON childbirth.d_file = {$this->tree->id()}" .
            " WHERE" .
            " birth.d_gid = parentfamily.l_to AND" .
            " childfamily.l_to = childbirth.d_gid AND" .
            " childfamily.l_type = 'CHIL' AND" .
            " parentfamily.l_type = '{$sex_field}' AND" .
            " childfamily.l_from = parentfamily.l_from AND" .
            " parentfamily.l_file = {$this->tree->id()} AND" .
            " birth.d_fact = 'BIRT' AND" .
            " childbirth.d_fact = 'BIRT' AND" .
            " birth.d_julianday1 <> 0 AND" .
            " childbirth.d_julianday2 > birth.d_julianday1" .
            " ORDER BY age {$age_dir} LIMIT 1"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $row = $rows[0];
        if (isset($row->id)) {
            $person = Individual::getInstance($row->id, $this->tree);
        }
        switch ($type) {
            default:
            case 'full':
                if ($person->canShow()) {
                    $result = $person->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'name':
                $result = '<a href="' . e($person->url()) . '">' . $person->getFullName() . '</a>';
                break;
            case 'age':
                $age = $row->age;
                if ($show_years) {
                    if ((int) ($age / 365.25) > 0) {
                        $age = (int) ($age / 365.25) . 'y';
                    } elseif ((int) ($age / 30.4375) > 0) {
                        $age = (int) ($age / 30.4375) . 'm';
                    } else {
                        $age = $age . 'd';
                    }
                    $result = FunctionsDate::getAgeAtEvent($age);
                } else {
                    $result = (string) floor($age / 365.25);
                }
                break;
        }

        return $result;
    }

    /**
     * General query on marriages.
     *
     * @param bool        $simple
     * @param bool        $first
     * @param int         $year1
     * @param int         $year2
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string|array
     */
    public function statsMarrQuery($simple = true, $first = false, $year1 = -1, $year2 = -1, string $size = null, string $color_from = null, string $color_to = null)
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        if ($simple) {
            $sql =
                "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total" .
                " FROM `##dates`" .
                " WHERE d_file={$this->tree->id()} AND d_year<>0 AND d_fact='MARR' AND d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
            if ($year1 >= 0 && $year2 >= 0) {
                $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
            }
            $sql .= " GROUP BY century ORDER BY century";
        } elseif ($first) {
            $years = '';
            if ($year1 >= 0 && $year2 >= 0) {
                $years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
            }
            $sql =
                " SELECT fam.f_id AS fams, fam.f_husb, fam.f_wife, married.d_julianday2 AS age, married.d_month AS month, indi.i_id AS indi" .
                " FROM `##families` AS fam" .
                " LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}" .
                " LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->id()}" .
                " WHERE" .
                " married.d_gid = fam.f_id AND" .
                " fam.f_file = {$this->tree->id()} AND" .
                " married.d_fact = 'MARR' AND" .
                " married.d_julianday2 <> 0 AND" .
                $years .
                " (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)" .
                " ORDER BY fams, indi, age ASC";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total" .
                " FROM `##dates`" .
                " WHERE d_file={$this->tree->id()} AND d_fact='MARR'";
            if ($year1 >= 0 && $year2 >= 0) {
                $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
            }
            $sql .= " GROUP BY d_month";
        }
        $rows = $this->runSql($sql);

        if ($simple) {
            $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
            $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
            $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

            $sizes = explode('x', $size);
            $tot   = 0;

            foreach ($rows as $values) {
                $values->total = (int) $values->total;
                $tot += (int) $values->total;
            }
            // Beware divide by zero
            if ($tot === 0) {
                return '';
            }
            $centuries = '';
            $counts    = [];
            foreach ($rows as $values) {
                $counts[] = intdiv(100 * $values->total, $tot);
                $centuries .= $this->centuryName($values->century) . ' - ' . I18N::number($values->total) . '|';
            }
            $chd = $this->arrayToExtendedEncoding($counts);
            $chl = substr($centuries, 0, -1);

            return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Marriages by century') . '" title="' . I18N::translate('Marriages by century') . '" />';
        }

        return $rows;
    }

    /**
     * General query on divorces.
     *
     * @param bool        $simple
     * @param bool        $first
     * @param int         $year1
     * @param int         $year2
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string|stdClass[]
     */
    private function statsDivQuery($simple = true, $first = false, $year1 = -1, $year2 = -1, string $size = null, string $color_from = null, string $color_to = null)
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        if ($simple) {
            $sql =
                "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total" .
                " FROM `##dates`" .
                " WHERE d_file={$this->tree->id()} AND d_year<>0 AND d_fact = 'DIV' AND d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
            if ($year1 >= 0 && $year2 >= 0) {
                $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
            }
            $sql .= " GROUP BY century ORDER BY century";
        } elseif ($first) {
            $years = '';
            if ($year1 >= 0 && $year2 >= 0) {
                $years = " divorced.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
            }
            $sql =
                " SELECT fam.f_id AS fams, fam.f_husb, fam.f_wife, divorced.d_julianday2 AS age, divorced.d_month AS month, indi.i_id AS indi" .
                " FROM `##families` AS fam" .
                " LEFT JOIN `##dates` AS divorced ON divorced.d_file = {$this->tree->id()}" .
                " LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->id()}" .
                " WHERE" .
                " divorced.d_gid = fam.f_id AND" .
                " fam.f_file = {$this->tree->id()} AND" .
                " divorced.d_fact = 'DIV' AND" .
                " divorced.d_julianday2 <> 0 AND" .
                $years .
                " (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)" .
                " ORDER BY fams, indi, age ASC";
        } else {
            $sql =
                "SELECT d_month, COUNT(*) AS total FROM `##dates` " .
                "WHERE d_file={$this->tree->id()} AND d_fact = 'DIV'";
            if ($year1 >= 0 && $year2 >= 0) {
                $sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
            }
            $sql .= " GROUP BY d_month";
        }
        $rows = $this->runSql($sql);

        if ($simple) {
            $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
            $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
            $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

            $sizes = explode('x', $size);
            $tot   = 0;
            foreach ($rows as $values) {
                $values->total = (int) $values->total;
                $tot += $values->total;
            }
            // Beware divide by zero
            if ($tot === 0) {
                return '';
            }
            $centuries = '';
            $counts    = [];
            foreach ($rows as $values) {
                $counts[] = intdiv(100 * $values->total, $tot);
                $centuries .= $this->centuryName($values->century) . ' - ' . I18N::number($values->total) . '|';
            }
            $chd = $this->arrayToExtendedEncoding($counts);
            $chl = substr($centuries, 0, -1);

            return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Divorces by century') . '" title="' . I18N::translate('Divorces by century') . '" />';
        }

        return $rows;
    }

    /**
     * Find the earliest marriage.
     *
     * @return string
     */
    public function firstMarriage(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'MARR');
    }

    /**
     * Find the year of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'MARR');
    }

    /**
     * Find the names of spouses of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'MARR');
    }

    /**
     * Find the place of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriagePlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'MARR');
    }

    /**
     * Find the latest marriage.
     *
     * @return string
     */
    public function lastMarriage(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'MARR');
    }

    /**
     * Find the year of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'MARR');
    }

    /**
     * Find the names of spouses of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'MARR');
    }

    /**
     * Find the location of the latest marriage.
     *
     * @return string
     */
    public function lastMarriagePlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'MARR');
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
        return $this->statsMarrQuery(true, false, -1, -1, $size, $color_from, $color_to);
    }

    /**
     * Find the earliest divorce.
     *
     * @return string
     */
    public function firstDivorce(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'DIV');
    }

    /**
     * Find the year of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'DIV');
    }

    /**
     * Find the names of individuals in the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'DIV');
    }

    /**
     * Find the location of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorcePlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'DIV');
    }

    /**
     * Find the latest divorce.
     *
     * @return string
     */
    public function lastDivorce(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'DIV');
    }

    /**
     * Find the year of the latest divorce.
     *
     * @return string
     */
    public function lastDivorceYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'DIV');
    }

    /**
     * Find the names of the individuals in the latest divorce.
     *
     * @return string
     */
    public function lastDivorceName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'DIV');
    }

    /**
     * Find the location of the latest divorce.
     *
     * @return string
     */
    public function lastDivorcePlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'DIV');
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
        return $this->statsDivQuery(true, false, -1, -1, $size, $color_from, $color_to);
    }

    /**
     * General query on ages at marriage.
     *
     * @param bool   $simple
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     * @param string $size
     *
     * @return array|string
     */
    public function statsMarrAgeQuery($simple = true, $sex = 'M', $year1 = -1, $year2 = -1, $size = '200x250')
    {
        if ($simple) {
            $sizes = explode('x', $size);

            $rows  = $this->runSql(
                "SELECT " .
                " ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, " .
                " FLOOR(married.d_year/100+1) AS century, " .
                " 'M' AS sex " .
                "FROM `##dates` AS married " .
                "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
                "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) " .
                "WHERE " .
                " '{$sex}' IN ('M', 'BOTH') AND " .
                " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
                " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
                " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
                "GROUP BY century, sex " .
                "UNION ALL " .
                "SELECT " .
                " ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, " .
                " FLOOR(married.d_year/100+1) AS century, " .
                " 'F' AS sex " .
                "FROM `##dates` AS married " .
                "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
                "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) " .
                "WHERE " .
                " '{$sex}' IN ('F', 'BOTH') AND " .
                " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
                " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
                " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
                " GROUP BY century, sex ORDER BY century"
            );
            if (empty($rows)) {
                return '';
            }
            $max = 0;
            foreach ($rows as $values) {
                if ($max < $values->age) {
                    $max = $values->age;
                }
            }
            $chxl    = '0:|';
            $chmm    = '';
            $chmf    = '';
            $i       = 0;
            $countsm = '';
            $countsf = '';
            $countsa = '';
            $out     = [];
            foreach ($rows as $values) {
                $out[$values->century][$values->sex] = $values->age;
            }
            foreach ($out as $century => $values) {
                if ($sizes[0] < 1000) {
                    $sizes[0] += 50;
                }
                $chxl .= $this->centuryName($century) . '|';
                $average = 0;
                if (isset($values['F'])) {
                    if ($max <= 50) {
                        $value = $values['F'] * 2;
                    } else {
                        $value = $values['F'];
                    }
                    $countsf .= $value . ',';
                    $average = $value;
                    $chmf    .= 't' . $values['F'] . ',000000,1,' . $i . ',11,1|';
                } else {
                    $countsf .= '0,';
                    $chmf    .= 't0,000000,1,' . $i . ',11,1|';
                }
                if (isset($values['M'])) {
                    if ($max <= 50) {
                        $value = $values['M'] * 2;
                    } else {
                        $value = $values['M'];
                    }
                    $countsm .= $value . ',';
                    if ($average == 0) {
                        $countsa .= $value . ',';
                    } else {
                        $countsa .= (($value + $average) / 2) . ',';
                    }
                    $chmm .= 't' . $values['M'] . ',000000,0,' . $i . ',11,1|';
                } else {
                    $countsm .= '0,';
                    if ($average == 0) {
                        $countsa .= '0,';
                    } else {
                        $countsa .= $value . ',';
                    }
                    $chmm .= 't0,000000,0,' . $i . ',11,1|';
                }
                $i++;
            }
            $countsm = substr($countsm, 0, -1);
            $countsf = substr($countsf, 0, -1);
            $countsa = substr($countsa, 0, -1);
            $chmf    = substr($chmf, 0, -1);
            $chd     = 't2:' . $countsm . '|' . $countsf . '|' . $countsa;
            if ($max <= 50) {
                $chxl .= '1:||' . I18N::translate('century') . '|2:|0|10|20|30|40|50|3:||' . I18N::translate('Age') . '|';
            } else {
                $chxl .= '1:||' . I18N::translate('century') . '|2:|0|10|20|30|40|50|60|70|80|90|100|3:||' . I18N::translate('Age') . '|';
            }
            if (count($rows) > 4 || mb_strlen(I18N::translate('Average age in century of marriage')) < 30) {
                $chtt = I18N::translate('Average age in century of marriage');
            } else {
                $offset  = 0;
                $counter = [];
                while ($offset = strpos(I18N::translate('Average age in century of marriage'), ' ', $offset + 1)) {
                    $counter[] = $offset;
                }
                $half = intdiv(count($counter), 2);
                $chtt = substr_replace(I18N::translate('Average age in century of marriage'), '|', $counter[$half], 1);
            }

            return '<img src="' . "https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|{$chmm}{$chmf}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=" . rawurlencode($chtt) . "&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . '&amp;chdl=' . rawurlencode(I18N::translate('Males') . '|' . I18N::translate('Females') . '|' . I18N::translate('Average age')) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Average age in century of marriage') . '" title="' . I18N::translate('Average age in century of marriage') . '" />';
        }

        if ($year1 >= 0 && $year2 >= 0) {
            $years = " married.d_year BETWEEN {$year1} AND {$year2} AND ";
        } else {
            $years = '';
        }
        $rows = $this->runSql(
            "SELECT " .
            " fam.f_id, " .
            " birth.d_gid, " .
            " married.d_julianday2-birth.d_julianday1 AS age " .
            "FROM `##dates` AS married " .
            "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
            "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) " .
            "WHERE " .
            " '{$sex}' IN ('M', 'BOTH') AND {$years} " .
            " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
            " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
            "UNION ALL " .
            "SELECT " .
            " fam.f_id, " .
            " birth.d_gid, " .
            " married.d_julianday2-birth.d_julianday1 AS age " .
            "FROM `##dates` AS married " .
            "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
            "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) " .
            "WHERE " .
            " '{$sex}' IN ('F', 'BOTH') AND {$years} " .
            " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
            " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 "
        );

        return $rows;
    }

    /**
     * Find the youngest wife.
     *
     * @return string
     */
    public function youngestMarriageFemale(): string
    {
        return $this->marriageQuery('full', 'ASC', 'F', false);
    }

    /**
     * Find the name of the youngest wife.
     *
     * @return string
     */
    public function youngestMarriageFemaleName(): string
    {
        return $this->marriageQuery('name', 'ASC', 'F', false);
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
        return $this->marriageQuery('age', 'ASC', 'F', (bool) $show_years);
    }

    /**
     * Find the oldest wife.
     *
     * @return string
     */
    public function oldestMarriageFemale(): string
    {
        return $this->marriageQuery('full', 'DESC', 'F', false);
    }

    /**
     * Find the name of the oldest wife.
     *
     * @return string
     */
    public function oldestMarriageFemaleName(): string
    {
        return $this->marriageQuery('name', 'DESC', 'F', false);
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
        return $this->marriageQuery('age', 'DESC', 'F', (bool) $show_years);
    }

    /**
     * Find the youngest husband.
     *
     * @return string
     */
    public function youngestMarriageMale(): string
    {
        return $this->marriageQuery('full', 'ASC', 'M', false);
    }

    /**
     * Find the name of the youngest husband.
     *
     * @return string
     */
    public function youngestMarriageMaleName(): string
    {
        return $this->marriageQuery('name', 'ASC', 'M', false);
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
        return $this->marriageQuery('age', 'ASC', 'M', (bool) $show_years);
    }

    /**
     * Find the oldest husband.
     *
     * @return string
     */
    public function oldestMarriageMale(): string
    {
        return $this->marriageQuery('full', 'DESC', 'M', false);
    }

    /**
     * Find the name of the oldest husband.
     *
     * @return string
     */
    public function oldestMarriageMaleName(): string
    {
        return $this->marriageQuery('name', 'DESC', 'M', false);
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
        return $this->marriageQuery('age', 'DESC', 'M', (bool) $show_years);
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
        return $this->statsMarrAgeQuery(true, 'BOTH', -1, -1, $size);
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
        return $this->ageBetweenSpousesQuery('nolist', 'DESC', (int) $total);
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
        return $this->ageBetweenSpousesQuery('list', 'DESC', (int) $total);
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
        return $this->ageBetweenSpousesQuery('nolist', 'ASC', (int) $total);
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
        return $this->ageBetweenSpousesQuery('list', 'ASC', (int) $total);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function topAgeOfMarriageFamily(): string
    {
        return $this->ageOfMarriageQuery('name', 'DESC', 1);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function topAgeOfMarriage(): string
    {
        return $this->ageOfMarriageQuery('age', 'DESC', 1);
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
        return $this->ageOfMarriageQuery('nolist', 'DESC', (int) $total);
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
        return $this->ageOfMarriageQuery('list', 'DESC', (int) $total);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function minAgeOfMarriageFamily(): string
    {
        return $this->ageOfMarriageQuery('name', 'ASC', 1);
    }

    /**
     * General query on marriage ages.
     *
     * @return string
     */
    public function minAgeOfMarriage(): string
    {
        return $this->ageOfMarriageQuery('age', 'ASC', 1);
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
        return $this->ageOfMarriageQuery('nolist', 'ASC', (int) $total);
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
        return $this->ageOfMarriageQuery('list', 'ASC', (int) $total);
    }

    /**
     * Find the youngest mother
     *
     * @return string
     */
    public function youngestMother(): string
    {
        return $this->parentsQuery('full', 'ASC', 'F', false);
    }

    /**
     * Find the name of the youngest mother.
     *
     * @return string
     */
    public function youngestMotherName(): string
    {
        return $this->parentsQuery('name', 'ASC', 'F', false);
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
        return $this->parentsQuery('age', 'ASC', 'F', (bool) $show_years);
    }

    /**
     * Find the oldest mother.
     *
     * @return string
     */
    public function oldestMother(): string
    {
        return $this->parentsQuery('full', 'DESC', 'F', false);
    }

    /**
     * Find the name of the oldest mother.
     *
     * @return string
     */
    public function oldestMotherName(): string
    {
        return $this->parentsQuery('name', 'DESC', 'F', false);
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
        return $this->parentsQuery('age', 'DESC', 'F', (bool) $show_years);
    }

    /**
     * Find the youngest father.
     *
     * @return string
     */
    public function youngestFather(): string
    {
        return $this->parentsQuery('full', 'ASC', 'M', false);
    }

    /**
     * Find the name of the youngest father.
     *
     * @return string
     */
    public function youngestFatherName(): string
    {
        return $this->parentsQuery('name', 'ASC', 'M', false);
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
        return $this->parentsQuery('age', 'ASC', 'M', (bool) $show_years);
    }

    /**
     * Find the oldest father.
     *
     * @return string
     */
    public function oldestFather(): string
    {
        return $this->parentsQuery('full', 'DESC', 'M', false);
    }

    /**
     * Find the name of the oldest father.
     *
     * @return string
     */
    public function oldestFatherName(): string
    {
        return $this->parentsQuery('name', 'DESC', 'M', false);
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
        return $this->parentsQuery('age', 'DESC', 'M', (bool) $show_years);
    }

    /**
     * Number of husbands.
     *
     * @return string
     */
    public function totalMarriedMales(): string
    {
        $n = (int) Database::prepare(
            "SELECT COUNT(DISTINCT f_husb) FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\\n1 MARR%'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($n);
    }

    /**
     * Number of wives.
     *
     * @return string
     */
    public function totalMarriedFemales(): string
    {
        $n = (int) Database::prepare(
            "SELECT COUNT(DISTINCT f_wife) FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\\n1 MARR%'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($n);
    }

    /**
     * General query on family.
     *
     * @param string $type
     *
     * @return string
     */
    private function familyQuery($type): string
    {
        $rows = $this->runSql(
            " SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE" .
            " f_file={$this->tree->id()}" .
            " AND f_numchil = (" .
            "  SELECT max( f_numchil )" .
            "  FROM `##families`" .
            "  WHERE f_file ={$this->tree->id()}" .
            " )" .
            " LIMIT 1"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $row    = $rows[0];
        $family = Family::getInstance($row->id, $this->tree);
        switch ($type) {
            default:
            case 'full':
                if ($family->canShow()) {
                    $result = $family->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'size':
                $result = I18N::number((int) $row->tot);
                break;
            case 'name':
                $result = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a>';
                break;
        }

        return $result;
    }

    /**
     * General query on families.
     *
     * @param string $type
     * @param int    $total
     *
     * @return string
     */
    private function topTenFamilyQuery(string $type, int $total): string
    {
        $total = (int) $total;

        $rows = $this->runSql(
            "SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE" .
            " f_file={$this->tree->id()}" .
            " ORDER BY tot DESC" .
            " LIMIT " . $total
        );

        if (empty($rows)) {
            return '';
        }

        $top10 = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->id, $this->tree);
            if ($family->canShow()) {
                $total = (int) $row->tot;

                if ($type === 'list') {
                    $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> - ' . I18N::plural('%s child', '%s children', $total, I18N::number($total));
                } else {
                    $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> - ' . I18N::plural('%s child', '%s children', $total, I18N::number($total));
                }
            }
        }
        if ($type === 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
    }

    /**
     * Find the ages between siblings.
     *
     * @param string $type
     * @param int    $total
     * @param bool   $one   Include each family only once if true
     *
     * @return string
     */
    private function ageBetweenSiblingsQuery(string $type, int $total, bool $one): string
    {
        $rows = $this->runSql(
            " SELECT DISTINCT" .
            " link1.l_from AS family," .
            " link1.l_to AS ch1," .
            " link2.l_to AS ch2," .
            " child1.d_julianday2-child2.d_julianday2 AS age" .
            " FROM `##link` AS link1" .
            " LEFT JOIN `##dates` AS child1 ON child1.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##dates` AS child2 ON child2.d_file = {$this->tree->id()}" .
            " LEFT JOIN `##link` AS link2 ON link2.l_file = {$this->tree->id()}" .
            " WHERE" .
            " link1.l_file = {$this->tree->id()} AND" .
            " link1.l_from = link2.l_from AND" .
            " link1.l_type = 'CHIL' AND" .
            " child1.d_gid = link1.l_to AND" .
            " child1.d_fact = 'BIRT' AND" .
            " link2.l_type = 'CHIL' AND" .
            " child2.d_gid = link2.l_to AND" .
            " child2.d_fact = 'BIRT' AND" .
            " child1.d_julianday2 > child2.d_julianday2 AND" .
            " child2.d_julianday2 <> 0 AND" .
            " child1.d_gid <> child2.d_gid" .
            " ORDER BY age DESC" .
            " LIMIT " . $total
        );
        if (!isset($rows[0])) {
            return '';
        }
        $top10 = [];
        $dist  = [];
        foreach ($rows as $fam) {
            $family = Family::getInstance($fam->family, $this->tree);
            $child1 = Individual::getInstance($fam->ch1, $this->tree);
            $child2 = Individual::getInstance($fam->ch2, $this->tree);
            if ($type == 'name') {
                if ($child1->canShow() && $child2->canShow()) {
                    $return = '<a href="' . e($child2->url()) . '">' . $child2->getFullName() . '</a> ';
                    $return .= I18N::translate('and') . ' ';
                    $return .= '<a href="' . e($child1->url()) . '">' . $child1->getFullName() . '</a>';
                    $return .= ' <a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';
                } else {
                    $return = I18N::translate('This information is private and cannot be shown.');
                }

                return $return;
            }
            $age = $fam->age;
            if ((int) ($age / 365.25) > 0) {
                $age = (int) ($age / 365.25) . 'y';
            } elseif ((int) ($age / 30.4375) > 0) {
                $age = (int) ($age / 30.4375) . 'm';
            } else {
                $age = $age . 'd';
            }
            $age = FunctionsDate::getAgeAtEvent($age);
            if ($type == 'age') {
                return $age;
            }
            if ($type == 'list') {
                if ($one && !in_array($fam->family, $dist)) {
                    if ($child1->canShow() && $child2->canShow()) {
                        $return = '<li>';
                        $return  .= '<a href="' . e($child2->url()) . '">' . $child2->getFullName() . '</a> ';
                        $return  .= I18N::translate('and') . ' ';
                        $return  .= '<a href="' . e($child1->url()) . '">' . $child1->getFullName() . '</a>';
                        $return  .= ' (' . $age . ')';
                        $return  .= ' <a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';
                        $return  .= '</li>';
                        $top10[] = $return;
                        $dist[]  = $fam->family;
                    }
                } elseif (!$one && $child1->canShow() && $child2->canShow()) {
                    $return = '<li>';
                    $return  .= '<a href="' . e($child2->url()) . '">' . $child2->getFullName() . '</a> ';
                    $return  .= I18N::translate('and') . ' ';
                    $return  .= '<a href="' . e($child1->url()) . '">' . $child1->getFullName() . '</a>';
                    $return  .= ' (' . $age . ')';
                    $return  .= ' <a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';
                    $return  .= '</li>';
                    $top10[] = $return;
                }
            } else {
                if ($child1->canShow() && $child2->canShow()) {
                    $return = $child2->formatList();
                    $return .= '<br>' . I18N::translate('and') . '<br>';
                    $return .= $child1->formatList();
                    $return .= '<br><a href="' . e($family->url()) . '">[' . I18N::translate('View this family') . ']</a>';

                    return $return;
                }

                return I18N::translate('This information is private and cannot be shown.');
            }
        }
        if ($type === 'list') {
            $top10 = implode('', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
    }

    /**
     * Find the month in the year of the birth of the first child.
     *
     * @param bool $sex
     * @param int  $year1
     * @param int  $year2
     *
     * @return stdClass[]
     */
    public function monthFirstChildQuery($sex = false, $year1 = -1, $year2 = -1): array
    {
        if ($year1 >= 0 && $year2 >= 0) {
            $sql_years = " AND (d_year BETWEEN '{$year1}' AND '{$year2}')";
        } else {
            $sql_years = '';
        }
        if ($sex) {
            $sql_sex1 = ', i_sex';
            $sql_sex2 = " JOIN `##individuals` AS child ON child1.d_file = i_file AND child1.d_gid = child.i_id ";
        } else {
            $sql_sex1 = '';
            $sql_sex2 = '';
        }
        $sql =
            "SELECT d_month{$sql_sex1}, COUNT(*) AS total " .
            "FROM (" .
            " SELECT family{$sql_sex1}, MIN(date) AS d_date, d_month" .
            " FROM (" .
            "  SELECT" .
            "  link1.l_from AS family," .
            "  link1.l_to AS child," .
            "  child1.d_julianday2 AS date," .
            "  child1.d_month as d_month" .
            $sql_sex1 .
            "  FROM `##link` AS link1" .
            "  LEFT JOIN `##dates` AS child1 ON child1.d_file = {$this->tree->id()}" .
            $sql_sex2 .
            "  WHERE" .
            "  link1.l_file = {$this->tree->id()} AND" .
            "  link1.l_type = 'CHIL' AND" .
            "  child1.d_gid = link1.l_to AND" .
            "  child1.d_fact = 'BIRT' AND" .
            "  child1.d_month IN ('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC')" .
            $sql_years .
            "  ORDER BY date" .
            " ) AS children" .
            " GROUP BY family, d_month{$sql_sex1}" .
            ") AS first_child " .
            "GROUP BY d_month";
        if ($sex) {
            $sql .= ', i_sex';
        }
        $rows = $this->runSql($sql);

        return $rows;
    }

    /**
     * Find the family with the most children.
     *
     * @return string
     */
    public function largestFamily(): string
    {
        return $this->familyQuery('full');
    }

    /**
     * Find the number of children in the largest family.
     *
     * @return string
     */
    public function largestFamilySize(): string
    {
        return $this->familyQuery('size');
    }

    /**
     * Find the family with the most children.
     *
     * @return string
     */
    public function largestFamilyName(): string
    {
        return $this->familyQuery('name');
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
        return $this->topTenFamilyQuery('nolist', (int) $total);
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
        return $this->topTenFamilyQuery('list', (int) $total);
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
    public function chartLargestFamilies(string $size = null, string $color_from = null, string $color_to = null, string $total = '10'): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_L_CHART_X    = Theme::theme()->parameter('stats-large-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? $WT_STATS_L_CHART_X . 'x' . $WT_STATS_S_CHART_Y;
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;
        $total      = $total ?? '10';

        $sizes = explode('x', $size);
        $total = (int) $total;
        $rows  = $this->runSql(
            " SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE f_file={$this->tree->id()}" .
            " ORDER BY tot DESC" .
            " LIMIT " . $total
        );
        if (!isset($rows[0])) {
            return '';
        }
        $tot = 0;
        foreach ($rows as $row) {
            $row->tot = (int) $row->tot;
            $tot += $row->tot;
        }
        $chd = '';
        $chl = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->id, $this->tree);
            if ($family->canShow()) {
                if ($tot == 0) {
                    $per = 0;
                } else {
                    $per = intdiv(100 * $row->tot, $tot);
                }
                $chd .= $this->arrayToExtendedEncoding([$per]);
                $chl[] = htmlspecialchars_decode(strip_tags($family->getFullName())) . ' - ' . I18N::number($row->tot);
            }
        }
        $chl = rawurlencode(implode('|', $chl));

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Largest families') . '" title="' . I18N::translate('Largest families') . '" />';
    }

    /**
     * Count the total children.
     *
     * @return string
     */
    public function totalChildren(): string
    {
        $total = (int) Database::prepare(
            "SELECT SUM(f_numchil) FROM `##families` WHERE f_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($total);
    }

    /**
     * Find the average number of children in families.
     *
     * @return string
     */
    public function averageChildren(): string
    {
        $average = (float) Database::prepare(
            "SELECT AVG(f_numchil) AS tot FROM `##families` WHERE f_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();

        return I18N::number($average, 2);
    }

    /**
     * General query on familes/children.
     *
     * @param bool   $simple
     * @param string $sex
     * @param int    $year1
     * @param int    $year2
     * @param string $size
     *
     * @return string|stdClass[]
     */
    public function statsChildrenQuery($simple = true, $sex = 'BOTH', $year1 = -1, $year2 = -1, $size = '220x200')
    {
        if ($simple) {
            $sizes = explode('x', $size);
            $max   = 0;
            $rows  = $this->runSql(
                " SELECT ROUND(AVG(f_numchil),2) AS num, FLOOR(d_year/100+1) AS century" .
                " FROM  `##families`" .
                " JOIN  `##dates` ON (d_file = f_file AND d_gid=f_id)" .
                " WHERE f_file = {$this->tree->id()}" .
                " AND   d_julianday1<>0" .
                " AND   d_fact = 'MARR'" .
                " AND   d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')" .
                " GROUP BY century" .
                " ORDER BY century"
            );
            if (empty($rows)) {
                return '';
            }
            foreach ($rows as $values) {
                if ($max < $values->num) {
                    $max = $values->num;
                }
            }
            $chm    = '';
            $chxl   = '0:|';
            $i      = 0;
            $counts = [];
            foreach ($rows as $values) {
                $chxl .= $this->centuryName($values->century) . '|';
                if ($max <= 5) {
                    $counts[] = (int) ($values->num * 819.2 - 1);
                } elseif ($max <= 10) {
                    $counts[] = (int) ($values->num * 409.6);
                } else {
                    $counts[] = (int) ($values->num * 204.8);
                }
                $chm .= 't' . $values->num . ',000000,0,' . $i . ',11,1|';
                $i++;
            }
            $chd = $this->arrayToExtendedEncoding($counts);
            $chm = substr($chm, 0, -1);
            if ($max <= 5) {
                $chxl .= '1:||' . I18N::translate('century') . '|2:|0|1|2|3|4|5|3:||' . I18N::translate('Number of children') . '|';
            } elseif ($max <= 10) {
                $chxl .= '1:||' . I18N::translate('century') . '|2:|0|1|2|3|4|5|6|7|8|9|10|3:||' . I18N::translate('Number of children') . '|';
            } else {
                $chxl .= '1:||' . I18N::translate('century') . '|2:|0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|3:||' . I18N::translate('Number of children') . '|';
            }

            return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0,3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Average number of children per family') . '" title="' . I18N::translate('Average number of children per family') . '" />';
        }

        if ($sex == 'M') {
            $sql =
                "SELECT num, COUNT(*) AS total FROM " .
                "(SELECT count(i_sex) AS num FROM `##link` " .
                "LEFT OUTER JOIN `##individuals` " .
                "ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' " .
                "JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->id()} GROUP BY l_to" .
                ") boys" .
                " GROUP BY num" .
                " ORDER BY num";
        } elseif ($sex == 'F') {
            $sql =
                "SELECT num, COUNT(*) AS total FROM " .
                "(SELECT count(i_sex) AS num FROM `##link` " .
                "LEFT OUTER JOIN `##individuals` " .
                "ON l_from=i_id AND l_file=i_file AND i_sex='F' AND l_type='FAMC' " .
                "JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->id()} GROUP BY l_to" .
                ") girls" .
                " GROUP BY num" .
                " ORDER BY num";
        } else {
            $sql = "SELECT f_numchil, COUNT(*) AS total FROM `##families` ";
            if ($year1 >= 0 && $year2 >= 0) {
                $sql .=
                    "AS fam LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->id()}"
                    . " WHERE"
                    . " married.d_gid = fam.f_id AND"
                    . " fam.f_file = {$this->tree->id()} AND"
                    . " married.d_fact = 'MARR' AND"
                    . " married.d_year BETWEEN '{$year1}' AND '{$year2}'";
            } else {
                $sql .= "WHERE f_file={$this->tree->id()}";
            }
            $sql .= ' GROUP BY f_numchil';
        }
        $rows = $this->runSql($sql);

        return $rows;
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
        return $this->statsChildrenQuery(true, 'BOTH', -1, -1, $size);
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
        return $this->ageBetweenSiblingsQuery('name', (int) $total, (bool) $one);
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
        return $this->ageBetweenSiblingsQuery('age', (int) $total, (bool) $one);
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
        return $this->ageBetweenSiblingsQuery('nolist', (int) $total, (bool) $one);
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
        return $this->ageBetweenSiblingsQuery('list', (int) $total, (bool) $one);
    }

    /**
     * Find the families with no children.
     *
     * @return int
     */
    private function noChildrenFamiliesQuery(): int
    {
        $rows = $this->runSql(
            " SELECT COUNT(*) AS tot" .
            " FROM  `##families`" .
            " WHERE f_numchil = 0 AND f_file = {$this->tree->id()}"
        );

        return (int) $rows[0]->tot;
    }

    /**
     * Find the families with no children.
     *
     * @return string
     */
    public function noChildrenFamilies(): string
    {
        return I18N::number($this->noChildrenFamiliesQuery());
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
        $rows = $this->runSql(
            " SELECT f_id AS family" .
            " FROM `##families` AS fam" .
            " WHERE f_numchil = 0 AND fam.f_file = {$this->tree->id()}"
        );
        if (!isset($rows[0])) {
            return '';
        }
        $top10 = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->family, $this->tree);
            if ($family->canShow()) {
                if ($type == 'list') {
                    $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a></li>';
                } else {
                    $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a>';
                }
            }
        }
        if ($type == 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
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
        $year1 = (int) $year1;
        $year2 = (int) $year2;

        $sizes = explode('x', $size);
        if ($year1 >= 0 && $year2 >= 0) {
            $years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
        } else {
            $years = '';
        }
        $max  = 0;
        $tot  = 0;
        $rows = $this->runSql(
            "SELECT" .
            " COUNT(*) AS count," .
            " FLOOR(married.d_year/100+1) AS century" .
            " FROM" .
            " `##families` AS fam" .
            " JOIN" .
            " `##dates` AS married ON (married.d_file = fam.f_file AND married.d_gid = fam.f_id)" .
            " WHERE" .
            " f_numchil = 0 AND" .
            " fam.f_file = {$this->tree->id()} AND" .
            $years .
            " married.d_fact = 'MARR' AND" .
            " married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')" .
            " GROUP BY century ORDER BY century"
        );
        if (empty($rows)) {
            return '';
        }
        foreach ($rows as $values) {
            if ($max < $values->count) {
                $max = $values->count;
            }
            $tot += (int) $values->count;
        }
        $unknown = $this->noChildrenFamiliesQuery() - $tot;
        if ($unknown > $max) {
            $max = $unknown;
        }
        $chm    = '';
        $chxl   = '0:|';
        $i      = 0;
        $counts = [];
        foreach ($rows as $values) {
            $chxl     .= $this->centuryName($values->century) . '|';
            $counts[] = intdiv(4095 * $values->count, $max + 1);
            $chm      .= 't' . $values->count . ',000000,0,' . $i . ',11,1|';
            $i++;
        }
        $counts[] = intdiv(4095 * $unknown, $max + 1);
        $chd      = $this->arrayToExtendedEncoding($counts);
        $chm      .= 't' . $unknown . ',000000,0,' . $i . ',11,1';
        $chxl     .= I18N::translateContext('unknown century', 'Unknown') . '|1:||' . I18N::translate('century') . '|2:|0|';
        $step     = $max + 1;
        for ($d = (int) ($max + 1); $d > 0; $d--) {
            if (($max + 1) < ($d * 10 + 1) && fmod(($max + 1), $d) == 0) {
                $step = $d;
            }
        }
        if ($step == (int) ($max + 1)) {
            for ($d = (int) ($max); $d > 0; $d--) {
                if ($max < ($d * 10 + 1) && fmod($max, $d) == 0) {
                    $step = $d;
                }
            }
        }
        for ($n = $step; $n <= ($max + 1); $n += $step) {
            $chxl .= $n . '|';
        }
        $chxl .= '3:||' . I18N::translate('Total families') . '|';

        return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:" . ($i - 1) . ",3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Number of families without children') . '" title="' . I18N::translate('Number of families without children') . '" />';
    }

    /**
     * Find the couple with the most grandchildren.
     *
     * @param string $type
     * @param int    $total
     *
     * @return string
     */
    private function topTenGrandFamilyQuery(string $type, int $total): string
    {
        $rows = $this->runSql(
            "SELECT COUNT(*) AS tot, f_id AS id" .
            " FROM `##families`" .
            " JOIN `##link` AS children ON children.l_file = {$this->tree->id()}" .
            " JOIN `##link` AS mchildren ON mchildren.l_file = {$this->tree->id()}" .
            " JOIN `##link` AS gchildren ON gchildren.l_file = {$this->tree->id()}" .
            " WHERE" .
            " f_file={$this->tree->id()} AND" .
            " children.l_from=f_id AND" .
            " children.l_type='CHIL' AND" .
            " children.l_to=mchildren.l_from AND" .
            " mchildren.l_type='FAMS' AND" .
            " mchildren.l_to=gchildren.l_from AND" .
            " gchildren.l_type='CHIL'" .
            " GROUP BY id" .
            " ORDER BY tot DESC" .
            " LIMIT " . $total
        );
        if (!isset($rows[0])) {
            return '';
        }
        $top10 = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->id, $this->tree);
            if ($family->canShow()) {
                $total  = (int) $row->tot;

                if ($type === 'list') {
                    $top10[] = '<li><a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> - ' . I18N::plural('%s grandchild', '%s grandchildren', $total, I18N::number($total));
                } else {
                    $top10[] = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a> - ' . I18N::plural('%s grandchild', '%s grandchildren', $total, I18N::number($total));
                }
            }
        }
        if ($type === 'list') {
            $top10 = implode('', $top10);
        } else {
            $top10 = implode('; ', $top10);
        }
        if (I18N::direction() === 'rtl') {
            $top10 = str_replace([
                '[',
                ']',
                '(',
                ')',
                '+',
            ], [
                '&rlm;[',
                '&rlm;]',
                '&rlm;(',
                '&rlm;)',
                '&rlm;+',
            ], $top10);
        }
        if ($type === 'list') {
            return '<ul>' . $top10 . '</ul>';
        }

        return $top10;
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
        return $this->topTenGrandFamilyQuery('nolist', (int) $total);
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
        return $this->topTenGrandFamilyQuery('list', (int) $total);
    }

    /**
     * Find common surnames.
     *
     * @param string $type
     * @param bool   $show_tot
     * @param int    $threshold
     * @param int    $number_of_surnames
     * @param string $sorting
     *
     * @return string
     */
    private function commonSurnamesQuery($type, $show_tot, int $threshold, int $number_of_surnames, string $sorting): string
    {
        $surnames = $this->topSurnames($number_of_surnames, $threshold);

        switch ($sorting) {
            default:
            case 'alpha':
                uksort($surnames, [I18N::class, 'strcasecmp']);
                break;
            case 'count':
                break;
            case 'rcount':
                $surnames = array_reverse($surnames, true);
                break;
        }

        return FunctionsPrintLists::surnameList($surnames, ($type == 'list' ? 1 : 2), $show_tot, 'individual-list', $this->tree);
    }

    /**
     * @param int $number_of_surnames
     * @param int $threshold
     *
     * @return array
     */
    private function topSurnames(int $number_of_surnames, int $threshold): array
    {
        // Use the count of base surnames.
        $top_surnames = Database::prepare(
            "SELECT n_surn FROM `##name`" .
            " WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '')" .
            " GROUP BY n_surn" .
            " ORDER BY COUNT(n_surn) DESC" .
            " LIMIT :limit"
        )->execute([
            'tree_id' => $this->tree->id(),
            'limit'   => $number_of_surnames,
        ])->fetchOneColumn();

        $surnames = [];
        foreach ($top_surnames as $top_surname) {
            $variants = Database::prepare(
                "SELECT n_surname COLLATE utf8_bin, COUNT(*) FROM `##name` WHERE n_file = :tree_id AND n_surn COLLATE :collate = :surname GROUP BY 1"
            )->execute([
                'collate' => I18N::collation(),
                'surname' => $top_surname,
                'tree_id' => $this->tree->id(),
            ])->fetchAssoc();

            if (array_sum($variants) > $threshold) {
                $surnames[$top_surname] = $variants;
            }
        }

        return $surnames;
    }

    /**
     * Find common surnames.
     *
     * @return string
     */
    public function getCommonSurname(): string
    {
        $top_surname = $this->topSurnames(1, 0);

        return implode(', ', $top_surname[0] ?? []);
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
    public function commonSurnames(string $threshold = '1', string $number_of_surnames = '10', string $sorting = 'alpha'): string
    {
        return $this->commonSurnamesQuery('nolist', false, (int) $threshold, (int) $number_of_surnames, $sorting);
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
    public function commonSurnamesTotals(string $threshold = '1', string $number_of_surnames = '10', string $sorting = 'rcount'): string
    {
        return $this->commonSurnamesQuery('nolist', true, (int) $threshold, (int) $number_of_surnames, $sorting);
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
    public function commonSurnamesList(string $threshold = '1', string $number_of_surnames = '10', string $sorting = 'alpha'): string
    {
        return $this->commonSurnamesQuery('list', false, (int) $threshold, (int) $number_of_surnames, $sorting);
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
    public function commonSurnamesListTotals(string $threshold = '1', string $number_of_surnames = '10', string $sorting = 'rcount'): string
    {
        return $this->commonSurnamesQuery('list', true, (int) $threshold, (int) $number_of_surnames, $sorting);
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
    public function chartCommonSurnames(string $size = null, string $color_from = null, string $color_to = null, string $number_of_surnames = '10'): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size               = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from         = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to           = $color_to ?? $WT_STATS_CHART_COLOR2;
        $number_of_surnames = (int) $number_of_surnames;

        $sizes    = explode('x', $size);
        $tot_indi = $this->totalIndividualsQuery();

        $all_surnames = $this->topSurnames($number_of_surnames, 0);

        if (empty($all_surnames)) {
            return '';
        }

        $SURNAME_TRADITION = $this->tree->getPreference('SURNAME_TRADITION');

        $tot = 0;

        foreach ($all_surnames as $surn => $surnames) {
            $tot += array_sum($surnames);
        }

        $chd = '';
        $chl = [];
        foreach ($all_surnames as $surns) {
            $count_per = 0;
            $max_name  = 0;
            $top_name  = '';
            foreach ($surns as $spfxsurn => $count) {
                $per = $count;
                $count_per += $per;
                // select most common surname from all variants
                if ($per > $max_name) {
                    $max_name = $per;
                    $top_name = $spfxsurn;
                }
            }
            switch ($SURNAME_TRADITION) {
                case 'polish':
                    // most common surname should be in male variant (Kowalski, not Kowalska)
                    $top_name = preg_replace([
                        '/ska$/',
                        '/cka$/',
                        '/dzka$/',
                        '/żka$/',
                    ], [
                        'ski',
                        'cki',
                        'dzki',
                        'żki',
                    ], $top_name);
            }
            $per   = intdiv(100 * $count_per, $tot_indi);
            $chd .= $this->arrayToExtendedEncoding([$per]);
            $chl[] = $top_name . ' - ' . I18N::number($count_per);
        }
        $per   = intdiv(100 * ($tot_indi - $tot), $tot_indi);
        $chd .= $this->arrayToExtendedEncoding([$per]);
        $chl[] = I18N::translate('Other') . ' - ' . I18N::number($tot_indi - $tot);

        $chart_title = implode(I18N::$list_separator, $chl);
        $chl         = implode('|', $chl);

        return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:' . $chd . '&amp;chs=' . $size . '&amp;chco=' . $color_from . ',' . $color_to . '&amp;chf=bg,s,ffffff00&amp;chl=' . rawurlencode($chl) . '" width="' . $sizes[0] . '" height="' . $sizes[1] . '" alt="' . $chart_title . '" title="' . $chart_title . '" />';
    }

    /**
     * Find common given names.
     *
     * @param string $sex
     * @param string $type
     * @param bool   $show_tot
     * @param int    $threshold
     * @param int    $maxtoshow
     *
     * @return string|int[]
     */
    private function commonGivenQuery(string $sex, string $type, bool $show_tot, int $threshold, int $maxtoshow)
    {
        switch ($sex) {
            case 'M':
                $sex_sql = "i_sex='M'";
                break;
            case 'F':
                $sex_sql = "i_sex='F'";
                break;
            case 'U':
                $sex_sql = "i_sex='U'";
                break;
            case 'B':
            default:
                $sex_sql = "i_sex<>'U'";
                break;
        }
        $ged_id = $this->tree->id();

        $rows     = Database::prepare("SELECT n_givn, COUNT(*) AS num FROM `##name` JOIN `##individuals` ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type<>'_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn")
            ->fetchAll();
        $nameList = [];
        foreach ($rows as $row) {
            $row->num = (int) $row->num;

            // Split “John Thomas” into “John” and “Thomas” and count against both totals
            foreach (explode(' ', $row->n_givn) as $given) {
                // Exclude initials and particles.
                if (!preg_match('/^([A-Z]|[a-z]{1,3})$/', $given)) {
                    if (array_key_exists($given, $nameList)) {
                        $nameList[$given] += (int) $row->num;
                    } else {
                        $nameList[$given] = (int) $row->num;
                    }
                }
            }
        }
        arsort($nameList);
        $nameList = array_slice($nameList, 0, $maxtoshow);

        foreach ($nameList as $given => $total) {
            if ($total < $threshold) {
                unset($nameList[$given]);
            }
        }

        switch ($type) {
            case 'chart':
                return $nameList;

            case 'table':
                return view('lists/given-names-table', [
                    'given_names' => $nameList,
                ]);

            case 'list':
                return view('lists/given-names-list', [
                    'given_names' => $nameList,
                    'show_totals' => $show_tot,
                ]);

            case 'nolist':
            default:
                array_walk($nameList, function (int &$value, string $key) use ($show_tot): void {
                    if ($show_tot) {
                        $value = '<span dir="auto">' . e($key);
                    } else {
                        $value = '<span dir="auto">' . e($key) . ' (' . I18N::number($value) . ')';
                    }
                });

                return implode(I18N::$list_separator, $nameList);
        }
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
        return $this->commonGivenQuery('B', 'nolist', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('B', 'nolist', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('B', 'list', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('B', 'list', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('B', 'table', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('F', 'nolist', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('F', 'nolist', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('F', 'list', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('F', 'list', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('F', 'table', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('M', 'nolist', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('M', 'nolist', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('M', 'list', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('M', 'list', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('M', 'table', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('U', 'nolist', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('U', 'nolist', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('U', 'list', false, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('U', 'list', true, (int) $threshold, (int) $maxtoshow);
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
        return $this->commonGivenQuery('U', 'table', false, (int) $threshold, (int) $maxtoshow);
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
    public function chartCommonGiven(string $size = null, string $color_from = null, string $color_to = null, string $maxtoshow = '7'): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;
        $maxtoshow  = (int) $maxtoshow;

        $sizes    = explode('x', $size);
        $tot_indi = $this->totalIndividualsQuery();
        $given    = $this->commonGivenQuery('B', 'chart', false, 1, (int) $maxtoshow);

        if (empty($given)) {
            return '';
        }

        $tot = 0;
        foreach ($given as $count) {
            $tot += $count;
        }
        $chd = '';
        $chl = [];
        foreach ($given as $givn => $count) {
            if ($tot == 0) {
                $per = 0;
            } else {
                $per = intdiv(100 * $count, $tot_indi);
            }
            $chd .= $this->arrayToExtendedEncoding([$per]);
            $chl[] = $givn . ' - ' . I18N::number($count);
        }
        $per   = intdiv(100 * ($tot_indi - $tot), $tot_indi);
        $chd .= $this->arrayToExtendedEncoding([$per]);
        $chl[] = I18N::translate('Other') . ' - ' . I18N::number($tot_indi - $tot);

        $chart_title = implode(I18N::$list_separator, $chl);
        $chl         = implode('|', $chl);

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl=" . rawurlencode($chl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
    }

    /**
     * Who is currently logged in?
     *
     * @TODO - this is duplicated from the LoggedInUsersModule class.
     *
     * @param string $type
     *
     * @return string
     */
    private function usersLoggedInQuery($type = 'nolist'): string
    {
        $content = '';
        // List active users
        $NumAnonymous = 0;
        $loggedusers  = [];
        foreach (User::allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
                $loggedusers[] = $user;
            } else {
                $NumAnonymous++;
            }
        }
        $LoginUsers = count($loggedusers);
        if ($LoginUsers == 0 && $NumAnonymous == 0) {
            return I18N::translate('No signed-in and no anonymous users');
        }
        if ($NumAnonymous > 0) {
            $content .= '<b>' . I18N::plural('%s anonymous signed-in user', '%s anonymous signed-in users', $NumAnonymous, I18N::number($NumAnonymous)) . '</b>';
        }
        if ($LoginUsers > 0) {
            if ($NumAnonymous) {
                if ($type == 'list') {
                    $content .= '<br><br>';
                } else {
                    $content .= ' ' . I18N::translate('and') . ' ';
                }
            }
            $content .= '<b>' . I18N::plural('%s signed-in user', '%s signed-in users', $LoginUsers, I18N::number($LoginUsers)) . '</b>';
            if ($type == 'list') {
                $content .= '<ul>';
            } else {
                $content .= ': ';
            }
        }
        if (Auth::check()) {
            foreach ($loggedusers as $user) {
                if ($type == 'list') {
                    $content .= '<li>' . e($user->getRealName()) . ' - ' . e($user->getUserName());
                } else {
                    $content .= e($user->getRealName()) . ' - ' . e($user->getUserName());
                }
                if (Auth::id() !== $user->getUserId() && $user->getPreference('contactmethod') !== 'none') {
                    if ($type == 'list') {
                        $content .= '<br>';
                    }
                    $content .= '<a href="' . e(route('message', ['to'  => $user->getUserName(), 'ged' => $this->tree->name()])) . '" class="btn btn-link" title="' . I18N::translate('Send a message') . '">' . view('icons/email') . '</a>';
                }
                if ($type == 'list') {
                    $content .= '</li>';
                }
            }
        }
        if ($type == 'list') {
            $content .= '</ul>';
        }

        return $content;
    }

    /**
     * NUmber of users who are currently logged in?
     *
     * @param string $type
     *
     * @return int
     */
    private function usersLoggedInTotalQuery($type = 'all'): int
    {
        $anon    = 0;
        $visible = 0;
        foreach (User::allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
                $visible++;
            } else {
                $anon++;
            }
        }
        if ($type == 'anon') {
            return $anon;
        }

        if ($type == 'visible') {
            return $visible;
        }

        return $visible + $anon;
    }

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedIn(): string
    {
        return $this->usersLoggedInQuery('nolist');
    }

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedInList(): string
    {
        return $this->usersLoggedInQuery('list');
    }

    /**
     * Who is currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotal(): int
    {
        return $this->usersLoggedInTotalQuery('all');
    }

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalAnon(): int
    {
        return $this->usersLoggedInTotalQuery('anon');
    }

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalVisible(): int
    {
        return $this->usersLoggedInTotalQuery('visible');
    }

    /**
     * Get the current user's ID.
     *
     * @return string
     */
    public function userId(): string
    {
        return (string) Auth::id();
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
        if (Auth::check()) {
            return e(Auth::user()->getUserName());
        }

        // if #username:visitor# was specified, then "visitor" will be returned when the user is not logged in
        return e($visitor_text);
    }

    /**
     * Get the current user's full name.
     *
     * @return string
     */
    public function userFullName(): string
    {
        return Auth::check() ? '<span dir="auto">' . e(Auth::user()->getRealName()) . '</span>' : '';
    }

    /**
     * Find the newest user on the site.
     *
     * If no user has registered (i.e. all created by the admin), then
     * return the current user.
     *
     * @return User
     */
    private function latestUser(): User
    {
        static $user;

        if (!$user instanceof User) {
            $user_id = (int) Database::prepare(
                "SELECT u.user_id" .
                " FROM `##user` u" .
                " LEFT JOIN `##user_setting` us ON (u.user_id=us.user_id AND us.setting_name='reg_timestamp') " .
                " ORDER BY us.setting_value DESC LIMIT 1"
            )->execute()->fetchOne();

            $user = User::find($user_id) ?? Auth::user();
        }

        return $user;
    }

    /**
     * Get the newest registered user's ID.
     *
     * @return string
     */
    public function latestUserId(): string
    {
        return (string) $this->latestUser()->getUserId();
    }

    /**
     * Get the newest registered user's username.
     *
     * @return string
     */
    public function latestUserName(): string
    {
        return e($this->latestUser()->getUserName());
    }

    /**
     * Get the newest registered user's real name.
     *
     * @return string
     */
    public function latestUserFullName(): string
    {
        return e($this->latestUser()->getRealName());
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
        $format = $format ?? I18N::dateFormat();

        $user = $this->latestUser();

        return FunctionsDate::timestampToGedcomDate((int) $user->getPreference('reg_timestamp'))->display(false, $format);
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
        $format = $format ?? str_replace('%', '', I18N::timeFormat());

        $user = $this->latestUser();

        return date($format, (int) $user->getPreference('reg_timestamp'));
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
        $yes = $yes ?? I18N::translate('yes');
        $no  = $no ?? I18N::translate('no');

        $user = $this->latestUser();

        $is_logged_in = (bool) Database::prepare(
            "SELECT 1 FROM `##session` WHERE user_id = :user_id LIMIT 1"
        )->execute([
            'user_id' => $user->getUserId()
        ])->fetchOne();

        return $is_logged_in ? $yes : $no;
    }

    /**
     * Create a link to contact the webmaster.
     *
     * @return string
     */
    public function contactWebmaster()
    {
        $user_id = $this->tree->getPreference('WEBMASTER_USER_ID');
        $user    = User::find((int) $user_id);

        if ($user instanceof User) {
            return Theme::theme()->contactLink($user);
        }

        return $user_id;
    }

    /**
     * Create a link to contact the genealogy contact.
     *
     * @return string
     */
    public function contactGedcom()
    {
        $user_id = $this->tree->getPreference('CONTACT_USER_ID');
        $user    = User::find((int) $user_id);

        if ($user instanceof User) {
            return Theme::theme()->contactLink($user);
        }

        return $user_id;
    }

    /**
     * What is the current date on the server?
     *
     * @return string
     */
    public function serverDate(): string
    {
        return FunctionsDate::timestampToGedcomDate(WT_TIMESTAMP)->display();
    }

    /**
     * What is the current time on the server (in 12 hour clock)?
     *
     * @return string
     */
    public function serverTime(): string
    {
        return date('g:i a');
    }

    /**
     * What is the current time on the server (in 24 hour clock)?
     *
     * @return string
     */
    public function serverTime24(): string
    {
        return date('G:i');
    }

    /**
     * What is the timezone of the server.
     *
     * @return string
     */
    public function serverTimezone(): string
    {
        return date('T');
    }

    /**
     * What is the client's date.
     *
     * @return string
     */
    public function browserDate(): string
    {
        return FunctionsDate::timestampToGedcomDate(WT_TIMESTAMP + WT_TIMESTAMP_OFFSET)->display();
    }

    /**
     * What is the client's timestamp.
     *
     * @return string
     */
    public function browserTime(): string
    {
        return date(str_replace('%', '', I18N::timeFormat()), WT_TIMESTAMP + WT_TIMESTAMP_OFFSET);
    }

    /**
     * What is the browser's tiemzone.
     *
     * @return string
     */
    public function browserTimezone(): string
    {
        return date('T', WT_TIMESTAMP + WT_TIMESTAMP_OFFSET);
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
     * These functions provide access to hitcounter for use in the HTML block.
     *
     * @param string $page_name
     * @param string $page_parameter
     *
     * @return string
     */
    private function hitCountQuery($page_name, string $page_parameter = ''): string
    {
        if ($page_name === '') {
            // index.php?ctype=gedcom
            $page_name      = 'index.php';
            $page_parameter = 'gedcom:' . $this->tree->id();
        } elseif ($page_name == 'index.php') {
            // index.php?ctype=user
            $user           = User::findByIdentifier($page_parameter);
            $page_parameter = 'user:' . ($user ? $user->getUserId() : Auth::id());
        }

        $hit_counter = new PageHitCounter(Auth::user(), $this->tree);

        return '<span class="odometer">' . I18N::digits($hit_counter->getCount($this->tree, $page_name, $page_parameter)) . '</span>';
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
        return $this->hitCountQuery('', $page_parameter);
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
        return $this->hitCountQuery('index.php', $page_parameter);
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
        return $this->hitCountQuery('individual.php', $page_parameter);
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
        return $this->hitCountQuery('family.php', $page_parameter);
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
        return $this->hitCountQuery('source.php', $page_parameter);
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
        return $this->hitCountQuery('repo.php', $page_parameter);
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
        return $this->hitCountQuery('note.php', $page_parameter);
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
        return $this->hitCountQuery('mediaviewer.php', $page_parameter);
    }

    /**
     * Convert numbers to Google's custom encoding.
     *
     * @link http://bendodson.com/news/google-extended-encoding-made-easy
     *
     * @param int[] $a
     *
     * @return string
     */
    private function arrayToExtendedEncoding($a): string
    {
        $xencoding = self::GOOGLE_CHART_ENCODING;

        $encoding = '';
        foreach ($a as $value) {
            if ($value < 0) {
                $value = 0;
            }
            $first    = intdiv($value, 64);
            $second   = $value % 64;
            $encoding .= $xencoding[(int) $first] . $xencoding[(int) $second];
        }

        return $encoding;
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
        static $cache = [];

        $id = md5($sql);
        if (isset($cache[$id])) {
            return $cache[$id];
        }
        $rows       = Database::prepare($sql)->fetchAll();
        $cache[$id] = $rows;

        return $rows;
    }

    /**
     * Find the favorites for the tree.
     *
     * @return string
     */
    public function gedcomFavorites(): string
    {
        $module = Module::getModuleByName('gedcom_favorites');

        if ($module instanceof FamilyTreeFavoritesModule) {
            $block = new FamilyTreeFavoritesModule(Webtrees::MODULES_PATH . 'gedcom_favorites');

            return $block->getBlock($this->tree, 0, false);
        }

        return '';
    }

    /**
     * Find the favorites for the user.
     *
     * @return string
     */
    public function userFavorites(): string
    {
        if (Auth::check() && Module::getModuleByName('user_favorites')) {
            $block = new UserFavoritesModule(Webtrees::MODULES_PATH . 'gedcom_favorites');

            return $block->getBlock($this->tree, 0, false);
        }

        return '';
    }

    /**
     * Find the number of favorites for the tree.
     *
     * @return string
     */
    public function totalGedcomFavorites(): string
    {
        $count = 0;

        $module = Module::getModuleByName('gedcom_favorites');

        if ($module instanceof FamilyTreeFavoritesModule) {
            $count = count($module->getFavorites($this->tree));
        }

        return I18N::number($count);
    }

    /**
     * Find the number of favorites for the user.
     *
     * @return string
     */
    public function totalUserFavorites(): string
    {
        $count = 0;

        $module = Module::getModuleByName('user_favorites');

        if ($module instanceof UserFavoritesModule) {
            $count = count($module->getFavorites($this->tree, Auth::user()));
        }

        return I18N::number($count);
    }

    /**
     * Create any of the other blocks.
     * Use as #callBlock:block_name#
     *
     * @param string $block
     * @param string ...$params
     *
     * @return string
     */
    public function callBlock(string $block = '', ...$params): string
    {
        global $ctype;

        $all_blocks = [];
        foreach (Module::getActiveBlocks($this->tree) as $name => $active_block) {
            if ($ctype == 'user' && $active_block->isUserBlock() || $ctype == 'gedcom' && $active_block->isGedcomBlock()) {
                $all_blocks[$name] = $active_block;
            }
        }
        if (!array_key_exists($block, $all_blocks) || $block == 'html') {
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
        $block    = $all_blocks[$block];
        $content  = $block->getBlock($this->tree, 0, false, $cfg);

        return $content;
    }

    /**
     * How many messages in the user's inbox.
     *
     * @return string
     */
    public function totalUserMessages(): string
    {
        $total = (int) Database::prepare("SELECT COUNT(*) FROM `##message` WHERE user_id = ?")
            ->execute([Auth::id()])
            ->fetchOne();

        return I18N::number($total);
    }

    /**
     * How many blog entries exist for this user.
     *
     * @return string
     */
    public function totalUserJournal(): string
    {
        try {
            $number = (int) Database::prepare("SELECT COUNT(*) FROM `##news` WHERE user_id = ?")
                ->execute([Auth::id()])
                ->fetchOne();
        } catch (PDOException $ex) {
            // The module may not be installed, so the table may not exist.
            $number = 0;
        }

        return I18N::number($number);
    }

    /**
     * How many news items exist for this tree.
     *
     * @return string
     */
    public function totalGedcomNews(): string
    {
        try {
            $number = (int) Database::prepare("SELECT COUNT(*) FROM `##news` WHERE gedcom_id = ?")
                ->execute([$this->tree->id()])
                ->fetchOne();
        } catch (PDOException $ex) {
            // The module may not be installed, so the table may not exist.
            $number = 0;
        }

        return I18N::number($number);
    }

    /**
     * ISO3166 3 letter codes, with their 2 letter equivalent.
     * NOTE: this is not 1:1. ENG/SCO/WAL/NIR => GB
     * NOTE: this also includes champman codes and others. Should it?
     *
     * @return string[]
     */
    public function iso3166(): array
    {
        return [
            'ABW' => 'AW',
            'AFG' => 'AF',
            'AGO' => 'AO',
            'AIA' => 'AI',
            'ALA' => 'AX',
            'ALB' => 'AL',
            'AND' => 'AD',
            'ARE' => 'AE',
            'ARG' => 'AR',
            'ARM' => 'AM',
            'ASM' => 'AS',
            'ATA' => 'AQ',
            'ATF' => 'TF',
            'ATG' => 'AG',
            'AUS' => 'AU',
            'AUT' => 'AT',
            'AZE' => 'AZ',
            'BDI' => 'BI',
            'BEL' => 'BE',
            'BEN' => 'BJ',
            'BFA' => 'BF',
            'BGD' => 'BD',
            'BGR' => 'BG',
            'BHR' => 'BH',
            'BHS' => 'BS',
            'BIH' => 'BA',
            'BLR' => 'BY',
            'BLZ' => 'BZ',
            'BMU' => 'BM',
            'BOL' => 'BO',
            'BRA' => 'BR',
            'BRB' => 'BB',
            'BRN' => 'BN',
            'BTN' => 'BT',
            'BVT' => 'BV',
            'BWA' => 'BW',
            'CAF' => 'CF',
            'CAN' => 'CA',
            'CCK' => 'CC',
            'CHE' => 'CH',
            'CHL' => 'CL',
            'CHN' => 'CN',
            'CIV' => 'CI',
            'CMR' => 'CM',
            'COD' => 'CD',
            'COG' => 'CG',
            'COK' => 'CK',
            'COL' => 'CO',
            'COM' => 'KM',
            'CPV' => 'CV',
            'CRI' => 'CR',
            'CUB' => 'CU',
            'CXR' => 'CX',
            'CYM' => 'KY',
            'CYP' => 'CY',
            'CZE' => 'CZ',
            'DEU' => 'DE',
            'DJI' => 'DJ',
            'DMA' => 'DM',
            'DNK' => 'DK',
            'DOM' => 'DO',
            'DZA' => 'DZ',
            'ECU' => 'EC',
            'EGY' => 'EG',
            'ENG' => 'GB',
            'ERI' => 'ER',
            'ESH' => 'EH',
            'ESP' => 'ES',
            'EST' => 'EE',
            'ETH' => 'ET',
            'FIN' => 'FI',
            'FJI' => 'FJ',
            'FLK' => 'FK',
            'FRA' => 'FR',
            'FRO' => 'FO',
            'FSM' => 'FM',
            'GAB' => 'GA',
            'GBR' => 'GB',
            'GEO' => 'GE',
            'GHA' => 'GH',
            'GIB' => 'GI',
            'GIN' => 'GN',
            'GLP' => 'GP',
            'GMB' => 'GM',
            'GNB' => 'GW',
            'GNQ' => 'GQ',
            'GRC' => 'GR',
            'GRD' => 'GD',
            'GRL' => 'GL',
            'GTM' => 'GT',
            'GUF' => 'GF',
            'GUM' => 'GU',
            'GUY' => 'GY',
            'HKG' => 'HK',
            'HMD' => 'HM',
            'HND' => 'HN',
            'HRV' => 'HR',
            'HTI' => 'HT',
            'HUN' => 'HU',
            'IDN' => 'ID',
            'IND' => 'IN',
            'IOT' => 'IO',
            'IRL' => 'IE',
            'IRN' => 'IR',
            'IRQ' => 'IQ',
            'ISL' => 'IS',
            'ISR' => 'IL',
            'ITA' => 'IT',
            'JAM' => 'JM',
            'JOR' => 'JO',
            'JPN' => 'JA',
            'KAZ' => 'KZ',
            'KEN' => 'KE',
            'KGZ' => 'KG',
            'KHM' => 'KH',
            'KIR' => 'KI',
            'KNA' => 'KN',
            'KOR' => 'KO',
            'KWT' => 'KW',
            'LAO' => 'LA',
            'LBN' => 'LB',
            'LBR' => 'LR',
            'LBY' => 'LY',
            'LCA' => 'LC',
            'LIE' => 'LI',
            'LKA' => 'LK',
            'LSO' => 'LS',
            'LTU' => 'LT',
            'LUX' => 'LU',
            'LVA' => 'LV',
            'MAC' => 'MO',
            'MAR' => 'MA',
            'MCO' => 'MC',
            'MDA' => 'MD',
            'MDG' => 'MG',
            'MDV' => 'MV',
            'MEX' => 'MX',
            'MHL' => 'MH',
            'MKD' => 'MK',
            'MLI' => 'ML',
            'MLT' => 'MT',
            'MMR' => 'MM',
            'MNG' => 'MN',
            'MNP' => 'MP',
            'MNT' => 'ME',
            'MOZ' => 'MZ',
            'MRT' => 'MR',
            'MSR' => 'MS',
            'MTQ' => 'MQ',
            'MUS' => 'MU',
            'MWI' => 'MW',
            'MYS' => 'MY',
            'MYT' => 'YT',
            'NAM' => 'NA',
            'NCL' => 'NC',
            'NER' => 'NE',
            'NFK' => 'NF',
            'NGA' => 'NG',
            'NIC' => 'NI',
            'NIR' => 'GB',
            'NIU' => 'NU',
            'NLD' => 'NL',
            'NOR' => 'NO',
            'NPL' => 'NP',
            'NRU' => 'NR',
            'NZL' => 'NZ',
            'OMN' => 'OM',
            'PAK' => 'PK',
            'PAN' => 'PA',
            'PCN' => 'PN',
            'PER' => 'PE',
            'PHL' => 'PH',
            'PLW' => 'PW',
            'PNG' => 'PG',
            'POL' => 'PL',
            'PRI' => 'PR',
            'PRK' => 'KP',
            'PRT' => 'PO',
            'PRY' => 'PY',
            'PSE' => 'PS',
            'PYF' => 'PF',
            'QAT' => 'QA',
            'REU' => 'RE',
            'ROM' => 'RO',
            'RUS' => 'RU',
            'RWA' => 'RW',
            'SAU' => 'SA',
            'SCT' => 'GB',
            'SDN' => 'SD',
            'SEN' => 'SN',
            'SER' => 'RS',
            'SGP' => 'SG',
            'SGS' => 'GS',
            'SHN' => 'SH',
            'SJM' => 'SJ',
            'SLB' => 'SB',
            'SLE' => 'SL',
            'SLV' => 'SV',
            'SMR' => 'SM',
            'SOM' => 'SO',
            'SPM' => 'PM',
            'STP' => 'ST',
            'SUR' => 'SR',
            'SVK' => 'SK',
            'SVN' => 'SI',
            'SWE' => 'SE',
            'SWZ' => 'SZ',
            'SYC' => 'SC',
            'SYR' => 'SY',
            'TCA' => 'TC',
            'TCD' => 'TD',
            'TGO' => 'TG',
            'THA' => 'TH',
            'TJK' => 'TJ',
            'TKL' => 'TK',
            'TKM' => 'TM',
            'TLS' => 'TL',
            'TON' => 'TO',
            'TTO' => 'TT',
            'TUN' => 'TN',
            'TUR' => 'TR',
            'TUV' => 'TV',
            'TWN' => 'TW',
            'TZA' => 'TZ',
            'UGA' => 'UG',
            'UKR' => 'UA',
            'UMI' => 'UM',
            'URY' => 'UY',
            'USA' => 'US',
            'UZB' => 'UZ',
            'VAT' => 'VA',
            'VCT' => 'VC',
            'VEN' => 'VE',
            'VGB' => 'VG',
            'VIR' => 'VI',
            'VNM' => 'VN',
            'VUT' => 'VU',
            'WLF' => 'WF',
            'WLS' => 'GB',
            'WSM' => 'WS',
            'YEM' => 'YE',
            'ZAF' => 'ZA',
            'ZMB' => 'ZM',
            'ZWE' => 'ZW',
        ];
    }

    /**
     * Country codes and names
     *
     * @return string[]
     */
    public function getAllCountries(): array
    {
        return [
            /* I18N: Name of a country or state */
            '???' => I18N::translate('Unknown'),
            /* I18N: Name of a country or state */
            'ABW' => I18N::translate('Aruba'),
            /* I18N: Name of a country or state */
            'AFG' => I18N::translate('Afghanistan'),
            /* I18N: Name of a country or state */
            'AGO' => I18N::translate('Angola'),
            /* I18N: Name of a country or state */
            'AIA' => I18N::translate('Anguilla'),
            /* I18N: Name of a country or state */
            'ALA' => I18N::translate('Aland Islands'),
            /* I18N: Name of a country or state */
            'ALB' => I18N::translate('Albania'),
            /* I18N: Name of a country or state */
            'AND' => I18N::translate('Andorra'),
            /* I18N: Name of a country or state */
            'ARE' => I18N::translate('United Arab Emirates'),
            /* I18N: Name of a country or state */
            'ARG' => I18N::translate('Argentina'),
            /* I18N: Name of a country or state */
            'ARM' => I18N::translate('Armenia'),
            /* I18N: Name of a country or state */
            'ASM' => I18N::translate('American Samoa'),
            /* I18N: Name of a country or state */
            'ATA' => I18N::translate('Antarctica'),
            /* I18N: Name of a country or state */
            'ATF' => I18N::translate('French Southern Territories'),
            /* I18N: Name of a country or state */
            'ATG' => I18N::translate('Antigua and Barbuda'),
            /* I18N: Name of a country or state */
            'AUS' => I18N::translate('Australia'),
            /* I18N: Name of a country or state */
            'AUT' => I18N::translate('Austria'),
            /* I18N: Name of a country or state */
            'AZE' => I18N::translate('Azerbaijan'),
            /* I18N: Name of a country or state */
            'AZR' => I18N::translate('Azores'),
            /* I18N: Name of a country or state */
            'BDI' => I18N::translate('Burundi'),
            /* I18N: Name of a country or state */
            'BEL' => I18N::translate('Belgium'),
            /* I18N: Name of a country or state */
            'BEN' => I18N::translate('Benin'),
            // BES => Bonaire, Sint Eustatius and Saba
            /* I18N: Name of a country or state */
            'BFA' => I18N::translate('Burkina Faso'),
            /* I18N: Name of a country or state */
            'BGD' => I18N::translate('Bangladesh'),
            /* I18N: Name of a country or state */
            'BGR' => I18N::translate('Bulgaria'),
            /* I18N: Name of a country or state */
            'BHR' => I18N::translate('Bahrain'),
            /* I18N: Name of a country or state */
            'BHS' => I18N::translate('Bahamas'),
            /* I18N: Name of a country or state */
            'BIH' => I18N::translate('Bosnia and Herzegovina'),
            // BLM => Saint Barthélemy
            /* I18N: Name of a country or state */
            'BLR' => I18N::translate('Belarus'),
            /* I18N: Name of a country or state */
            'BLZ' => I18N::translate('Belize'),
            /* I18N: Name of a country or state */
            'BMU' => I18N::translate('Bermuda'),
            /* I18N: Name of a country or state */
            'BOL' => I18N::translate('Bolivia'),
            /* I18N: Name of a country or state */
            'BRA' => I18N::translate('Brazil'),
            /* I18N: Name of a country or state */
            'BRB' => I18N::translate('Barbados'),
            /* I18N: Name of a country or state */
            'BRN' => I18N::translate('Brunei Darussalam'),
            /* I18N: Name of a country or state */
            'BTN' => I18N::translate('Bhutan'),
            /* I18N: Name of a country or state */
            'BVT' => I18N::translate('Bouvet Island'),
            /* I18N: Name of a country or state */
            'BWA' => I18N::translate('Botswana'),
            /* I18N: Name of a country or state */
            'CAF' => I18N::translate('Central African Republic'),
            /* I18N: Name of a country or state */
            'CAN' => I18N::translate('Canada'),
            /* I18N: Name of a country or state */
            'CCK' => I18N::translate('Cocos (Keeling) Islands'),
            /* I18N: Name of a country or state */
            'CHE' => I18N::translate('Switzerland'),
            /* I18N: Name of a country or state */
            'CHL' => I18N::translate('Chile'),
            /* I18N: Name of a country or state */
            'CHN' => I18N::translate('China'),
            /* I18N: Name of a country or state */
            'CIV' => I18N::translate('Cote d’Ivoire'),
            /* I18N: Name of a country or state */
            'CMR' => I18N::translate('Cameroon'),
            /* I18N: Name of a country or state */
            'COD' => I18N::translate('Democratic Republic of the Congo'),
            /* I18N: Name of a country or state */
            'COG' => I18N::translate('Republic of the Congo'),
            /* I18N: Name of a country or state */
            'COK' => I18N::translate('Cook Islands'),
            /* I18N: Name of a country or state */
            'COL' => I18N::translate('Colombia'),
            /* I18N: Name of a country or state */
            'COM' => I18N::translate('Comoros'),
            /* I18N: Name of a country or state */
            'CPV' => I18N::translate('Cape Verde'),
            /* I18N: Name of a country or state */
            'CRI' => I18N::translate('Costa Rica'),
            /* I18N: Name of a country or state */
            'CUB' => I18N::translate('Cuba'),
            // CUW => Curaçao
            /* I18N: Name of a country or state */
            'CXR' => I18N::translate('Christmas Island'),
            /* I18N: Name of a country or state */
            'CYM' => I18N::translate('Cayman Islands'),
            /* I18N: Name of a country or state */
            'CYP' => I18N::translate('Cyprus'),
            /* I18N: Name of a country or state */
            'CZE' => I18N::translate('Czech Republic'),
            /* I18N: Name of a country or state */
            'DEU' => I18N::translate('Germany'),
            /* I18N: Name of a country or state */
            'DJI' => I18N::translate('Djibouti'),
            /* I18N: Name of a country or state */
            'DMA' => I18N::translate('Dominica'),
            /* I18N: Name of a country or state */
            'DNK' => I18N::translate('Denmark'),
            /* I18N: Name of a country or state */
            'DOM' => I18N::translate('Dominican Republic'),
            /* I18N: Name of a country or state */
            'DZA' => I18N::translate('Algeria'),
            /* I18N: Name of a country or state */
            'ECU' => I18N::translate('Ecuador'),
            /* I18N: Name of a country or state */
            'EGY' => I18N::translate('Egypt'),
            /* I18N: Name of a country or state */
            'ENG' => I18N::translate('England'),
            /* I18N: Name of a country or state */
            'ERI' => I18N::translate('Eritrea'),
            /* I18N: Name of a country or state */
            'ESH' => I18N::translate('Western Sahara'),
            /* I18N: Name of a country or state */
            'ESP' => I18N::translate('Spain'),
            /* I18N: Name of a country or state */
            'EST' => I18N::translate('Estonia'),
            /* I18N: Name of a country or state */
            'ETH' => I18N::translate('Ethiopia'),
            /* I18N: Name of a country or state */
            'FIN' => I18N::translate('Finland'),
            /* I18N: Name of a country or state */
            'FJI' => I18N::translate('Fiji'),
            /* I18N: Name of a country or state */
            'FLD' => I18N::translate('Flanders'),
            /* I18N: Name of a country or state */
            'FLK' => I18N::translate('Falkland Islands'),
            /* I18N: Name of a country or state */
            'FRA' => I18N::translate('France'),
            /* I18N: Name of a country or state */
            'FRO' => I18N::translate('Faroe Islands'),
            /* I18N: Name of a country or state */
            'FSM' => I18N::translate('Micronesia'),
            /* I18N: Name of a country or state */
            'GAB' => I18N::translate('Gabon'),
            /* I18N: Name of a country or state */
            'GBR' => I18N::translate('United Kingdom'),
            /* I18N: Name of a country or state */
            'GEO' => I18N::translate('Georgia'),
            /* I18N: Name of a country or state */
            'GGY' => I18N::translate('Guernsey'),
            /* I18N: Name of a country or state */
            'GHA' => I18N::translate('Ghana'),
            /* I18N: Name of a country or state */
            'GIB' => I18N::translate('Gibraltar'),
            /* I18N: Name of a country or state */
            'GIN' => I18N::translate('Guinea'),
            /* I18N: Name of a country or state */
            'GLP' => I18N::translate('Guadeloupe'),
            /* I18N: Name of a country or state */
            'GMB' => I18N::translate('Gambia'),
            /* I18N: Name of a country or state */
            'GNB' => I18N::translate('Guinea-Bissau'),
            /* I18N: Name of a country or state */
            'GNQ' => I18N::translate('Equatorial Guinea'),
            /* I18N: Name of a country or state */
            'GRC' => I18N::translate('Greece'),
            /* I18N: Name of a country or state */
            'GRD' => I18N::translate('Grenada'),
            /* I18N: Name of a country or state */
            'GRL' => I18N::translate('Greenland'),
            /* I18N: Name of a country or state */
            'GTM' => I18N::translate('Guatemala'),
            /* I18N: Name of a country or state */
            'GUF' => I18N::translate('French Guiana'),
            /* I18N: Name of a country or state */
            'GUM' => I18N::translate('Guam'),
            /* I18N: Name of a country or state */
            'GUY' => I18N::translate('Guyana'),
            /* I18N: Name of a country or state */
            'HKG' => I18N::translate('Hong Kong'),
            /* I18N: Name of a country or state */
            'HMD' => I18N::translate('Heard Island and McDonald Islands'),
            /* I18N: Name of a country or state */
            'HND' => I18N::translate('Honduras'),
            /* I18N: Name of a country or state */
            'HRV' => I18N::translate('Croatia'),
            /* I18N: Name of a country or state */
            'HTI' => I18N::translate('Haiti'),
            /* I18N: Name of a country or state */
            'HUN' => I18N::translate('Hungary'),
            /* I18N: Name of a country or state */
            'IDN' => I18N::translate('Indonesia'),
            /* I18N: Name of a country or state */
            'IND' => I18N::translate('India'),
            /* I18N: Name of a country or state */
            'IOM' => I18N::translate('Isle of Man'),
            /* I18N: Name of a country or state */
            'IOT' => I18N::translate('British Indian Ocean Territory'),
            /* I18N: Name of a country or state */
            'IRL' => I18N::translate('Ireland'),
            /* I18N: Name of a country or state */
            'IRN' => I18N::translate('Iran'),
            /* I18N: Name of a country or state */
            'IRQ' => I18N::translate('Iraq'),
            /* I18N: Name of a country or state */
            'ISL' => I18N::translate('Iceland'),
            /* I18N: Name of a country or state */
            'ISR' => I18N::translate('Israel'),
            /* I18N: Name of a country or state */
            'ITA' => I18N::translate('Italy'),
            /* I18N: Name of a country or state */
            'JAM' => I18N::translate('Jamaica'),
            //'JEY' => Jersey
            /* I18N: Name of a country or state */
            'JOR' => I18N::translate('Jordan'),
            /* I18N: Name of a country or state */
            'JPN' => I18N::translate('Japan'),
            /* I18N: Name of a country or state */
            'KAZ' => I18N::translate('Kazakhstan'),
            /* I18N: Name of a country or state */
            'KEN' => I18N::translate('Kenya'),
            /* I18N: Name of a country or state */
            'KGZ' => I18N::translate('Kyrgyzstan'),
            /* I18N: Name of a country or state */
            'KHM' => I18N::translate('Cambodia'),
            /* I18N: Name of a country or state */
            'KIR' => I18N::translate('Kiribati'),
            /* I18N: Name of a country or state */
            'KNA' => I18N::translate('Saint Kitts and Nevis'),
            /* I18N: Name of a country or state */
            'KOR' => I18N::translate('Korea'),
            /* I18N: Name of a country or state */
            'KWT' => I18N::translate('Kuwait'),
            /* I18N: Name of a country or state */
            'LAO' => I18N::translate('Laos'),
            /* I18N: Name of a country or state */
            'LBN' => I18N::translate('Lebanon'),
            /* I18N: Name of a country or state */
            'LBR' => I18N::translate('Liberia'),
            /* I18N: Name of a country or state */
            'LBY' => I18N::translate('Libya'),
            /* I18N: Name of a country or state */
            'LCA' => I18N::translate('Saint Lucia'),
            /* I18N: Name of a country or state */
            'LIE' => I18N::translate('Liechtenstein'),
            /* I18N: Name of a country or state */
            'LKA' => I18N::translate('Sri Lanka'),
            /* I18N: Name of a country or state */
            'LSO' => I18N::translate('Lesotho'),
            /* I18N: Name of a country or state */
            'LTU' => I18N::translate('Lithuania'),
            /* I18N: Name of a country or state */
            'LUX' => I18N::translate('Luxembourg'),
            /* I18N: Name of a country or state */
            'LVA' => I18N::translate('Latvia'),
            /* I18N: Name of a country or state */
            'MAC' => I18N::translate('Macau'),
            // MAF => Saint Martin
            /* I18N: Name of a country or state */
            'MAR' => I18N::translate('Morocco'),
            /* I18N: Name of a country or state */
            'MCO' => I18N::translate('Monaco'),
            /* I18N: Name of a country or state */
            'MDA' => I18N::translate('Moldova'),
            /* I18N: Name of a country or state */
            'MDG' => I18N::translate('Madagascar'),
            /* I18N: Name of a country or state */
            'MDV' => I18N::translate('Maldives'),
            /* I18N: Name of a country or state */
            'MEX' => I18N::translate('Mexico'),
            /* I18N: Name of a country or state */
            'MHL' => I18N::translate('Marshall Islands'),
            /* I18N: Name of a country or state */
            'MKD' => I18N::translate('Macedonia'),
            /* I18N: Name of a country or state */
            'MLI' => I18N::translate('Mali'),
            /* I18N: Name of a country or state */
            'MLT' => I18N::translate('Malta'),
            /* I18N: Name of a country or state */
            'MMR' => I18N::translate('Myanmar'),
            /* I18N: Name of a country or state */
            'MNG' => I18N::translate('Mongolia'),
            /* I18N: Name of a country or state */
            'MNP' => I18N::translate('Northern Mariana Islands'),
            /* I18N: Name of a country or state */
            'MNT' => I18N::translate('Montenegro'),
            /* I18N: Name of a country or state */
            'MOZ' => I18N::translate('Mozambique'),
            /* I18N: Name of a country or state */
            'MRT' => I18N::translate('Mauritania'),
            /* I18N: Name of a country or state */
            'MSR' => I18N::translate('Montserrat'),
            /* I18N: Name of a country or state */
            'MTQ' => I18N::translate('Martinique'),
            /* I18N: Name of a country or state */
            'MUS' => I18N::translate('Mauritius'),
            /* I18N: Name of a country or state */
            'MWI' => I18N::translate('Malawi'),
            /* I18N: Name of a country or state */
            'MYS' => I18N::translate('Malaysia'),
            /* I18N: Name of a country or state */
            'MYT' => I18N::translate('Mayotte'),
            /* I18N: Name of a country or state */
            'NAM' => I18N::translate('Namibia'),
            /* I18N: Name of a country or state */
            'NCL' => I18N::translate('New Caledonia'),
            /* I18N: Name of a country or state */
            'NER' => I18N::translate('Niger'),
            /* I18N: Name of a country or state */
            'NFK' => I18N::translate('Norfolk Island'),
            /* I18N: Name of a country or state */
            'NGA' => I18N::translate('Nigeria'),
            /* I18N: Name of a country or state */
            'NIC' => I18N::translate('Nicaragua'),
            /* I18N: Name of a country or state */
            'NIR' => I18N::translate('Northern Ireland'),
            /* I18N: Name of a country or state */
            'NIU' => I18N::translate('Niue'),
            /* I18N: Name of a country or state */
            'NLD' => I18N::translate('Netherlands'),
            /* I18N: Name of a country or state */
            'NOR' => I18N::translate('Norway'),
            /* I18N: Name of a country or state */
            'NPL' => I18N::translate('Nepal'),
            /* I18N: Name of a country or state */
            'NRU' => I18N::translate('Nauru'),
            /* I18N: Name of a country or state */
            'NZL' => I18N::translate('New Zealand'),
            /* I18N: Name of a country or state */
            'OMN' => I18N::translate('Oman'),
            /* I18N: Name of a country or state */
            'PAK' => I18N::translate('Pakistan'),
            /* I18N: Name of a country or state */
            'PAN' => I18N::translate('Panama'),
            /* I18N: Name of a country or state */
            'PCN' => I18N::translate('Pitcairn'),
            /* I18N: Name of a country or state */
            'PER' => I18N::translate('Peru'),
            /* I18N: Name of a country or state */
            'PHL' => I18N::translate('Philippines'),
            /* I18N: Name of a country or state */
            'PLW' => I18N::translate('Palau'),
            /* I18N: Name of a country or state */
            'PNG' => I18N::translate('Papua New Guinea'),
            /* I18N: Name of a country or state */
            'POL' => I18N::translate('Poland'),
            /* I18N: Name of a country or state */
            'PRI' => I18N::translate('Puerto Rico'),
            /* I18N: Name of a country or state */
            'PRK' => I18N::translate('North Korea'),
            /* I18N: Name of a country or state */
            'PRT' => I18N::translate('Portugal'),
            /* I18N: Name of a country or state */
            'PRY' => I18N::translate('Paraguay'),
            /* I18N: Name of a country or state */
            'PSE' => I18N::translate('Occupied Palestinian Territory'),
            /* I18N: Name of a country or state */
            'PYF' => I18N::translate('French Polynesia'),
            /* I18N: Name of a country or state */
            'QAT' => I18N::translate('Qatar'),
            /* I18N: Name of a country or state */
            'REU' => I18N::translate('Reunion'),
            /* I18N: Name of a country or state */
            'ROM' => I18N::translate('Romania'),
            /* I18N: Name of a country or state */
            'RUS' => I18N::translate('Russia'),
            /* I18N: Name of a country or state */
            'RWA' => I18N::translate('Rwanda'),
            /* I18N: Name of a country or state */
            'SAU' => I18N::translate('Saudi Arabia'),
            /* I18N: Name of a country or state */
            'SCT' => I18N::translate('Scotland'),
            /* I18N: Name of a country or state */
            'SDN' => I18N::translate('Sudan'),
            /* I18N: Name of a country or state */
            'SEA' => I18N::translate('At sea'),
            /* I18N: Name of a country or state */
            'SEN' => I18N::translate('Senegal'),
            /* I18N: Name of a country or state */
            'SER' => I18N::translate('Serbia'),
            /* I18N: Name of a country or state */
            'SGP' => I18N::translate('Singapore'),
            /* I18N: Name of a country or state */
            'SGS' => I18N::translate('South Georgia and the South Sandwich Islands'),
            /* I18N: Name of a country or state */
            'SHN' => I18N::translate('Saint Helena'),
            /* I18N: Name of a country or state */
            'SJM' => I18N::translate('Svalbard and Jan Mayen'),
            /* I18N: Name of a country or state */
            'SLB' => I18N::translate('Solomon Islands'),
            /* I18N: Name of a country or state */
            'SLE' => I18N::translate('Sierra Leone'),
            /* I18N: Name of a country or state */
            'SLV' => I18N::translate('El Salvador'),
            /* I18N: Name of a country or state */
            'SMR' => I18N::translate('San Marino'),
            /* I18N: Name of a country or state */
            'SOM' => I18N::translate('Somalia'),
            /* I18N: Name of a country or state */
            'SPM' => I18N::translate('Saint Pierre and Miquelon'),
            /* I18N: Name of a country or state */
            'SSD' => I18N::translate('South Sudan'),
            /* I18N: Name of a country or state */
            'STP' => I18N::translate('Sao Tome and Principe'),
            /* I18N: Name of a country or state */
            'SUR' => I18N::translate('Suriname'),
            /* I18N: Name of a country or state */
            'SVK' => I18N::translate('Slovakia'),
            /* I18N: Name of a country or state */
            'SVN' => I18N::translate('Slovenia'),
            /* I18N: Name of a country or state */
            'SWE' => I18N::translate('Sweden'),
            /* I18N: Name of a country or state */
            'SWZ' => I18N::translate('Swaziland'),
            // SXM => Sint Maarten
            /* I18N: Name of a country or state */
            'SYC' => I18N::translate('Seychelles'),
            /* I18N: Name of a country or state */
            'SYR' => I18N::translate('Syria'),
            /* I18N: Name of a country or state */
            'TCA' => I18N::translate('Turks and Caicos Islands'),
            /* I18N: Name of a country or state */
            'TCD' => I18N::translate('Chad'),
            /* I18N: Name of a country or state */
            'TGO' => I18N::translate('Togo'),
            /* I18N: Name of a country or state */
            'THA' => I18N::translate('Thailand'),
            /* I18N: Name of a country or state */
            'TJK' => I18N::translate('Tajikistan'),
            /* I18N: Name of a country or state */
            'TKL' => I18N::translate('Tokelau'),
            /* I18N: Name of a country or state */
            'TKM' => I18N::translate('Turkmenistan'),
            /* I18N: Name of a country or state */
            'TLS' => I18N::translate('Timor-Leste'),
            /* I18N: Name of a country or state */
            'TON' => I18N::translate('Tonga'),
            /* I18N: Name of a country or state */
            'TTO' => I18N::translate('Trinidad and Tobago'),
            /* I18N: Name of a country or state */
            'TUN' => I18N::translate('Tunisia'),
            /* I18N: Name of a country or state */
            'TUR' => I18N::translate('Turkey'),
            /* I18N: Name of a country or state */
            'TUV' => I18N::translate('Tuvalu'),
            /* I18N: Name of a country or state */
            'TWN' => I18N::translate('Taiwan'),
            /* I18N: Name of a country or state */
            'TZA' => I18N::translate('Tanzania'),
            /* I18N: Name of a country or state */
            'UGA' => I18N::translate('Uganda'),
            /* I18N: Name of a country or state */
            'UKR' => I18N::translate('Ukraine'),
            /* I18N: Name of a country or state */
            'UMI' => I18N::translate('US Minor Outlying Islands'),
            /* I18N: Name of a country or state */
            'URY' => I18N::translate('Uruguay'),
            /* I18N: Name of a country or state */
            'USA' => I18N::translate('United States'),
            /* I18N: Name of a country or state */
            'UZB' => I18N::translate('Uzbekistan'),
            /* I18N: Name of a country or state */
            'VAT' => I18N::translate('Vatican City'),
            /* I18N: Name of a country or state */
            'VCT' => I18N::translate('Saint Vincent and the Grenadines'),
            /* I18N: Name of a country or state */
            'VEN' => I18N::translate('Venezuela'),
            /* I18N: Name of a country or state */
            'VGB' => I18N::translate('British Virgin Islands'),
            /* I18N: Name of a country or state */
            'VIR' => I18N::translate('US Virgin Islands'),
            /* I18N: Name of a country or state */
            'VNM' => I18N::translate('Vietnam'),
            /* I18N: Name of a country or state */
            'VUT' => I18N::translate('Vanuatu'),
            /* I18N: Name of a country or state */
            'WLF' => I18N::translate('Wallis and Futuna'),
            /* I18N: Name of a country or state */
            'WLS' => I18N::translate('Wales'),
            /* I18N: Name of a country or state */
            'WSM' => I18N::translate('Samoa'),
            /* I18N: Name of a country or state */
            'YEM' => I18N::translate('Yemen'),
            /* I18N: Name of a country or state */
            'ZAF' => I18N::translate('South Africa'),
            /* I18N: Name of a country or state */
            'ZMB' => I18N::translate('Zambia'),
            /* I18N: Name of a country or state */
            'ZWE' => I18N::translate('Zimbabwe'),
        ];
    }

    /**
     * Century name, English => 21st, Polish => XXI, etc.
     *
     * @param int $century
     *
     * @return string
     */
    private function centuryName($century): string
    {
        if ($century < 0) {
            return I18N::translate('%s BCE', $this->centuryName(-$century));
        }

        // The current chart engine (Google charts) can't handle <sup></sup> markup
        switch ($century) {
            case 21:
                return strip_tags(I18N::translateContext('CENTURY', '21st'));
            case 20:
                return strip_tags(I18N::translateContext('CENTURY', '20th'));
            case 19:
                return strip_tags(I18N::translateContext('CENTURY', '19th'));
            case 18:
                return strip_tags(I18N::translateContext('CENTURY', '18th'));
            case 17:
                return strip_tags(I18N::translateContext('CENTURY', '17th'));
            case 16:
                return strip_tags(I18N::translateContext('CENTURY', '16th'));
            case 15:
                return strip_tags(I18N::translateContext('CENTURY', '15th'));
            case 14:
                return strip_tags(I18N::translateContext('CENTURY', '14th'));
            case 13:
                return strip_tags(I18N::translateContext('CENTURY', '13th'));
            case 12:
                return strip_tags(I18N::translateContext('CENTURY', '12th'));
            case 11:
                return strip_tags(I18N::translateContext('CENTURY', '11th'));
            case 10:
                return strip_tags(I18N::translateContext('CENTURY', '10th'));
            case 9:
                return strip_tags(I18N::translateContext('CENTURY', '9th'));
            case 8:
                return strip_tags(I18N::translateContext('CENTURY', '8th'));
            case 7:
                return strip_tags(I18N::translateContext('CENTURY', '7th'));
            case 6:
                return strip_tags(I18N::translateContext('CENTURY', '6th'));
            case 5:
                return strip_tags(I18N::translateContext('CENTURY', '5th'));
            case 4:
                return strip_tags(I18N::translateContext('CENTURY', '4th'));
            case 3:
                return strip_tags(I18N::translateContext('CENTURY', '3rd'));
            case 2:
                return strip_tags(I18N::translateContext('CENTURY', '2nd'));
            case 1:
                return strip_tags(I18N::translateContext('CENTURY', '1st'));
            default:
                return ($century - 1) . '01-' . $century . '00';
        }
    }
}
