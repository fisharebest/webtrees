<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;
use function route;

final class CreateTree
{
    use ViewResponseTrait;

    public function __construct(
        private readonly TreeService $tree_service,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $title      = I18N::translate('Create a family tree');
        $tree_name  = Validator::queryParams($request)->string('name', $this->tree_service->uniqueTreeName());
        $tree_title = Validator::queryParams($request)->string('title', I18N::translate('My family tree'));

        return $this->viewResponse('admin/trees-create', [
            'title'      => $title,
            'tree_name'  => $tree_name,
            'tree_title' => $tree_title,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $name  = Validator::parsedBody($request)->string('name');
        $title = Validator::parsedBody($request)->string('title');

        if ($this->tree_service->all()->get($name) instanceof Tree) {
            FlashMessages::addMessage(I18N::translate('The family tree "%s" already exists.', e($name)), 'danger');

            return redirect(route(self::class, ['title' => $title]));
        }

        $tree = $this->tree_service->create($name, $title);

        FlashMessages::addMessage(I18N::translate('The family tree "%s" has been created.', e($name)), 'success');

        return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
    }
}
