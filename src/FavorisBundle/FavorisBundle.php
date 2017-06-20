<?php

namespace FavorisBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FavorisBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
