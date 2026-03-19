<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;

class DeprecatedClassHelper
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getClassType(ClassReflection $class): string
	{
		if ($class->isInterface()) {
			return 'interface';
		}

		return 'class';
	}

	public function getClassDeprecationDescription(ClassReflection $class): string
	{
		$description = $class->getDeprecatedDescription();
		if ($description === null) {
			return '.';
		}

		return sprintf(":\n%s", $description);
	}

	/**
	 * @param string[] $referencedClasses
	 * @return ClassReflection[]
	 */
	public function filterDeprecatedClasses(array $referencedClasses): array
	{
		$deprecatedClasses = [];
		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->reflectionProvider->getClass($referencedClass);
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				continue;
			}

			if (!$class->isDeprecated()) {
				continue;
			}

			$deprecatedClasses[] = $class;
		}

		return $deprecatedClasses;
	}

}
