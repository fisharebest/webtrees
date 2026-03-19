<?php declare(strict_types = 1);

namespace PHPStan\Rules\StrictCalls;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;

class DynamicCallOnStaticMethodsRule implements \PHPStan\Rules\Rule
{

	/** @var \PHPStan\Rules\RuleLevelHelper */
	private $ruleLevelHelper;

	public function __construct(RuleLevelHelper $ruleLevelHelper)
	{
		$this->ruleLevelHelper = $ruleLevelHelper;
	}

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\MethodCall $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->name instanceof Node\Identifier) {
			return [];
		}

		$name = $node->name->name;
		$type = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$node->var,
			'',
			function (Type $type) use ($name): bool {
				return $type->canCallMethods()->yes() && $type->hasMethod($name)->yes();
			}
		)->getType();

		if ($type instanceof ErrorType || !$type->canCallMethods()->yes() || !$type->hasMethod($name)->yes()) {
			return [];
		}

		$methodReflection = $type->getMethod($name, $scope);
		if ($methodReflection->isStatic()) {
			return [sprintf(
				'Dynamic call to static method %s::%s().',
				$methodReflection->getDeclaringClass()->getDisplayName(),
				$methodReflection->getName()
			)];
		}

		return [];
	}

}
