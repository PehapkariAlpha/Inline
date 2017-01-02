<?php

declare(strict_types=1);

namespace Pehapkari\InlineEditable\Tests\Integration\PersistentLayer;

use Pehapkari\InlineEditable\Model\PersistenceLayerInterface;
use Tester\Assert;
use Tester\TestCase;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
abstract class BaseTest extends TestCase
{
    /**
     * @var PersistenceLayerInterface
     */
    protected $persistentLayer;

    /**
     * Init persistent layer - ex. connection to db
     */
    abstract protected function initPersistentLayer();

    /**
     *
     */
    public function setUp()
    {
        $this->initPersistentLayer();
    }

    public function testGetNamespaceContent()
    {
        $data = $this->persistentLayer->getNamespaceContent('spaceX', 'cs');
        Assert::type('array', $data);
    }

    public function testSaveContent()
    {
        $result = $this->persistentLayer->saveContent('spaceX', 'nameZ', 'cs', 'contentZ');
        Assert::same(true, $result);
    }
}
