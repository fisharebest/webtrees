<?php
namespace WT;

use PHPUnit_Framework_TestCase;

/**
 * Unit tests for the global functions in the file includes/functions/functions_print.php
 *
 * @package   webtrees
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */

class FunctionsPrintTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		require_once 'includes/functions/functions_print.php';
	}

	/**
	 * Test that function print_pedigree_person() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintPedigreePersonExists() {
		$this->assertEquals(function_exists('\\print_pedigree_person'), true);
	}

	/**
	 * Test that function header_links() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionHeaderLinksExists() {
		$this->assertEquals(function_exists('\\header_links'), true);
	}

	/**
	 * Test that function execution_statistics() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionExecutionStatsExists() {
		$this->assertEquals(function_exists('\\execution_stats'), true);
	}

	/**
	 * Test that function login_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionLoginLinkExists() {
		$this->assertEquals(function_exists('\\login_link'), true);
	}

	/**
	 * Test that function logout_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionLogoutLinkExists() {
		$this->assertEquals(function_exists('\\logout_link'), true);
	}

	/**
	 * Test that function whoisonline() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionWhoisonlineExists() {
		$this->assertEquals(function_exists('\\whoisonline'), true);
	}

	/**
	 * Test that function user_contact_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionUserContactLinkExists() {
		$this->assertEquals(function_exists('\\user_contact_link'), true);
	}

	/**
	 * Test that function contact_links() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionContactLinksExists() {
		$this->assertEquals(function_exists('\\contact_links'), true);
	}

	/**
	 * Test that function print_note_record() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintNoteRecordExists() {
		$this->assertEquals(function_exists('\\print_note_record'), true);
	}

	/**
	 * Test that function print_fact_notes() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFactNotesExists() {
		$this->assertEquals(function_exists('\\print_fact_notes'), true);
	}

	/**
	 * Test that function help_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionHelpLinkExists() {
		$this->assertEquals(function_exists('\\help_link'), true);
	}

	/**
	 * Test that function wiki_help_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionWikiHelpLinkExists() {
		$this->assertEquals(function_exists('\\wiki_help_link'), true);
	}

	/**
	 * Test that function highlight_search_hits() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionHighlightSearchHitsExists() {
		$this->assertEquals(function_exists('\\highlight_search_hits'), true);
	}

	/**
	 * Test that function format_asso_rela_record() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatAssoRelaRecordExists() {
		$this->assertEquals(function_exists('\\format_asso_rela_record'), true);
	}

	/**
	 * Test that function format_parents_age() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatParentsAgeExists() {
		$this->assertEquals(function_exists('\\format_parents_age'), true);
	}

	/**
	 * Test that function format_fact_date() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatFactDateExists() {
		$this->assertEquals(function_exists('\\format_fact_date'), true);
	}

	/**
	 * Test that function format_fact_place() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionFormatFactPlaceExists() {
		$this->assertEquals(function_exists('\\format_fact_place'), true);
	}

	/**
	 * Test that function CheckFactUnique() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionCheckFactUniqueExists() {
		$this->assertEquals(function_exists('\\CheckFactUnique'), true);
	}

	/**
	 * Test that function print_add_new_fact() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAddNewFactExists() {
		$this->assertEquals(function_exists('\\print_add_new_fact'), true);
	}

	/**
	 * Test that function init_calendar_popup() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionInitCalendarPopupExists() {
		$this->assertEquals(function_exists('\\init_calendar_popup'), true);
	}

	/**
	 * Test that function print_findindi_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindindiLinkExists() {
		$this->assertEquals(function_exists('\\print_findindi_link'), true);
	}

	/**
	 * Test that function print_findplace_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindplaceLinkExists() {
		$this->assertEquals(function_exists('\\print_findplace_link'), true);
	}

	/**
	 * Test that function print_findfamily_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindfamilyLinkExists() {
		$this->assertEquals(function_exists('\\print_findfamily_link'), true);
	}

	/**
	 * Test that function print_specialchar_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintSpecialcharLinkExists() {
		$this->assertEquals(function_exists('\\print_specialchar_link'), true);
	}

	/**
	 * Test that function print_autopaste_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAutopasteLinkExists() {
		$this->assertEquals(function_exists('\\print_autopaste_link'), true);
	}

	/**
	 * Test that function print_findsource_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindsourceLinkExists() {
		$this->assertEquals(function_exists('\\print_findsource_link'), true);
	}

	/**
	 * Test that function print_findnote_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindnoteLinkExists() {
		$this->assertEquals(function_exists('\\print_findnote_link'), true);
	}

	/**
	 * Test that function print_findrepository_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindrepositoryLinkExists() {
		$this->assertEquals(function_exists('\\print_findrepository_link'), true);
	}

	/**
	 * Test that function print_findmedia_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindmediaLinkExists() {
		$this->assertEquals(function_exists('\\print_findmedia_link'), true);
	}

	/**
	 * Test that function print_findfact_link() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintFindfactLinkExists() {
		$this->assertEquals(function_exists('\\print_findfact_link'), true);
	}

	/**
	 * Test that function get_lds_glance() exists in the global namespace.
	 *
	 * @return void
	 */
	public function testFunctionGetLdsGlanceExists() {
		$this->assertEquals(function_exists('\\get_lds_glance'), true);
	}
}
