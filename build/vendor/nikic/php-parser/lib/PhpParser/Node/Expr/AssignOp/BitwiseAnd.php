<?php

declare (strict_types=1);
namespace SMTP2GOWPPlugin\PhpParser\Node\Expr\AssignOp;

use SMTP2GOWPPlugin\PhpParser\Node\Expr\AssignOp;
class BitwiseAnd extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseAnd';
    }
}