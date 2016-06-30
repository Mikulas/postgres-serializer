<?php

namespace Mikulas\Tests\PostgresSerializer;

use Tester\Environment;


if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo "Install dependenies using `composer update`\n";
	exit(1);
}

Environment::setup();
