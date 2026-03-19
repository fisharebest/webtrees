<?php declare(strict_types = 1);

namespace PHPStan\Rules\VariableVariables;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;

class VariableMethodCallRule implements Rule
{

	public function getNodeType(): string
	{
		return Node\Expr\MethodCall::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\MethodCall $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->name instanceof Node\Identifier) {
			return [];
		}

		return [
			sprintf(
				'Variable method call on %s.',
				$scope->getType($node->var)->describe(VerbosityLevel::typeOnly())
			),
		];
	}

}
