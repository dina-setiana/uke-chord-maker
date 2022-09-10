<?php


/**
 * View Model Builder -- Creates a "Song" View Model
 * @class SongVmb
 */
class SongVmb extends BaseVmb
{
    /**
     * Parses file (using URL query param) and attempts to load View Model
     * @return SongVm
     */
    public function build()
    {
        $filename = FileHelper::getFilename();
        $fileContent = FileHelper::getFile(Config::$SongDirectory . $filename);
        $song = SongHelper::parseSong($fileContent);

        $title = htmlspecialchars((($song->isOK) ? ($song->title . ((strlen($song->subtitle) > 0) ? (' | ' . $song->subtitle) : '')) : 'Not Found'));

        $viewModel = new SongVm();
        $viewModel->PageTitle = $this->_makePageTitle($song, $filename);
        $viewModel->SongTitle = htmlspecialchars($song->title);
        $viewModel->Subtitle = htmlspecialchars($song->subtitle);
        $viewModel->Artist = $song->artist;
        $viewModel->Album = $song->album; // htmlspecialchars();
        $viewModel->Body = $song->body;
        $viewModel->UgsMeta = $song->meta;
        $viewModel->SourceUri = Ugs::makeUri(Actions::SOURCE, $filename);
        $viewModel->EditUri = Ugs::makeUri(Actions::EDIT, $filename);

        $viewModel->Id = $filename;
        $viewModel->IsUpdateAllowed = $this->SiteUser->MayEdit && $this->SiteUser->IsAuthenticated;

        $viewModel->EditorSettingsJson = $this->getSettings();
        return $viewModel;
    }

    /**
     * Does not validate values, but does ensure only valid JSON was provided.
     * @method getSettings
     * @return string
     */
    private function getSettings()
    {
        $settings = FileHelper::getFile(Config::$AppDirectory . 'settings.json');
        if ($settings === null) {
            return '{}';
        }

        if (!function_exists('json_decode')) {
            return $settings;
        }

        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $settings);
        if (json_decode($json)) {
            return $settings;
        }

        return '{"invalidJson": "There is a problem with your settings: invalid JSON. Please check for typos."}';
    }

    /**
     * Uses either Title(s) from Song or the file name
     * @param object $song
     * @param string $filename
     * @return string
     */
    private function _makePageTitle($song, $filename)
    {
        $title = '';

        if ($song->isOK) {
            $title = $song->title;

            if (strlen($song->artist) > 0) {
                $title .= ' - '    . $song->artist;
            } else if (strlen($song->subtitle) > 0) {
                $title .= ' - ' . $song->subtitle;
            }

            $title = htmlspecialchars($title);
        }

        return ((strlen($title) > 0) ? $title : $filename) . ' ' . Config::PAGE_TITLE_SUFFIX;
    }
}
