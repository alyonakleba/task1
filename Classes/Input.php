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

        if (empty($options)) {
            throw new Exception('Passed invalid arguments');
        }

        foreach($this->arguments as $shortArg => $longArg) {
            $shortArg = substr($shortArg, 0, -1);
            $longArg = substr($longArg, 0, -1);

            if(array_key_exists($longArg, $options) || array_key_exists($shortArg, $options)) {
                $options[$longArg] = array_key_exists($longArg, $options) ? $options[$longArg] : $options[$shortArg];
            } else {
                throw new Exception('Option "'.$longArg.'" or "'.$shortArg.'" is required');
            }
        }

        return $options;
    }

}