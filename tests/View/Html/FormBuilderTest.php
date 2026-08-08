<?php

declare(strict_types=1);

namespace Tests\View\Html;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Html\FormBuilder;
use Palet\Framework\View\Html\HtmlBuilder;
use Palet\Framework\Contracts\Session\SessionInterface;
use Palet\Framework\Contracts\Security\CsrfManagerInterface;

class FormBuilderTest extends TestCase
{
    protected FormBuilder $form;

    protected function setUp(): void
    {
        $html = new HtmlBuilder();
        $this->form = new FormBuilder($html);
    }

    public function test_form_open_and_close()
    {
        $open = $this->form->open(['action' => '/test', 'method' => 'POST'])->toHtml();
        $this->assertEquals('<form action="/test" method="POST">', $open);
        
        $close = $this->form->close()->toHtml();
        $this->assertEquals('</form>', $close);
    }

    public function test_form_open_spoofs_methods()
    {
        $open = $this->form->open(['method' => 'PUT'])->toHtml();
        $this->assertStringContainsString('method="POST"', $open);
        $this->assertStringContainsString('<input type="hidden" name="_method"', $open);
        $this->assertStringContainsString('value="PUT"', $open);
    }

    public function test_text_input()
    {
        $input = $this->form->text('username', 'john', ['class' => 'form-control'])->toHtml();
        $this->assertStringContainsString('type="text"', $input);
        $this->assertStringContainsString('name="username"', $input);
        $this->assertStringContainsString('class="form-control"', $input);
        $this->assertStringContainsString('value="john"', $input);
    }

    public function test_select_input()
    {
        $input = $this->form->select('status', [1 => 'Active', 0 => 'Inactive'], '1')->toHtml();
        $this->assertStringContainsString('<option value="1" selected>Active</option>', $input);
        $this->assertStringContainsString('<option value="0">Inactive</option>', $input);
    }

    public function test_model_binding()
    {
        $model = ['email' => 'john@example.com', 'is_admin' => true];
        $this->form->model($model);
        
        $input = $this->form->email('email')->toHtml();
        $this->assertEquals('<input type="email" name="email" id="email" value="john@example.com">', $input);
        
        $checkbox = $this->form->checkbox('is_admin', '1', false)->toHtml();
        $this->assertEquals('<input type="checkbox" name="is_admin" value="1" checked id="is_admin_1">', $checkbox);
    }
}
