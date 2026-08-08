<?php

declare(strict_types=1);

namespace Palet\Framework\View\Events;

use Palet\Framework\Contracts\View\ViewInterface;

class ViewRendering
{
    public ViewInterface $view;

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }
}
