<?php

declare(strict_types = 1);

namespace Pehapkari\InlineEditable\Tests\Integration\PersistentLayer;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Pehapkari\InlineEditable\Model\PersistenceLayer\Dbal;
use Tester\Environment;

require __DIR__ . '/../../bootstrap.php';
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
