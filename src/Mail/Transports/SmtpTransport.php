<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Transports;

use Palet\Framework\Contracts\Mail\MailTransportInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;
use RuntimeException;

class SmtpTransport implements MailTransportInterface
{
    protected string $host;
    protected int $port;
    protected ?string $username;
    protected ?string $password;
    protected string $encryption;

    public function __construct(
        string $host = '127.0.0.1',
        int $port = 25,
        ?string $username = null,
        ?string $password = null,
        string $encryption = ''
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = $encryption;
    }

    public function send(MailMessageInterface $message): void
    {
        // For a production framework without external dependencies like SwiftMailer/SymfonyMailer,
        // we use PHP's stream_socket_client for basic SMTP.
        $protocol = '';
        if ($this->encryption === 'ssl') {
            $protocol = 'ssl://';
        } elseif ($this->encryption === 'tls') {
            $protocol = 'tls://'; // Note: STARTTLS is more complex, requiring stream_crypto_enable
        }

        $socket = stream_socket_client($protocol . $this->host . ':' . $this->port, $errno, $errstr, 15);

        if (!$socket) {
            throw new RuntimeException("Could not connect to SMTP server: {$this->host}:{$this->port} - {$errstr} ({$errno})");
        }

        $this->readResponse($socket);

        $this->sendCommand($socket, "EHLO localhost");
        $this->readResponse($socket);

        if ($this->encryption === 'tls' && $protocol === '') {
            $this->sendCommand($socket, "STARTTLS");
            $this->readResponse($socket);
            stream_crypto_enable($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->sendCommand($socket, "EHLO localhost");
            $this->readResponse($socket);
        }

        if ($this->username && $this->password) {
            $this->sendCommand($socket, "AUTH LOGIN");
            $this->readResponse($socket);
            $this->sendCommand($socket, base64_encode($this->username));
            $this->readResponse($socket);
            $this->sendCommand($socket, base64_encode($this->password));
            $this->readResponse($socket);
        }

        $from = $message->getFrom();
        if (!$from) {
            throw new RuntimeException("Sender is required.");
        }

        $this->sendCommand($socket, "MAIL FROM:<{$from['address']}>");
        $this->readResponse($socket);

        $recipients = array_merge($message->getTo(), $message->getCc(), $message->getBcc());
        foreach ($recipients as $recipient) {
            $this->sendCommand($socket, "RCPT TO:<{$recipient['address']}>");
            $this->readResponse($socket);
        }

        $this->sendCommand($socket, "DATA");
        $this->readResponse($socket);

        $headers = $this->buildHeaders($message);
        $body = $message->getBody();
        $mailData = $headers . "\r\n\r\n" . $body . "\r\n.";

        $this->sendCommand($socket, $mailData);
        $this->readResponse($socket);

        $this->sendCommand($socket, "QUIT");
        $this->readResponse($socket);

        fclose($socket);
    }

    protected function sendCommand($socket, string $command): void
    {
        fwrite($socket, $command . "\r\n");
    }

    protected function readResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }

    protected function buildHeaders(MailMessageInterface $message): string
    {
        $headers = [];
        $headers[] = "Subject: " . $message->getSubject();
        
        $from = $message->getFrom();
        if ($from) {
            $headers[] = "From: " . $this->buildAddress($from);
        }

        $to = $message->getTo();
        if (!empty($to)) {
            $headers[] = "To: " . $this->buildAddresses($to);
        }

        $cc = $message->getCc();
        if (!empty($cc)) {
            $headers[] = "Cc: " . $this->buildAddresses($cc);
        }

        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";

        return implode("\r\n", $headers);
    }

    protected function buildAddresses(array $addresses): string
    {
        return implode(', ', array_map([$this, 'buildAddress'], $addresses));
    }

    protected function buildAddress(array $address): string
    {
        return $address['name'] ? "{$address['name']} <{$address['address']}>" : $address['address'];
    }
}
