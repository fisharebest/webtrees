<?php declare(strict_types = 1);

namespace PHPStan\Rules\BooleansInConditions;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\BooleanType;
use PHPStan\Type\ErrorType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

class BooleanRuleHelper
{

	/** @var \PHPStan\Rules\RuleLevelHelper */
	private $ruleLevelHelper;

	public function __construct(RuleLevelHelper $ruleLevelHelper)
	{
		$this->ruleLevelHelper = $ruleLevelHelper;
	}

	public function passesAsBoolean(Scope $scope, Expr $expr): bool
	{
		$type = $scope->getType($expr);
		if ($type instanceof MixedType) {
			return !$type->isExplicitMixed();
		}
		$typeToCheck = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$expr,
			'',
			function (Type $type): bool {
				return $type instanceof BooleanType;
			}
		);
		$foundType = $typeToCheck->getType();
		if ($foundType instanceof ErrorType) {
			return true;
		}

		return $foundType instanceof BooleanType;
	}

}
