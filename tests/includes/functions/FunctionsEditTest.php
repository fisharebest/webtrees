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
 * Unit tests for the global functions in the file includes/functions/functions_edit.php
 */
class FunctionsEditTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
		defined('WT_ROOT') || define('WT_ROOT', '');
	}

	/**
	 * Test that function select_edit_control() exists in the correct namespace.
	 */
	public function testFunctionSelectEditControlExists() {
		$this->assertEquals(function_exists('select_edit_control'), true);
	}

	/**
	 * Test that function radio_buttons() exists in the correct namespace.
	 */
	public function testFunctionRadioButtonsExists() {
		$this->assertEquals(function_exists('radio_buttons'), true);
	}

	/**
	 * Test that function edit_field_yes_no() exists in the correct namespace.
	 */
	public function testFunctionEditFieldYesNoExists() {
		$this->assertEquals(function_exists('edit_field_yes_no'), true);
	}

	/**
	 * Test that function checkbox() exists in the correct namespace.
	 */
	public function testFunctionCheckboxExists() {
		$this->assertEquals(function_exists('checkbox'), true);
	}

	/**
	 * Test that function two_state_checkbox() exists in the correct namespace.
	 */
	public function testFunctionTwoStateCheckboxExists() {
		$this->assertEquals(function_exists('two_state_checkbox'), true);
	}

	/**
	 * Test that function edit_language_checkboxes() exists in the correct namespace.
	 */
	public function testFunctionEditLanguageCheckboxesExists() {
		$this->assertEquals(function_exists('edit_language_checkboxes'), true);
	}

	/**
	 * Test that function edit_field_access_level() exists in the correct namespace.
	 */
	public function testFunctionEditFieldAccessLevelExists() {
		$this->assertEquals(function_exists('edit_field_access_level'), true);
	}

	/**
	 * Test that function edit_field_resn() exists in the correct namespace.
	 */
	public function testFunctionEditFieldResnExists() {
		$this->assertEquals(function_exists('edit_field_resn'), true);
	}

	/**
	 * Test that function edit_field_contact() exists in the correct namespace.
	 */
	public function testFunctionEditFieldContactExists() {
		$this->assertEquals(function_exists('edit_field_contact'), true);
	}

	/**
	 * Test that function edit_field_language() exists in the correct namespace.
	 */
	public function testFunctionEditFieldLangaugeExists() {
		$this->assertEquals(function_exists('edit_field_language'), true);
	}

	/**
	 * Test that function edit_field_integers() exists in the correct namespace.
	 */
	public function testFunctionEditFieldIntegersExists() {
		$this->assertEquals(function_exists('edit_field_integers'), true);
	}

	/**
	 * Test that function edit_field_username() exists in the correct namespace.
	 */
	public function testFunctionEditFieldUsernameExists() {
		$this->assertEquals(function_exists('edit_field_username'), true);
	}

	/**
	 * Test that function edit_field_adop() exists in the correct namespace.
	 */
	public function testFunctionEditFieldAdopExists() {
		$this->assertEquals(function_exists('edit_field_adop'), true);
	}

	/**
	 * Test that function edit_field_pedi() exists in the correct namespace.
	 */
	public function testFunctionEditFieldPediExists() {
		$this->assertEquals(function_exists('edit_field_pedi'), true);
	}

	/**
	 * Test that function edit_field_name_type() exists in the correct namespace.
	 */
	public function testFunctionEditFieldNameTypeExists() {
		$this->assertEquals(function_exists('edit_field_name_type'), true);
	}

	/**
	 * Test that function edit_field_rela() exists in the correct namespace.
	 */
	public function testFunctionEditFieldRelaExists() {
		$this->assertEquals(function_exists('edit_field_rela'), true);
	}

	/**
	 * Test that function remove_links() exists in the correct namespace.
	 */
	public function testFunctionRemoveLinksExists() {
		$this->assertEquals(function_exists('remove_links'), true);
	}

	/**
	 * Test that function print_calendar_popup() exists in the correct namespace.
	 */
	public function testFunctionPrintCalendarPopupExists() {
		$this->assertEquals(function_exists('print_calendar_popup'), true);
	}

	/**
	 * Test that function print_addnewmedia_link() exists in the correct namespace.
	 */
	public function testFunctionPrintAddnewmediaLinkExists() {
		$this->assertEquals(function_exists('print_addnewmedia_link'), true);
	}

	/**
	 * Test that function print_addnewrepository_link() exists in the correct namespace.
	 */
	public function testFunctionPrintAddnewrepositoryLinkExists() {
		$this->assertEquals(function_exists('print_addnewrepository_link'), true);
	}

	/**
	 * Test that function print_addnewnote_link() exists in the correct namespace.
	 */
	public function testFunctionPrintAddnewnoteLinkExists() {
		$this->assertEquals(function_exists('print_addnewnote_link'), true);
	}

	/**
	 * Test that function print_editnote_link() exists in the correct namespace.
	 */
	public function testFunctionPrintEditnoteLinkExists() {
		$this->assertEquals(function_exists('print_editnote_link'), true);
	}

	/**
	 * Test that function print_addnewsource_link() exists in the correct namespace.
	 */
	public function testFunctionPrintAddnewsourceLinkExists() {
		$this->assertEquals(function_exists('print_addnewsource_link'), true);
	}

	/**
	 * Test that function add_simple_tag() exists in the correct namespace.
	 */
	public function testFunctionAddSimpleTagExists() {
		$this->assertEquals(function_exists('add_simple_tag'), true);
	}

	/**
	 * Test that function print_add_layer() exists in the correct namespace.
	 */
	public function testFunctionPrintAddLayerExists() {
		$this->assertEquals(function_exists('print_add_layer'), true);
	}

	/**
	 * Test that function addSimpleTags() exists in the correct namespace.
	 */
	public function testFunctionAddSimpleTagsExists() {
		$this->assertEquals(function_exists('addSimpleTags'), true);
	}

	/**
	 * Test that function addNewName() exists in the correct namespace.
	 */
	public function testFunctionAddNewNameExists() {
		$this->assertEquals(function_exists('addNewName'), true);
	}

	/**
	 * Test that function addNewSex() exists in the correct namespace.
	 */
	public function testFunctionAddNewSexExists() {
		$this->assertEquals(function_exists('addNewSex'), true);
	}

	/**
	 * Test that function addNewFact() exists in the correct namespace.
	 */
	public function testFunctionAddNewFactExists() {
		$this->assertEquals(function_exists('addNewFact'), true);
	}

	/**
	 * Test that function splitSOUR() exists in the correct namespace.
	 */
	public function testFunctionSplitSourExists() {
		$this->assertEquals(function_exists('splitSOUR'), true);
	}

	/**
	 * Test that function updateSOUR() exists in the correct namespace.
	 */
	public function testFunctionUpdateSourExists() {
		$this->assertEquals(function_exists('updateSOUR'), true);
	}

	/**
	 * Test that function updateRest() exists in the correct namespace.
	 */
	public function testFunctionUpdateRestExists() {
		$this->assertEquals(function_exists('updateRest'), true);
	}

	/**
	 * Test that function handle_updates() exists in the correct namespace.
	 */
	public function testFunctionHandleUpdatesExists() {
		$this->assertEquals(function_exists('handle_updates'), true);
	}

	/**
	 * Test that function create_add_form() exists in the correct namespace.
	 */
	public function testFunctionCreateAddFromExists() {
		$this->assertEquals(function_exists('create_add_form'), true);
	}

	/**
	 * Test that function create_edit_form() exists in the correct namespace.
	 */
	public function testFunctionCreateEditFromExists() {
		$this->assertEquals(function_exists('create_edit_form'), true);
	}

	/**
	 * Test that function insert_missing_subtags() exists in the correct namespace.
	 */
	public function testFunctionInsertMissingSubtagsExists() {
		$this->assertEquals(function_exists('insert_missing_subtags'), true);
	}
}
