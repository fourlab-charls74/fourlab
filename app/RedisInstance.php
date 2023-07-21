<?php
namespace App;

use Illuminate\Support\Facades\Redis;
class RedisInstance extends Redis
{

	private static $singleton;

	public static function getInstance() {
		if(!isset(RedisInstance::$singleton)) RedisInstance::$singleton = self::connection(); 
        return RedisInstance::$singleton; 
    }
	// method
	public function set($key, $value)
	{
		$this->singleton->client()->set($key, $value, 'EX', 3600 * 24);
	}

	public function get($key)
	{
		return $this->singleton->client()->get($key);
	}

	public function del($key)
	{
		$this->singleton->client()->del($key);
	}
	
}
