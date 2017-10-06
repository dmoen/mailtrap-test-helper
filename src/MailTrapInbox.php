<?php

namespace Dmoen\MailtrapTestHelper;

use GuzzleHttp\Client;

class MailTrapInbox
{
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
        return $this->getMessage(0);
    }

    public function getFirstMessage()
    {
        return $this->getMessage(-1);
    }

    public function getMessage($index)
    {
        $messages = $this->fetchAllMessages();

        if($index < 0){
            $index = count($messages) + $index;
        }

        if(!isset($messages[$index])){
            $this->asserts->fail("No message found with index: $index");
        }

        return $messages[$index];
    }

    public function fetchAllMessages($searchString = null)
    {
        $response = $this->client->request('GET', "inboxes/$this->mailtrapInbox/messages"
            .($searchString ?: "?search=$searchString"));
        $messages = json_decode((string) $response->getBody());

        if(empty($messages)){
            $this->asserts->fail("No messages found in inbox");
        }

        array_walk($messages, function(&$message){
            $message = new MailTrapMessage($message);
        });

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

    private function searchMail(callable $condition)
    {
        $messages = $this->fetchAllMessages();
        $found = false;

        foreach($messages as $message){
            $found = $condition($message);
        }

        return $found;
    }

    public function findUnique(callable $condition)
    {
        $messages = $this->fetchAllMessages();

        $foundMessages = array_filter($messages, function($message) use($condition){
            return $condition($message);
        });

        if(count($foundMessages) > 1){
            $this->asserts->fail("Found more than one message matching condition");
        }
        else if(!$foundMessages){
            $this->asserts->fail("Found no message matching condition");
        }

        return $foundMessages[0];
    }

    public function findMessages(callable $condition)
    {
        $messages = $this->fetchAllMessages();

        $foundMessages = array_filter($messages, function($message) use($condition){
            return $condition($message);
        });

        if(!$foundMessages){
            $this->asserts->fail("Found no messages matching condition");
        }

        return $foundMessages;
    }

    public function assertHasMailFrom($sender, $name = null)
    {
        $found = $this->searchMail(function($message) use($sender, $name){
            if($name){
                return $message->from_email == $sender
                    && $message->from_name == $name;
            }

            return $message->from_email == $sender;
        });

        if(!$name){
            $this->asserts->assertTrue($found, "No message found with sender: $sender");
        }
        else{
            $this->asserts->assertTrue($found, "No message found with sender: $name <$sender>");
        }
    }

    public function assertHasMailFor($receiver, $name = null)
    {
        $found = $this->searchMail(function($message) use($receiver, $name){
            if($name){
                return $message->to_email == $receiver
                    && $message->to_name == $name;
            }

            return $message->to_email == $receiver;
        });

        if(!$name){
            $this->asserts->assertTrue($found, "No message found with receiver: $receiver");
        }
        else{
            $this->asserts->assertTrue($found, "No message found with receiver: $name <$receiver>");
        }
    }

    public function assertHasMailWithSubject($subject)
    {
        $found = $this->searchMail(function($message) use($subject){
            return $message->subject == $subject;
        });

        $this->asserts->assertTrue($found, "No message found with subject: $subject");
    }

    public function assertHasMailWithHtmlContent($content)
    {
        $found = $this->searchMail(function($message) use($content){
            return strpos($message->html_body, $content) !== false;
        });

        $this->asserts->assertTrue($found, "No message found with content: $content");
    }

    public function assertHasMailWithTextContent($content)
    {
        $found = $this->searchMail(function($message) use($content){
            return strpos($message->text_body, $content) !== false;
        });

        $this->asserts->assertTrue($found, "No message found with content: $content");
    }

}