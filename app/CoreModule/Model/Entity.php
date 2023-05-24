<?php declare(strict_types = 1);

namespace App\CoreModule\Model;

class Entity implements \ArrayAccess, \IteratorAggregate, \Countable
{

	protected int $id;


	final public function count(): int
	{
		return count((array) $this);
	}


	final public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this);
	}


	final public function offsetSet($offset, $value): void
	{
		$this->$offset = $value;
	}


	final public function offsetGet($offset)
	{
		return $this->$offset;
	}


	final public function offsetExists($offset): bool
	{
		return isset($this->$offset);
	}


	final public function offsetUnset($offset): void
	{
		unset($this->$offset);
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function setId(int $id): void
	{
		$this->id = $id;
	}


	public function toArray()
	{
		$array = [];

		$reflection = new \ReflectionObject($this);
		$properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);

		foreach ($properties as $property) {
			$getterName = 'get' . \ucfirst($property->getName());

			if ($reflection->hasMethod($getterName) && $reflection->getMethod($getterName)->isPublic()) {
				$getter = $reflection->getMethod($getterName);
				$propertyValue = $getter->invoke($this);

				$array[$property->getName()] = $propertyValue;
			}
		}

		return $array;
	}

}
