<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Table;

use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\ConfigStorage\RelationCleanup;
use PhpMyAdmin\Controllers\Table\DropColumnController;
use PhpMyAdmin\FlashMessages;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;

/**
 * @covers \PhpMyAdmin\Controllers\Table\DropColumnController
 */
class DropColumnControllerTest extends AbstractTestCase
{
    public function testDropColumnController(): void
    {
        $GLOBALS['db'] = 'test_db';
        $GLOBALS['table'] = 'test_table';
        $_POST = [
            'db' => 'test_db',
            'table' => 'test_table',
            'selected' => ['name', 'datetimefield'],
            'mult_btn' => 'Yes',
        ];
        $_SESSION = [' PMA_token ' => 'token'];

        $this->dummyDbi->addSelectDb('test_db');
        $this->dummyDbi->addResult('ALTER TABLE `test_table` DROP `name`, DROP `datetimefield`;', []);

        $this->assertArrayNotHasKey('flashMessages', $_SESSION);

        (new DropColumnController(
            new ResponseRenderer(),
            new Template(),
            $this->dbi,
            new FlashMessages(),
            new RelationCleanup($this->dbi, new Relation($this->dbi))
        ))();

        $this->assertArrayHasKey('flashMessages', $_SESSION);
        /** @psalm-suppress InvalidArrayOffset */
        $this->assertSame(['success' => ['2 columns have been dropped successfully.']], $_SESSION['flashMessages']);
    }
}
