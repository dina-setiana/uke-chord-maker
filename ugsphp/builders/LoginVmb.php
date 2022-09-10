<?php

/**
 * View Model Builder -- Creates a "Login" View Model
 * @class LoginVmb
 */
class LoginVmb extends BaseVmb
{

    /**
     * Populates Login View Model
     * @return LoginVm
     */
    public function build($login = null)
    {
        $viewModel = new LoginVm();
        $viewModel->PageTitle = 'Login Required';

        if (isset($_REQUEST['username'])) {
            $login = $login == null ? new SimpleLogin : $login;
            $viewModel->Username = $_REQUEST['username'];
            $password = $_REQUEST['password'];

            $viewModel->ErrorMessage = $login->attemptLogin($viewModel->Username, $password);
        }

        return $viewModel;
    }
}
