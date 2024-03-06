<?php
namespace SMTP2GO\App\Concerns;

trait GetsOption
{
    public function getOption($optionKey)
    {
        if (function_exists('smtp2go_mu_options')) {
            $options = smtp2go_mu_options();
            if (isset($options[$optionKey])) {
                return $options[$optionKey];
            }
        } else {
            return get_option($optionKey);
        }
    }
}