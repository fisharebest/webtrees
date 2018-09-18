<?php

/*
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
	->exclude('packages')
	->exclude('themes/_custom')
	->exclude('vendor');

return PhpCsFixer\Config::create()
	->setRules(array(
		'@PSR2' => true,
		// Exclude these PSR2 rules
		'--indentation',
		'--braces',
		// symfony rules
		'--blankline_after_open_tag',
		'--concat_without_spaces',
		'--double_arrow_multiline_whitespaces',
		'duplicate_semicolons',
		'--empty_return',
		'extra_empty_lines',
		'include',
		'join_function',
		'list_commas',
		'multiline_array_trailing_comma',
		'namespace_no_leading_whitespace',
		'--new_with_braces',
		'no_blank_lines_after_class_opening',
		'object_operator',
		'operators_spaces',
		'phpdoc_indent',
		'phpdoc_no_access',
		'phpdoc_no_empty_return',
		'phpdoc_no_package',
		'phpdoc_params',
		'phpdoc_scalar',
		'phpdoc_separation',
		'--phpdoc_short_description',
		'phpdoc_trim',
		'phpdoc_type_to_var',
		'phpdoc_var_without_name',
		'--pre_increment',
		'remove_leading_slash_use',
		'remove_lines_between_uses',
		'return',
		'self_accessor',
		'single_array_no_trailing_comma',
		'--single_blank_line_before_namespace',
		'--single_quote', // WE WANT THIS?
		'spaces_before_semicolon',
		'spaces_cast',
		'standardize_not_equal',
		'ternary_spaces',
		'trim_array_spaces',
		'--unalign_double_arrow',
		'--unalign_equals',
		'unary_operators_spaces',
		'unused_use',
		'whitespacy_lines',
		// contrib rules
		'align_double_arrow',
		'align_equals',
		'concat_with_spaces',
		'ereg_to_preg',
		'--header_comment',
		'long_array_syntax',
		'multiline_spaces_before_semicolon',
		'--newline_after_open_tag',
		'no_blank_lines_before_namespace',
		'ordered_use',
		'php4_constructor',
		'phpdoc_order',
		'--phpdoc_var_to_type',
		'--short_array_syntax',
		'--short_echo_tag',
		'--strict',
		'--strict_param',
	))->setFinder($finder);
