<?php
declare(strict_types=1);

namespace Ctefan\Redux\Action;

interface ActionInterface
{
    /**
     * Get the action type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get the action payload.
     *
     * @return mixed
     */
    public function getPayload();
}