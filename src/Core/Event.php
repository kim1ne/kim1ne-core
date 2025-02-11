<?php

namespace Kim1ne\Core;

class Event
{
    public function __construct(
        public readonly array $parameters
    ) {}

    public function get(string $parameterName): mixed
    {
        return $this->parameters[$parameterName] ?? null;
    }
}