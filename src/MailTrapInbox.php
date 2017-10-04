<?php

namespace Dmoen\MailtrapAssertions;

use GuzzleHttp\Client;

class MailTrapInbox {

    private $client;

    private $mailtrapInbox;

    const BASEURL = "https://mailtrap.io/api/v1/";

    public function __construct($apiToken, $mailtrapInbox)
    {
        $this->mailtrapInbox = $mailtrapInbox;

        $this->client = new Client([
            'base_uri' => self::BASEURL,
            'headers' => [
                'Api-Token' => $apiToken
            ]
        ]);
    }

    public function assertMailSentTo($reciever)
    {
        $response = $this->client->request('GET', "inboxes/$this->mailtrapInbox/messages");

        var_dump(json_decode((string) $response->getBody()));

        return json_decode((string) $response->getBody());
    }

}