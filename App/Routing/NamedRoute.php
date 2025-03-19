<?php

namespace App\Routing;

use Symfony\Component\Routing\Route;

class NamedRoute extends Route
{
    protected $name;
    protected $middleware = [];

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function middleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }
}