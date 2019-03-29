<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\TestKit;

class Spy
{
  private $result = [];
 
  public function apply($value)
  {
    $this->result[] = $value;
  }
  public function calls()
  {
    return $this->result;
  }
}