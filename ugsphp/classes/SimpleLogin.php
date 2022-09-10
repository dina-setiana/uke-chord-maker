<?php

class SimpleLogin
{
    const SESSION_KEY = 'user';

    private $_user = null;

    // ----------------------------------------------------------------------
    //  Public Methods
    // ----------------------------------------------------------------------

    function __construct()
    {
        $this->_startSession();
        $this->_set($this->_getSession());
    }

    /**
     * Redirects if successful login; otherwise returns a message
     * @return  string message describing error or blank if no error
     */
    public function attemptLogin($username, $password)
    {
        $username = strtolower(trim($username));
        $password = trim($password);
        $this->_set($this->_validateUser($username, $password));

        return $this->_user->IsAllowAccess ? 'Success!' : 'invalid username/password';
    }

    /**
     * Cleans class members and session info for current user.
     */
    public function logout()
    {
        $this->_set(null);
        $this->_unsetSession();
    }

    public function getUser()
    {
        return ($this->_user == null) ? new SiteUser() : $this->_user;
    }

    // ----------------------------------------------------------------------
    //  Helper Methods (wraps working with with member variables & misc)
    // ----------------------------------------------------------------------

    /**
     * sets class members and session info
     * @param [SiteUser] $siteUser
     */
    private function _set($siteUser)
    {
        $this->_user = ($siteUser == null) ? new SiteUser() : $siteUser;
        $this->_setSession($siteUser);
    }

    /**
     * Returns info about user if found, null otherwise
     * @param [string] $username [description]
     * @param [string] $password [description]
     * @return SiteUser [description]
     */
    private function _validateUser($username, $password)
    {
        $siteUser = new SiteUser();
        foreach (Config::$Accounts as $account) {
            if (($username == strtolower($account['user'])) && ($password == $account['pass'])) {
                if ($account['isActive']) {
                    $siteUser->Username = $account['user'];
                    $siteUser->MayEdit  = $account['mayEdit'];
                    $siteUser->DisplayName = $account['name'];
                    $siteUser->IsAllowAccess = true;
                    $siteUser->IsAuthenticated = true;
                }
                break;
            }
        }
        return $siteUser;
    }

    // ----------------------------------------------------------------------
    // Session Management Methods
    // ----------------------------------------------------------------------

    /**
     * Nukes session info
     */
    private function _unsetSession()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Sets session info for current User.
     * @param [SiteUser] $siteUser
     */
    private function _setSession($siteUser)
    {
        $_SESSION[self::SESSION_KEY] = $siteUser;
    }

    /**
     * returns current user object from session; returns null if not found.
     */
    private function _getSession()
    {
        return isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : null;
    }

    /**
     * Preps the current user's session, however, it's important to remember a session isn't
     * actually created until a value is set.
     */
    private function _startSession()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }
}
