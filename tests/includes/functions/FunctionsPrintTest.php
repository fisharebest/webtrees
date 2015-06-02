<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Unit tests for the global functions in the file includes/functions/functions_print.php
 */
class FunctionsPrintTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
	}

	/**
	 * Test that function print_pedigree_person() exists in the correct namespace.
	 */
	public function testFunctionPrintPedigreePersonExists() {
		$this->assertEquals(function_exists('print_pedigree_person'), true);
	}

	/**
	 /**
	 * Test that function print_note_record() exists in the correct namespace.
	 */
	public function testFunctionPrintNoteRecordExists() {
		$this->assertEquals(function_exists('print_note_record'), true);
	}

	/**
	 * Test that function print_fact_notes() exists in the correct namespace.
	 */
	public function testFunctionPrintFactNotesExists() {
		$this->assertEquals(function_exists('print_fact_notes'), true);
	}

	/**
	 * Test that function help_link() exists in the correct namespace.
	 */
	public function testFunctionHelpLinkExists() {
		$this->assertEquals(function_exists('help_link'), true);
	}

	/**
	 * Test that function wiki_help_link() exists in the correct namespace.
	 */
	public function testFunctionWikiHelpLinkExists() {
		$this->assertEquals(function_exists('wiki_help_link'), true);
	}

	/**
	 * Test that function highlight_search_hits() exists in the correct namespace.
	 */
	public function testFunctionHighlightSearchHitsExists() {
		$this->assertEquals(function_exists('highlight_search_hits'), true);
	}

	/**
	 * Test that function format_asso_rela_record() exists in the correct namespace.
	 */
	public function testFunctionFormatAssoRelaRecordExists() {
		$this->assertEquals(function_exists('format_asso_rela_record'), true);
	}

	/**
	 * Test that function format_parents_age() exists in the correct namespace.
	 */
	public function testFunctionFormatParentsAgeExists() {
		$this->assertEquals(function_exists('format_parents_age'), true);
	}

	/**
	 * Test that function format_fact_date() exists in the correct namespace.
	 */
	public function testFunctionFormatFactDateExists() {
		$this->assertEquals(function_exists('format_fact_date'), true);
	}

	/**
	 * Test that function format_fact_place() exists in the correct namespace.
	 */
	public function testFunctionFormatFactPlaceExists() {
		$this->assertEquals(function_exists('format_fact_place'), true);
	}

	/**
	 * Test that function CheckFactUnique() exists in the correct namespace.
	 */
	public function testFunctionCheckFactUniqueExists() {
		$this->assertEquals(function_exists('CheckFactUnique'), true);
	}

	/**
	 * Test that function print_add_new_fact() exists in the correct namespace.
	 */
	public function testFunctionPrintAddNewFactExists() {
		$this->assertEquals(function_exists('print_add_new_fact'), true);
	}

	/**
	 * Test that function init_calendar_popup() exists in the correct namespace.
	 */
	public function testFunctionInitCalendarPopupExists() {
		$this->assertEquals(function_exists('init_calendar_popup'), true);
	}

	/**
	 * Test that function print_findindi_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindindiLinkExists() {
		$this->assertEquals(function_exists('print_findindi_link'), true);
	}

	/**
	 * Test that function print_findplace_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindplaceLinkExists() {
		$this->assertEquals(function_exists('print_findplace_link'), true);
	}

	/**
	 * Test that function print_findfamily_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindfamilyLinkExists() {
		$this->assertEquals(function_exists('print_findfamily_link'), true);
	}

	/**
	 * Test that function print_specialchar_link() exists in the correct namespace.
	 */
	public function testFunctionPrintSpecialcharLinkExists() {
		$this->assertEquals(function_exists('print_specialchar_link'), true);
	}

	/**
	 * Test that function print_autopaste_link() exists in the correct namespace.
	 */
	public function testFunctionPrintAutopasteLinkExists() {
		$this->assertEquals(function_exists('print_autopaste_link'), true);
	}

	/**
	 * Test that function print_findsource_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindsourceLinkExists() {
		$this->assertEquals(function_exists('print_findsource_link'), true);
	}

	/**
	 * Test that function print_findnote_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindnoteLinkExists() {
		$this->assertEquals(function_exists('print_findnote_link'), true);
	}

	/**
	 * Test that function print_findrepository_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindrepositoryLinkExists() {
		$this->assertEquals(function_exists('print_findrepository_link'), true);
	}

	/**
	 * Test that function print_findmedia_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindmediaLinkExists() {
		$this->assertEquals(function_exists('print_findmedia_link'), true);
	}

	/**
	 * Test that function print_findfact_link() exists in the correct namespace.
	 */
	public function testFunctionPrintFindfactLinkExists() {
		$this->assertEquals(function_exists('print_findfact_link'), true);
	}

	/**
	 * Test that function get_lds_glance() exists in the correct namespace.
	 */
	public function testFunctionGetLdsGlanceExists() {
		$this->assertEquals(function_exists('get_lds_glance'), true);
	}
}
