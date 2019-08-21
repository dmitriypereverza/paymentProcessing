<?php
$privateKeyPassphrase = "mypassword";
$sensitiveData = "This is the data that we want to encrypt.";

/*
// Load the keys from a file (as you would most likely do in a production environment)
$priv_key_file_name = realpath("private.pem");
$publ_key_file_name = realpath("public.pem");

// Note: This function needs an array of parameters!
$privateKey = openssl_pkey_get_private(array("file://$priv_key_file_name", $privateKeyPassphrase));
$publicKey = openssl_pkey_get_public(array("file://$publ_key_file_name", $privateKeyPassphrase));
*/

// Get keys from a string so that this example can be run without the need for extra files
$privateKeyString = <<<PK
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,D21679087FE8490E

hXTtfXC4qYNoE9hySVwPD+Mwhb7RiCae589Z952Z+ucz9i8j+1MO4Sx2nOMCH5Eg
uotMSr3FipJ/Bqbh66AqqYK3PG7NFYA41f/7xrTA6gwq6MDjmAy6z8TW+NE3OCpF
n+9zPzT15wcNm4U4ZRpEO+Fi8cYTLu0LlX+k8Djrd+CuS6wX4p8SgpAplDrnAiAH
z3sJtf2+M67yTNT7v/hIJmkebCwES43pTlNrxluJpD7HBl4BGmFWFI+MJ/gPuFn6
etQjDpzgep0Wn4FKi34IkDQ9kM4/9tWy0Fhf8ytdg0NZshMt/PWRPrNrs+2qLoJu
1rHc0rtKVvALQOKU+SbxaYVBlEzelxB0XJ2uQMSIs46vHZiUG3Q2JBmlxRshHQse
8n9CAYmwm++cPmXq06rVMclCJR0pDlOzGQvIgmo4eiY=
-----END RSA PRIVATE KEY-----
PK;

$publicKeyString = <<<PK
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKcNEHgry/zIFpKdKz2E/ksoDkBn00K7
v2CxB2kHMWjAxgaFPCYs/8gHclSkcJYARKqvU/0Gsc0mrrPtCs5CytcCAwEAAQ==
-----END PUBLIC KEY-----
PK;

// Load private key
$privateKey = openssl_pkey_get_private(array($privateKeyString, $privateKeyPassphrase));
if (!$privateKey) {
    echo "Private key NOT OK\n";
}
if (!openssl_private_encrypt($sensitiveData, $encryptedWithPrivate, $privateKey)) {
    echo "Error encrypting with private key\n";
}
if (!openssl_private_decrypt($encryptedWithPublic, $decryptedWithPrivateFromPublic, $privateKey)) {
    echo "Error decrypting with private key what was encrypted with public key\n";
}
echo "Encrypted with private key: " . $encryptedWithPrivate . "\n";
echo "Decrypted with private key what was encrypted with public key: " . $decryptedWithPrivateFromPublic . "\n";



// Load public key
$publicKey = openssl_pkey_get_public(array($publicKeyString, $privateKeyPassphrase));
if (!$publicKey) {
    echo "Public key NOT OK\n";
}
if (!openssl_public_encrypt($sensitiveData, $encryptedWithPublic, $publicKey)) {
    echo "Error encrypting with public key\n";
}
if (!openssl_public_decrypt($encryptedWithPrivate, $decryptedWithPublicFromPrivate, $publicKey)) {
    echo "Error decrypting with public key what was encrypted with private key\n";
}
echo "Encrypted with public key: " . $encryptedWithPublic . "\n"; // This is different every time
echo "Decrypted with public key what was encrypted with private key: " . $decryptedWithPublicFromPrivate . "\n";
