<?php declare(strict_types = 1);

namespace PHPStan\Rules\BooleansInConditions;

use PHPStan\Type\VerbosityLevel;

class BooleanInBooleanNotRule implements \PHPStan\Rules\Rule
{

	/** @var BooleanRuleHelper */
	private $helper;

	public function __construct(BooleanRuleHelper $helper)
	{
		$this->helper = $helper;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\BooleanNot::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\BooleanNot $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
	{
		if ($this->helper->passesAsBoolean($scope, $node->expr)) {
			return [];
		}

		$expressionType = $scope->getType($node->expr);

		return [
			sprintf(
				'Only booleans are allowed in a negated boolean, %s given.',
				$expressionType->describe(VerbosityLevel::typeOnly())
			),
		];
	}

}
