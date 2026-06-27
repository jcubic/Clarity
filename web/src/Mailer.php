<?php

namespace Clarity;

use Resend;

class Mailer {
    private ?Resend\Client $client;
    private string $from;

    private function __construct(?Resend\Client $client, string $from) {
        $this->client = $client;
        $this->from = $from;
    }

    public static function create(string $apiKey, string $from): self {
        return new self(Resend::client($apiKey), $from);
    }

    public static function null(): self {
        return new self(null, '');
    }

    public function isConnected(): bool {
        return $this->client !== null;
    }

    /**
     * @param string|array<string> $to
     */
    public function send(string|array $to, string $subject, string $html): ?string {
        if (!$this->client) {
            return null;
        }
        $result = $this->client->emails->send([
            'from' => $this->from,
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
        ]);
        return $result->id ?? null;
    }

    /**
     * @param string|array<string> $to
     */
    public function sendMagicLink(string|array $to, string $token, string $baseUrl): ?string {
        $link = rtrim($baseUrl, '/') . '/auth/verify?token=' . urlencode($token);
        $html = <<<HTML
        <div style="font-family: system-ui, sans-serif; max-width: 480px; margin: 0 auto;">
            <h2 style="color: #333;">Sign in to Clarity</h2>
            <p>Click the link below to sign in. This link expires in 15 minutes.</p>
            <p><a href="{$link}" style="display: inline-block; padding: 12px 24px; background: oklch(72% 0.16 230); color: #fff; text-decoration: none; border-radius: 6px;">Sign in to Clarity</a></p>
            <p style="color: #888; font-size: 13px;">If you didn't request this, you can safely ignore this email.</p>
        </div>
        HTML;
        return $this->send($to, 'Your Clarity sign-in link', $html);
    }
}
