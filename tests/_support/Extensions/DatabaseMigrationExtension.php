<?php
declare(strict_types = 1);

namespace Extensions;

use Codeception\Events;
use Codeception\Extension;
use Codeception\Module\Cli;

/**
 * Class DatabaseMigrationExtension
 */
class DatabaseMigrationExtension extends Extension {
	public static $events = [
		Events::SUITE_BEFORE => 'beforeSuite',
	];

	public function beforeSuite() {
		/** @var Cli $cli */
		$cli = $this->getModule('Cli');
		$alias = __DIR__.'/../../_app/yii';
		$cli->runShellCommand("php $alias migrate/fresh --interactive=0");
		$cli->runShellCommand("php $alias migrate/up --migrationPath=./migrations --interactive=0");
		$cli->runShellCommand("php $alias migrate/up --migrationPath=@vendor/pozitronik/yii2-users-options/migrations --interactive=0");
	}
}