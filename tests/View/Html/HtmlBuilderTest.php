<?php

declare(strict_types=1);

namespace Tests\View\Html;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Html\HtmlBuilder;

class HtmlBuilderTest extends TestCase
{
    public function test_builds_div()
    {
        $html = new HtmlBuilder();
        $el = $html->div('Content', ['class' => 'container']);
        
        $this->assertEquals('<div class="container">Content</div>', $el->toHtml());
    }

    public function test_builds_link()
    {
        $html = new HtmlBuilder();
        $el = $html->a('https://example.com', 'Example');
        
        $this->assertEquals('<a href="https://example.com">Example</a>', $el->toHtml());
    }

    public function test_builds_image()
    {
        $html = new HtmlBuilder();
        $el = $html->img('image.jpg', 'My Image');
        
        $this->assertEquals('<img src="image.jpg" alt="My Image">', $el->toHtml());
    }

    public function test_magic_call()
    {
        $html = new HtmlBuilder();
        $el = $html->section('Section Content', ['id' => 'about']);
        
        $this->assertEquals('<section id="about">Section Content</section>', $el->toHtml());
    }
}
