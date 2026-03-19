<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements \PHPStan\Rules\Rule<TraitUse>
 */
class UsageOfDeprecatedTraitRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getNodeType(): string
	{
		return TraitUse::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		$classReflection = $scope->getClassReflection();
		if ($classReflection === null) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		$errors = [];
		$className = $classReflection->getName();

		foreach ($node->traits as $traitNameNode) {
			$traitName = (string) $traitNameNode;

			try {
				$trait = $this->reflectionProvider->getClass($traitName);
				if (!$trait->isDeprecated()) {
					continue;
				}

				$description = $trait->getDeprecatedDescription();
				if ($description === null) {
					$errors[] = sprintf(
						'Usage of deprecated trait %s in class %s.',
						$traitName,
						$className
					);
				} else {
					$errors[] = sprintf(
						"Usage of deprecated trait %s in class %s:\n%s",
						$traitName,
						$className,
						$description
					);
				}
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				continue;
			}
		}

		return $errors;
	}

}
