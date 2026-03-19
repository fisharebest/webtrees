<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

class OperandInArithmeticPostIncrementRule extends OperandInArithmeticIncrementOrDecrementRule
{

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\PostInc::class;
	}

	protected function describeOperation(): string
	{
		return 'post-increment';
	}

}
