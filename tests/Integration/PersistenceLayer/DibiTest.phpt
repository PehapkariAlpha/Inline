<?php

namespace Pehapkari\Inline\Tests\Integration\PersistentLayer;

use Dibi\Connection;
use Pehapkari\Inline\Model\PersistenceLayer\Dibi;
use Tester\Environment;

require __DIR__ . '/BaseTest.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class DibiTest extends BaseTest
{
    /**
     *
     */
    protected function initPersistentLayer()
    {
        $params = Environment::loadData();
        $params['database'] = $params['dbname'];

        if ($params['driver'] === 'pdo_mysql') {
            $params['driver'] = 'mysqli';
        } elseif ($params['driver'] === 'pdo_pgsql') {
            $params['driver'] = 'postgre';
            $params['string'] = 'host=' . $params['host'] . ' port=' . $params['port'] . ' dbname=' . $params['dbname'];
        }

        $connection = new Connection($params);
        $this->persistentLayer = new Dibi('inline_content', $connection);
    }
}

(new DibiTest)->run();
