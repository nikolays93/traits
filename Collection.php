<?php

namespace ORM;

trait Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array $items
     */
    protected $items = array();

    /**
     * Class constructor.
     *
     * @param array $items
     */
    public function __construct($items = array())
    {
        $this->add($items);
    }

    /**
     * Return all items as array.
     *
     * @return array
     */
    public function fetch()
    {
        return $this->items;
    }

    /**
     * Get item by index
     *
     * @param $offset
     *
     * @return mixed
     */
    public function get($offset)
    {
        if (isset($this->items[$offset])) {
            return $this->items[$offset];
        }

        return null;
    }

    /**
     * Add item to collection.
     *
     * @param array|object $item
     *
     * @return $this Collection
     */
    public function add($item)
    {
        if (is_array($item) || $item instanceof \Traversable) {
            array_walk($item, [$this, 'add'])
        } else {
            array_push($this->items, $item);
        }

        return $this;
    }

    /**
     * @param object $item
     *
     * @return bool|array
     */
    public function has($item)
    {
        return array_search($item, $this->items);
    }

    /**
     * @param object[]|object $items
     *
     * @return $this Collection
     */
    public function replace($items)
    {
        if (is_array($items) || $items instanceof \Traversable) {
            foreach ($items as $item) {
                $this->replace($item);
            }

            return $this;
        }

        if (($position = $this->has($items)) !== false) {
            $this->offsetSet($position, $items);
        } else {
            $this->add($items);
        }

        return $this;
    }

    /**
     * @param object[]|object $items
     *
     * @return $this Collection
     */
    public function remove($items)
    {
        if (is_array($items) || $items instanceof \Traversable) {
            array_walk($items, [$this, 'remove'])
        } else {
            if (($position = $this->has($items)) !== false) {
                array_splice($this->items, $position, 1);
            }
        }

        return $this;
    }

    /**
     * Check collection for empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @param $cb
     *
     * @return $this Collection
     */
    public function usort(\Closure $cb)
    {
        if (is_callable($cb)) {
            usort($this->items, $cb);
        }

        return $this;
    }

    /**
     * @param $cb
     *
     * @return Collection
     */
    public function filter(\Closure $cb)
    {
        return new static(array_filter($this->items, $cb));
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return array_shift(array_values($this->items));
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]) || array_key_exists($offset, $this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        if (isset($this->items[$offset])) {
            return $this->items[$offset];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            return $this->add($value);
        }

        return $this->items[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        if (isset($this->items[$offset]) || array_key_exists($offset, $this->items)) {
            $removed = $this->items[$offset];
            unset($this->items[$offset]);

            return $removed;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->items);
    }
}