<?php

namespace Kim1ne\Core;

use Kim1ne\Loop\EventEmitter;
use Kim1ne\Loop\Server as LoopServer;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

trait EventLoopTrait
{
    /**
     * @var <string, callable>[]
     */
    private array $events = [];

    private bool $isRun = false;

    private ?LoopInterface $loop = null;

    public function __destruct()
    {
        $this->stop();
    }

    public function getLoop(): LoopInterface
    {
        if ($this->loop === null) {
            $this->loop = Loop::get();
        }

        return $this->loop;
    }

    public function setLoop(LoopInterface $loop): static
    {
        if ($this->isRun) {
            throw new \RuntimeException($this->getScopeName() . ' is already run.');
        }

        $this->loop = $loop;
        return $this;
    }

    public function isRun(): bool
    {
        return $this->isRun;
    }

    public function on(string $eventName, callable $callback): static
    {
        $this->events[$eventName] = $callback;

        return $this;
    }

    public function stopLoop(): void
    {
        $this->isRun = false;

        if (
            !class_exists(LoopServer::class) ||
            LoopServer::isStart() === false
        ) {
            $this->getLoop()->stop();
        } else {
            LoopServer::destroy($this);
        }
    }

    public function runLoop(): void
    {
        $this->isRun = true;

        if (
            !class_exists(LoopServer::class) ||
            LoopServer::isStart() === false
        ) {
            $this->getLoop()->run();
        }
    }

    private function call(string $eventName, ...$params): static
    {
        try {
            if (!empty($callback = $this->events[$eventName] ?? null)) {
                call_user_func_array($callback, $params);
            }
        } catch (\Throwable $throwable) {
            try {
                $this->call('error', $throwable);
            } catch (\Throwable $throwable) {

            }
        }

        return $this;
    }

    public function dispatchEvent(string $eventName, Event $event): void
    {
        if (!class_exists(EventEmitter::class)) {
            return;
        }

        EventEmitter::dispatch(
            $this->prepareEventName($eventName),
            $event
        );
    }

    private function prepareEventName(string $eventName): string
    {
        return $this->getScopeName() . ':' . $eventName;
    }

    abstract public function getScopeName(): string;
}