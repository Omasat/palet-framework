<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View\Html;

interface FormBuilderInterface
{
    /**
     * Open a new form.
     */
    public function open(array $options = []): HtmlStringInterface;

    /**
     * Close the current form.
     */
    public function close(): HtmlStringInterface;

    /**
     * Bind a model to the form.
     */
    public function model(mixed $model, array $options = []): HtmlStringInterface;

    /**
     * Generate a text input field.
     */
    public function text(string $name, string $value = null, array $options = []): HtmlElementInterface;
    
    /**
     * Generate an email input field.
     */
    public function email(string $name, string $value = null, array $options = []): HtmlElementInterface;
    
    /**
     * Generate a password input field.
     */
    public function password(string $name, array $options = []): HtmlElementInterface;

    /**
     * Generate a submit button.
     */
    public function submit(string $value = 'Submit', array $options = []): HtmlElementInterface;
}
