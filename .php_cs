<?php

/*
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

// https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__)
	->exclude('data/cache')
	->exclude('packages')
	->exclude('themes/_custom')
	->exclude('vendor');

return PhpCsFixer\Config::create()
	->setUsingCache(false)
	->setIndent("\t")
	->setFinder($finder)
	->setRiskyAllowed(true)
	->setRules([
		// Mostly use PSR-2 ...
		'@PSR2' => true,

		// ... exceptions
		'braces' => [
			'position_after_functions_and_oop_constructs' => 'same',
		],

		// ... additions
		'binary_operator_spaces' => [
			'operators' => [
				'===' => 'align_single_space_minimal',
				'!==' => 'align_single_space_minimal',
				'=='  => 'align_single_space_minimal',
				'!='  => 'align_single_space_minimal',
				'='   => 'align_single_space_minimal',
				'=>'  => 'align_single_space_minimal',
			],
		],

		'array_syntax' => [
			'syntax' => 'short',
		],

		'concat_space' => [
			'spacing' => 'one',
		],
	]);
