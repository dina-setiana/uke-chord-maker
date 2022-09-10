<?php

class SongVm extends BaseVm
{
    public $SongTitle = '';
    public $Subtitle = '';
    public $Album = '';
    public $Artist = '';
    public $Body = '';
    public $UgsMeta = null;
    public $SourceUri = '';
    public $EditUri = '';
    public $EditorSettingsJson = '';

    public $Id = '';

    /**
     * URL where "New Song" AJAX is sent.
     * -- Only used if Editing is enabled and user has permission.
     * @var string
     */
    public $UpdateAjaxUri = '';

    /**
     * If TRUE View may show edit form
     * -- Only used if Editing is enabled and user has permission.
     * @var boolean
     */
    public $IsUpdateAllowed = false;

    function __construct()
    {
        parent::__construct();
        $this->UpdateAjaxUri = Ugs::makeUri(Actions::AJAX_UPDATE_SONG);
    }
}
