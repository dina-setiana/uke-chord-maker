<?php

/**
 * 
 * @class RebuildSongCacheVmb
 */
class RebuildSongCacheVmb extends BaseVmb
{
    // -----------------------------------------
    // PUBLIC METHODS
    // -----------------------------------------
    public function build()
    {
        $timeStart = microtime(true);

        $cache = new SongListCacheManager();
        $songList = $cache->rebuild();

        $viewModel = new RebuildSongCacheVm();
        $viewModel->SongCount = count($songList);
        $viewModel->ElapsedTime = round(microtime(true) - $timeStart, 5);
        return $viewModel;
    }
}
