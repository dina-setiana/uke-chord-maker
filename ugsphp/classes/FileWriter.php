<?php
class NewSongVm
{
    public $IsHttpPost = false;
    public $HasErrors = false;
    public $Title = '';
    public $Artist = '';
    public $Message = '';
    public $Filename = '';
}

class FileWriter
{
    const ID_PREFIX = '.ugs';

    const MAX_TITLE_LENGTH = 40;
    const MAX_ARTIST_NAME = 25;

    private static $allowedCharsRegEx = '/[^a-z_\-0-9]/';
    private static $dupeSpacersRegEx = '/(_|-)+/';

    /**
     * returns a OS-safe suitable filename from supplied value
     *
     * @param [string] $value     [description]
     * @param integer $maxLength (optional) maximum length (might be off by two characters as trimming leading _ happens last)
     * @return string
     */
    public function scrubForFilename($value, $maxLength = 35)
    {
        $value = str_replace('\'', '', trim(strtolower($value)));
        if (strlen($value) < 1) {
            return '';
        }

        $value = preg_replace(self::$dupeSpacersRegEx, '_', preg_replace(self::$allowedCharsRegEx, '_', $value));
        $value = substr($value, 0, $maxLength);

        return preg_replace('/^_/', '', preg_replace('/_$/', '', $value));
    }

    /**
     * Generates a unique filename (within a given directory)
     * If name is omitted uses "untitled".
     *
     * @param [string] $title
     * @param [string] $artist
     * @return string
     */
    public function makeFile($title, $artist)
    {
        $f = $this->scrubForFilename($title, self::MAX_TITLE_LENGTH);
        if (strlen($f) < 1) {
            $f = 'untitled';
        }

        $a = $this->scrubForFilename($artist, self::MAX_ARTIST_NAME);
        if (strlen($a) > 0) {
            $f .= '.' . $a;
        }

        $content = $this->_makeChordProStub($title, $artist);

        try {
            if (!is_writable(Config::$SongDirectory)) {
                return '';
            }
            $filename = $this->_uniqueFilename(Config::$SongDirectory, $f, Config::FILE_EXTENSION);
            if (strlen($filename) < 1) {
                return '';
            }

            $filesize = file_put_contents(Config::$SongDirectory . $filename, $content);

            return ($filesize > 0) ? $filename : '';
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * using supplied info keeps testing for a unique file name by appending random-ish string to
     * filename created using base name
     *
     * @param string  $directory    where the file should live
     * @param string  $baseFilename this does not need to be unique, but is ideally descriptive
     * @param string  $extension    (optional) file's extension, please include the dot!
     * @return string final file name
     */
    private function _uniqueFilename($directory, $baseFilename, $extension = '.txt')
    {
        try {
            $filename = '';
            while (true) {
                $m = microtime();
                $filename = $baseFilename . self::ID_PREFIX . (($m - floor($m)) * 1000000) . $extension;
                if (!file_exists($directory . $baseFilename)) {
                    break;
                }
            }

            return $filename;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * returns skeleton ChordPro (text) file using supplied Title, Artist, etc
     *
     * @param [string] $title  song title
     * @param [string] $artist song's artist (optional)
     * @return string
     */
    private function _makeChordProStub($title = '', $artist = '')
    {
        $title = trim($title);
        $artist = trim($artist);

        $content = '{title: ' . (strlen($title) > 0 ? $title : 'untitled') . "}\n";
        $content .= '{artist: ' . (strlen($artist) > 0 ? $artist : 'traditional') . "}\n";

        return $content . 'TYPE_YOUR_SONG_HERE';
    }
}
