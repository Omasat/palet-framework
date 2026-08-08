<?php

declare(strict_types=1);

namespace Palet\Framework\Container\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
