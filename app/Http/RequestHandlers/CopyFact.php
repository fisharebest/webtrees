<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function response;

/**
 * Copy a fact to the clipboard.
 */
class CopyFact implements RequestHandlerInterface
{
    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * CopyFact constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * Copy a fact to the clipboard.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $fact_id = $request->getAttribute('fact_id');
        $record  = Factory::gedcomRecord()->make($xref, $tree);
        $record  = Auth::checkRecordAccess($record, true);

        foreach ($record->facts() as $fact) {
            if ($fact->id() === $fact_id) {
                $this->clipboard_service->copyFact($fact);

                FlashMessages::addMessage(I18N::translate('The record has been copied to the clipboard.'));
                break;
            }
        }

        return response();
    }
}
