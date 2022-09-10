<?php 

class RebuildSongCacheVm extends BaseVm {
    public $ElapsedTime = 0.0;
    public $SongCount = 0;

    function __construct(){
        parent::__construct();
        $this->SongbookUri = Ugs::makeUri(Actions::SONGBOOK);
    }
}