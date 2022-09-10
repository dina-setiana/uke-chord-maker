<?php

/**
 * a "lite" MVC-ish Controller
 *
 * @class Ugs
 * @namespace ugsPhp
 */
class Ugs
{

    /**
     * Bootstrap and runs the entire danged system!
     */
    function __construct()
    {
        $this->_bootstrap();

        // Reads query param to pick appropriate Actions
        $action = isset($_GET['action']) ? Actions::toEnum($_GET['action']) : Actions::SONGBOOK;

        $user = $this->_doAuthenticate($action);
        if (!$user->IsAllowAccess) {
            return;
        }

        $builder = $this->_getBuilder($action, $user);
        $model = $builder->build();

        // all done, time to render
        if ($model->IsJson) {
            $this->_renderJson($model);
        } else {
            $model->SiteUser = $user;
            $this->_renderView($model, $action);
        }
    }

    /**
     * builds (relative) URL
     *
     * @param Actions(enum) $action [description]
     * @param string  $param  (optional) extra query param value (right now only "song")
     * @return  string
     */
    public static function makeUri($action, $param = '')
    {
        $directory = defined('Config::SUB_DIRECTORY') ? Config::SUB_DIRECTORY : '/';
        $actionName = Actions::toName($action);
        $param = trim($param);

        if (!Config::USE_MOD_REWRITE) {
            $actionParams = strlen($param) > 0 ? '&song=' . $param : '';
            return $directory . 'music.php?action=' . strtolower($actionName) . $actionParams;
        }

        if ($action == Actions::SONG) {
            $actionName = 'songbook';
        }
        return $directory . strtolower($actionName) . '/' . $param;
    }

    /**
     * The rather quirky way to interface with jQuery.ajax with serialize,
     * returns a PHP Object version of the posted JSON.
     *
     * @return Object
     */
    public static function getJsonObject()
    {
        $input = @file_get_contents('php://input');
        return json_decode($input);
    }

    /**
     * Renders View associated with Action, making only $model available on the page
     *
     * @param [ViewModel] $model  appropriate view model entity
     * @param [Actions(int)] $action
     */
    private function _renderView($model, $action)
    {
        header('X-Powered-By: ' . Config::POWERED_BY);
        include_once Config::$AppDirectory . 'views/' . $this->_getViewName($action);
    }


    /**
     * Emits serialized JSON version of the $model with appropriate headers
     *
     * @param unknown $model
     */
    private function _renderJson($model)
    {
        header('Content-Type: application/json');
        if (isset($model->HasErrors) && $model->HasErrors) {
            header('HTTP/1.1 500');
        }
        unset($model->IsJson);
        echo json_encode($model);
    }

    /**
     * returns initialized SiteUser object, check the "Is Allow Access" property.
     * This method MAY hijack flow control by performing a redirect
     * or by rendering an alternate view
     *
     * @param Actions(enum) $action
     * @return SiteUser
     */
    private function _doAuthenticate($action)
    {

        if (!Config::IS_LOGIN_REQUIRED) {
            $user = new SiteUser();
            $user->IsAllowAccess = true;
            return  $user;
        }

        $login = new SimpleLogin();

        if ($action == Actions::LOGOUT) {
            $login->logout();
            header('Location: ' . self::makeUri(Actions::LOGIN));
            return  $login->getUser();
        }

        $user = $login->getUser();
        if (!$user->IsAllowAccess) {
            $builder = $this->_getBuilder(Actions::LOGIN, $user);
            $model = $builder->build($login);
            $user = $login->getUser();

            // during form post the builder automatically attempts a login -- let's check whether that succeeded...
            if (!$user->IsAllowAccess) {
                $this->_renderView($model, Actions::LOGIN);
                return  $user;
            }

            // successful login we redirect:
            header('Location: ' . self::makeUri(Actions::SONGBOOK));
            return  $user;
        } elseif ($action == Actions::LOGIN) {
            // if for some reason visitor is already logged in but attempting to view the Login page, redirect:
            header('Location: ' . self::makeUri(Actions::SONGBOOK));
            return $user;
        }

        return $user;
    }

    /**
     * Returns instance of appropriate Builder class
     *
     * @param ActionEnum $action desired action
     * @param SiteUser $siteUser current visitor
     * @return ViewModelBuilder-Object (Instantiated class)
     */
    private function _getBuilder($action, $siteUser)
    {
        $builder = null;

        switch ($action) {
            case Actions::EDIT:
            case Actions::SONG:
                $builder = new SongVmb();
                break;
            case Actions::SOURCE:
                $builder = new SourceVmb();
                break;
            case Actions::REINDEX:
                $builder = new RebuildSongCacheVmb();
                break;
            case Actions::LOGOUT:
            case Actions::LOGIN:
                $builder = new LoginVmb();
                break;
            case Actions::AJAX_NEW_SONG:
                $builder = new AjaxNewSongVmb();
                break;
            case Actions::AJAX_UPDATE_SONG:
                $builder = new AjaxUpdateSongVmb();
                break;
            default:
                $builder = Config::USE_DETAILED_LIST
                    ? new SongListDetailedVmb()
                    : new SongListVmb();
                break;
        }

        $builder->SiteUser = $siteUser;
        return $builder;
    }

    /**
     * Bootstraps UGS...
     * > Instantiates configs class
     * > Automatically includes ALL of the PHP classes in these directories: "classes" and "view-models".
     * This is a naive approach, see not about including base classes first.
     *
     * @private
     */
    private function _bootstrap()
    {
        // let's get Config setup
        $appRoot = dirname(__FILE__);
        include_once $appRoot . '/Config.php';

        // some dependencies: make sure base classes are included first...
        include_once $appRoot . '/classes/SiteUser.php';
        include_once $appRoot . '/view-models/BaseVm.php';
        include_once $appRoot . '/builders/BaseVmb.php';

        Config::Init();

        foreach (array('classes', 'view-models', 'builders') as $directory) {
            foreach (glob($appRoot . '/' . $directory . '/*.php') as $filename) {
                include_once $filename;
            }
        }
    }

    /**
     * Gets the PHP filename (aka "View") to be rendered
     *
     * @param Actions(int-enum) $action
     * @return  string
     */
    private function _getViewName($action)
    {
        $view = Config::USE_DETAILED_LIST ? 'song-list-detailed.php' : 'song-list.php';
        switch ($action) {
            case Actions::SONG:
                $view = Config::USE_EDITABLE_SONG ? 'song-editable.php' : 'song.php';
                break;
            case Actions::EDIT:
                $view = 'song-editable.php';
                break;
            case Actions::SOURCE:
                $view = 'song-source.php';
                break;
            case Actions::REINDEX:
                $view = 'songs-rebuild-cache.php';
                break;
            case Actions::LOGOUT:
            case Actions::LOGIN:
                $view = 'login.php';
                break;
        }
        return $view;
    }
}
