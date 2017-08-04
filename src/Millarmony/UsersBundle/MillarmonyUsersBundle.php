<?php

namespace Millarmony\UsersBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MillarmonyUsersBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
