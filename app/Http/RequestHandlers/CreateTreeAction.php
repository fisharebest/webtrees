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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;
use function route;

/**
 * Create a new tree.
 */
class CreateTreeAction extends AbstractBaseController
{
    /** @var TreeService */
    private $tree_service;

    /**
     * CreateTreePage constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();
        $name   = $params['name'];
        $title  = $params['title'];

        if ($this->tree_service->all()->get($name) instanceof Tree) {
            FlashMessages::addMessage(I18N::translate('The family tree “%s” already exists.', e($name)), 'danger');

            return redirect(route(CreateTreePage::class, ['title' => $title]));
        }

        $tree = $this->tree_service->create($name, $title);

        FlashMessages::addMessage(I18N::translate('The family tree “%s” has been created.', e($name)), 'success');

        return redirect(route('manage-trees', ['tree' => $tree->name()]));
    }
}
