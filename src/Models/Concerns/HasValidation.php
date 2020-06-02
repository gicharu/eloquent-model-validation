<?php

namespace Laragrad\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

trait HasValidation
{

    /**
     * @var array
     */
    protected static $tempFillable = [];

    /**
     * @var array
     */
    protected static $configAttributeNames = null;

    /**
     * Validation model attributes
     *
     * @param array $data
     * @param array $additionalRules
     * @param array $messages
     * @param array $customAttributes
     * @throws ValidationException
     * @return \App\Laragrad\Models\Concerns\HasValidation
     */
    public function validate(array $data = [], array $additionalRules = [], array $messages = [], array $customAttributes = [])
    {
        $data = array_replace($this->attributes, $data);

        $modelRules = $this->rules ?? [];

        $attributeNames = $this->getAttributeNames($customAttributes);

        // Validate for default rules
        if (!empty($this->defaultRules)) {
            $validator = \Validator::make($data, array_fill_keys(array_keys($data), $this->defaultRules ?? []), $messages, $attributeNames);
        }

        // Validate for additional rules
        if ($validator->passes() && !empty($additionalRules)) {
            $validator = \Validator::make($data, $additionalRules, $messages, $attributeNames);
        }

        // Validate for additional rules
        if ($validator->passes() && !empty($modelRules)) {
            $validator = \Validator::make($data, $modelRules, $messages, $attributeNames);
        }

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this;

    }

    /**
     * Get attribute names
     *
     * Priority:
     *  1. CustomAttributes
     *  2. ConfigAttributeNames
     *  3. ModelAttributeNames
     *
     * @param array $customAttributes
     * @return array|NULL
     */
    protected function getAttributeNames(array $customAttributes = [])
    {
        static $configAttributeNames = null;

        // Custom attribute names
        $attributeNames = $customAttributes;

        // Model configuration attribute names
        $configAttributeNames = $this->getConfigAttributeNames();
        if (!empty($configAttributeNames)) {
            $attributeNames = $attributeNames + $configAttributeNames;
        }

        // Model attribute names
        if (!empty($this->attributeNames)) {
            $attributeNames = $attributeNames + ($this->attributeNames ?? []);
        }

        return is_array($attributeNames) ? $attributeNames : null;
    }

    /**
     * Get model configuration attribute names
     *
     * @return array
     */
    protected function getConfigAttributeNames()
    {
        static $attributeNames = null;

        if (!static::$configAttributeNames) {

            $transFile = config('laragrad.models.trans_path', 'model') . '/' .
                \Str::snake(str_replace('\\', '/', strtolower(get_class($this))));

            static::$configAttributeNames = trans($transFile . ".attributes");
            if (empty(static::$configAttributeNames) || !is_array(static::$configAttributeNames)) {
                static::$configAttributeNames = [];
            }
        }

        return static::$configAttributeNames;
    }

    /**
     * Temoporary attribute list for mass filling
     *
     * @return Model
     */
    public function tempFillable()
    {
        $args = func_get_args();
        array_shift($args);

        if (!isset($args[0])) {
            $args[0] = [];
        }

        if (is_array($args[0])) {
            $args = $args[0];
        }

        $list = array_replace($this->fillable, $args);
        static::$tempFillable = &$list;

        return $this;
    }

    /**
     * @see \Illuminate\Database\Eloquent\Model::getFillable()
     *
     * @return array
     */
    public function getFillable()
    {
        if (!static::$tempFillable) {
            return parent::getFillable();
        }

        return static::$tempFillable;
    }

    /**
     * @see \Illuminate\Database\Eloquent\Model::save()
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $result = parent::save($options);

        $list = [];
        static::$tempFillable = &$list;

        return $result;
    }
}