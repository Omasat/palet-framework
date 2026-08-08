<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Query\Grammars;

class PostgresGrammar extends Grammar
{
    // PostgreSQL generally follows the standard standard ANSI quotes ("), 
    // which is the default in the base Grammar class.
    
    // We can add Postgres specific overrides here (like returning ids on insert).
}
