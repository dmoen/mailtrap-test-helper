<?php
use PHPUnit\Framework\TestCase;
use Dmoen\MailtrapTestHelper\MailTrapInbox;
use Dmoen\MailtrapTestHelper\Helpers\Mail;
use Dmoen\MailtrapTestHelper\MailTrapMessage;

class MailTrapInboxTest extends TestCase
{
    private $inbox;

    public function setUp()
    {
        parent::setUp();

        $dotenv = new Dotenv\Dotenv(dirname(__DIR__));
        $dotenv->load();

        $this->inbox = new MailTrapInbox(getenv('MAILTRAP_API_TOKEN'), getenv('MAILTREP_INBOX'));
        $this->inbox->deleteAllMessages();
    }

    public function test_has_mail_for_assert_works()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com", "Receiver Receiversson")
            ->send();

        $this->inbox->assertHasMailFor("reciever@example.com");
        $this->inbox->assertHasMailFor("reciever@example.com", "Receiver Receiversson");
    }

    public function test_has_mail_from_assert_works()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.co")
            ->send();

        $this->inbox->assertHasMailFrom("sender@example.com");
        $this->inbox->assertHasMailFrom("sender@example.com", "Sender Sendersson");
    }

    public function test_has_mails_assert_works()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $this->inbox->assertHasMails();
    }

    public function test_has_mail_with_subject_assert_works()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $this->inbox->assertHasMailWithSubject('Lorem subject');
    }

    public function test_has_mail_with_html_content_assert_works()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('<b>Lorem ipsum sit amet</b>')
            ->to("reciever@example.com")
            ->send();

        $this->inbox->assertHasMailWithHtmlContent('<b>Lorem ipsum sit amet</b>');
    }

    public function test_has_mail_with_text_content_assert_works()
    {
        (new Mail(false))
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $this->inbox->assertHasMailWithTextContent('Lorem ipsum sit amet');
    }

    public function test_it_can_retrieve_the_last_message()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        (new Mail())
            ->from('sender2@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever2@example.com")
            ->send();

        $this->assertEquals("reciever2@example.com", $this->inbox->getLastMessage()->to_email);
    }

    public function test_it_can_retrieve_the_first_message()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        (new Mail())
            ->from('sender2@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever2@example.com")
            ->send();

        $this->assertEquals("reciever@example.com", $this->inbox->getFirstMessage()->to_email);
    }

    public function test_it_can_retrieve_a_correct_message_at_a_specific_index()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('<b>Lorem ipsum sit amet</b>')
            ->to("reciever@example.com", "Reviever Recieversson")
            ->send();

        $message = $this->inbox->getMessage(0);

        $this->assertInstanceOf(MailTrapMessage::class, $message);

        $message->assertIsFrom('sender@example.com', 'Sender Sendersson')
            ->assertIsFor("reciever@example.com", "Reviever Recieversson")
            ->assertHasSubject("Lorem subject")
            ->assertHasHtmlContent("<b>Lorem ipsum sit amet</b>");
    }

    public function test_it_finds_an_unique_message()
    {
        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('<b>Lorem ipsum sit amet</b>')
            ->to("receiver@example.com", "Reviever Recieversson")
            ->send();

        $message = $this->inbox->findUnique(function($message){
            return $message->to_email == "receiver@example.com";
        });

        $this->assertInstanceOf(MailTrapMessage::class, $message);
        $this->assertEquals("receiver@example.com", $message->to_email);
    }
}