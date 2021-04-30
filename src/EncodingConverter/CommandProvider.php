<?php

namespace Citrus\EncodingConverter;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability
{
    public static function getClass()
    {
        return get_called_class();
    }

    public function getCommands()
    {
        return array(new Command);
    }
}
