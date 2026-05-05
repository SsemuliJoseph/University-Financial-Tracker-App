<?php
// models/Cache.php
// Simple file-based caching system

class Cache
{
    private $cacheDir;

    public function __construct($cacheDir = 'cache/')
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get($key)
    {
        $file = $this->cacheDir . md5($key) . '.cache';
        if (file_exists($file) && (time() - filemtime($file) < 3600)) { // 1 hour expiry
            return unserialize(file_get_contents($file));
        }
        return false;
    }

    public function set($key, $data)
    {
        $file = $this->cacheDir . md5($key) . '.cache';
        file_put_contents($file, serialize($data));
    }

    public function delete($key)
    {
        $file = $this->cacheDir . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}