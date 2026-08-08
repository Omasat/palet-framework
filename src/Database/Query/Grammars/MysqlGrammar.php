<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Query\Grammars;

class MysqlGrammar extends Grammar
{
    protected function wrapValue(string $value): string
    {
        if ($value === '*') {
            return $value;
        }

        return '`' . str_replace('`', '``', $value) . '`';
    }
}
