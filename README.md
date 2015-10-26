# ReMail
A simple PHP email class
## Description
It uses PHP's `mail()` function. There is also a possibility to attach file.
## How to use it
First of all, you need to include it:
```PHP
include_once( 'ReMail.class.php' );
```
Here is an example how to setup and send mail:
```PHP
$mail = new ReMail( );
$mail->setFrom( 'Alice <alice@wonderland.go>' );
$mail->addTo( 'hatter@wonderland.go' );
$mail->setSubject( 'The Rabbit Hole' );
$mail->setMessage( 'I am coming, dear Hatter!' );
$mail->addAttachment( '/path/to/RabbitHole.jpg' );
$mail->send( );
```
## Interface

### *`addAttachment()`*
**Info**: This method adds an attachment(s) to the mail  
**Arguments**:

1. **Attachment file(s)**
	* **Type**: String *or* Array of strings
	* **Value** - should be absolute or relative path, that PHP engine will parse correctly
2. **Type of the file(s)**
  * **Type**: String
  * **Value** - should be "inline" or "attachment" - in fact, this parameter is used only from another function - to provide the source for HTML images in the message. Not tested for user manipulation.

**Return**: N/A

### *`setMessage()`*
**Info**: This method sets the mail message  
**Arguments**:

1. **The message**
	* **Type**: String
	* **Value** - should be text message

**Return**: N/A

### *`addBcc()`*
**Info**: This method adds an email(s) to *BCC* section  
**Arguments**:

1. **Email(s)**
	* **Type**: String *or* Array of strings
	* **Value** - should be valid email address(es)

**Return**: N/A

### *`addCc()`*
**Info**: This method adds an email(s) to *CC* section  
**Arguments**:

1. **Email(s)**
	* **Type**: String *or* Array of strings
	* **Value** - should be valid email address(es)

**Return**: N/A

### *`addTo()`*
**Info**: This method adds an email(s) to *To* section  
**Arguments**:

1. **Email(s)**
	* **Type**: String *or* Array of strings
	* **Value** - should be valid email address(es)

**Return**: N/A

### *`addReplyTo()`*
**Info**: This method adds an email(s) to *Reply-To* section  
**Arguments**:

1. **Email(s)**
	* **Type**: String *or* Array of strings
	* **Value** - should be valid email address(es)

**Return**: N/A

### *`setFrom()`*
**Info**: This method set the name/email of the sender  
**Arguments**:

1. **Sender**
	* **Type**: String
	* **Value** - should be text

**Return**: N/A

### *`setSubject()`*
**Info**: This method set the subject of the conversation  
**Arguments**:

1. **Subject**
	* **Type**: String
	* **Value** - should be text

**Return**: N/A

### *`send()`*
**Info**: This method sends the email conversation  
**Arguments**: N/A  
**Return**: *`true`* for success, `'no-valid-emails'` - if the object hasn't been provided with an email addresses or other value in rest cases
