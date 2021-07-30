<?php

declare (strict_types=1);
namespace SMTP2GOWPPlugin\PhpParser\Node\Scalar\MagicConst;

use SMTP2GOWPPlugin\PhpParser\Node\Scalar\MagicConst;
class File extends MagicConst
{
    public function getName() : string
    {
        return '__FILE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_File';
    }
}
