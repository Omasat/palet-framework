<?php

declare(strict_types=1);

namespace Palet\Framework\View\Html;

use Palet\Framework\Contracts\View\Html\FormBuilderInterface;
use Palet\Framework\Contracts\View\Html\HtmlBuilderInterface;
use Palet\Framework\Contracts\View\Html\HtmlElementInterface;
use Palet\Framework\Contracts\View\Html\HtmlStringInterface;
use Palet\Framework\Contracts\Security\CsrfManagerInterface;
use Palet\Framework\Contracts\Session\SessionInterface;

class FormBuilder implements FormBuilderInterface
{
    protected HtmlBuilderInterface $html;
    protected ?SessionInterface $session = null;
    protected ?CsrfManagerInterface $csrf = null;
    
    protected mixed $model = null;

    public function __construct(HtmlBuilderInterface $html, ?SessionInterface $session = null, ?CsrfManagerInterface $csrf = null)
    {
        $this->html = $html;
        $this->session = $session;
        $this->csrf = $csrf;
    }

    public function open(array $options = []): HtmlStringInterface
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $attributes = $options;
        
        $append = '';

        if ($method !== 'GET' && $method !== 'POST') {
            $append .= $this->hidden('_method', $method)->toHtml();
            $attributes['method'] = 'POST';
        }

        if (isset($attributes['method']) && strtoupper($attributes['method']) !== 'GET' && $this->csrf) {
            $append .= $this->hidden('_token', $this->csrf->token())->toHtml();
        }

        // We use tag directly, but since we just want the opening tag, we can manually build it or use string.
        $form = $this->html->tag('form', '', $attributes)->toHtml();
        // Remove closing tag for open()
        $form = str_replace('</form>', '', $form) . $append;

        return new HtmlString($form);
    }

    public function close(): HtmlStringInterface
    {
        $this->model = null;
        return new HtmlString('</form>');
    }

    public function model(mixed $model, array $options = []): HtmlStringInterface
    {
        $this->model = $model;
        return $this->open($options);
    }

    public function input(string $type, string $name, string $value = null, array $options = []): HtmlElementInterface
    {
        $options['type'] = $type;
        $options['name'] = $name;
        
        if (!isset($options['id'])) {
            $options['id'] = $this->transformKey($name);
        }

        $options['value'] = $this->getValueAttribute($name, $value);

        return $this->html->tag('input', '', $options);
    }

    public function text(string $name, string $value = null, array $options = []): HtmlElementInterface
    {
        return $this->input('text', $name, $value, $options);
    }

    public function email(string $name, string $value = null, array $options = []): HtmlElementInterface
    {
        return $this->input('email', $name, $value, $options);
    }

    public function password(string $name, array $options = []): HtmlElementInterface
    {
        return $this->input('password', $name, null, $options);
    }

    public function hidden(string $name, string $value = null, array $options = []): HtmlElementInterface
    {
        return $this->input('hidden', $name, $value, $options);
    }

    public function select(string $name, array $list = [], string $selected = null, array $options = []): HtmlElementInterface
    {
        $selected = $this->getValueAttribute($name, $selected);

        $html = '';
        foreach ($list as $value => $display) {
            $optionAttrs = ['value' => (string) $value];
            if ((string) $value === (string) $selected) {
                $optionAttrs['selected'] = true;
            }
            $html .= $this->html->tag('option', $display, $optionAttrs)->toHtml();
        }

        if (!isset($options['id'])) {
            $options['id'] = $this->transformKey($name);
        }
        $options['name'] = $name;

        return $this->html->tag('select', new HtmlString($html), $options);
    }

    public function checkbox(string $name, string $value = '1', bool $checked = false, array $options = []): HtmlElementInterface
    {
        return $this->checkable('checkbox', $name, $value, $checked, $options);
    }

    public function radio(string $name, string $value = null, bool $checked = false, array $options = []): HtmlElementInterface
    {
        if ($value === null) {
            $value = $name;
        }

        return $this->checkable('radio', $name, $value, $checked, $options);
    }

    protected function checkable(string $type, string $name, string $value, bool $checked, array $options): HtmlElementInterface
    {
        $options['type'] = $type;
        $options['name'] = $name;
        $options['value'] = $value;

        if ($this->getCheckboxCheckedState($name, $value, $checked)) {
            $options['checked'] = true;
        }

        if (!isset($options['id'])) {
            $options['id'] = $this->transformKey($name) . '_' . $value;
        }

        return $this->html->tag('input', '', $options);
    }

    protected function getCheckboxCheckedState(string $name, string $value, bool $default): bool
    {
        $old = $this->getOldInput($name);
        
        if ($old !== null) {
            return is_array($old) ? in_array($value, $old) : (string) $old === (string) $value;
        }

        $modelValue = $this->getModelValueAttribute($name);
        if ($modelValue !== null) {
            return is_array($modelValue) ? in_array($value, $modelValue) : (string) $modelValue === (string) $value;
        }

        return $default;
    }

    public function submit(string $value = 'Submit', array $options = []): HtmlElementInterface
    {
        $options['type'] = 'submit';
        $options['value'] = $value;
        return $this->html->tag('input', '', $options);
    }

    protected function getValueAttribute(string $name, ?string $value = null): ?string
    {
        $old = $this->getOldInput($name);

        if ($old !== null) {
            return (string) $old;
        }

        if ($value !== null) {
            return $value;
        }

        return (string) $this->getModelValueAttribute($name);
    }

    protected function getModelValueAttribute(string $name): mixed
    {
        if ($this->model === null) {
            return null;
        }

        $key = $this->transformKey($name);

        if (is_array($this->model) && array_key_exists($key, $this->model)) {
            return $this->model[$key];
        }

        if (is_object($this->model) && isset($this->model->{$key})) {
            return $this->model->{$key};
        }

        return null;
    }

    protected function getOldInput(string $name): mixed
    {
        if ($this->session && $this->session->has('_old_input')) {
            $old = $this->session->get('_old_input');
            $key = $this->transformKey($name);
            return $old[$key] ?? null;
        }

        return null;
    }

    protected function transformKey(string $key): string
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}
