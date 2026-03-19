<?php declare(strict_types = 1);

namespace PHPStan\Rules\DisallowedConstructs;

use PHPStan\Analyser\Scope;

class DisallowedImplicitArrayCreationRule implements \PHPStan\Rules\Rule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\Assign::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\Assign $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(\PhpParser\Node $node, Scope $scope): array
	{
		if (!$node->var instanceof \PhpParser\Node\Expr\ArrayDimFetch) {
			return [];
		}

		$node = $node->var;
		while ($node instanceof \PhpParser\Node\Expr\ArrayDimFetch) {
			$node = $node->var;
		}

		if (!$node instanceof \PhpParser\Node\Expr\Variable) {
			return [];
		}

		if (!is_string($node->name)) {
			return [];
		}

		$certainty = $scope->hasVariableType($node->name);
		if ($certainty->no()) {
			return [
				sprintf('Implicit array creation is not allowed - variable $%s does not exist.', $node->name),
			];
		}

		if ($certainty->maybe()) {
			return [
				sprintf('Implicit array creation is not allowed - variable $%s might not exist.', $node->name),
			];
		}

		return [];
	}

}
