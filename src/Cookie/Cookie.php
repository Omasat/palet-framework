<?php

declare(strict_types=1);

namespace Palet\Framework\Cookie;

use Stringable;

class Cookie implements Stringable
{
    protected string $name;
    protected string $value;
    protected int $expires;
    protected string $path;
    protected string $domain;
    protected bool $secure;
    protected bool $httpOnly;
    protected bool $raw;
    protected string $sameSite;

    public function __construct(string $name, string $value = '', int $minutes = 0, ?string $path = null, ?string $domain = null, ?bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expires = $minutes > 0 ? time() + ($minutes * 60) : 0;
        $this->path = $path ?: '/';
        $this->domain = $domain ?: '';
        $this->secure = $secure ?? false;
        $this->httpOnly = $httpOnly;
        $this->raw = $raw;
        $this->sameSite = $sameSite ?: 'Lax';
    }

    public function getName(): string { return $this->name; }
    public function getValue(): string { return $this->value; }
    
    public function __toString(): string
    {
        $str = urlencode($this->name) . '=' . urlencode($this->value);
        if ($this->expires !== 0) {
            $str .= '; expires=' . gmdate('D, d-M-Y H:i:s T', $this->expires);
        }
        $str .= '; path=' . $this->path;
        if ($this->domain) {
            $str .= '; domain=' . $this->domain;
        }
        if ($this->secure) {
            $str .= '; secure';
        }
        if ($this->httpOnly) {
            $str .= '; httponly';
        }
        $str .= '; samesite=' . $this->sameSite;
        
        return $str;
    }
}
