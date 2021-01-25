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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function response;
use function view;

/**
 * Trait ModuleTabTrait - default implementation of ModuleTabInterface
 */
trait ModuleTabTrait
{
    /** @var int The default position for this tab.  It can be changed in the control panel. */
    protected $tab_order;

    /**
     * The text that appears on the tab.
     *
     * @return string
     */
    public function tabTitle(): string
    {
        return $this->title();
    }

    /**
     * Users change change the order of tabs using the control panel.
     *
     * @param int $tab_order
     *
     * @return void
     */
    public function setTabOrder(int $tab_order): void
    {
        $this->tab_order = $tab_order;
    }

    /**
     * Users change change the order of tabs using the control panel.
     *
     * @return int
     */
    public function getTabOrder(): int
    {
        return $this->tab_order ?? $this->defaultTabOrder();
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 9999;
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<string>
     */
    public function supportedFacts(): Collection
    {
        return new Collection();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getTabAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getQueryParams()['xref'];

        $record = Registry::individualFactory()->make($xref, $tree);
        $record = Auth::checkIndividualAccess($record);

        $user = $request->getAttribute('user');

        if ($this->accessLevel($tree, ModuleTabInterface::class) < Auth::accessLevel($tree, $user)) {
            throw new HttpAccessDeniedException();
        }

        $layout = view('layouts/ajax', [
            'content' => $this->getTabContent($record),
        ]);

        return response($layout);
    }
}
