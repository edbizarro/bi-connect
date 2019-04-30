<?php

namespace Bi\Connect\Google;

use Tightenco\Collect\Support\Collection;

class YoutubeService extends \Google_Service_YouTube
{
    /**
     * @return Collection
     */
    public function getAccounts(): Collection
    {
        return $this->formatResponse(
            $this->channels->listChannels()
        );
    }
}
