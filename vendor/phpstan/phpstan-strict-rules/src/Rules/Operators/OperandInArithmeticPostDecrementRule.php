<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

class OperandInArithmeticPostDecrementRule extends OperandInArithmeticIncrementOrDecrementRule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\PostDec::class;
	}

	protected function describeOperation(): string
	{
		return 'post-decrement';
	}

}
