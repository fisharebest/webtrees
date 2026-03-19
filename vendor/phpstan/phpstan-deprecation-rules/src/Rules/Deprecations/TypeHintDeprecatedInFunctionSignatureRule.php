<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InFunctionNode;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;

/**
 * @implements \PHPStan\Rules\Rule<InFunctionNode>
 */
class TypeHintDeprecatedInFunctionSignatureRule implements \PHPStan\Rules\Rule
{

	/** @var DeprecatedClassHelper */
	private $deprecatedClassHelper;

	public function __construct(DeprecatedClassHelper $deprecatedClassHelper)
	{
		$this->deprecatedClassHelper = $deprecatedClassHelper;
	}

	public function getNodeType(): string
	{
		return InFunctionNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		$function = $scope->getFunction();
		if (!$function instanceof FunctionReflection) {
			throw new \PHPStan\ShouldNotHappenException();
		}
		$functionSignature = ParametersAcceptorSelector::selectSingle($function->getVariants());

		$errors = [];
		foreach ($functionSignature->getParameters() as $i => $parameter) {
			$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($parameter->getType()->getReferencedClasses());
			foreach ($deprecatedClasses as $deprecatedClass) {
				$errors[] = sprintf(
					'Parameter $%s of function %s() has typehint with deprecated %s %s%s',
					$parameter->getName(),
					$function->getName(),
					$this->deprecatedClassHelper->getClassType($deprecatedClass),
					$deprecatedClass->getName(),
					$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
				);
			}
		}

		$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($functionSignature->getReturnType()->getReferencedClasses());
		foreach ($deprecatedClasses as $deprecatedClass) {
			$errors[] = sprintf(
				'Return type of function %s() has typehint with deprecated %s %s%s',
				$function->getName(),
				$this->deprecatedClassHelper->getClassType($deprecatedClass),
				$deprecatedClass->getName(),
				$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
			);
		}

		return $errors;
	}

}
