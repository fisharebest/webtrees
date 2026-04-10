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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TreePreferencesAction::class)]
class TreePreferencesActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(TreePreferencesAction::class));
    }

    public function testHandleSavesPreferencesAndRedirects(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('prefs-action', 'Prefs Action Tree');

        $handler  = new TreePreferencesAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'CALENDAR_FORMAT0'         => 'gregorian',
            'CALENDAR_FORMAT1'         => 'gregorian',
            'CHART_BOX_TAGS'           => [],
            'CONTACT_USER_ID'          => '0',
            'EXPAND_NOTES'             => '1',
            'EXPAND_SOURCES'           => '',
            'FAM_FACTS_QUICK'          => [],
            'FORMAT_TEXT'              => '',
            'gedcom'                   => 'prefs-action',
            'GENERATE_UIDS'           => '',
            'HIDE_GEDCOM_ERRORS'       => '1',
            'INDI_FACTS_QUICK'         => [],
            'MEDIA_DIRECTORY'          => 'media/',
            'MEDIA_UPLOAD'             => '0',
            'META_DESCRIPTION'         => '',
            'META_TITLE'               => '',
            'NO_UPDATE_CHAN'            => '',
            'PEDIGREE_ROOT_ID'         => '',
            'QUICK_REQUIRED_FACTS'     => [],
            'QUICK_REQUIRED_FAMFACTS'  => [],
            'SHOW_COUNTER'             => '1',
            'SHOW_EST_LIST_DATES'      => '1',
            'SHOW_FACT_ICONS'          => '1',
            'SHOW_GEDCOM_RECORD'       => '',
            'SHOW_HIGHLIGHT_IMAGES'    => '1',
            'SHOW_LAST_CHANGE'         => '1',
            'SHOW_MEDIA_DOWNLOAD'      => '0',
            'SHOW_NO_WATERMARK'        => '1',
            'SHOW_PARENTS_AGE'         => '1',
            'SHOW_PEDIGREE_PLACES'     => '9',
            'SHOW_PEDIGREE_PLACES_SUFFIX' => '0',
            'SHOW_RELATIVES_EVENTS'    => [],
            'SUBLIST_TRIGGER_I'        => '200',
            'SURNAME_LIST_STYLE'       => 'style2',
            'SURNAME_TRADITION'        => 'paternal',
            'USE_SILHOUETTE'           => '1',
            'WEBMASTER_USER_ID'        => '0',
            'title'                    => 'Prefs Action Tree',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Verify some preferences were persisted
        self::assertSame('1', $tree->getPreference('EXPAND_NOTES'));
        self::assertSame('1', $tree->getPreference('HIDE_GEDCOM_ERRORS'));
        self::assertSame('style2', $tree->getPreference('SURNAME_LIST_STYLE'));
        self::assertSame('paternal', $tree->getPreference('SURNAME_TRADITION'));
    }

    public function testHandleWithBooleanFalseValues(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('prefs-bool', 'Boolean Test Tree');

        $handler  = new TreePreferencesAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'CALENDAR_FORMAT0'         => 'gregorian',
            'CALENDAR_FORMAT1'         => 'jewish',
            'CHART_BOX_TAGS'           => [],
            'CONTACT_USER_ID'          => '0',
            'EXPAND_NOTES'             => '',
            'EXPAND_SOURCES'           => '',
            'FAM_FACTS_QUICK'          => [],
            'FORMAT_TEXT'              => 'markdown',
            'gedcom'                   => 'prefs-bool',
            'GENERATE_UIDS'           => '',
            'HIDE_GEDCOM_ERRORS'       => '',
            'INDI_FACTS_QUICK'         => [],
            'MEDIA_DIRECTORY'          => 'media/',
            'MEDIA_UPLOAD'             => '1',
            'META_DESCRIPTION'         => 'Test description',
            'META_TITLE'               => 'Test title',
            'NO_UPDATE_CHAN'            => '',
            'PEDIGREE_ROOT_ID'         => '',
            'QUICK_REQUIRED_FACTS'     => [],
            'QUICK_REQUIRED_FAMFACTS'  => [],
            'SHOW_COUNTER'             => '',
            'SHOW_EST_LIST_DATES'      => '',
            'SHOW_FACT_ICONS'          => '',
            'SHOW_GEDCOM_RECORD'       => '',
            'SHOW_HIGHLIGHT_IMAGES'    => '',
            'SHOW_LAST_CHANGE'         => '',
            'SHOW_MEDIA_DOWNLOAD'      => '0',
            'SHOW_NO_WATERMARK'        => '1',
            'SHOW_PARENTS_AGE'         => '',
            'SHOW_PEDIGREE_PLACES'     => '9',
            'SHOW_PEDIGREE_PLACES_SUFFIX' => '0',
            'SHOW_RELATIVES_EVENTS'    => [],
            'SUBLIST_TRIGGER_I'        => '200',
            'SURNAME_LIST_STYLE'       => 'style1',
            'SURNAME_TRADITION'        => 'none',
            'USE_SILHOUETTE'           => '',
            'WEBMASTER_USER_ID'        => '0',
            'title'                    => 'Boolean Test Tree',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Boolean false => (string) false === ''
        self::assertSame('', $tree->getPreference('EXPAND_NOTES'));
        self::assertSame('', $tree->getPreference('SHOW_COUNTER'));
        // Calendar format combined
        self::assertSame('gregorian_and_jewish', $tree->getPreference('CALENDAR_FORMAT'));
        self::assertSame('markdown', $tree->getPreference('FORMAT_TEXT'));
        self::assertSame('Test description', $tree->getPreference('META_DESCRIPTION'));
    }
}
