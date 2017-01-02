<?php

namespace Pehapkari\Inline\Tests\Unit\Model;

use Pehapkari\Inline\Model\ContentProvider;
use Pehapkari\Inline\Tests\Mock\Cache;
use Pehapkari\Inline\Tests\Mock\PersistenceLayer;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../Mock/Cache.php';
require __DIR__ . '/../../Mock/PersistenceLayer.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ContentProviderTest extends TestCase
{
    /**
     * @var ContentProvider
     */
    protected $contentProvider;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var PersistenceLayer
     */
    protected $db;

    /**
     *
     */
    public function setUp()
    {
        $this->cache = $cache = new Cache;
        $this->db = $persistenceLayer = new PersistenceLayer;
        $this->contentProvider = new ContentProvider([], $cache, $persistenceLayer);
    }

    /**
     *
     */
    public function testGeneratingNKey()
    {
        $nKey = ContentProvider::getNKey('space', 'cs');
        Assert::equal('__inline_prefix_space.cs', $nKey);
    }

    /**
     *
     */
    public function testSuccessGetFromDb()
    {
        $this->db->setData(['space.cs' => ['name' => 'test content db']]);
        $content = $this->contentProvider->getContent('space', 'cs', 'name');
        Assert::same('test content db', $content);
    }

    /**
     *
     */
    public function testSuccessGetFromCache()
    {
        $this->cache->setData(['space.cs' => ['name' => 'test content cache']]);
        $content = $this->contentProvider->getContent('space', 'cs', 'name');
        Assert::same('test content cache', $content);
    }

    /**
     *
     */
    public function testNotFoundGetFromDb()
    {
        $this->db->setData(['space.cs' => ['name' => 'test content']]);
        $this->cache->setData(['space.cs' => ['name' => 'test content']]);
        $content = $this->contentProvider->getContent('space', 'en', 'name');
        Assert::same('', $content);
    }

    /**
     *
     */
    public function testLevelGet()
    {
        $this->db->setData(['space.cs' => ['name' => 'test content DB']]);
        $this->cache->setData(['space.cs' => ['name' => 'test content CACHE']]);
        $content = $this->contentProvider->getContent('space', 'cs', 'name');
        Assert::same('test content CACHE', $content);
    }

    /**
     *
     */
    public function testFallbackGet()
    {
        $this->db->setData(['space.en' => ['name' => 'test content DB']]);
        $contentProvider = new ContentProvider(['fallback' => 'en'], $this->cache, $this->db);
        $content = $contentProvider->getContent('space', 'cs', 'name');
        Assert::same('test content DB', $content);
    }
}

(new ContentProviderTest)->run();
