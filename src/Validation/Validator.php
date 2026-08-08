<?php

declare(strict_types=1);

namespace Palet\Framework\Validation;

use Palet\Framework\Contracts\Validation\ValidatorInterface;
use Palet\Framework\Contracts\Validation\MessageBagInterface;
use Palet\Framework\Contracts\Validation\RuleInterface;
use Palet\Framework\Validation\Rules\RequiredRule;
use Palet\Framework\Validation\Rules\EmailRule;
use Palet\Framework\Validation\Rules\MinRule;
use Palet\Framework\Validation\Rules\MaxRule;

class Validator implements ValidatorInterface
{
    protected array $data;
    protected array $rules;
    protected MessageBagInterface $errors;
    protected array $failedRules = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->errors = new MessageBag();
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function passes(): bool
    {
        $this->errors = new MessageBag();
        $this->failedRules = [];
        $passed = true;

        foreach ($this->rules as $attribute => $attributeRules) {
            $value = $this->data[$attribute] ?? null;
            
            $pipeline = new RulePipeline();
            $bail = false;
            
            // Normalize rules (e.g. "required|email" -> ['required', 'email'])
            if (is_string($attributeRules)) {
                $attributeRules = explode('|', $attributeRules);
            }
            
            foreach ($attributeRules as $ruleString) {
                if ($ruleString === 'bail') {
                    $bail = true;
                    continue;
                }
                
                $rule = $this->resolveRule($ruleString);
                if ($rule) {
                    $pipeline->addRule($rule);
                }
            }
            
            if (!$pipeline->process($attribute, $value, $this->errors, $bail)) {
                $passed = false;
                $this->failedRules[$attribute] = true;
            }
        }

        return $passed;
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): MessageBagInterface
    {
        return $this->errors;
    }
    
    public function failed(): array
    {
        return $this->failedRules;
    }

    public function validate(): array
    {
        if ($this->fails()) {
            throw new ValidationException($this->errors());
        }
        
        return $this->data; // Ideally returns only validated data
    }
    
    protected function resolveRule(string|RuleInterface $rule): ?RuleInterface
    {
        if ($rule instanceof RuleInterface) {
            return $rule;
        }
        
        $params = [];
        if (str_contains($rule, ':')) {
            list($rule, $paramStr) = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }
        
        return match($rule) {
            'required' => new RequiredRule(),
            'email' => new EmailRule(),
            'min' => new MinRule($params[0] ?? '0'),
            'max' => new MaxRule($params[0] ?? '0'),
            default => null,
        };
    }
}
