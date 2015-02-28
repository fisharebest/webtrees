<?php
namespace Fisharebest\Webtrees;

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

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_db.php
 */
class FunctionsDbTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Test that function fetch_all_links() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionFetchAllLinksExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\fetch_all_links'), true);
	}

	/**
	 * Test that function get_source_list() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetSourceListExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_source_list'), true);
	}

	/**
	 * Test that function get_repo_list() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRepoListExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_repo_list'), true);
	}

	/**
	 * Test that function get_note_list() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetNoteListExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_note_list'), true);
	}

	/**
	 * Test that function search_indis() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchIndisExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_indis'), true);
	}

	/**
	 * Test that function search_indis_names() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchIndisNamesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_indis_names'), true);
	}

	/**
	 * Test that function search_indis_soundex() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchIndisSoundexExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_indis_soundex'), true);
	}

	/**
	 * Test that function get_recent_changes() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetRecentChangesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_recent_changes'), true);
	}

	/**
	 * Test that function search_indis_dates() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchIndisDatesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_indis_dates'), true);
	}

	/**
	 * Test that function search_fams() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchFamsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_fams'), true);
	}

	/**
	 * Test that function search_fams_names() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchFamsNamesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_fams_names'), true);
	}

	/**
	 * Test that function search_sources() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchSourcesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_sources'), true);
	}

	/**
	 * Test that function search_notes() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchNotesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_notes'), true);
	}

	/**
	 * Test that function search_repos() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSearchReposExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\search_repos'), true);
	}

	/**
	 * Test that function find_rin_id() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionFindRinIdExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\find_rin_id'), true);
	}

	/**
	 * Test that function get_common_surnames() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetCommonSurnamesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_common_surnames'), true);
	}

	/**
	 * Test that function get_top_surnames() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetTopSurnamesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_top_surnames'), true);
	}

	/**
	 * Test that function get_anniversary_events() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetAnniversaryEventsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_anniversary_events'), true);
	}

	/**
	 * Test that function get_calendar_events() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetCalendarEventsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_calendar_events'), true);
	}

	/**
	 * Test that function is_media_used_in_other_gedcom() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionIsMediaUsedInOtherGedcomExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\is_media_used_in_other_gedcom'), true);
	}

	/**
	 * Test that function get_gedcom_from_id() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetGedcomFromIdExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_gedcom_from_id'), true);
	}

	/**
	 * Test that function get_id_from_gedcom() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetIdFromGedcomExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_id_from_gedcom'), true);
	}

	/**
	 * Test that function get_user_blocks() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetUserBlocksExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_user_blocks'), true);
	}

	/**
	 * Test that function get_gedcom_blocks() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetGedcomBlocksExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_gedcom_blocks'), true);
	}

	/**
	 * Test that function get_block_setting() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetBlockSettingExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_block_setting'), true);
	}

	/**
	 * Test that function set_block_setting() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSetBlockSettingExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\set_block_setting'), true);
	}

	/**
	 * Test that function update_favorites() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateFavoritesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\update_favorites'), true);
	}

	/**
	 * Test that function get_events_list() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetEventsListExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\get_events_list'), true);
	}
}
