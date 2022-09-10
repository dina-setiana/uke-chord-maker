<?php

/**
 * View Model Builder --
 * @class SongListDetailedVmb
 */
class SongListDetailedVmb extends BaseVmb
{

    /**
     * Populates SongList View Model using Cache Manager
     */
    public function build()
    {
        $viewModel = new SongListVm();
        $viewModel->IsNewAllowed = $this->SiteUser->MayEdit && $this->SiteUser->IsAuthenticated;
        $cache = new SongListCacheManager();
        $viewModel->SongList = $cache->get();

        return $viewModel;
    }
}
