<?php

namespace Bi\Connect\Google;

use Bi\Connect\ConnectResponse;
use Tightenco\Collect\Support\Collection;

class YoutubeService extends \Google_Service_YouTube
{
    /**
     * @param $part
     * @param array $optParams
     *
     * @return Collection
     */
    public function channels($part, array $optParams = []): Collection
    {
        return $this->formatResponse(
            $this->channels->listChannels($part, $optParams)
        );
    }

    /**
     * @param $originalResponse
     *
     * @return Collection
     */
    public function formatResponse($originalResponse): Collection
    {
        return (new ConnectResponse(
            [],
            collect($originalResponse)->transform(function ($item) {
                return [$item->id => $item->name];
            })->all(),
            $originalResponse
        ))->getBody();
    }
}
