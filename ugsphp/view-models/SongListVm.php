<?php

/**
 * wraps list of songs for both basic and "detailed" views
 */
class SongListVm extends BaseVm
{
    public $SongList = array();

    /**
     * URL where "New Song" AJAX is sent.
     * -- Only used if Editing is enabled and user has permission.
     * @var string
     */
    public $EditAjaxUri = '';

    /**
     * If TRUE View may show edit form
     * -- Only used if Editing is enabled and user has permission.
     * @var boolean
     */
    public $IsNewAllowed = false;

    public $LogoutUri = '';

    public $Headline = '';
    public $SubHeadline = '';

    function __construct()
    {
        parent::__construct();
        $title = defined('Config::SONGBOOK_HEADLINE') ? Config::SONGBOOK_HEADLINE : 'The BIG UKE Book';

        $this->EditAjaxUri = Ugs::makeUri(Actions::AJAX_NEW_SONG);
        $this->LogoutUri = Ugs::makeUri(Actions::LOGOUT);
        $this->Headline = $title;
        $this->SubHeadline = defined('Config::SONGBOOK_SUB_HEADLINE') ? Config::SONGBOOK_SUB_HEADLINE : 'Sample Styled Songbook &raquo;';
        $this->PageTitle = $title . ' ' . Config::PAGE_TITLE_SUFFIX;
    }

    /**
     * Sorts songs based on title
     * @method sortSongs
     * @return (song array)
     */
    public function sort()
    {
        $temp = array();
        $sortedTitles = array();
        foreach ($this->SongList as $song) {
            $sortedTitles[] = $song->Title;
            $temp[$song->Title] = $song;
        }

        sort($sortedTitles);

        $this->SongList = array();
        foreach ($sortedTitles as $title) {
            $this->SongList[] = $temp[$title];
        }

        return $this->SongList;
    }

    /**
     * Adds a new SongLinkPvm to list
     * @method Add
     * @param string $title
     * @param string $url
     * @return (none)
     */
    public function add($title, $url)
    {
        $this->SongList[] = new SongLinkPvm($title, $url);
    }
}
