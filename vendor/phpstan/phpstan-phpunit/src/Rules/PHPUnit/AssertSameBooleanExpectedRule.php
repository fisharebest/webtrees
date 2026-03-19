<?php declare(strict_types = 1);

namespace PHPStan\Rules\PHPUnit;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Constant\ConstantBooleanType;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\NodeAbstract>
 */
class AssertSameBooleanExpectedRule implements \PHPStan\Rules\Rule
{

	public function getNodeType(): string
	{
		return \PhpParser\NodeAbstract::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!AssertRuleHelper::isMethodOrStaticCallOnAssert($node, $scope)) {
			return [];
		}

		/** @var \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node */
		$node = $node;

		if (count($node->getArgs()) < 2) {
			return [];
		}
		if (!$node->name instanceof Node\Identifier || strtolower($node->name->name) !== 'assertsame') {
			return [];
		}

		$leftType = $scope->getType($node->getArgs()[0]->value);
		if (!$leftType instanceof ConstantBooleanType) {
			return [];
		}

		if ($leftType->getValue()) {
			return [
				'You should use assertTrue() instead of assertSame() when expecting "true"',
			];
		}

		return [
			'You should use assertFalse() instead of assertSame() when expecting "false"',
		];
	}

}
