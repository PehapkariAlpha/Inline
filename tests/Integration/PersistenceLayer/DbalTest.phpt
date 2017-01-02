<?php

namespace Pehapkari\Inline\Tests\Integration\PersistentLayer;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Pehapkari\Inline\Model\PersistenceLayer\Dbal;
use Tester\Environment;

require __DIR__ . '/BaseTest.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class DbalTest extends BaseTest
{
    /**
     *
     */
    protected function initPersistentLayer()
    {
        $params = Environment::loadData();
        $connection = DriverManager::getConnection($params, new Configuration);

        $this->persistentLayer = new Dbal('inline_content', $connection);
    }
}

(new DbalTest)->run();
