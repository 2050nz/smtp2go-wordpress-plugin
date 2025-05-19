<?php

namespace SMTP2GO\App;

class SecureApiKeyHelper
{

    const CIPHER = 'AES-256-CTR';

    private $ivlen = 0;

    private $key = 'default-key-not-secure';

    private $hasShownWarning = false;

    public function __construct()
    {
        if (!$this->canEncrypt()) {
            return;
        }

        $this->ivlen = openssl_cipher_iv_length(self::CIPHER);
        if (false != ($val = SettingsHelper::getSettingFromFileSystem('SMTP2GO_ENCRYPTION_KEY'))) {
            $this->key = $val;
        } elseif (defined('AUTH_KEY')) {
            $this->key = AUTH_KEY;
        }
    }

    /**
     * Checks whether the OpenSSL extension is enabled in the system.
     *
     * @return bool
     */
    protected function canEncrypt()
    {
        return \extension_loaded('openssl')
            && in_array(strtolower(self::CIPHER), openssl_get_cipher_methods());
    }

    public function encryptKey($plain)
    {
        if (!$this->canEncrypt()) {
            return $plain;
        }

        $iv = openssl_random_pseudo_bytes($this->ivlen);

        $encrypted = openssl_encrypt($plain, self::CIPHER, $this->key, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    public function decryptKey($maybeEncryptedKey)
    {
        if (!$this->canEncrypt() || strpos($maybeEncryptedKey, 'api-') === 0) {
            return $maybeEncryptedKey;
        }

        $encrypted = base64_decode($maybeEncryptedKey);

        $iv = substr($encrypted, 0, $this->ivlen);

        $encrypted = substr($encrypted, $this->ivlen);

        $decrypted = openssl_decrypt($encrypted, self::CIPHER, $this->key, 0, $iv);

        
        return $decrypted;
    }
}
