<?php

namespace Gos\Bundle\NotificationBundle\Redis;

use Predis\Command\ScriptedCommand;

class IndexOfElement extends ScriptedCommand
{
    /**
     * @return int
     */
    protected function getKeysCount()
    {
        return 3;
    }

    /**
     * Gets the body of a Lua script.
     *
     * lidxof notification:user:username uuid my-super-uuid
     *
     * @return string
     */
    public function getScript()
    {
        return <<<LUA
local key = KEYS[1]
local lkey = KEYS[2]
local lid = KEYS[3]
local items = redis.call('lrange', key, 0, -1)

for i=1,#items do
    if cjson.decode(items[i])[lkey] == lid then
        return i
    end
end

return -1
LUA;
    }
}
