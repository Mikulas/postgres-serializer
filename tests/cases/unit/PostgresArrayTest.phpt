<?php

/**
 * @testCase
 */

namespace Mikulas\Tests\PostgresSerializer;

use DateTimeImmutable;
use DateTimeZone;
use Mikulas\PostgresSerializer\PostgresArray;
use Mikulas\PostgresSerializer\PostgresArrayException;
use Tester\Assert;
use Tester\TestCase;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class PostgresArrayTest extends TestCase
{

	public function testParseString()
	{
		$toString = function($partial) {
			return $partial === NULL ? NULL : (string) $partial;
		};

		Assert::same(NULL, PostgresArray::parse(NULL, $toString));
		Assert::same([], PostgresArray::parse('{}', $toString));
		Assert::same(['a', 'b'], PostgresArray::parse('{"a","b"}', $toString));

		Assert::same(['q"o', 'trims'], PostgresArray::parse('{"q\"o",  trims  }', $toString));

		Assert::same(['a', NULL, 'b'], PostgresArray::parse('{"a",NULL,"b"}', $toString));
	}


	public function testParseDateTime()
	{
		$toDate = function($partial) {
			return $partial === NULL ? NULL : new DateTimeImmutable($partial);
		};

		/** @var DateTimeImmutable[] $parsed */
		$parsed = PostgresArray::parse('{"2015-01-01 10:11:12","2015-02-02 12:13:14"}', $toDate);

		Assert::count(2, $parsed);
		Assert::type(DateTimeImmutable::class, $parsed[0]);
		Assert::type(DateTimeImmutable::class, $parsed[1]);
		Assert::same('2015-01-01 10:11:12', $parsed[0]->format('Y-m-d H:i:s'));
		Assert::same('2015-02-02 12:13:14', $parsed[1]->format('Y-m-d H:i:s'));
	}


	public function testParseNested()
	{
		$toNumber = function($partial) {
			return $partial === NULL ? NULL : (int) $partial;
		};

		Assert::same([[1, 2], [3, 4, 5]], PostgresArray::parse('{{1,2},{3,4,5}}', $toNumber));
	}


	public function testParseFails()
	{
		$id = function($a) {return $a;};

		Assert::exception(function() use ($id) {
			PostgresArray::parse('pre {}', $id);
		}, PostgresArrayException::class, '~first token~i');

		Assert::exception(function() use ($id) {
			PostgresArray::parse('{} post', $id);
		}, PostgresArrayException::class, '~last token~i');

		Assert::exception(function() use ($id) {
			PostgresArray::parse('{1,,2} post', $id);
		}, PostgresArrayException::class);

		Assert::exception(function() use ($id) {
			PostgresArray::parse('{ " }', $id);
		}, PostgresArrayException::class, '~Malformed~i');
	}


	public function testSerializeString()
	{
		$fromString = function($partial) {
			return $partial === NULL ? NULL : '"' . str_replace('"', '\\"', $partial) . '"';
		};

		Assert::same(NULL, PostgresArray::serialize(NULL, $fromString));
		Assert::same('{}', PostgresArray::serialize([], $fromString));
		Assert::same('{"a",NULL,"b"}', PostgresArray::serialize(['a', NULL, 'b'], $fromString));
		Assert::same('{" spaces ","q\\"o"}', PostgresArray::serialize([' spaces ', 'q"o'], $fromString));
	}


	public function testSerializeDate()
	{
		$fromDate = function(DateTimeImmutable $partial) {
			if ($partial === NULL) {
				return NULL;
			}
			$normalized = $partial->setTimezone(new DateTimeZone(date_default_timezone_get()));
			return '"' . $normalized->format('Y-m-d H:i:s') . '"';
		};

		$dates = [
			new DateTimeImmutable('2015-01-01 10:11:12'),
			new DateTimeImmutable('2015-02-02 12:13:14'),
		];
		Assert::same('{"2015-01-01 10:11:12","2015-02-02 12:13:14"}', PostgresArray::serialize($dates, $fromDate));
	}


	public function testSerializeNested()
	{
		$fromNumber = function($partial) {
			return $partial === NULL ? NULL : (int) $partial;
		};

		Assert::same('{{1,2},{3,4,5}}', PostgresArray::serialize([[1, 2], [3, 4, 5]], $fromNumber));

		Assert::same(NULL, PostgresArray::serialize([], $fromNumber, TRUE));
		Assert::same('{}', PostgresArray::serialize([], $fromNumber, FALSE));
	}

}


(new PostgresArrayTest($dic))->run();
