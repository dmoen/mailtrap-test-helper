<?php
use PHPUnit\Framework\TestCase;
use Dmoen\MailtrapTestHelper\MailTrapMessage;

class MailTrapMessageTest extends TestCase
{
    private $message;

    public function setUp()
    {
        parent::setUp();

        $source = json_decode(file_get_contents(__DIR__.'/message.json'));

        $this->message = new MailTrapMessage($source);
    }


    public function test_is_for_assert_works()
    {
        $this->message->assertIsFor("test@railsware.com");
        $this->message->assertIsFor("test@railsware.com", "A Test User");
    }

    public function test_is_from_assert_works()
    {
        $this->message->assertIsFrom("me@railsware.com");
        $this->message->assertIsFrom("me@railsware.com", "Private Person");
    }

    public function test_has_subject_assert_works()
    {
        $this->message->assertHasSubject("SMTP e-mail test");
    }

    public function test_has_html_content_assert_works()
    {
        $this->message->assertHasHtmlContent("<b>Lorem ipsum sit amet.</b>");
    }

    public function test_has_text_content_assert_works()
    {
        $this->message->assertHasTextContent("This is a test e-mail message");
    }

    public function test_fluent_api_works()
    {
        $this->message->assertIsFrom("me@railsware.com", "Private Person")
            ->assertIsFor("test@railsware.com", "A Test User")
            ->assertHasSubject("SMTP e-mail test")
            ->assertHasTextContent("This is a test e-mail message")
            ->assertHasHtmlContent("<b>Lorem ipsum sit amet.</b>");
    }
}