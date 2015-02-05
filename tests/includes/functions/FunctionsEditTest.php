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
 * Unit tests for the global functions in the file includes/functions/functions_edit.php
 */
class FunctionsEditTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 *
	 * @return void
	 */
	public function setUp() {
		defined('WT_ROOT') || define('WT_ROOT', '');
	}

	/**
	 * Test that function select_edit_control() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSelectEditControlExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\select_edit_control'), true);
	}

	/**
	 * Test that function radio_buttons() exists in the correct namespace.
	 * @return void
	 *
	 * @return void
	 */
	public function testFunctionRadioButtonsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\radio_buttons'), true);
	}

	/**
	 * Test that function edit_field_yes_no() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldYesNoExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_yes_no'), true);
	}

	/**
	 * Test that function checkbox() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionCheckboxExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\checkbox'), true);
	}

	/**
	 * Test that function two_state_checkbox() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionTwoStateCheckboxExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\two_state_checkbox'), true);
	}

	/**
	 * Test that function edit_language_checkboxes() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditLanguageCheckboxesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_language_checkboxes'), true);
	}

	/**
	 * Test that function edit_field_access_level() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldAccessLevelExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_access_level'), true);
	}

	/**
	 * Test that function edit_field_resn() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldResnExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_resn'), true);
	}

	/**
	 * Test that function edit_field_contact() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldContactExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_contact'), true);
	}

	/**
	 * Test that function edit_field_language() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldLangaugeExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_language'), true);
	}

	/**
	 * Test that function edit_field_integers() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldIntegersExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_integers'), true);
	}

	/**
	 * Test that function edit_field_username() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldUsernameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_username'), true);
	}

	/**
	 * Test that function edit_field_adop() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldAdopExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_adop'), true);
	}

	/**
	 * Test that function edit_field_pedi() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldPediExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_pedi'), true);
	}

	/**
	 * Test that function edit_field_name_type() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldNameTypeExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_name_type'), true);
	}

	/**
	 * Test that function edit_field_rela() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionEditFieldRelaExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\edit_field_rela'), true);
	}

	/**
	 * Test that function remove_links() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionRemoveLinksExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\remove_links'), true);
	}

	/**
	 * Test that function print_calendar_popup() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintCalendarPopupExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_calendar_popup'), true);
	}

	/**
	 * Test that function print_addnewmedia_link() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAddnewmediaLinkExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_addnewmedia_link'), true);
	}

	/**
	 * Test that function print_addnewrepository_link() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAddnewrepositoryLinkExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_addnewrepository_link'), true);
	}

	/**
	 * Test that function print_addnewnote_link() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAddnewnoteLinkExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_addnewnote_link'), true);
	}

	/**
	 * Test that function print_editnote_link() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintEditnoteLinkExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_editnote_link'), true);
	}

	/**
	 * Test that function print_addnewsource_link() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAddnewsourceLinkExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_addnewsource_link'), true);
	}

	/**
	 * Test that function add_simple_tag() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionAddSimpleTagExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\add_simple_tag'), true);
	}

	/**
	 * Test that function print_add_layer() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionPrintAddLayerExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\print_add_layer'), true);
	}

	/**
	 * Test that function addSimpleTags() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionAddSimpleTagsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\addSimpleTags'), true);
	}

	/**
	 * Test that function addNewName() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionAddNewNameExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\addNewName'), true);
	}

	/**
	 * Test that function addNewSex() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionAddNewSexExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\addNewSex'), true);
	}

	/**
	 * Test that function addNewFact() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionAddNewFactExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\addNewFact'), true);
	}

	/**
	 * Test that function splitSOUR() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionSplitSourExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\splitSOUR'), true);
	}

	/**
	 * Test that function updateSOUR() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateSourExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\updateSOUR'), true);
	}

	/**
	 * Test that function updateRest() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionUpdateRestExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\updateRest'), true);
	}

	/**
	 * Test that function handle_updates() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionHandleUpdatesExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\handle_updates'), true);
	}

	/**
	 * Test that function create_add_form() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionCreateAddFromExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\create_add_form'), true);
	}

	/**
	 * Test that function create_edit_form() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionCreateEditFromExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\create_edit_form'), true);
	}

	/**
	 * Test that function insert_missing_subtags() exists in the correct namespace.
	 *
	 * @return void
	 */
	public function testFunctionInsertMissingSubtagsExists() {
		$this->assertEquals(function_exists(__NAMESPACE__ . '\\insert_missing_subtags'), true);
	}
}
