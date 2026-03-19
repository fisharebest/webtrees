<?php declare(strict_types = 1);

namespace PHPStan\Rules\DisallowedConstructs;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

class DisallowedBacktickRule implements \PHPStan\Rules\Rule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\ShellExec::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\Empty_ $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return [
			'Backtick operator is not allowed. Use shell_exec() instead.',
		];
	}

}
