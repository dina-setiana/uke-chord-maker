<?php

class SongLinkPlusPvm
{
    public $Uri = '';
    public $Title = '';
    public $Subtitle = '';
    public $Album = '';
    public $Artist = '';
    public $HasInfo = false;
}

/**
 *
 */
class SongListPlusPvm
{
    public $SongList = array();

    /**
     * Sorts the Song List based on title
     * @method Sort
     * @return (SongLinkPlusPvm array)
     */
    public function sort()
    {

        function scrub($val)
        {
            return trim(preg_replace('/\s+/', ' ', preg_replace('/\W/', ' ', strtolower($val))));
        }

        $tieBreaker = 0;
        $songsListRekeyed = array();
        $titlesList = array();
        $titleKey = '';

        foreach ($this->SongList as $song) {
            $titleKey = scrub($song->Title);
            if (!isset($temp[$titleKey])) {
                $titleKey .= ' _' . $tieBreaker . '_ugs87!';
                $tieBreaker++;
            }
            $titlesList[] = $titleKey;
            $songsListRekeyed[$titleKey] = $song;
        }

        sort($titlesList);

        $this->SongList = array();
        foreach ($titlesList as $key) {
            $this->SongList[] = $songsListRekeyed[$key];
        }
        return $this->SongList;
    }
}
