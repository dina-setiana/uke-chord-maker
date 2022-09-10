<?php

/**
 * View Model Builder -- Creates a "Song List" View Model
 * @class SongListVmb
 */
class SongListVmb extends BaseVmb
{
    /**
     * Populates SongList View Model by reading and parsing filenames in the source directory
     * @return SongListVm
     */
    public function build()
    {
        $files = FileHelper::getFilenames(Config::$SongDirectory);
        $viewModel = new SongListVm();

        foreach ($files as $filename) {
            // Parse the filename (to make a Title) and create URL.
            $s = preg_replace(Config::FILENAME_PATTERN, '$1', $filename);
            $viewModel->add(
                $this->getTitle($s),
                Ugs::makeUri(Actions::SONG, $s)
            );
        }

        $viewModel->sort();

        return $viewModel;
    }

    /**
     * Handles titles beginning with "The"
     * @method getTitle
     * @param string $filename
     * @return string
     */
    private function getTitle($filename)
    {
        $title = trim(ucwords(str_replace('-', ' ', str_replace('_', ' ', $filename))));
        $pos = strpos($title, 'The ');
        if (($pos !== false) && ($pos == 0)) {
            $title = substr($title, 4, strlen($title)) . ', The';
        }
        return $title;
    }
}
