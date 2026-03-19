<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClosureNode;
use PHPStan\Reflection\ParametersAcceptor;

/**
 * @implements \PHPStan\Rules\Rule<InClosureNode>
 */
class TypeHintDeprecatedInClosureSignatureRule implements \PHPStan\Rules\Rule
{

	/** @var DeprecatedClassHelper */
	private $deprecatedClassHelper;

	public function __construct(DeprecatedClassHelper $deprecatedClassHelper)
	{
		$this->deprecatedClassHelper = $deprecatedClassHelper;
	}

	public function getNodeType(): string
	{
		return InClosureNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		$functionSignature = $scope->getAnonymousFunctionReflection();
		if (!$functionSignature instanceof ParametersAcceptor) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		$errors = [];
		foreach ($functionSignature->getParameters() as $i => $parameter) {
			$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($parameter->getType()->getReferencedClasses());
			foreach ($deprecatedClasses as $deprecatedClass) {
				$errors[] = sprintf(
					'Parameter $%s of anonymous function has typehint with deprecated %s %s%s',
					$parameter->getName(),
					$this->deprecatedClassHelper->getClassType($deprecatedClass),
					$deprecatedClass->getName(),
					$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
				);
			}
		}

		$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($functionSignature->getReturnType()->getReferencedClasses());
		foreach ($deprecatedClasses as $deprecatedClass) {
			$errors[] = sprintf(
				'Return type of anonymous function has typehint with deprecated %s %s%s',
				$this->deprecatedClassHelper->getClassType($deprecatedClass),
				$deprecatedClass->getName(),
				$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
			);
		}

		return $errors;
	}

}
