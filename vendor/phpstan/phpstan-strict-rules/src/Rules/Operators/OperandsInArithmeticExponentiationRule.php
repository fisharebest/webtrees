<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

use PHPStan\Type\VerbosityLevel;

class OperandsInArithmeticExponentiationRule implements \PHPStan\Rules\Rule
{

	/** @var OperatorRuleHelper */
	private $helper;

	public function __construct(OperatorRuleHelper $helper)
	{
		$this->helper = $helper;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\BinaryOp\Pow::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\BinaryOp\BooleanAnd $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
	{
		$messages = [];
		$leftType = $scope->getType($node->left);
		if (!$this->helper->isValidForArithmeticOperation($scope, $node->left)) {
			$messages[] = sprintf(
				'Only numeric types are allowed in **, %s given on the left side.',
				$leftType->describe(VerbosityLevel::typeOnly())
			);
		}

		$rightType = $scope->getType($node->right);
		if (!$this->helper->isValidForArithmeticOperation($scope, $node->right)) {
			$messages[] = sprintf(
				'Only numeric types are allowed in **, %s given on the right side.',
				$rightType->describe(VerbosityLevel::typeOnly())
			);
		}

		return $messages;
	}

}
