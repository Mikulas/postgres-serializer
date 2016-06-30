<?php

/**
 * @testCase
 */

namespace Mikulas\Tests\PostgresSerializer;

use Mikulas\PostgresSerializer\CompositeType;
use Tester\Assert;
use Tester\TestCase;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class CompositeTypeTest extends TestCase
{

	/**
	 * @return array [$sql, $php]
	 */
	public function provideCases()
	{
		return [
			['(pre,)', ['pre', NULL]],
			['(,post)', [NULL, 'post']],

			['(a)', ['a']],

			['("q""o")', ['q"o']],
			['("par)en")', ['par)en']],

			['( chars )', [' chars ']],
			['(42)', [42]],

			['((lat,lng),radius)', [['lat', 'lng'], 'radius']],
		];
	}


	/**
	 * @dataProvider provideCases
	 */
	public function testParse($sql, $php)
	{
		Assert::same($php, CompositeType::parse($sql));
	}


	public function testParseAlternativeQuoteSyntax()
	{
		Assert::same(['q"o'], CompositeType::parse('("q\\"o")'));
	}


	public function testParseAlternativeNullSyntax()
	{
		Assert::same(NULL, CompositeType::parse('()'));
		Assert::same(NULL, CompositeType::parse('(,,,)'));
	}


	/**
	 * @dataProvider provideCases
	 */
	public function testSerialize($sql, $php)
	{
		Assert::same($sql, CompositeType::serialize($php));
	}

}


(new CompositeTypeTest($dic))->run();
