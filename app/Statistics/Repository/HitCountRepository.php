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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

use function view;

class HitCountRepository
{
    private Tree $tree;

    private UserService $user_service;

    /**
     * @param Tree        $tree
     * @param UserService $user_service
     */
    public function __construct(Tree $tree, UserService $user_service)
    {
        $this->tree         = $tree;
        $this->user_service = $user_service;
    }

    /**
     * These functions provide access to hitcounter for use in the HTML block.
     *
     * @param string $page_name
     * @param string $page_parameter
     *
     * @return string
     */
    private function hitCountQuery(string $page_name, string $page_parameter = ''): string
    {
        if ($page_name === '') {
            // index.php?context=gedcom
            $page_name      = 'index.php';
            $page_parameter = 'gedcom:' . $this->tree->id();
        } elseif ($page_name === 'index.php') {
            // index.php?context=user
            $user           = $this->user_service->findByIdentifier($page_parameter);
            $page_parameter = 'user:' . ($user instanceof UserInterface ? $user->id() : Auth::id());
        }

        $count = (int) DB::table('hit_counter')
            ->where('gedcom_id', '=', $this->tree->id())
            ->where('page_name', '=', $page_name)
            ->where('page_parameter', '=', $page_parameter)
            ->value('page_count');

        return view(
            'statistics/hit-count',
            [
                'count' => $count,
            ]
        );
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCount(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountUser(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('index.php', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountIndi(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('individual.php', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountFam(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('family.php', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountSour(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('source.php', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountRepo(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('repo.php', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountNote(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('note.php', $page_parameter);
    }

    /**
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountObje(string $page_parameter = ''): string
    {
        return $this->hitCountQuery('mediaviewer.php', $page_parameter);
    }
}
