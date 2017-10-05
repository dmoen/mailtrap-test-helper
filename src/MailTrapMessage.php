<?php

namespace Dmoen\MailtrapTestHelper;

class MailTrapMessage
{
    private $asserts;

    private $source;

    public function __construct($source)
    {
        $this->asserts = new Asserts();
        $this->source = $source;
    }

    public function __get($name)
    {
        if (property_exists($this->source, $name)) {
            return $this->source->$name;
        }

        throw new \OutOfBoundsException("Message property $name doesn't exist");
    }

    public function assertIsFor($receiver, $name = null)
    {
        if(!$name) {
            $found = $this->source->to_email == $receiver;

            $this->asserts->assertTrue($found, "Message not sent to: $receiver. Message sent to: {$this->source->to_email}");
        }
        else{
            $found = $this->source->to_email == $receiver
                && $this->source->to_name == $name;

            $this->asserts->assertTrue($found, "Message not sent to: $name <$receiver>. Message sent to: {$this->source->to_name} <{$this->source->to_email}>");
        }

        return $this;
    }

    public function assertIsFrom($sender, $name = null)
    {
        if(!$name) {
            $found = $this->source->from_email == $sender;

            $this->asserts->assertTrue($found, "Message not sent from: $sender. Message sent from: {$this->source->from_email}");
        }
        else{
            $found = $this->source->from_email == $sender
                && $this->source->from_name == $name;

            $this->asserts->assertTrue($found, "Message not sent from: $name <$sender>. Message sent from: {$this->source->from_name} <{$this->source->from_email}>");
        }

        return $this;
    }

    public function assertHasSubject($subject)
    {
        $found = $this->source->subject == $subject;

        $this->asserts->assertTrue($found, "Message doesn't have subject: $subject. Message has subject: ".$this->source->subject);

        return $this;
    }

    public function assertHasHtmlContent($content)
    {
        $found = strpos($this->source->html_body, $content) !== false;

        $this->asserts->assertTrue($found, "Message doesn't have content: $content. Message has content: ".$this->source->html_body);

        return $this;
    }

    public function assertHasTextContent($content)
    {
        $found = strpos($this->source->text_body, $content) !== false;

        $this->asserts->assertTrue($found, "Message doesn't have content: $content. Message has content: ".$this->source->text_body);

        return $this;
    }
}