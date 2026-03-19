<?php declare(strict_types = 1);

namespace PHPStan\Rules\DisallowedConstructs;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\Ternary>
 */
class DisallowedShortTernaryRule implements \PHPStan\Rules\Rule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\Ternary::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->if !== null) {
			return [];
		}

		return [
			'Short ternary operator is not allowed. Use null coalesce operator if applicable or consider using long ternary.',
		];
	}

}
