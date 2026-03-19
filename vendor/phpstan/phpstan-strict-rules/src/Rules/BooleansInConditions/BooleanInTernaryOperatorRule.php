<?php declare(strict_types = 1);

namespace PHPStan\Rules\BooleansInConditions;

use PHPStan\Type\VerbosityLevel;

class BooleanInTernaryOperatorRule implements \PHPStan\Rules\Rule
{

	/** @var BooleanRuleHelper */
	private $helper;

	public function __construct(BooleanRuleHelper $helper)
	{
		$this->helper = $helper;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\Ternary::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\Ternary $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
	{
		if ($node->if === null) {
			return []; // elvis ?:
		}

		if ($this->helper->passesAsBoolean($scope, $node->cond)) {
			return [];
		}

		$conditionExpressionType = $scope->getType($node->cond);

		return [
			sprintf(
				'Only booleans are allowed in a ternary operator condition, %s given.',
				$conditionExpressionType->describe(VerbosityLevel::typeOnly())
			),
		];
	}

}
