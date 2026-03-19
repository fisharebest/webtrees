<?php declare(strict_types = 1);

namespace PHPStan\Rules\Operators;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\ErrorType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

class OperatorRuleHelper
{

	/** @var \PHPStan\Rules\RuleLevelHelper */
	private $ruleLevelHelper;

	public function __construct(RuleLevelHelper $ruleLevelHelper)
	{
		$this->ruleLevelHelper = $ruleLevelHelper;
	}

	public function isValidForArithmeticOperation(Scope $scope, Expr $expr): bool
	{
		$type = $scope->getType($expr);
		if ($type instanceof MixedType) {
			return true;
		}

		// already reported by PHPStan core
		if ($type->toNumber() instanceof ErrorType) {
			return true;
		}

		return $this->isSubtypeOfNumber($scope, $expr);
	}

	public function isValidForIncrementOrDecrement(Scope $scope, Expr $expr): bool
	{
		$type = $scope->getType($expr);
		if ($type instanceof MixedType) {
			return true;
		}

		return $this->isSubtypeOfNumber($scope, $expr);
	}

	private function isSubtypeOfNumber(Scope $scope, Expr $expr): bool
	{
		$acceptedType = new UnionType([new IntegerType(), new FloatType(), new IntersectionType([new StringType(), new AccessoryNumericStringType()])]);

		$type = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$expr,
			'',
			function (Type $type) use ($acceptedType): bool {
				return $acceptedType->isSuperTypeOf($type)->yes();
			}
		)->getType();

		if ($type instanceof ErrorType) {
			return true;
		}

		$isSuperType = $acceptedType->isSuperTypeOf($type);
		if ($type instanceof \PHPStan\Type\BenevolentUnionType) {
			return !$isSuperType->no();
		}

		return $isSuperType->yes();
	}

}
