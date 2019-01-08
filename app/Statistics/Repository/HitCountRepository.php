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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Middleware\PageHitCounter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\HitCountRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * A repository providing methods for hit count related statistics.
 */
class HitCountRepository implements HitCountRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * These functions provide access to hitcounter for use in the HTML block.
     *
     * @param string $page_name
     * @param string $page_parameter
     *
     * @return string
     *
     * @todo Use View
     */
    private function hitCountQuery($page_name, string $page_parameter = ''): string
    {
        if ($page_name === '') {
            // index.php?ctype=gedcom
            $page_name      = 'index.php';
            $page_parameter = 'gedcom:' . $this->tree->id();
        } elseif ($page_name === 'index.php') {
            // index.php?ctype=user
            $user           = User::findByIdentifier($page_parameter);
            $page_parameter = 'user:' . ($user ? $user->id() : Auth::id());
        }

        $hit_counter = new PageHitCounter(Auth::user(), $this->tree);

        return '<span class="odometer">'
            . I18N::digits($hit_counter->getCount($this->tree, $page_name, $page_parameter))
            . '</span>';
    }

    /**
     * @inheritDoc
     */
    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('index.php', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('individual.php', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('family.php', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('source.php', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('repo.php', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('note.php', $page_parameter);
    }

    /**
     * @inheritDoc
     */
    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('mediaviewer.php', $page_parameter);
    }
}
