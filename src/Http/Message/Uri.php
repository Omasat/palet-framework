<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Message;

use Palet\Framework\Contracts\Http\Message\UriInterface;
use InvalidArgumentException;

class Uri implements UriInterface
{
    protected string $scheme = '';
    protected string $userInfo = '';
    protected string $host = '';
    protected ?int $port = null;
    protected string $path = '';
    protected string $query = '';
    protected string $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            $parts = parse_url($uri);
            if ($parts === false) {
                throw new InvalidArgumentException("Unable to parse URI: {$uri}");
            }
            
            $this->scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
            $this->userInfo = $parts['user'] ?? '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
            $this->host = isset($parts['host']) ? strtolower($parts['host']) : '';
            $this->port = $parts['port'] ?? null;
            $this->path = $parts['path'] ?? '/';
            if ($this->path === '') {
                $this->path = '/';
            }
            $this->query = $parts['query'] ?? '';
            $this->fragment = $parts['fragment'] ?? '';
        }
    }

    public function getScheme(): string { return $this->scheme; }
    public function getAuthority(): string
    {
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }
    public function getUserInfo(): string { return $this->userInfo; }
    public function getHost(): string { return $this->host; }
    public function getPort(): ?int { return $this->port; }
    public function getPath(): string { return $this->path; }
    public function getQuery(): string { return $this->query; }
    public function getFragment(): string { return $this->fragment; }
    
    public function withScheme(string $scheme): static
    {
        $new = clone $this;
        $new->scheme = strtolower($scheme);
        return $new;
    }
    
    public function withUserInfo(string $user, ?string $password = null): static
    {
        $new = clone $this;
        $new->userInfo = $user;
        if ($password !== null) {
            $new->userInfo .= ':' . $password;
        }
        return $new;
    }
    
    public function withHost(string $host): static
    {
        $new = clone $this;
        $new->host = strtolower($host);
        return $new;
    }
    
    public function withPort(?int $port): static
    {
        $new = clone $this;
        $new->port = $port;
        return $new;
    }
    
    public function withPath(string $path): static
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }
    
    public function withQuery(string $query): static
    {
        $new = clone $this;
        $new->query = ltrim($query, '?');
        return $new;
    }
    
    public function withFragment(string $fragment): static
    {
        $new = clone $this;
        $new->fragment = ltrim($fragment, '#');
        return $new;
    }
    
    public function __toString(): string
    {
        $uri = '';
        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }
        
        $authority = $this->getAuthority();
        if ($authority !== '') {
            $uri .= '//' . $authority;
        }
        
        if ($this->path !== '') {
            if ($authority !== '' && !str_starts_with($this->path, '/')) {
                $uri .= '/' . $this->path;
            } else {
                $uri .= $this->path;
            }
        }
        
        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }
        
        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }
        
        return $uri;
    }
}
