<?php

namespace Mikulas\PostgresSerializer;

use Nette\Utils\TokenizerException;


class PostgresArrayException extends \InvalidArgumentException implements Exception
{

	/**
	 * @internal
	 * @param string     $message
	 * @param \Exception $previous
	 */
	public function __construct($message, \Exception $previous = NULL)
	{
		parent::__construct($message, NULL, $previous);
	}


	public static function tokenizerFailure(TokenizerException $e): PostgresArrayException
	{
		return self::malformedInput('Failed during tokenization.', $e);
	}


	public static function openFailed(\Exception $previous = NULL): PostgresArrayException
	{
		return self::malformedInput("Expected '{' as first token.", $previous);
	}


	public static function mismatchedBrackets(\Exception $previous = NULL): PostgresArrayException
	{
		return self::malformedInput("Expected '}' as last token.", $previous);
	}


	public static function malformedInput($reason = '', \Exception $previous = NULL): PostgresArrayException
	{
		return new self("Malformed input, expected recursive '{ val1 delim val2 delim ... }' syntax."
			. ($reason ? " $reason" : ''), $previous);
	}

}
