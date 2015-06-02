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
 * Unit tests for the global functions in the file includes/functions/functions_rtl.php
 */
class FunctionsRtlTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
	}

	/**
	 * Test that function stripLRMRLM() exists in the correct namespace.
	 */
	public function testFunctionStripLRMRLMExists() {
		$this->assertEquals(function_exists('stripLRMRLM'), true);
	}

	/**
	 * Test that function spanLTRRTL() exists in the correct namespace.
	 */
	public function testFunctionSpanLTRRTLExists() {
		$this->assertEquals(function_exists('spanLTRRTL'), true);
	}

	/**
	 * Test that function starredName() exists in the correct namespace.
	 */
	public function testFunctionStarredNameExists() {
		$this->assertEquals(function_exists('starredName'), true);
	}

	/**
	 * Test that function getChar() exists in the correct namespace.
	 */
	public function testFunctionGetCharExists() {
		$this->assertEquals(function_exists('getChar'), true);
	}

	/**
	 * Test that function breakCurrentSpan() exists in the correct namespace.
	 */
	public function testFunctionBreakCurrentSpanExists() {
		$this->assertEquals(function_exists('breakCurrentSpan'), true);
	}

	/**
	 * Test that function beginCurrentSpan() exists in the correct namespace.
	 */
	public function testFunctionBeginCurrentSpanExists() {
		$this->assertEquals(function_exists('beginCurrentSpan'), true);
	}

	/**
	 * Test that function finishCurrentSpan() exists in the correct namespace.
	 */
	public function testFunctionFinishCurrentSpanExists() {
		$this->assertEquals(function_exists('finishCurrentSpan'), true);
	}

	/**
	 * Test that function utf8_wordwrap() exists in the correct namespace.
	 */
	public function testFunctionUtf8WordwrapExists() {
		$this->assertEquals(function_exists('utf8_wordwrap'), true);
	}
}
