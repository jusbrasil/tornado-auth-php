<?php

namespace Jusbrasil\TornadoAuthPhp;

class TornadoAuthPhpLib
{
    private $maxAgeDays = 31;
    private $userCookieName = 'user';
    private $secretKey = null;

    public function __construct(array $options)
    {
        $this->configure($options);
    }

    private function createSignature(string $secret, string $name, array $parts = []): string
    {
        $hmac = hash_init('sha1', HASH_HMAC, $secret);
        hash_update($hmac, $name);
        foreach ($parts as $part) {
            hash_update($hmac, $part);
        }
        return hash_final($hmac);
    }

    private function createAuth(?string $secret, string $name, $value): string
    {
        $timestamp = utf8_encode(time());
        $utf8Value = utf8_encode(is_string($value) ? $value : json_encode($value));
        $valueBase64 = base64_encode($utf8Value);
        $signature = $this->createSignature($secret, $name, [$valueBase64, $timestamp]);

        return implode('|', [$valueBase64, $timestamp, $signature]);
    }

    public function configure(array $options): void
    {
        $this->maxAgeDays = $options['max_age_days'] ?? $this->maxAgeDays;
        $this->userCookieName = $options['user_cookie'] ?? $this->userCookieName;
        $this->secretKey = $options['secret_key'] ?? $this->secretKey;
    }

    public function createSignedValue($value): string
    {
        return $this->createAuth($this->secretKey, $this->userCookieName, $value);
    }

    public function createSignedCookie(string $cookieName, $value): string
    {
        return $this->createAuth($this->secretKey, $cookieName, $value);
    }

    public function decodeSignedValue(?string $secret, string $name, $value, int $maxAgeDays = null): string
    {
        $maxAgeDays = empty($maxAgeDays) ? $this->maxAgeDays : $maxAgeDays;
        if ($value === null) {
            return null;
        }

        $parts = explode('|', $value);
        if (count($parts) != 3) {
            return null;
        }

        $signature = $this->createSignature($secret, $name, [$parts[0], $parts[1]]);
        if ($parts[2] != $signature) {
            trigger_error(sprintf('Invalid cookie signature %s', $value), E_USER_WARNING);
            return null;
        }

        $now = time();
        $timestamp = $parts[1];
        if ($timestamp < $now - $maxAgeDays * 86400) {
            trigger_error(sprintf('Expired cookie %s', $value), E_USER_WARNING);
            return null;
        }

        if ($timestamp > $now + 31 * 864000) {
            trigger_error(
                sprintf('Cookie timestamp in future; possible tampering %s', $value),
                E_USER_WARNING
            );
            return null;
        }

        if ($timestamp[0] === '0') {
            trigger_error(sprintf('Tampered cookie %s', $value), E_USER_WARNING);
        }

        return base64_decode($parts[0]);
    }

    public function getSecureCookie(string $cookieName, $value, int $maxAgeDays = null): object
    {
        if ($this->secretKey === null) {
            throw new \Exception('Please, configure the secret key first.');
        }
        return json_decode(
            $this->decodeSignedValue($this->secretKey, $cookieName, $value, $maxAgeDays)
        );
    }

    public function getCurrentUser($value, int $maxAgeDays = null): object
    {
        return $this->getSecureCookie($this->userCookieName, $value, $maxAgeDays);
    }
};
