<?php
declare(strict_types=1);

namespace Ctefan\Redux\Action;

class Action implements ActionInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * Action constructor.
     *
     * @param string $type
     * @param null $payload
     */
    public function __construct(string $type, $payload = null)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    /**
     * Get the action type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the action payload.
     *
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}