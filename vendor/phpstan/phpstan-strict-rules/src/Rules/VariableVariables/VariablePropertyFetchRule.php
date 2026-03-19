<?php declare(strict_types = 1);

namespace PHPStan\Rules\VariableVariables;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\VerbosityLevel;

class VariablePropertyFetchRule implements Rule
{

	/** @var Broker */
	private $broker;

	/** @var string[] */
	private $universalObjectCratesClasses;

	/**
	 * @param Broker $broker
	 * @param string[] $universalObjectCratesClasses
	 */
	public function __construct(Broker $broker, array $universalObjectCratesClasses)
	{
		$this->broker = $broker;
		$this->universalObjectCratesClasses = $universalObjectCratesClasses;
	}

	public function getNodeType(): string
	{
		return Node\Expr\PropertyFetch::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\PropertyFetch $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->name instanceof Node\Identifier) {
			return [];
		}

		$fetchedOnType = $scope->getType($node->var);
		foreach (TypeUtils::getDirectClassNames($fetchedOnType) as $referencedClass) {
			if (!$this->broker->hasClass($referencedClass)) {
				continue;
			}

			if ($this->isUniversalObjectCrate($this->broker->getClass($referencedClass))) {
				return [];
			}
		}

		return [
			sprintf(
				'Variable property access on %s.',
				$fetchedOnType->describe(VerbosityLevel::typeOnly())
			),
		];
	}

	private function isUniversalObjectCrate(
		ClassReflection $classReflection
	): bool
	{
		foreach ($this->universalObjectCratesClasses as $className) {
			if (!$this->broker->hasClass($className)) {
				continue;
			}

			if (
				$classReflection->getName() === $className
				|| $classReflection->isSubclassOf($className)
			) {
				return true;
			}
		}

		return false;
	}

}
