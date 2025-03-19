<?php

namespace App\Support;

class Collection
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function all()
    {
        return $this->items;
    }

    public function __get($key)
    {
        if ($key === 'items') {
            return $this->items;
        }
        return null;
    }

    public function __toString()
    {
        return (string) json_encode($this->items);
    }
}
