<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;

/**
 * @implements \PHPStan\Rules\Rule<New_>
 */
class InstantiationOfDeprecatedClassRule implements \PHPStan\Rules\Rule
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
		return New_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		$referencedClasses = [];

		if ($node->class instanceof Name) {
			$referencedClasses[] = $scope->resolveName($node->class);
		} elseif ($node->class instanceof Class_) {
			if (!isset($node->class->namespacedName)) {
				return [];
			}

			$referencedClasses[] = $scope->resolveName($node->class->namespacedName);
		} else {
			$classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$node->class,
				'', // We don't care about the error message
				function (): bool {
					return true;
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
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				continue;
			}

			if (!$class->isDeprecated()) {
				continue;
			}

			$description = $class->getDeprecatedDescription();
			if ($description === null) {
				$errors[] = sprintf(
					'Instantiation of deprecated class %s.',
					$referencedClass
				);
			} else {
				$errors[] = sprintf(
					"Instantiation of deprecated class %s:\n%s",
					$referencedClass,
					$description
				);
			}
		}

		return $errors;
	}

}
