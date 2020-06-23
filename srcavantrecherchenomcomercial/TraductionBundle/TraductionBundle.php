<?php

namespace TraductionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TraductionBundle extends Bundle
{
     public function getParent()
    {
        return 'LexikTranslationBundle';
    }
}
