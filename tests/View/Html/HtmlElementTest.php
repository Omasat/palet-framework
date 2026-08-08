<?php

declare(strict_types=1);

namespace Tests\View\Html;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Html\HtmlElement;
use Palet\Framework\View\Html\HtmlString;

class HtmlElementTest extends TestCase
{
    public function test_renders_basic_tag()
    {
        $el = new HtmlElement('div', 'Content');
        $this->assertEquals('<div>Content</div>', $el->toHtml());
    }

    public function test_renders_self_closing_tag()
    {
        $el = new HtmlElement('input', '', ['type' => 'text']);
        $this->assertEquals('<input type="text">', $el->toHtml());
    }

    public function test_renders_attributes()
    {
        $el = new HtmlElement('a', 'Link', ['href' => '#']);
        $el->class('btn')->id('my-link')->attribute('data-test', 'true');
        
        $this->assertEquals('<a href="#" class="btn" id="my-link" data-test="true">Link</a>', $el->toHtml());
    }

    public function test_escapes_content_by_default()
    {
        $el = new HtmlElement('div', '<strong>Bold</strong>');
        $this->assertEquals('<div>&lt;strong&gt;Bold&lt;/strong&gt;</div>', $el->toHtml());
    }

    public function test_does_not_escape_html_string()
    {
        $el = new HtmlElement('div', new HtmlString('<strong>Bold</strong>'));
        $this->assertEquals('<div><strong>Bold</strong></div>', $el->toHtml());
    }
}
