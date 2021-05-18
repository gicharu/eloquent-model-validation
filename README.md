# laragrad/eloquent-model-validation

This package provides a trait `\Laragrad\Models\Concerns\HasValidation` for model validation.

## Installing

Run command in console

	composer require gicharu/eloquent-model-validation

## Using validation

### Modify your model

Add into your model next code:

1. Use trait `\Laragrad\Models\Concerns\HasValidation` declaration
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

There used next methods provided by `\Laragrad\Models\Concerns\HasValidation` trait:

* `tempFillable()`
* `validate()`

## `HasValidation` trait methods

### validate()

Syntax:

`validate(array $data = [], array $rules = [], array $messages = [], array $customAttributes = []) : Model`

Arguments:

* **$data** - Validated data. By default model attributes values;
* **$rules** - Validation rules. By default used $rules property of model;
* **$messages** - Custom error messages;
* **$customAttributes** - Custom attribute names for generating validation error messages.

Returns a Model. 

Note about $customAttributes argument. 

You can define $customAttribute by 3 way:

1. Define a `$attributeNames` property of model.
2. Define translated attribute names in lang files by path `/resources/lang/xx/model/{YourModelPath}.attributes`. For example, for model `App\Models\ContractType` lang file is `/resources/lang/xx/model/app/models/contract_type`
3. Define a `$customAttributes` argument in the `validation()` call.

### tempFillable()

Syntax:

`tempFillable(array $fields) : Model`

Arguments:

* **$fields** - List of fields that you can fill by `fill()` until next `save()` has called.

Returns a Model. 

