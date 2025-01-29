<?php

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Capsule\Manager as DB;

use function strip_tags;

/**
 * Generate raw data for statistics
 */
class StatisticsService
{
    private Tree $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function countAllRecords(): int
    {
        return
            $this->countIndividuals() +
            $this->countFamilies() +
            $this->countMedia() +
            $this->countNotes() +
            $this->countRepositories() +
            $this->countSources();
    }

    /**
     * @param array<string> $events
     */
    public function countEvents(array $events): int
    {
        return DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->whereIn('d_fact', $events)
            ->count();
    }

    /**
     * @return array<int,array{0:string,1:int}>
     */
    public function countEventsByCentury(string $event): array
    {
        return DB::table('dates')
            ->select([new Expression('ROUND((d_year + 49) / 100, 0) AS century'), new Expression('COUNT(*) AS total')])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_year', '<>', 0)
            ->where('d_fact', '=', $event)
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(fn (object $row): array => [$this->centuryName((int) $row->century), (int) $row->total])
            ->all();
    }

    public function countFamilies(): int
    {
        return DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->count();
    }

    public function countIndividuals(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->count();
    }

    public function countIndividualsBySex(string $sex): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_sex', '=', $sex)
            ->count();
    }

    public function countMedia(): int
    {
        return DB::table('media')
            ->where('m_file', '=', $this->tree->id())
            ->count();
    }

    public function countNotes(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'NOTE')
            ->count();
    }

    /**
     * @param array<string> $events
     */
    public function countOtherEvents(array $events): int
    {
        return DB::table('dates')
            ->where('d_file', '=', $this->tree->id())
            ->whereNotIn('d_fact', $events)
            ->count();
    }

    public function countRepositories(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'REPO')
            ->count();
    }

    public function countSources(): int
    {
        return DB::table('sources')
            ->where('s_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Century name, English => 21st, Polish => XXI, etc.
     */
    private function centuryName(int $century): string
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
