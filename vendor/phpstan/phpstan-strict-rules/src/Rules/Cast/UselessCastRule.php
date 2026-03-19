<?php declare(strict_types = 1);

namespace PHPStan\Rules\Cast;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ErrorType;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\VerbosityLevel;

class UselessCastRule implements \PHPStan\Rules\Rule
{

	/** @var bool */
	private $treatPhpDocTypesAsCertain;

	public function __construct(bool $treatPhpDocTypesAsCertain)
	{
		$this->treatPhpDocTypesAsCertain = $treatPhpDocTypesAsCertain;
	}

	public function getNodeType(): string
	{
		return Cast::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\Cast $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return RuleError[] errors
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$castType = $scope->getType($node);
		if ($castType instanceof ErrorType) {
			return [];
		}
		$castType = TypeUtils::generalizeType($castType, GeneralizePrecision::lessSpecific());

		if ($this->treatPhpDocTypesAsCertain) {
			$expressionType = $scope->getType($node->expr);
		} else {
			$expressionType = $scope->getNativeType($node->expr);
		}
		if ($castType->isSuperTypeOf($expressionType)->yes()) {
			$addTip = function (RuleErrorBuilder $ruleErrorBuilder) use ($scope, $node, $castType): RuleErrorBuilder {
				if (!$this->treatPhpDocTypesAsCertain) {
					return $ruleErrorBuilder;
				}

				$expressionTypeWithoutPhpDoc = $scope->getNativeType($node->expr);
				if ($castType->isSuperTypeOf($expressionTypeWithoutPhpDoc)->yes()) {
					return $ruleErrorBuilder;
				}

				return $ruleErrorBuilder->tip('Because the type is coming from a PHPDoc, you can turn off this check by setting <fg=cyan>treatPhpDocTypesAsCertain: false</> in your <fg=cyan>%configurationFile%</>.');
			};
			return [
				$addTip(RuleErrorBuilder::message(sprintf(
					'Casting to %s something that\'s already %s.',
					$castType->describe(VerbosityLevel::typeOnly()),
					$expressionType->describe(VerbosityLevel::typeOnly())
				)))->build(),
			];
		}

		return [];
	}

}
