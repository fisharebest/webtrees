<?php

/*
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
		'@Symfony'                                  => true,
		'array_syntax'                              => ['syntax' => 'short'],
		'binary_operator_spaces'                    => ['align_double_arrow' => true, 'align_equals' => true],
		'class_definition'                          => false,
		'combine_consecutive_unsets'                => true,
		'concat_space'                              => ['spacing' => 'one'],
		'dir_constant'                              => true,
		'ereg_to_preg'                              => true,
		'linebreak_after_opening_tag'               => true,
		'modernize_types_casting'                   => true,
		'new_with_braces'                           => false,
		'no_blank_lines_before_namespace'           => true,
		'no_multiline_whitespace_before_semicolons' => true,
		'ordered_imports'                           => true,
		'single_blank_line_before_namespace'        => false,
	]);
