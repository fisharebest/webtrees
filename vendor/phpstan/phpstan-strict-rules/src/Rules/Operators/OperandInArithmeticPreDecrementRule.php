<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

class OperandInArithmeticPreDecrementRule extends OperandInArithmeticIncrementOrDecrementRule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\PreDec::class;
	}

	protected function describeOperation(): string
	{
		return 'pre-decrement';
	}

}
