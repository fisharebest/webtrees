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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
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
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $ctype
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $ctype = '', array $cfg = []): string
    {
        $show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
        $show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
        $show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

        extract($cfg, EXTR_OVERWRITE);

        $end_jd      = $show_future ? Carbon::maxValue()->julianDay() : Carbon::now()->julianDay();
        $individuals = $this->individualsWithTasks($tree, $end_jd);
        $families    = $this->familiesWithTasks($tree, $end_jd);

        /** @var GedcomRecord[] $records */
        $records = $individuals->merge($families);

        $tasks = [];

        foreach ($records as $record) {
            foreach ($record->facts(['_TODO']) as $task) {
                $user_name = $task->attribute('_WT_USER');

                if ($user_name === Auth::user()->userName() || empty($user_name) && $show_unassigned || !empty($user_name) && $show_other) {
                    $tasks[] = $task;
                }
            }
        }

        if (empty($records)) {
            $content = '<p>' . I18N::translate('There are no research tasks in this family tree.') . '</p>';
        } else {
            $content = view('modules/todo/research-tasks', ['tasks' => $tasks]);
        }

        if ($ctype !== '') {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $config_url,
                'title'      => $this->title(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /** {@inheritdoc} */
    public function loadAjax(): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function isUserBlock(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $this->setBlockSetting($block_id, 'show_other', $request->get('show_other', ''));
        $this->setBlockSetting($block_id, 'show_unassigned', $request->get('show_unassigned', ''));
        $this->setBlockSetting($block_id, 'show_future', $request->get('show_future', ''));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): void
    {
        $show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
        $show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
        $show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

        echo view('modules/todo/config', [
            'show_future'     => $show_future,
            'show_other'      => $show_other,
            'show_unassigned' => $show_unassigned,
        ]);
    }

    /**
     * @param Tree $tree
     * @param int  $max_julian_day
     *
     * @return Collection
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
            ->map(Family::rowMapper())
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @param Tree $tree
     * @param int  $max_julian_day
     *
     * @return Collection
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
            ->map(Individual::rowMapper())
            ->filter(GedcomRecord::accessFilter());
    }
}
