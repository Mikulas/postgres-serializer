<?php

namespace Mikulas\PostgresSerializer;

use Nette\Utils\TokenizerException;


class CompositeTypeException extends \InvalidArgumentException implements Exception
{

	/** @var string */
	private $input;


	/**
	 * @internal
	 * @param string     $input
	 * @param string     $message
	 * @param \Exception $previous
	 */
	public function __construct($input, $message, \Exception $previous = NULL)
	{
		$this->input = $input;

		$message .= "\nInput: '$input'";
		parent::__construct($message, NULL, $previous);
	}


	public static function tokenizerFailure(string $input, TokenizerException $e): CompositeTypeException
	{
		return self::malformedInput($input, 'Failed during tokenization.', $e);
	}


	public static function openFailed(string $input, \Exception $previous = NULL): CompositeTypeException
	{
		return self::malformedInput($input, "Expected '(' as first token.", $previous);
	}


	public static function mismatchedParens(string $input, \Exception $previous = NULL): CompositeTypeException
	{
		return self::malformedInput($input, "Expected ')' as last token.", $previous);
	}


	public static function malformedInput(string $input, $reason = '', \Exception $previous = NULL): CompositeTypeException
	{
		return new self($input, "Malformed input, expected recursive '( val1 , val2 , ... )' syntax."
			. ($reason ? " $reason" : ''), $previous);
	}

}
