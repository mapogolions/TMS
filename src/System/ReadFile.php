<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\System\{ SystemCall };
use Mapogolions\Suspendable\{ Scheduler, Task, StopIteration };

class ReadFile extends SystemCall
{
  private $fileName;
  private $mode;

  public function __construct(string $fileName, string $mode="r")
  {
    $this->fileName = $fileName;
    $this->mode = $mode;
  }

  private function readable()
  {
    $file = \fopen($this->fileName, $this->mode);
    if (!isset($file)) {
      throw new StopIteration();
    }
    try {
      while (!\feof($file)) {
        $data = \fgets($file);
        echo $data;
        yield $data;
      }
    } finally {
      \fclose($file);
    }
    return true;
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $tid = $scheduler->spawn($this->readable());
    $task->setValue($tid);
    $scheduler->schedule($task);
  }
}