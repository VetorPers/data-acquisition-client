<?php

namespace ReportAgent\Entity;

/**
 * 实体基类
 */
class Entity
{
    public mixed $metadata;

    /**
     * @param mixed $metadata 元数.
     */
    public function __construct(mixed $metadata)
    {
        $this->metadata = $metadata;

        $metaArray = is_array($metadata) ? $metadata : [];
        if (is_string($metadata)) {
            $metaArray = json_decode($metadata, true);
            $metaArray = is_array($metaArray) ? $metaArray : [];
        }

        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, ['metadata', 'metaArray'], true)) {
                continue;
            }

            if (!isset($metaArray[$key])) {
                continue;
            }

            $v = $metaArray[$key];
            $ok = settype($v, gettype($this->{$key}));
            $this->{$key} = $ok ? $v : $value;
        }
    }

    /**
     * 转json
     * @return string
     * @author xionglin
     */
    public function toJson(): string
    {
        $array = get_object_vars($this);
        unset($array['metadata']);

        $string = json_encode($array);
        return is_string($string) ? $string : '';
    }

    /**
     * @return array
     * @author xionglin
     */
    public function toArray(): array
    {
        $array = get_object_vars($this);
        unset($array['metadata']);

        return $array;
    }
}
