<?php 
class RedisStorage {
    private $redis;
    private $isConnected = false;

    public function __construct() {
        $this->redis = new Redis();
        try {
            $this->isConnected = $this->redis->connect('127.0.0.1', 6379);
            if (!$this->isConnected) {
                throw new RedisException("Unable to connect to Redis.");
            }
        } catch (RedisException $e) {
            $this->isConnected = false;
            error_log("Redis connection failed: " . $e->getMessage());
        }
    }

    public function set($key, $value) {
        if ($this->isConnected) {
            return $this->redis->set($key, $value);
        }
        return false;
    }

    public function get($key) {
        if ($this->isConnected) {
            return $this->redis->get($key);
        }
        return false;
    }

    public function isConnected() {
        return $this->isConnected;
    }


    public function getRedisInstance() {
        if ($this->isConnected) {
            return $this->redis;
        }
        return null;
    }
}
?>
