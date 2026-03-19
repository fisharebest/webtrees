<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;

/**
 * @implements \PHPStan\Rules\Rule<ClassConstFetch>
 */
class FetchingClassConstOfDeprecatedClassRule implements \PHPStan\Rules\Rule
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
		return ClassConstFetch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!$node->name instanceof Identifier) {
			return [];
		}

		$constantName = $node->name->name;
		$referencedClasses = [];

		if ($node->class instanceof Name) {
			$referencedClasses[] = $scope->resolveName($node->class);
		} else {
			$classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$node->class,
				'', // We don't care about the error message
				function (Type $type) use ($constantName): bool {
					return $type->canAccessConstants()->yes() && $type->hasConstant($constantName)->yes();
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

			if ($class->isDeprecated()) {
				$classDescription = $class->getDeprecatedDescription();
				if ($classDescription === null) {
					$errors[] = sprintf(
						'Fetching class constant %s of deprecated class %s.',
						$constantName,
						$referencedClass
					);
				} else {
					$errors[] = sprintf(
						"Fetching class constant %s of deprecated class %s:\n%s",
						$constantName,
						$referencedClass,
						$classDescription
					);
				}
			}

			if (strtolower($constantName) === 'class') {
				continue;
			}

			if (!$class->hasConstant($constantName)) {
				continue;
			}

			$constantReflection = $class->getConstant($constantName);

			if (!$constantReflection->isDeprecated()->yes()) {
				continue;
			}

			$description = $constantReflection->getDeprecatedDescription();
			if ($description === null) {
				$errors[] = sprintf(
					'Fetching deprecated class constant %s of class %s.',
					$constantName,
					$referencedClass
				);
			} else {
				$errors[] = sprintf(
					"Fetching deprecated class constant %s of class %s:\n%s",
					$constantName,
					$referencedClass,
					$description
				);
			}
		}

		return $errors;
	}

}
