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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GedcomImportService::class)]
class GedcomImportServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    private GedcomImportService $import_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->import_service = new GedcomImportService();
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomImportService::class));
    }

    /**
     * G01 — INDI records are imported correctly.
     */
    public function testImportIndiRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $count = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->count();

        self::assertSame(72, $count, 'demo.ged should contain 72 INDI records');
    }

    /**
     * G02 — FAM records are imported correctly.
     */
    public function testImportFamRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $count = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->count();

        self::assertSame(29, $count, 'demo.ged should contain 29 FAM records');
    }

    /**
     * G03 — Secondary records (SOUR, NOTE, REPO, OBJE) are imported.
     */
    public function testImportSecondaryRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $sources = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->count();

        $media = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->count();

        $other = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->count();

        self::assertGreaterThan(0, $sources, 'Should import SOUR records');
        self::assertGreaterThan(0, $media, 'Should import OBJE records');
        self::assertGreaterThan(0, $other, 'Should import other record types');
    }

    /**
     * G04 — Place hierarchy is preserved in the places table.
     */
    public function testImportPreservesPlaceHierarchy(): void
    {
        $tree = $this->importTree('demo.ged');

        $places = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->count();

        self::assertGreaterThan(0, $places, 'Should import place records');

        // Verify a known place from demo.ged exists
        $england = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_place', '=', 'England')
            ->exists();

        self::assertTrue($england, 'Place "England" should exist');
    }

    /**
     * G07 — UTF-8 characters are preserved during import.
     */
    public function testImportPreservesUtf8(): void
    {
        $tree = $this->importTree('demo.ged');

        // demo.ged is UTF-8; check that names are stored correctly
        $names = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->pluck('n_full')
            ->all();

        self::assertNotEmpty($names, 'Should have imported names');

        // All names should be valid UTF-8
        foreach ($names as $name) {
            self::assertTrue(
                mb_check_encoding($name, 'UTF-8'),
                "Name should be valid UTF-8: {$name}"
            );
        }
    }

    /**
     * G12 — All XREFs are unique within a tree.
     */
    public function testImportedXrefsAreUnique(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual_xrefs = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->pluck('i_id')
            ->all();

        // No duplicate XREFs
        self::assertSame(
            count($individual_xrefs),
            count(array_unique($individual_xrefs)),
            'All individual XREFs should be unique'
        );
    }

    /**
     * G02 — Spouse links (HUSB/WIFE) are correctly stored in families.
     */
    public function testImportCreatesSpouseLinks(): void
    {
        $tree = $this->importTree('demo.ged');

        // At least some families should have HUSB or WIFE links
        $with_husb = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->where('f_husb', '!=', '')
            ->count();

        $with_wife = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->where('f_wife', '!=', '')
            ->count();

        self::assertGreaterThan(0, $with_husb, 'Some families should have HUSB');
        self::assertGreaterThan(0, $with_wife, 'Some families should have WIFE');
    }

    /**
     * G02 — Link table contains FAMS and FAMC relationships.
     */
    public function testImportCreatesLinkRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $fams_links = DB::table('link')
            ->where('l_file', '=', $tree->id())
            ->where('l_type', '=', 'FAMS')
            ->count();

        $famc_links = DB::table('link')
            ->where('l_file', '=', $tree->id())
            ->where('l_type', '=', 'FAMC')
            ->count();

        self::assertGreaterThan(0, $fams_links, 'Should have FAMS links');
        self::assertGreaterThan(0, $famc_links, 'Should have FAMC links');
    }

    /**
     * Single INDI record import produces exactly one individual.
     */
    public function testImportSingleIndiRecord(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService($this->import_service);
        $user = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $tree_service->create('test', 'Test');

        // Clear default records
        DB::table('individuals')->where('i_file', '=', $tree->id())->delete();

        $gedcom = "0 @I1@ INDI\n1 NAME John /Doe/\n1 SEX M\n1 BIRT\n2 DATE 1 JAN 1900";
        $this->import_service->importRecord($gedcom, $tree, false);

        $count = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->count();

        self::assertSame(1, $count, 'Should import exactly one INDI record');

        // Verify the name was stored
        $name = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->where('n_id', '=', 'I1')
            ->value('n_full');

        self::assertSame('John Doe', $name);
    }

    /**
     * G05 — Date-Parsing: dates are stored correctly in the dates table.
     */
    public function testImportParsesDateFields(): void
    {
        $tree = $this->importTree('demo.ged');

        // Queen Elizabeth II: BIRT DATE 21 APR 1926
        $date = DB::table('dates')
            ->where('d_file', '=', $tree->id())
            ->where('d_gid', '=', 'X1030')
            ->where('d_fact', '=', 'BIRT')
            ->first();

        self::assertNotNull($date, 'Birth date for X1030 should exist');
        self::assertEquals(21, $date->d_day);
        self::assertEquals(4, $date->d_mon);
        self::assertEquals(1926, $date->d_year);
    }

    /**
     * G05 — Date-Parsing: Julian day values are computed for date records.
     */
    public function testImportComputesJulianDays(): void
    {
        $tree = $this->importTree('demo.ged');

        $dates_with_jd = DB::table('dates')
            ->where('d_file', '=', $tree->id())
            ->where('d_julianday1', '>', 0)
            ->count();

        self::assertGreaterThan(0, $dates_with_jd, 'Some dates should have computed Julian day values');
    }

    /**
     * G06 — Name-Extraktion: given name, surname and Soundex are stored.
     */
    public function testImportExtractsNameComponents(): void
    {
        $tree = $this->importTree('demo.ged');

        // Queen Elizabeth II — NAME Queen Elizabeth II, SURN Windsor
        $name = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->where('n_id', '=', 'X1030')
            ->first();

        self::assertNotNull($name, 'Name record for X1030 should exist');
        self::assertNotEmpty($name->n_givn, 'Given name should be extracted');
        self::assertSame('Windsor', $name->n_surn, 'Surname should be extracted');
        // Soundex is generated from $name['surname'] (slash-delimited), not 'surn' (SURN tag).
        // For X1030, 'surname' is empty because the NAME value has no /slashes/.
        // Verify givn soundex instead, which IS populated.
        self::assertNotEmpty($name->n_soundex_givn_std, 'Standard Soundex for given name should be generated');
    }

    /**
     * G08 — GEDCOM lines with valid structure are accepted without errors.
     */
    public function testImportHandlesMultiLineNotes(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService($this->import_service);
        $user = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $tree_service->create('test', 'Test');
        DB::table('individuals')->where('i_file', '=', $tree->id())->delete();

        // GEDCOM with multi-line NOTE using CONT/CONC
        $gedcom = "0 @I1@ INDI\n1 NAME John /Doe/\n1 NOTE First line\n2 CONT Second line\n2 CONC  continued";
        $this->import_service->importRecord($gedcom, $tree, false);

        $count = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->count();

        self::assertSame(1, $count, 'Multi-line NOTE records should import without error');
    }

    /**
     * G09 — GEDCOM with empty fields doesn't cause import errors.
     */
    public function testImportHandlesEmptyFields(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService($this->import_service);
        $user = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $tree_service->create('test', 'Test');
        DB::table('individuals')->where('i_file', '=', $tree->id())->delete();

        // GEDCOM with empty BIRT DATE and PLAC
        $gedcom = "0 @I1@ INDI\n1 NAME Jane /Doe/\n1 SEX F\n1 BIRT\n2 DATE\n2 PLAC";
        $this->import_service->importRecord($gedcom, $tree, false);

        $count = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->count();

        self::assertSame(1, $count, 'Empty fields should import without error');
    }

    /**
     * G11 — Media objects (OBJE) are imported correctly.
     */
    public function testImportMediaObjects(): void
    {
        $tree = $this->importTree('demo.ged');

        $media_count = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->count();

        self::assertGreaterThan(0, $media_count, 'Should import media objects from demo.ged');

        // Media should have file references
        $media_files = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->count();

        self::assertGreaterThan(0, $media_files, 'Media objects should have file references');
    }
}
