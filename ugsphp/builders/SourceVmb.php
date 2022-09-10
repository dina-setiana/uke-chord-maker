<?php

/**
 * View Model Builder -- Creates a "Source" View Model
 * @class SourceVmb
 */
class SourceVmb extends BaseVmb
{
    /**
     * Populates Source View Model
     * @return SourceVm
     */
    public function build()
    {
        $fname = FileHelper::getFilename();
        $data = FileHelper::getFile(Config::$SongDirectory . $fname);
        $viewModel = new SourceVm();
        $viewModel->PageTitle = 'Song Source for &quot;' . $fname . '&quot; ChordPro (CPM)/UkeGeeks File Format';
        $viewModel->Body = htmlspecialchars($data);

        return $viewModel;
    }
}
