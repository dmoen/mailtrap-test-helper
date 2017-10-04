<?php
use PHPUnit\Framework\TestCase;
use Dmoen\MailtrapAssertions\MailTrapInbox;

class AssertionsTest extends TestCase
{
    public function test_reciever_assertion_works()
    {
        $mailer = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');

        $mailer->assertMailSentTo("reciever@example.com");
    }
}