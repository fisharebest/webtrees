<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Elements\DateValueToday;
use Fisharebest\Webtrees\Elements\ResearchTask;
use Fisharebest\Webtrees\Elements\WebtreesUser;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ResearchTaskModule
 */
class ResearchTaskModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private const DEFAULT_SHOW_OTHER      = '1';
    private const DEFAULT_SHOW_UNASSIGNED = '1';
    private const DEFAULT_SHOW_FUTURE     = '1';

    // 31 DEC 9999
    private const MAXIMUM_JULIAN_DAY = 5373484;

    // Pagination
    private const LIMIT_LOW  = 10;
    private const LIMIT_HIGH = 20;

    /**
     * Early initialisation.  Called before most of the middleware.
     */
    public function boot(): void
    {
        Registry::elementFactory()->registerTags([
            'FAM:_TODO'           => new ResearchTask(I18N::translate('Research task')),
            'FAM:_TODO:DATE'      => new DateValueToday(I18N::translate('Date')),
            'FAM:_TODO:_WT_USER'  => new WebtreesUser(I18N::translate('User')),
            'INDI:_TODO'          => new ResearchTask(I18N::translate('Research task')),
            'INDI:_TODO:DATE'     => new DateValueToday(I18N::translate('Date')),
            'INDI:_TODO:_WT_USER' => new WebtreesUser(I18N::translate('User')),
        ]);

        Registry::elementFactory()->make('FAM')->subtag('_TODO', '0:M');
        Registry::elementFactory()->make('INDI')->subtag('_TODO', '0:M');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of “Research tasks” module */
        return I18N::translate('A list of tasks and activities that are linked to the family tree.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
        $show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
        $show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

        extract($config, EXTR_OVERWRITE);

        $end_jd      = $show_future ? self::MAXIMUM_JULIAN_DAY : Registry::timestampFactory()->now()->julianDay();
        $individuals = $this->individualsWithTasks($tree, $end_jd);
        $families    = $this->familiesWithTasks($tree, $end_jd);

        $records = $individuals->merge($families);

        $tasks = new Collection();

        foreach ($records as $record) {
            foreach ($record->facts(['_TODO']) as $task) {
                $user_name = $task->attribute('_WT_USER');

                if ($user_name === Auth::user()->userName()) {
                    // Tasks belonging to us.
                    $tasks->add($task);
                } elseif ($user_name === '' && $show_unassigned) {
                    // Tasks belonging to nobody.
                    $tasks->add($task);
                } elseif ($user_name !== '' && $show_other) {
                    // Tasks belonging to others.
                    $tasks->add($task);
                }
            }
        }

        if ($records->isEmpty()) {
            $content = '<p>' . I18N::translate('There are no research tasks in this family tree.') . '</p>';
        } else {
            $content = view('modules/todo/research-tasks', [
                'limit_low'  => self::LIMIT_LOW,
                'limit_high' => self::LIMIT_HIGH,
                'tasks'      => $tasks,
            ]);
        }

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
                'title'      => $this->title(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * @param Tree $tree
     * @param int  $max_julian_day
     *
     * @return Collection<int,Individual>
     */
    private function individualsWithTasks(Tree $tree, int $max_julian_day): Collection
    {
        return DB::table('individuals')
            ->join('dates', static function (JoinClause $join): void {
                $join
                    ->on('i_file', '=', 'd_file')
                    ->on('i_id', '=', 'd_gid');
            })
            ->where('i_file', '=', $tree->id())
            ->where('d_fact', '=', '_TODO')
            ->where('d_julianday1', '<', $max_julian_day)
            ->select(['individuals.*'])
            ->distinct()
            ->get()
            ->map(Registry::individualFactory()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @param Tree $tree
     * @param int  $max_julian_day
     *
     * @return Collection<int,Family>
     */
    private function familiesWithTasks(Tree $tree, int $max_julian_day): Collection
    {
        return DB::table('families')
            ->join('dates', static function (JoinClause $join): void {
                $join
                    ->on('f_file', '=', 'd_file')
                    ->on('f_id', '=', 'd_gid');
            })
            ->where('f_file', '=', $tree->id())
            ->where('d_fact', '=', '_TODO')
            ->where('d_julianday1', '<', $max_julian_day)
            ->select(['families.*'])
            ->distinct()
            ->get()
            ->map(Registry::familyFactory()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module. Tasks that need further research. */
        return I18N::translate('Research tasks');
    }

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
        return false;
    }

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $params = (array) $request->getParsedBody();

        $this->setBlockSetting($block_id, 'show_other', $params['show_other']);
        $this->setBlockSetting($block_id, 'show_unassigned', $params['show_unassigned']);
        $this->setBlockSetting($block_id, 'show_future', $params['show_future']);
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        $show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
        $show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
        $show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

        return view('modules/todo/config', [
            'show_future'     => $show_future,
            'show_other'      => $show_other,
            'show_unassigned' => $show_unassigned,
        ]);
    }
}
