<?php

namespace Dmoen\MailtrapAssertions;

use GuzzleHttp\Client;

class MailTrapInbox{

    private $client;

    private $mailtrapInbox;

    private $asserts;

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
        $this->asserts = new Asserts();
    }

    public function getLastMessage()
    {
        $messages = $this->fetchAllMessages();

        return reset($messages);
    }

    public function getFirstMessage()
    {
        $messages = $this->fetchAllMessages();

        return end($messages);
    }

    public function getMessage($index)
    {
        $messages = $this->fetchAllMessages();

        if(!isset($messages[$index])){
            $this->asserts->fail("No message found with index $index");
        }

        return $messages[$index];
    }

    public function fetchAllMessages()
    {
        $response = $this->client->request('GET', "inboxes/$this->mailtrapInbox/messages");

        $messages = json_decode((string) $response->getBody());

        if(empty($messages)){
            $this->asserts->fail("No messages in inbox");
        }

        return $messages;
    }

    public function deleteAllMessages()
    {
        $this->client->patch("inboxes/$this->mailtrapInbox/clean");
    }

    public function assertHasMails()
    {
        $this->asserts->assertNotEmpty($this->fetchAllMessages(), "The inbox has no messages");
    }

    public function searchMail(callable $condition)
    {
        $messages = $this->fetchAllMessages();
        $found = false;

        foreach($messages as $message){
            $found = $condition($message);
        }

        return $found;
    }

    public function assertHasMailFor($receiver)
    {
        $found = $this->searchMail(function($message) use($receiver){
            return $message->to_email == $receiver;
        });

        $this->asserts->assertTrue($found, "No message found with receiver $receiver");
    }

    public function assertHasMailWithSubject($subject)
    {
        $found = $this->searchMail(function($message) use($subject){
            return $message->subject == $subject;
        });

        $this->asserts->assertTrue($found, "No message found with subject $subject");
    }

    public function assertHasMailWithHtmlContent($content)
    {
        $found = $this->searchMail(function($message) use($content){
            return strpos($message->html_body, $content) !== false;
        });

        $this->asserts->assertTrue($found, "No message found with content $content");
    }

    public function assertHasMailWithTextContent($content)
    {
        $found = $this->searchMail(function($message) use($content){
            return strpos($message->text_body, $content) !== false;
        });

        $this->asserts->assertTrue($found, "No message found with content $content");
    }

}