<?php

class LoginVm extends BaseVm
{
    public $ErrorMessage = '';
    public $Username = '';
    public $FormPostUri = '';

    function __construct()
    {
        parent::__construct();
        $this->FormPostUri = Ugs::makeUri(Actions::LOGIN);
    }
}
