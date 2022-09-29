<?php

namespace Fearless\Tool\Support;;

use Illuminate\Contracts\Validation\{Factory, Validator};

trait SceneValidator
{
    protected $scene = 'create';

    protected $onlyRule = [];

    protected $autoValidate = true;

    /**
     * Validate.
     *
     * @param string|array $scene
     */
    public function validate($scene = '')
    {
        if (!$this->autoValidate) {
            if (is_array($scene)) {
                $this->onlyRule = $scene;
            } else {
                $this->scene = $scene;
            }
            $this->handleValidate();
        }
    }

    /**
     * 覆盖 ValidatesWhenResolvedTrait->validateResolved
     */
    public function validateResolved()
    {
        if (method_exists($this, 'autoValidate')) {
            $this->autoValidate = $this->container->call([$this, 'autoValidate']);
        }
        if ($this->autoValidate) {
            $this->handleValidate();
        }
    }

    /**
     * Handle validate.
     */
    protected function handleValidate()
    {
        parent::validateResolved();
    }

    /**
     * 定义 FormRequest->getValidatorInstance 下 validator 验证器
     *
     * @param Factory $factory
     *`
     * @return Validator
     */
    public function validator(Factory $factory)
    {
        $validationData = $this->isMethod('GET') ? $this->query() : $this->post();
        return $factory->make($validationData, $this->getRules(), $this->messages(), $this->attributes());
    }

    /**
     * Get rules.
     *
     * @return array
     */
    protected function getRules()
    {
        return $this->handleScene($this->container->call([$this, 'rules']));
    }

    /**
     * Handle scene.
     *
     * @param array $rules
     *
     * @return array
     */
    protected function handleScene(array $rules)
    {
        if ($this->onlyRule) {
            return $this->handleRules($this->onlyRule, $rules);
        }

        if (!empty($this->scene) && method_exists($this, 'scene')) {
            $scene = $this->container->call([$this, 'scene']);
            if (array_key_exists($this->scene, $scene)) {
                return $this->handleRules($scene[$this->scene], $rules);
            }
        }
        return $rules;
    }

    /**
     * Handle rules.
     *
     * @param array $sceneRules
     * @param array $rules
     *
     * @return array
     */
    protected function handleRules(array $sceneRules, array $rules)
    {
        $result = [];
        foreach ($sceneRules as $key => $value) {
            if (is_numeric($key) && array_key_exists($value, $rules)) {
                $result[$value] = $rules[$value];
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
