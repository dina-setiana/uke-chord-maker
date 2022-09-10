<?php

/**
 * Builds the SongList array, either reading from or writing to the SimpleCache.
 * @class SongListCacheManager
 */
class SongListCacheManager
{

    private $_cache;

    // -----------------------------------------
    // PUBLIC METHODS
    // -----------------------------------------
    function __construct()
    {
        $this->_cache = new SimpleCache();
        $this->_cache->setCacheDir(Config::$AppDirectory . 'cache/');
    }

    /**
     * Rebuilds the cache file by reading & parsing all ChordPro song files.
     * @return array song list
     */
    public function rebuild()
    {
        // large song collections (1,000's of songs) might timeout, set max number of seconds for this task
        set_time_limit(45);
        $files = FileHelper::getFilenames(Config::$SongDirectory);
        $songList = $this->_buildFileList($files);

        $this->_cache->put(Config::SONG_CACHE_KEY_FILENAME, serialize($songList));

        return $songList;
    }

    /**
     * returns the song list -- tries to fetch from cache, if that fails, rebuilds
     */
    public function get()
    {
        if (!$this->_cache->exists(Config::SONG_CACHE_KEY_FILENAME)) {
            return $this->rebuild();
        }

        $cachedSongList = $this->_cache->get(Config::SONG_CACHE_KEY_FILENAME);
        return unserialize($cachedSongList);
    }

    // -----------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------

    /**
     * Emits list of links to all songs in the directory.
     * @method _buildFileList
     * @return (song array)
     */
    private function _buildFileList($files)
    {
        $list = new SongListPlusPvm();

        foreach ($files as $fname) {
            $s = preg_replace(Config::FILENAME_PATTERN, '$1', $fname);

            $content = FileHelper::getFile(Config::$SongDirectory . $fname);
            $parsed = SongHelper::parseSong($content);

            $song = new SongLinkPlusPvm();
            $song->Uri = Ugs::makeUri(Actions::SONG, $s);
            $song->HasInfo = (strlen($parsed->title) + strlen($parsed->artist)) > 0;
            $song->Title = $this->_fixLeadingArticle((strlen($parsed->title) > 0) ? $parsed->title : $this->_filenameToTitle($s));
            $song->Subtitle = $parsed->subtitle;
            $song->Album = $parsed->album;
            $song->Artist = $parsed->artist;

            $list->SongList[] = $song;
        }
        return $list->sort();
    }

    /**
     * convert a filename to a pseudo-title
     * @method _filenameToTitle
     * @param string $filename
     * @return string
     */
    private function _filenameToTitle($filename)
    {
        return trim(ucwords(str_replace('-', ' ', str_replace('_', ' ', $filename))));
    }

    /**
     * Handles titles beginning with "The", "A", "An"
     * @method _fixLeadingArticle
     * @param string $title
     * @return string
     */
    private function _fixLeadingArticle($title)
    {
        $r = '/^(the|a|an) (.*)$/i';
        if (preg_match($r, $title)) {
            $title = preg_replace($r, '$2, $1', $title);
        }

        return $title;
    }
}
