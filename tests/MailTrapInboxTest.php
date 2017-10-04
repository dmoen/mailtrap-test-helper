<?php
use PHPUnit\Framework\TestCase;
use Dmoen\MailtrapAssertions\MailTrapInbox;
use Dmoen\MailtrapAssertions\Helpers\Mail;

class MailTrapInboxTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_has_mail_for_assert_works()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $inbox->assertHasMailFor("reciever@example.com");
    }

    public function test_has_mails_assert_works()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $inbox->assertHasMails();
    }

    public function test_has_mail_with_subject_assert_works()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $inbox->assertHasMailWithSubject('Lorem subject');
    }

    public function test_has_mail_with_html_content_assert_works()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('<b>Lorem ipsum sit amet</b>')
            ->to("reciever@example.com")
            ->send();

        $inbox->assertHasMailWithHtmlContent('<b>Lorem ipsum sit amet</b>');
    }

    public function test_has_mail_with_text_content_assert_works()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

        (new Mail(false))
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $inbox->assertHasMailWithTextContent('Lorem ipsum sit amet');
    }

    public function test_it_can_retrieve_the_last_message()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

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

        $this->assertEquals("reciever2@example.com", $inbox->getLastMessage()->to_email);
    }

    public function test_it_can_retrieve_the_first_message()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

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

        $this->assertEquals("reciever@example.com", $inbox->getFirstMessage()->to_email);
    }

    public function test_it_can_retrieve_a_message_at_a_specific_index()
    {
        $inbox = new MailTrapInbox('4572a3ecfd10210a085f542c84b3b1b7', '268334');
        $inbox->deleteAllMessages();

        (new Mail())
            ->from('sender@example.com', 'Sender Sendersson')
            ->subject('Lorem subject')
            ->body('Lorem ipsum sit amet')
            ->to("reciever@example.com")
            ->send();

        $this->assertEquals("reciever@example.com", $inbox->getMessage(0)->to_email);
    }
}