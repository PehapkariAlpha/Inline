<?php

declare(strict_types = 1);

namespace Pehapkari\InlineEditable\Tests\Mock;

use Pehapkari\InlineEditable\Model\PersistenceLayerInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final class PersistenceLayer implements PersistenceLayerInterface
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param string $namespace
     * @param string $locale
     * @return array - output format ["name" => "value", ...]
     */
    public function getNamespaceContent(string $namespace, string $locale): array
    {
        return $this->data[$namespace . '.' . $locale] ?? [];
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param string $locale
     * @param string $content
     * @return bool - TRUE on success or FALSE on failure
     */
    public function saveContent(string $namespace, string $name, string $locale, string $content): bool
    {
        $this->data[$namespace . '.' . $locale][$name] = $content;
        return true;
    }

    /**
     * For debug
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
