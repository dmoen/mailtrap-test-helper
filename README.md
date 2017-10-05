# Mailtrap test helper

Test helper libary for testing email submissions through Mailtrap, based on PHPUnit.

## Installation

This package can be installed via Composer:

``` bash
composer require dmoen/mailtrap-test-helper --dev
```

## Usage

Create an instance of MailTrapInbox with api key and id of the inbox:

```php
$inbox = new MailTrapInbox('api_key', 'inbox_id');  
```

### Simple inbox testing

Test if the inbox has any messages:

```php
$inbox->assertHasMails();  
```

Test if the inbox has any messages from a specific address:

```php
$inbox->assertHasMailFrom("sender@example.com");
```

With name:

```php
$inbox->assertHasMailFrom("sender@example.com", "Sender Sendersson");
```

Test if the inbox has any messages to a specific address:

```php
$inbox->assertHasMailFor("reciever@example.com");
```

With name:

```php
$inbox->assertHasMailFor("reciever@example.com", "Receiver Receiversson");
```

Test if inbox has a message with a subject:

```php
$inbox->assertHasMailWithSubject('Lorem subject');
```

Test if inbox has a message with a specific body:

```php
$inbox->assertHasMailWithHtmlContent('<b>Lorem ipsum sit amet</b>');
$inbox->assertHasMailWithTextContent('Lorem ipsum sit amet');
```

### A bit more advanced testing for specific emails

To retrieve a specific message in the inbox:

```php
$inbox->getLastMessage()
$inbox->getFirstMessage()
```
Or if you know the specific index in the inbox:

```php
$message = $inbox->getMessage(2);
```

The message can then be tested with a combination of tests:

```php
$message->assertIsFrom("me@railsware.com", "Private Person")
    ->assertIsFor("test@railsware.com", "A Test User")
    ->assertHasSubject("SMTP e-mail test")
    ->assertHasTextContent("This is a test e-mail message")
    ->assertHasHtmlContent("<b>Lorem ipsum sit amet.</b>");
```