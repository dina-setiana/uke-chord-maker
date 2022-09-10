<?php

/**
 * Enum for possible Actions (url to ViewModel mappings)
 * @class Actions
 * @namespace ugsPhp
 */
final class Actions
{
    const SONG = 0;
    const SONGBOOK = 1;
    const SOURCE = 2;
    const REINDEX = 3;
    const LOGIN = 4;
    const LOGOUT = 5;
    const EDIT = 6;
    // AJAX Actions
    const AJAX_NEW_SONG = 7;
    const AJAX_UPDATE_SONG = 8;

    /**
     * convert passed in string value to corresponding Actions enum
     * @param [string] $value
     * @return  Actions
     */
    public static function toEnum($value)
    {
        switch (strtolower($value)) {
            case 'song':
                return self::SONG;
            case 'reindex':
                return self::REINDEX;
            case 'source':
                return self::SOURCE;
            case 'edit':
                return self::EDIT;
            case 'login':
                return self::LOGIN;
            case 'logout':
                return self::LOGOUT;
            case 'ajaxnewsong':
                return self::AJAX_NEW_SONG;
            case 'ajaxupdatesong':
                return self::AJAX_UPDATE_SONG;
        }
        return self::SONGBOOK;
    }

    /**
     * Converts Actions enum to a string; you should use this for URI's
     * @param Actions(int-enum) $value
     * @return string
     */
    public static function toName($value)
    {
        switch ($value) {
            case self::SONG:
                return 'Song';
            case self::SOURCE:
                return 'Source';
            case self::EDIT:
                return 'Edit';
            case self::REINDEX:
                return 'ReIndex';
            case self::LOGIN:
                return 'Login';
            case self::LOGOUT:
                return 'Logout';
            case self::AJAX_NEW_SONG:
                return 'AjaxNewSong';
            case self::AJAX_UPDATE_SONG:
                return 'AjaxUpdateSong';
        }
        return 'Songbook';
    }
}
