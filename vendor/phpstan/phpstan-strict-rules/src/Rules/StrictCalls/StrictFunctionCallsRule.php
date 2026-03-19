<?php declare(strict_types = 1);

namespace PHPStan\Rules\StrictCalls;

use PHPStan\Type\Constant\ConstantBooleanType;

class StrictFunctionCallsRule implements \PHPStan\Rules\Rule
{

	/** @var int[] */
	private $functionArguments = [
		'in_array' => 2,
		'array_search' => 2,
		'base64_decode' => 1,
		'array_keys' => 2,
	];

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	public function __construct(\PHPStan\Broker\Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\FuncCall::class;
	}

	/**
	 * @param \PhpParser\Node\Expr\FuncCall $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
	{
		if (!$node->name instanceof \PhpParser\Node\Name) {
			return [];
		}

		$functionName = $this->broker->resolveFunctionName($node->name, $scope);
		if ($functionName === null) {
			return [];
		}
		$functionName = strtolower($functionName);
		if (!array_key_exists($functionName, $this->functionArguments)) {
			return [];
		}

		if ($functionName === 'array_keys' && !array_key_exists(1, $node->getArgs())) {
			return [];
		}

		$argumentPosition = $this->functionArguments[$functionName];
		if (!array_key_exists($argumentPosition, $node->getArgs())) {
			return [sprintf('Call to function %s() requires parameter #%d to be set.', $functionName, $argumentPosition + 1)];
		}

		$argumentType = $scope->getType($node->getArgs()[$argumentPosition]->value);
		$trueType = new ConstantBooleanType(true);
		if (!$trueType->isSuperTypeOf($argumentType)->yes()) {
			return [sprintf('Call to function %s() requires parameter #%d to be true.', $functionName, $argumentPosition + 1)];
		}

		return [];
	}

}
