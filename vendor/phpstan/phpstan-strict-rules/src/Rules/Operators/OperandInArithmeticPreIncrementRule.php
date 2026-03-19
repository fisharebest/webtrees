<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

class OperandInArithmeticPreIncrementRule extends OperandInArithmeticIncrementOrDecrementRule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\PreInc::class;
	}

	protected function describeOperation(): string
	{
		return 'pre-increment';
	}

}
