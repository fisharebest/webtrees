<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements \PHPStan\Rules\Rule<ConstFetch>
 */
class FetchingDeprecatedConstRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var array<string,string> */
	private $deprecatedConstants = [];

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;

		// phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
		if (PHP_VERSION_ID >= 70300) {
			$this->deprecatedConstants['FILTER_FLAG_SCHEME_REQUIRED'] = 'Use of constant %s is deprecated since PHP 7.3.';
			$this->deprecatedConstants['FILTER_FLAG_HOST_REQUIRED'] = 'Use of constant %s is deprecated since PHP 7.3.';
		}
	}

	public function getNodeType(): string
	{
		return ConstFetch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!$this->reflectionProvider->hasConstant($node->name, $scope)) {
			return [];
		}

		$constantReflection = $this->reflectionProvider->getConstant($node->name, $scope);
		$defaultMessage = 'Use of constant %s is deprecated.';

		if ($constantReflection->isDeprecated()->yes()) {
			return [sprintf(
				$constantReflection->getDeprecatedDescription() ?? $defaultMessage,
				$constantReflection->getName()
			)];
		}

		if (isset($this->deprecatedConstants[$constantReflection->getName()])) {
			return [sprintf(
				$this->deprecatedConstants[$constantReflection->getName()] ?? $defaultMessage,
				$constantReflection->getName()
			)];
		}

		return [];
	}

}
