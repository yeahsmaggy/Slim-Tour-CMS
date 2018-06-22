<?php
declare(strict_types=1);
namespace Src\Transformers;
abstract class AbstractTransformer
{
    public function collection(array $records)
    {
        return array_map([$this, 'item'], $records);
    }
    abstract public function item($record);
}