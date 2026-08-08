<?php

declare(strict_types=1);

namespace Tests\Validation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Validation\Validator;
use Palet\Framework\Validation\ValidationException;

class ValidatorTest extends TestCase
{
    public function test_passes_valid_data()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30
        ];
        
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'age' => 'required|min:18|max:65'
        ];
        
        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->passes());
        $this->assertFalse($validator->fails());
    }

    public function test_fails_invalid_data()
    {
        $data = [
            'name' => '',
            'email' => 'not-an-email',
            'age' => 12
        ];
        
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'age' => 'required|min:18'
        ];
        
        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertFalse($validator->passes());
        
        $errors = $validator->errors();
        
        $this->assertTrue($errors->has('name'));
        $this->assertTrue($errors->has('email'));
        $this->assertTrue($errors->has('age'));
    }
    
    public function test_bail_stops_on_first_failure()
    {
        $data = [
            'email' => ''
        ];
        
        // Bail should stop after 'required' fails, so 'email' rule won't run.
        $rules = [
            'email' => 'bail|required|email'
        ];
        
        $validator = Validator::make($data, $rules);
        $validator->fails();
        
        $errors = $validator->errors()->get('email');
        
        // There should be only 1 error (from required), not 2
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('required', $errors[0]);
    }
    
    public function test_validate_method_throws_exception()
    {
        $this->expectException(ValidationException::class);
        
        $validator = Validator::make([], ['name' => 'required']);
        $validator->validate();
    }
}
