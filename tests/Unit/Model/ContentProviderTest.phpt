<?php

declare(strict_types = 1);

namespace Pehapkari\InlineEditable\Tests\Unit\Model;

use Pehapkari\InlineEditable\Model\ContentProvider;
use Pehapkari\InlineEditable\Tests\Mock\Cache;
use Pehapkari\InlineEditable\Tests\Mock\PersistenceLayer;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../Mock/CacheItem.php';
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
        $nsKey = ContentProvider::getNamespaceKey('space', 'cs');
        Assert::equal('__inline_prefix_space.cs', $nsKey);
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
    public function testSuccessGetHitL1()
    {
        $this->db->setData(['space.cs' => ['name' => 'test content db']]);
        $content = $this->contentProvider->getContent('space', 'cs', 'name');
        Assert::same('test content db', $content);

        $this->cache->data = [];
        $this->db->data = [];
        $content = $this->contentProvider->getContent('space', 'cs', 'name');
        Assert::same('test content db', $content);
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

    /**
     *
     */
    public function testSaveContent()
    {
        $this->contentProvider->saveContent('space', 'cs', 'name', 'content string');
        Assert::same([], $this->cache->data);
        Assert::same(['space.cs' => ['name' => 'content string']], $this->db->data);
        $content = $this->contentProvider->getContent('space', 'cs', 'name');
        Assert::same('content string', $content);
    }
}

(new ContentProviderTest)->run();
