<?php declare(strict_types=1);

namespace App\Utils;

use Closure;

class Callback
{

	/** @var Closure */
	private $callback;

	/** @var array */
	private $args;

	/** @var array */
	private $binds = [];

	/** @var array */
	private $bindsEnd = [];

	/**
	 * Callback constructor.
	 * @param callable $callback
	 * @param array $args
	 */
	public function __construct(callable $callback, ...$args)
	{
		$this->callback = Closure::fromCallable($callback);
		$this->args = $args;
	}

	public static function from(callable $callable, ...$args): self
	{
		return new self($callable, ...$args);
	}

	public function bind(...$args): self
	{
		array_push($this->binds, ...$args);
		return $this;
	}

	public function bindEnd(...$args): self
	{
		array_push($this->bindsEnd, ...$args);
		return $this;
	}

	public function __invoke(...$args)
	{
		return call_user_func_array($this->callback, array_merge($this->binds, $this->args, $args, $this->bindsEnd));
	}

	public function call($newThis, ...$args)
	{
		return $this->callback->call($newThis, ...array_merge($this->binds, $this->args, $args, $this->bindsEnd));
	}

}
