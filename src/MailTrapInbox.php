<?php

namespace Dmoen\MailtrapAssertions;

use PHPUnit\Framework\Assert;
use GuzzleHttp\Client;

class MailTrapInbox extends Assert{

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

    public function getLastMessage()
    {
        $messages = $this->fetchAllMessages();

        if(empty($messages)) {
            $this->fail("No messages received");
        }

        return reset($messages);
    }

    public function fetchAllMessages()
    {
        $response = $this->client->request('GET', "inboxes/$this->mailtrapInbox/messages");
        return json_decode((string) $response->getBody());
    }

    public function deleteAllMessages()
    {
        $this->client->patch("inboxes/$this->mailtrapInbox/clean");
    }

    public function assertHasMails()
    {
        $this->assertNotEmpty($this->fetchAllMessages(), "The inbox has no messages");
    }

    public function assertHasMailFor($receiver)
    {
        $messages = $this->fetchAllMessages();
        $found = false;

        foreach($messages as $message){
            if($message->to_email == $receiver){
                $found = true;
            }
        }

        $this->assertTrue($found, "No message found with receiver $receiver");
    }

    public function assertHasMailWithSubject($subject)
    {
        $messages = $this->fetchAllMessages();
        $found = false;

        foreach($messages as $message){
            if($message->subject == $subject){
                $found = true;
            }
        }

        $this->assertTrue($found, "No message found with subject $subject");
    }

    public function assertHasMailWithHtmlContent($content)
    {
        $messages = $this->fetchAllMessages();
        $found = false;

        foreach($messages as $message){
            if(strpos($message->html_body, $content) !== false){
                $found = true;
            }
        }

        $this->assertTrue($found, "No message found with content $content");
    }

    public function assertHasMailWithTextContent($content)
    {
        $messages = $this->fetchAllMessages();
        $found = false;

        foreach($messages as $message){
            if(strpos($message->text_body, $content) !== false){
                $found = true;
            }
        }

        $this->assertTrue($found, "No message found with content $content");
    }

}