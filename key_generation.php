<?php

// Generate an encryption key
$encryptionKey = bin2hex(random_bytes(16)); // 16 bytes = 128 bits

// Generate an encryption cipher
$cipher = openssl_get_cipher_methods()[0]; // Get the first available cipher method

// Display the encryption key and cipher
echo "Encryption Key: " . $encryptionKey . PHP_EOL;
echo "Encryption Cipher: " . $cipher . PHP_EOL;