<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Apigility\Admin\InputFilter;

use Zend\InputFilter\InputFilter;

class VersionInputFilter extends InputFilter
{
    public function init()
    {
        $this->add([
            'name' => 'module',
            'validators' => [
                ['name' => 'ZF\Apigility\Admin\InputFilter\Validator\ModuleNameValidator'],
            ],
            'error_message' => 'Please provide a valid API module name',
        ]);
        $this->add([
            'name' => 'version',
            'validators' => [
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^[a-z0-9_]+$/',
                    ],
                ],
            ],
            'error_message' => 'Please provide a valid version string; may consist of a-Z, 0-9, and "_"',
        ]);
    }
}
