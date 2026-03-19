<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements \PHPStan\Rules\Rule<FuncCall>
 */
class CallToDeprecatedFunctionRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getNodeType(): string
	{
		return FuncCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!($node->name instanceof \PhpParser\Node\Name)) {
			return [];
		}

		try {
			$function = $this->reflectionProvider->getFunction($node->name, $scope);
		} catch (\PHPStan\Broker\FunctionNotFoundException $e) {
			// Other rules will notify if the function is not found
			return [];
		}

		if ($function->isDeprecated()->yes()) {
			$description = $function->getDeprecatedDescription();
			if ($description === null) {
				return [sprintf(
					'Call to deprecated function %s().',
					$function->getName()
				)];
			}

			return [sprintf(
				"Call to deprecated function %s():\n%s",
				$function->getName(),
				$description
			)];
		}

		return [];
	}

}
