# laragrad/eloquent-model-validation

This package provides a trait for Model validation.

## Installing

Run command in console

	composer require laragrad/eloquent-model-validation

## Using validation

### Modify your model

Add into your model next code:

1. Use trait `\Laragrad\Models\Concerns\HasValidation' declaration
2. Validation rules

```php

    class Test extends Model
    {
        use \Laragrad\Models\Concerns\UseValidation;
        
        protected $rules => [
            'title' => ['string', 'max:150'],
            'value' => ['integer', 'min:0', 'max:50'],
            'description' => ['string', 'nullable'],
        ];
        
        ...
    }

```
    
### Controller add() method example

```php

    use App\Models\Test;
    
    class TestController extends Controller
    {
        public function add(Request $request)
        {
            $model = \App::make(Test)
                ->tempFillable([
                    'title',
                    'value',
                    'description',
                ])
                ->fill($request->all())
                ->validate()
                ->save();
        }
    }

```

