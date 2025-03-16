<?php

use PHPUnit\Framework\TestCase;
use src\AgoraAPI;

class TestApiSimulated extends TestCase
{
    public function testGetBusiness()
    {
        $businessDay = '2013-05-21';
        $enpoint = "api/export/?business-day=" . urlencode($businessDay);

        $agoraAPI = new AgoraAPI();
        $apiresponse = $agoraAPI->connectionApi($enpoint);

        echo $apiresponse;
    }
}
