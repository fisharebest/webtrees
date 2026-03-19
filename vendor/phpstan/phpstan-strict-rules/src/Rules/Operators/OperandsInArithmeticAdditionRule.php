<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\VerbosityLevel;

class OperandsInArithmeticAdditionRule implements \PHPStan\Rules\Rule
{

	/** @var OperatorRuleHelper */
	private $helper;

	public function __construct(OperatorRuleHelper $helper)
	{
		$this->helper = $helper;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\BinaryOp\Plus::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\BinaryOp\BooleanAnd $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
	{
		$leftType = $scope->getType($node->left);
		$rightType = $scope->getType($node->right);
		$mixedArrayType = new ArrayType(new MixedType(), new MixedType());

		if ($mixedArrayType->isSuperTypeOf($leftType)->yes() && $mixedArrayType->isSuperTypeOf($rightType)->yes()) {
			return [];
		}

		$messages = [];
		if (!$this->helper->isValidForArithmeticOperation($scope, $node->left)) {
			$messages[] = sprintf(
				'Only numeric types are allowed in +, %s given on the left side.',
				$leftType->describe(VerbosityLevel::typeOnly())
			);
		}
		if (!$this->helper->isValidForArithmeticOperation($scope, $node->right)) {
			$messages[] = sprintf(
				'Only numeric types are allowed in +, %s given on the right side.',
				$rightType->describe(VerbosityLevel::typeOnly())
			);
		}

		return $messages;
	}

}
