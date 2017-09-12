<?php
namespace Shopex\LubanSite\Coroutine;

class Task
{
    protected $id;
    protected $coroutine;
    protected $exception;
    protected $child;

    public function __construct($id, \Generator $coroutine)
    {
        $this->id = $id;
        $this->step = 0;
        $this->coroutine = $coroutine;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function run()
    {
        $retValue = $this->coroutine->current();
        $this->coroutine->send($retValue);
    }

    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}