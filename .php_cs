<?php

$header = <<<EOF
This file is part of Laravel OAuth 2.0.

(c) Luca Degasperi <packages@lucadegasperi.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$fixers = [
    // PSR-0
    '-psr0',

    // PSR-1
    'encoding',
    'short_tag',

    // Symfony
    'array_element_white_space_after_comma',
    'blankline_after_open_tag',
    'concat_without_spaces',
    'duplicate_semicolon',
    'empty_return',
    'extra_empty_lines',
    'function_typehint_space',
    'include',
    'join_function',
    'list_commas',
    'multiline_array_trailing_comma',
    'namespace_no_leading_whitespace',
    'new_with_braces',
    'no_blank_lines_after_class_opening',
    'no_empty_lines_after_phpdocs',
    'object_operator',
    'operators_spaces',
    'phpdoc_indent',
    'phpdoc_no_access',
    'phpdoc_no_package',
    'phpdoc_scalar',
    'phpdoc_separation',
    'phpdoc_short_description',
    'phpdoc_to_comment',
    'phpdoc_trim',
    'phpdoc_type_to_var',
    'phpdoc_var_without_name',
    'print_to_echo',
    'remove_leading_slash_use',
    'remove_lines_between_uses',
    'return',
    'self_accessor',
    'short_bool_cast',
    'single_array_no_trailing_comma',
    'single_blank_line_before_namespace',
    'single_quote',
    'spaces_before_semicolon',
    'spaces_cast',
    'standardize_not_equal',
    'ternary_spaces',
    'trim_array_spaces',
    'unalign_double_arrow',
    'unalign_equals',
    'unary_operators_spaces',
    'unneeded_control_parentheses',
    'unused_use',
    'whitespacy_lines',

    // Contrib
    'header_comment',
    'multiline_spaces_before_semicolon',
    'newline_after_open_tag',
    'ordered_use',
    'php_unit_construct',
    'php_unit_strict',
    'phpdoc_order',
    'short_array_syntax',
    'short_echo_tag',
];

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers($fixers)
    ->finder($finder);
