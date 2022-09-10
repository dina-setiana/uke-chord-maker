<?php

/**
 * singleton class configuring various application options
 */
class Config
{
    // --------------------------------------
    // finding & reading your ChordPro files
    // --------------------------------------
    const FILE_EXTENSION = '.cpm.txt';
    const FILENAME_PATTERN = '/(.*?)\.cpm\.txt$/';

    const MAX_FILESIZE = 100000;
    const NOT_FOUND_404_FILE = 'error.txt';

    // --------------------------------------
    // file paths/directories (DO NOT DIRECTLY EDIT THESE... see Init method below)
    // --------------------------------------
    static public $SongDirectory = '';
    static public $AppDirectory = '';

    // --------------------------------------
    // Alternate directory or path locations
    // --------------------------------------

    /**
     * Location of UGS asset directories, i.e. JavaScript (JS), Stylesheet (CSS), and Image (leave as "/" if standard install)
     * @constant(STATIC_PREFIX)
     * @var String
     */
    const STATIC_PREFIX = '/';

    /**
     * If you want your URLs to be prefixed with a subdirectory specify that here (leave as "/" if standard install)
     * @constant(SUB_DIRECTORY)
     * @var String
     */
    const SUB_DIRECTORY = '/';

    // --------------------------------------
    // Attribution & Site Credits
    // --------------------------------------
    const PAGE_TITLE_SUFFIX = ' | UkeGeek\'s Scriptasaurus';
    const POWERED_BY = 'UkeGeeks-Scriptasaurus-v1.4';
    const SUPPORT_EMAIL = 'buz@your-domain-not-mine.com';

    // --------------------------------------
    // Page Headings & Titles
    // --------------------------------------
    const SONGBOOK_HEADLINE  = 'The BIG UKE Book';
    const SONGBOOK_SUB_HEADLINE = 'Sample Styled Songbook &raquo;';

    // --------------------------------------
    // Boolean Options/Settings
    // --------------------------------------

    /**
     * Apache Web Server Only: if true links are generated using ModRewrite rules syntax (no query params)
     * @constant(USE_MOD_REWRITE)
     * @var Boolean
     */
    const USE_MOD_REWRITE = false;

    /**
     * if true the Songbook shows the detailed (title, artist, subtitle) song page and uses the song list cache.
     * If false the song list page uses the filenames for the link text (does minor tidy-up)
     * @constant(USE_DETAILED_LIST)
     * @var Boolean
     */
    const USE_DETAILED_LIST = true;

    /**
     * If true when visitor clicks to a page the full editor toolbar is present; if false only the song is displayed (no formatting or other features)
     * @constant(USE_EDITABLE_SONG)
     * @var Boolean
     */
    const USE_EDITABLE_SONG = true;

    /**
     * If true visitors must login to view or edit any page. Login must be enabled to Add or Update songs
     * @constant(IS_LOGIN_REQUIRED)
     * @var Boolean
     */
    const IS_LOGIN_REQUIRED = true;

    /**
     * File names used for song list cache files (only if "USE_DETAILED_LIST" enabled).
     * @constant(SONG_CACHE_KEY_FILENAME)
     * @var string
     */
    const SONG_CACHE_KEY_FILENAME = 'SongList';

    // --------------------------------------
    // Logins (only if "IS_LOGIN_REQUIRED" enabled)
    // --------------------------------------
    public static $Accounts = array(
        array(
            'user' => 'admin',
            'pass' => 'secret',
            'name' => 'Admin',
            'isActive' => true,
            'mayEdit' => true
        ),
        array(
            'user' => 'guest',
            'pass' => '12345',
            'name' => 'Honored Guest',
            'isActive' => true,
            'mayEdit' => false
        ),
    );

    /**
     * any dynamic setup happens here
     */
    public static function Init()
    {
        self::$SongDirectory = getcwd() . '/cpm/';
        self::$AppDirectory = dirname(__FILE__) . '/';
    }
}
