<?php

namespace BFI\Email;

/**
 * Class Smtp
 * @package BFI\Email
 */
class Smtp
{
    /**
     * SMTP Username
     * @var string
     */
    protected $_username;

    /**
     * SMTP password
     * @var string
     */
    protected $_password;

    /**
     * SMTP Server
     * @var string
     */
    protected $_server;

    /**
     * SMTP Port
     * @var int
     */
    protected $_port;


    /**
     * Email address of the sender
     * @var string
     */
    protected $_sender;

    /**
     * Plain name of the sender
     * @var string
     */
    protected $_senderName;

    /**
     * The email recipients
     * @usage array('email' => 'name')
     * @var array
     */
    protected $_recipients = array();

    /**
     * The email CC recipients
     * @usage array('email' => 'name')
     * @var array
     */
    protected $_recipientsCc = array();

    /**
     * The email BCC recipients
     * @usage array('email' => 'name')
     * @var array
     */
    protected $_recipientsBcc = array();

    /**
     * The email subject
     * @var string
     */
    protected $_subject = '';

    /**
     * The array of all email body parts
     * @var array
     */
    protected $_bodyParts = array();

    /**
     * The MIME boundary
     * @var string
     */
    protected $_boundary;

    /**
     * @param string $username
     * @param string $password
     * @param string $server
     * @param int $port
     */
    function __construct($username, $password, $server, $port = 25)
    {
        $this->_server = $server;
        $this->_port = $port;
        $this->_username = base64_encode($username);
        $this->_password = base64_encode($password);
        $this->_boundary = uniqid() . uniqid();
    }

    /**
     * Submit the message
     * @return array
     */
    public function submit()
    {
        $talk = array();

        $SmtpConnection = fsockopen($this->_server, $this->_port);
        $commands = array(
            'hello' => 'EHLO ' . $_SERVER["HTTP_HOST"] . "\r\n",
            'res' => 'AUTH PLAIN ' . "\r\n",
            'user' => $this->_username . "\r\n",
            'pass' => $this->_password . "\r\n",
            'from' => 'MAIL FROM: <' . $this->_sender . ">\r\n"
        );
        foreach ($this->_recipients as $rcpt => $name) {
            $commands['to ' . $rcpt] = 'RCPT TO: <' . $rcpt . ">\r\n";
        }
        $commands['data'] = "DATA\r\n";
        $commands['send'] = $this->assembleMail();
        $commands['quit'] = "QUIT\r\n";
        if ($SmtpConnection) {
            foreach ($commands as $key => $cmd) {
                echo $key . ' -> ' . $cmd;
                fputs($SmtpConnection, $cmd);
                do {
                    $line = fgets($SmtpConnection, 2048);
                    echo $key . ' <- ' . $line;
                } while (! preg_match('/^[0-9]{3} /', $line));
            }
            fclose($SmtpConnection);
        }
        return $talk;
    }

    /**
     * Assemble the Mail content
     * @return string
     */
    public function assembleMail()
    {
        $mailOut = 'To: ' . $this->_assembleAdresses($this->_recipients) . "\r\n";
        $mailOut .= 'From: ' . $this->_assembleEmail($this->_sender, $this->_senderName) . "\r\n";
        if (! empty($this->_recipientsCc)) {
            $mailOut .= 'Cc: ' . $this->_assembleAdresses($this->_recipientsCc) . "\r\n";
        }
        if (! empty($this->_recipientsCc)) {
            $mailOut .= 'Bcc: ' . $this->_assembleAdresses($this->_recipientsBcc) . "\r\n";
        }
        $date = new \DateTime();
        $mailOut .= 'Date: ' . $date->format(\DateTime::RFC2822) . "\r\n";
        $mailOut .= 'Subject: ' . $this->_subject . "\r\n";
        $mailOut .= "MIME-Version: 1.0\r\n";
        $mailOut .= 'Content-Type: multipart/mixed; boundary=' . $this->_boundary . "\r\n\r\n";
        $mailOut .= "This is a MIME encoded Message";
        foreach ($this->_bodyParts as $part) {
            $mailOut .= $part->assemble();
        }
        $mailOut .= '--' . $this->_boundary . '--' . "\r\n.\r\n";
        return $mailOut;
    }

    /**
     * Assemble Email correct for mail header
     * @param string $mail
     * @param string $name
     * @return string
     */
    protected function _assembleEmail($mail, $name = null)
    {
        $sender = '<' . $mail . '>';
        if (! empty($name)) {
            $sender = $name . ' ' . $sender;
        }
        return $sender;
    }

    /**
     * Assemble an array of email adresses correct for mail header
     * @param array $adresses
     * @return string
     */
    protected function _assembleAdresses(array $adresses)
    {
        $out = array();
        foreach ($adresses as $mail => $name) {
            $out[] = $this->_assembleEmail($mail, $name);
        }
        return implode(',', $out);
    }

    /**
     * Add plain string part
     * @param string $text
     * @return $this
     */
    public function addTextBody($text)
    {
        $this->addBody(new BodyPart($text, $this->_boundary, BodyPart::CONTENT_TYPE_PLAIN));
        return $this;
    }

    /**
     * Add html part
     * @param string $html
     * @return $this
     */
    public function addHtmlBody($html)
    {
        $this->addBody(new BodyPart($html, $this->_boundary, BodyPart::CONTENT_TYPE_HTML));
        return $this;
    }

    /**
     * Add a file attachment
     * @param string $filename
     * @return $this
     */
    public function addAttachment($filename)
    {
        $this->addBody(new BodyPart($filename, $this->_boundary, BodyPart::CONTENT_TYPE_ATTACHMENT));
        return $this;
    }

    /**
     * Add a body part
     * @param BodyPart $part
     * @return $this;
     */
    public function addBody(BodyPart $part)
    {
        $this->_bodyParts[] = $part;
        return $this;
    }

    /**
     * Get all body parts
     * @return array
     */
    public function getBodyParts()
    {
        return $this->_bodyParts;
    }

    /**
     * Add receiver
     * @param string $recipient
     * @param string $name
     * @return $this;
     */
    public function addRecipient($recipient, $name = '')
    {
        $this->_recipients[$recipient] = $name;
        return $this;
    }

    /**
     * Get all recipients
     * @return array
     */
    public function getRecipients()
    {
        return $this->_recipients;
    }

    /**
     * @param string $sender
     * @param string $name
     * @return $this;
     */
    public function setSender($sender, $name = '')
    {
        $this->_sender = $sender;
        $this->_senderName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->_sender;
    }

    /**
     * Return Name of the Sender
     * @return string
     */
    public function getSenderName()
    {
        if (! empty($this->_senderName)) {
            return $this->_senderName;
        }
        return $this->_sender;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }
} 