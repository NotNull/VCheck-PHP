<?php
/**
 * User: Jeotic
 * Date: 2/13/13
 * Time: 6:51 PM
 */
class VCheck
{
    public static function check()
    {
        $stuff = VCheck::validateArgs(func_get_args());

        $target = $stuff['target'];
        $options = $stuff['options'];

        if(($target !== null && ($options !== null && VCheck::isArray($options))))
        {
            foreach ($options as $type => $option)
            {
                VCheck::launchCallback($target, $type, $option);
            }
        }

    }

    protected static function launchCallback($target, $type, $options)
    {
        $is = VCheck::checkType($type, $target);

        VCheck::validateCallbackArgs($target, $is, $options);

        return $is;
    }

    public static function checkType($type, $target)
    {
        $is = false;

        if(is_string($type))
        {
            if(strstr($type, 'is') !== false)
                $type = str_replace('is', ' ', $type);

            $type = trim($type);

            switch (strtolower($type))
            {
                case 'function':
                {
                    if(is_callable($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'object':
                {
                    if(is_object($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'array':
                {
                    if(is_array($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'string':
                {
                    if(is_string($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'null':
                {
                    if(is_null($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'empty':
                {
                    if(is_string($target))
                    {
                        if($target == '')
                        {
                            $is = true;
                        }
                    }
                }
                case 'number':
                {
                    if(is_numeric($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'infinite':
                {
                    if(is_numeric($target))
                    {
                        if(is_infinite($target))
                        {
                            $is = true;
                        }
                    }
                }
                    break;
                case 'boolean':
                {
                    if(is_bool($target))
                    {
                        $is = true;
                    }
                }
                    break;
                case 'date':
                {
                    if(strtotime($target) !== false)
                    {
                        $is = true;
                    }
                }
                    break;
                case 'nan':
                {
                    if(is_nan($target))
                    {
                        $is = true;
                    }
                }
                    break;
            }
        }

        return $is;
    }

    protected static function validateArgs($args)
    {
        $results = array();

        $results['target'] = null;
        $results['options'] = null;

        $target = -1;
        $options = array();

        if(is_array($args))
        {
            $argCount = count($args);

            if($argCount > 0)
            {
                if($argCount == 1)//assumes array or object was passed
                {
                    if(VCheck::checkType('array', $args))
                    {
                        if(isset($args[0]['target']))
                            $target = $args[0]['target'];
                    }

                    if(VCheck::checkType('object', $args))
                    {
                        $target = $args;
                    }

                    if(VCheck::checkType('array', $target))
                    {
                        $options = $args['options'];
                    }

                }
                else if($argCount == 2)//assumed target and options are passed
                {
                    $target = $args[0];
                    $options = $args[1];
                }
            }
        }

        if(is_array($options))
        {
            $results['target'] = $target;
            $results['options'] = $options;
        }

        return $results;
    }

    protected static function validateCallbackArgs($target, $is, $args)
    {
        if(is_array($args))
        {
            $argCount = count($args);
            $true = null;
            $false = null;

            if($argCount > 0)
            {
                if($is === true)
                {
                    if(isset($args['true']))
                    {
                        $true = $args['true'];

                        if(VCheck::checkType('function', $true))
                        {
                            //$true($target);
                            call_user_func($true, $target);
                        }
                    }
                }
                else if($is === false)
                {
                    if(isset($args['false']))
                    {
                        $true = $args['false'];

                        if(VCheck::checkType('function', $false))
                        {
                            //$false($target);
                            call_user_func($false, $target);
                        }
                    }
                }
            }
        }
        else if(VCheck::checkType('function', $args))
        {
            if($is === true)
            {
                //$args($target);
                call_user_func($args, $target);
            }
        }
    }

    public static function __callStatic($name, $arguments)
    {
        return VCheck::checkType($name, $arguments[0]);
    }

}

VCheck::check('I am a string!', array(
    'string' => function($t){
        echo $t;
    }
));