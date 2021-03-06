<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\{ Task, Scheduler };

final class GetTid extends SystemCall
{
    public function handle(Task $task, Scheduler $scheduler)
    {
        $task->update($task->tid());
        $scheduler->schedule($task);
    }

    public function __toString()
    {
        return "<system call> GetTid";
    }
}
