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

use Aura\Router\RouterContainer;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sabre\VObject\Component\VCalendar;

use function app;
use function assert;
use function response;
use function route;
use function strip_tags;
use function view;

/**
 * Class ShareAnniversaryModule
 */
class ShareAnniversaryModule extends AbstractModule implements ModuleShareInterface, RequestHandlerInterface
{
    use ModuleShareTrait;

    protected const INDIVIDUAL_EVENTS = ['BIRT', 'DEAT'];
    protected const FAMILY_EVENTS     = ['MARR'];

    protected const ROUTE_URL = '/tree/{tree}/anniversary-ics/{xref}/{fact_id}';

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        $router_container = app(RouterContainer::class);
        assert($router_container instanceof RouterContainer);

        $router_container->getMap()
            ->get(static::class, static::ROUTE_URL, $this);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Share the anniversary of an event');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Download a .ICS file containing an anniversary');
    }

    /**
     * HTML to include in the share links page.
     *
     * @param GedcomRecord $record
     *
     * @return string
     */
    public function share(GedcomRecord $record): string
    {
        if ($record instanceof Individual) {
            $facts = $record->facts(static::INDIVIDUAL_EVENTS, true)
                ->merge($record->spouseFamilies()->map(fn (Family $family): Collection => $family->facts(static::FAMILY_EVENTS, true)));
        } elseif ($record instanceof Family) {
            $facts = $record->facts(static::FAMILY_EVENTS, true);
        } else {
            return '';
        }

        // iCalendar only supports exact Gregorian dates.
        $facts = $facts
            ->flatten()
            ->filter(fn (Fact $fact): bool => $fact->date()->isOK())
            ->filter(fn (Fact $fact): bool => $fact->date()->qual1 === '')
            ->filter(fn (Fact $fact): bool => $fact->date()->minimumDate() instanceof GregorianDate)
            ->filter(fn (Fact $fact): bool => $fact->date()->minimumJulianDay() === $fact->date()->maximumJulianDay())
            ->mapWithKeys(fn (Fact $fact): array => [
                route(static::class, ['tree' => $record->tree()->name(), 'xref' => $fact->record()->xref(), 'fact_id' => $fact->id()]) =>
                    $fact->label() . ' — ' . $fact->date()->display(false, null, false),
            ]);

        if ($facts->isNotEmpty()) {
            $url = route(static::class, ['tree' => $record->tree()->name(), 'xref' => $record->xref()]);

            return view('modules/share-anniversary/share', [
                'facts'  => $facts,
                'record' => $record,
                'url'    => $url,
            ]);
        }

        return '';
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref    = $request->getAttribute('xref');
        $fact_id = $request->getAttribute('fact_id');

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record);

        $fact = $record->facts()
            ->filter(fn (Fact $fact): bool => $fact->id() === $fact_id)
            ->first();

        if ($fact instanceof Fact) {
            $date             = $fact->date()->minimumDate()->format('%Y%m%d');
            $vcalendar        = new VCalendar();
            $vevent           = $vcalendar->add('VEVENT');
            $dtstart          = $vevent->add('DTSTART', $date);
            $dtstart['VALUE'] = 'DATE';
            $vevent->add('RRULE', 'FREQ=YEARLY');
            $vevent->add('SUMMARY', strip_tags($record->fullName()) . ' — ' . $fact->label());

            return response($vcalendar->serialize())
                ->withHeader('Content-Type', 'text/calendar')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $fact->id() . '.ics');
        }

        throw new HttpNotFoundException();
    }
}
