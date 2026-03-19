<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;

/**
 * @implements \PHPStan\Rules\Rule<StaticCall>
 */
class CallToDeprecatedStaticMethodRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	public function __construct(ReflectionProvider $reflectionProvider, RuleLevelHelper $ruleLevelHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->ruleLevelHelper = $ruleLevelHelper;
	}

	public function getNodeType(): string
	{
		return StaticCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!$node->name instanceof Identifier) {
			return [];
		}

		$methodName = $node->name->name;
		$referencedClasses = [];

		if ($node->class instanceof Name) {
			$referencedClasses[] = $scope->resolveName($node->class);
		} else {
			$classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$node->class,
				'', // We don't care about the error message
				function (Type $type) use ($methodName): bool {
					return $type->canCallMethods()->yes() && $type->hasMethod($methodName)->yes();
				}
			);

			if ($classTypeResult->getType() instanceof ErrorType) {
				return [];
			}

			$referencedClasses = $classTypeResult->getReferencedClasses();
		}

		$errors = [];

		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->reflectionProvider->getClass($referencedClass);
				$methodReflection = $class->getMethod($methodName, $scope);
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				continue;
			} catch (\PHPStan\Reflection\MissingMethodFromReflectionException $e) {
				continue;
			}

			if ($class->isDeprecated()) {
				$classDescription = $class->getDeprecatedDescription();
				if ($classDescription === null) {
					$errors[] = sprintf(
						'Call to method %s() of deprecated class %s.',
						$methodReflection->getName(),
						$methodReflection->getDeclaringClass()->getName()
					);
				} else {
					$errors[] = sprintf(
						"Call to method %s() of deprecated class %s:\n%s",
						$methodReflection->getName(),
						$methodReflection->getDeclaringClass()->getName(),
						$classDescription
					);
				}
			}

			if (!$methodReflection->isDeprecated()->yes()) {
				continue;
			}

			$description = $methodReflection->getDeprecatedDescription();
			if ($description === null) {
				$errors[] = sprintf(
					'Call to deprecated method %s() of class %s.',
					$methodReflection->getName(),
					$methodReflection->getDeclaringClass()->getName()
				);
			} else {
				$errors[] = sprintf(
					"Call to deprecated method %s() of class %s:\n%s",
					$methodReflection->getName(),
					$methodReflection->getDeclaringClass()->getName(),
					$description
				);
			}
		}

		return $errors;
	}

}
