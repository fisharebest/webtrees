<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements \PHPStan\Rules\Rule<Class_>
 */
class InheritanceOfDeprecatedClassRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getNodeType(): string
	{
		return Class_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if ($node->extends === null) {
			return [];
		}

		$errors = [];

		$className = isset($node->namespacedName)
			? (string) $node->namespacedName
			: (string) $node->name;

		try {
			$class = $this->reflectionProvider->getClass($className);
		} catch (\PHPStan\Broker\ClassNotFoundException $e) {
			return [];
		}

		$parentClassName = (string) $node->extends;

		try {
			$parentClass = $this->reflectionProvider->getClass($parentClassName);
			$description = $parentClass->getDeprecatedDescription();
			if ($parentClass->isDeprecated()) {
				if (!$class->isAnonymous()) {
					if ($description === null) {
						$errors[] = sprintf(
							'Class %s extends deprecated class %s.',
							$className,
							$parentClassName
						);
					} else {
						$errors[] = sprintf(
							"Class %s extends deprecated class %s:\n%s",
							$className,
							$parentClassName,
							$description
						);
					}
				} else {
					if ($description === null) {
						$errors[] = sprintf(
							'Anonymous class extends deprecated class %s.',
							$parentClassName
						);
					} else {
						$errors[] = sprintf(
							"Anonymous class extends deprecated class %s:\n%s",
							$parentClassName,
							$description
						);
					}
				}
			}
		} catch (\PHPStan\Broker\ClassNotFoundException $e) {
			// Other rules will notify if the interface is not found
		}

		return $errors;
	}

}
