<?php

/**
 * Helper function
 */
if (!function_exists('dd')) {
    function dd($data)
    {
        var_dump($data);
        die();
    }
}
