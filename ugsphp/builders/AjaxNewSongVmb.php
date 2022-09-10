<?php

class AjaxNewSongVmb extends BaseVmb
{
    public function build()
    {
        $viewModel = new JsonResponseVm();

        if (!$this->SiteUser->MayEdit || !$this->SiteUser->IsAuthenticated) {
            return $viewModel;
        }

        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            return $viewModel;
        }

        $json = Ugs::getJsonObject();
        if ($this->_createSongFile($json->songTitle, $json->songArtist, $viewModel)) {
            $cache = new SongListCacheManager();
            $cache->rebuild();
        }
        return $viewModel;
    }

    private function _createSongFile($title, $artist, $viewModel)
    {
        if (strlen($title) < 1) {
            $viewModel->HasErrors = true;
            $viewModel->Message = 'Song title is required, sorry.';
            return false;
        }
        try {
            $fWriter = new FileWriter();
            $viewModel->Id = $fWriter->makeFile($title, $artist);
            $viewModel->HasErrors = (strlen($viewModel->Id) < 1);
            if ($viewModel->HasErrors) {
                $viewModel->Message = '(e:803) Something\'s gone wrong whilst saving.';
                return false;
            }
        } catch (Exception $e) {
            $viewModel->Message = '(e:805) Something\'s gone wrong whilst saving.';
            return false;
        }

        $viewModel->ContinueUri = Ugs::makeUri(Actions::EDIT, $viewModel->Id);
        return true;
    }
}
