<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements \PHPStan\Rules\Rule<Interface_>
 */
class InheritanceOfDeprecatedInterfaceRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getNodeType(): string
	{
		return Interface_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->extends === null) {
			return [];
		}

		$interfaceName = isset($node->namespacedName)
			? (string) $node->namespacedName
			: (string) $node->name;

		try {
			$interface = $this->reflectionProvider->getClass($interfaceName);
		} catch (\PHPStan\Broker\ClassNotFoundException $e) {
			return [];
		}

		if ($interface->isDeprecated()) {
			return [];
		}

		$errors = [];

		foreach ($node->extends as $parentInterfaceName) {
			$parentInterfaceName = (string) $parentInterfaceName;

			try {
				$parentInterface = $this->reflectionProvider->getClass($parentInterfaceName);

				if (!$parentInterface->isDeprecated()) {
					continue;
				}

				$description = $parentInterface->getDeprecatedDescription();
				if ($description === null) {
					$errors[] = sprintf(
						'Interface %s extends deprecated interface %s.',
						$interfaceName,
						$parentInterfaceName
					);
				} else {
					$errors[] = sprintf(
						"Interface %s extends deprecated interface %s:\n%s",
						$interfaceName,
						$parentInterfaceName,
						$description
					);
				}
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				// Other rules will notify if the interface is not found
			}
		}

		return $errors;
	}

}
