<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ GetTid, NewTask };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class NewTaskTest extends TestCase
{
  public function testSequentialExecuctionOfTwoTasks()
  {
    $spy = new Spy();
    $inner = TestKit::trackedAsDataProducer(TestKit::countdown(4), $spy);
    $outer = (function () use ($inner) {
      $tid = yield new GetTid();
      yield $tid;
      $child = yield new NewTask($inner);
    })();
    $pl = Scheduler::of(
      TestKit::trackedAsDataProducer($outer, $spy, TestKit::ignoreSystemCalls())
    );
    $pl->launch();
    $this->assertEquals([1, 4, 3, 2, 1], $spy->calls());
  }

  public function testOverlappingBetweenTwoTasks()
  {
    $spy = new Spy();
    $inner= TestKit::trackedAsDataProducer(TestKit::countdown(5), $spy);
    $outer = (function () use ($inner) {
      yield "start";
      $child = yield new NewTask($inner);
      yield "task $child is spawned";
      yield "end";
    })();
    $pl = Scheduler::of(TestKit::trackedAsDataProducer($outer, $spy, TestKit::ignoreSystemCalls()));
    $pl->launch();
    $this->assertEquals(
      ["start", 5, "task 2 is spawned", 4, "end", 3, 2, 1],
      $spy->calls()
    );
  }
}