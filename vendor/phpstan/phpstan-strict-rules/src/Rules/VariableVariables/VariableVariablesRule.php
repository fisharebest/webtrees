<?php declare(strict_types = 1);

namespace PHPStan\Rules\VariableVariables;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

class VariableVariablesRule implements Rule
{

	public function getNodeType(): string
	{
		return Node\Expr\Variable::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\Variable $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (is_string($node->name)) {
			return [];
		}

		return [
			'Variable variables are not allowed.',
		];
	}

}
