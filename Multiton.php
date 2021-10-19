<?php

namespace Creational;


trait Multiton {

	use Singleton;

	protected static $instance = [];

	/**
	 * @return static
	 */
	public static function getInstance()
	{
		return static::getNamedInstance();
	}

	public static function getNamedInstance($key = '__DEFAULT__')
	{
		if (!isset(static::$instance[$key])) {
			static::$instance[$key] = (new \ReflectionClass(get_called_class()))
				->newInstanceWithoutConstructor();
		}

		return static::$instance[$key];
	}
}