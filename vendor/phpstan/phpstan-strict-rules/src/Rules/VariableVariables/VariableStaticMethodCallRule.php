<?php declare(strict_types = 1);

namespace PHPStan\Rules\VariableVariables;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;

class VariableStaticMethodCallRule implements Rule
{

	public function getNodeType(): string
	{
		return Node\Expr\StaticCall::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\StaticCall $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->name instanceof Node\Identifier) {
			return [];
		}

		if ($node->class instanceof Node\Name) {
			$methodCalledOn = $scope->resolveName($node->class);
		} else {
			$methodCalledOn = $scope->getType($node->class)->describe(VerbosityLevel::typeOnly());
		}

		return [
			sprintf(
				'Variable static method call on %s.',
				$methodCalledOn
			),
		];
	}

}
