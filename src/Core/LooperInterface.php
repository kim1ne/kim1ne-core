<?php

namespace Kim1ne\Core;

use React\EventLoop\LoopInterface;

/**
 * The general interface for the event-loop of component
 *
 * For that interface written the trait
 * @see EventLoopTrait
 */
interface LooperInterface
{
    public function setLoop(LoopInterface $loop): static;

    public function getLoop(): LoopInterface;

    public function run(): void;

    public function stop(): void;

    public function dispatchEvent(string $eventName, Event $event): void;

    public function on(string $eventName, callable $callback): static;

    public function getScopeName(): string;
}