<?php

/**
 * base class for all View Models with visible Views (as opposed to AJAX etc)
 * @class BaseVm
 */
abstract class BaseVm
{
    public $PoweredBy = Config::POWERED_BY;
    public $PageTitle = '';
    public $SupportEmail = Config::SUPPORT_EMAIL;
    public $IsJson = false;
    public $SiteUser = null;
    public $StaticsPrefix = '/';

    function __construct()
    {
        $this->StaticsPrefix = defined('Config::STATIC_PREFIX') ? Config::STATIC_PREFIX : '/';
    }
}
