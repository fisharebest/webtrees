<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

use PHPStan\Rules\Rule;
use PHPStan\Type\VerbosityLevel;

abstract class OperandInArithmeticIncrementOrDecrementRule implements Rule
{

	/** @var OperatorRuleHelper */
	private $helper;

	public function __construct(OperatorRuleHelper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * @param \PhpParser\Node\Expr\PreInc|\PhpParser\Node\Expr\PreDec|\PhpParser\Node\Expr\PostInc|\PhpParser\Node\Expr\PostDec $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
	{
		$messages = [];
		$varType = $scope->getType($node->var);

		if (!$this->helper->isValidForIncrementOrDecrement($scope, $node->var)) {
			$messages[] = sprintf(
				'Only numeric types are allowed in %s, %s given.',
				$this->describeOperation(),
				$varType->describe(VerbosityLevel::typeOnly())
			);
		}

		return $messages;
	}

	abstract protected function describeOperation(): string;

}
