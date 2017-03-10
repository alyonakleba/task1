<?php

namespace Classes;

use Exception;

class Input
{
    /**
     * Required cli arguments
     *
     * @var array $arguments
     */
    private $arguments = [
        'p:'    => 'path:',
        'c:'    => 'criteria:',
        'f:'    => 'from:',
        't:'    => 'to:'
    ];


    /**
     * Get CLI request options
     *
     * @throws Exception
     * @return array $options
     */
    public function getOptions()
    {
        $options = getopt(implode('', array_keys($this->arguments)), array_values($this->arguments));

        if (empty($options) === true) {
            throw new Exception('Passed invalid arguments');
        }

        if (isset($options['path']) || isset($options['p']))  {
            $options['path'] = isset( $options['path'] ) ? $options['path'] : $options['p'];
        } else {
            throw new Exception('File is required');
        }

        if (isset($options['criteria']) || isset($options['c']))  {
            $options['criteria'] = isset( $options['criteria'] ) ? $options['criteria'] : $options['c'];
        } else {
            throw new Exception('Criteria field is required');
        }

        if (isset($options['from']) || isset($options['f']))  {
            $options['from'] = isset( $options['from'] ) ? $options['from'] : $options['f'];
        } else {
            throw new Exception('Min criteria is required');
        }

        if (isset($options['to']) || isset($options['t']))  {
            $options['to'] = isset( $options['to'] ) ? $options['to'] : $options['t'];
        } else {
            throw new Exception('Max criteria is required');
        }

        return $options;
    }

}