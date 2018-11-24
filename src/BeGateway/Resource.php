<?php

namespace BeGateway;

class Resource
{
    /**
     * @var array the list of loaded resources.
     */
    private static $resources;

    /**
     * @param string $name the resource name
     * @return array
     */
    public function get($name)
    {
        if (!isset(self::$resources[$name])) {
            self::$resources[$name] = $this->loadResource($name);
        }

        return self::$resources[$name];
    }

    /**
     * @param string
     * @return array
     */
    private function loadResource($name)
    {
        $file = __DIR__ . "/../../resources/{$name}.php";

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException("Failed to load {$name} resource.");
    }
}
