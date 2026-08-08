<?php

declare(strict_types=1);

namespace Palet\Framework\Mail\Transports;

use Palet\Framework\Contracts\Mail\MailTransportInterface;
use Palet\Framework\Contracts\Mail\MailMessageInterface;
use RuntimeException;

class SendmailTransport implements MailTransportInterface
{
    protected string $command;

    public function __construct(string $command = '/usr/sbin/sendmail -bs')
    {
        $this->command = $command;
    }

    public function send(MailMessageInterface $message): void
    {
        $headers = $this->buildHeaders($message);
        $body = $message->getBody();
        $to = $this->buildAddresses($message->getTo());

        $mailCommand = $this->command;
        
        $process = proc_open($mailCommand, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException("Could not execute sendmail command: {$mailCommand}");
        }

        $mailData = $headers . "\r\n\r\n" . $body;

        fwrite($pipes[0], $mailData);
        fclose($pipes[0]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $status = proc_close($process);

        if ($status !== 0) {
            throw new RuntimeException("Sendmail command failed with status {$status}: {$error}");
        }
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

        $bcc = $message->getBcc();
        if (!empty($bcc)) {
            $headers[] = "Bcc: " . $this->buildAddresses($bcc);
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
