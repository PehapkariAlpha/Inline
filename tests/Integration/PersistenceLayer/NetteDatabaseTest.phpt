<?php

namespace Pehapkari\Inline\Tests\Integration\PersistentLayer;

use Nette\Database\Connection;
use Pehapkari\Inline\Model\PersistenceLayer\NetteDatabase;
use Tester\Environment;

require __DIR__ . '/BaseTest.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class NetteDatabaseTest extends BaseTest
{
    /**
     *
     */
    protected function initPersistentLayer()
    {
        $params = Environment::loadData();
        $connection = new Connection($params['dsn'], $params['user'], $params['password']);
        $this->persistentLayer = new NetteDatabase('inline_content', $connection);
    }
}

(new NetteDatabaseTest)->run();
