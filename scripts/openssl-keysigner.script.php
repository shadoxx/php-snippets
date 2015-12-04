<?php header('Content-Type: text/plain');
$DEBUG = true;

/*~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_
* OpenSSL - This PHP script demonstrates how to sign and verify an arbitrary
*           payload using public/private keys.
*
* @ref https://en.wikibooks.org/wiki/Cryptography/Generate_a_keypair_using_OpenSSL
*
* How To Generate An RSA Public/Private Keypair:
*   openssl genrsa -out privatekey.pem 4096                         // generate the private key
*   openssl rsa -pubout -in rsa-privatekey.pem -out publickey.pem   // extract the public key from the private key
~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_*/

function notice( $tag, $mesg ) { printf("[%s] %s: %s\n", microtime(), $tag, $mesg); return; }
function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data) { return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); }
/*~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_*/

// the digest algorithm openssl will use when generating a verification signature
$opensslDigest = "SHA256";

// grab the public and private keys from the filesystem
$rsaPrivateKey  = file_get_contents("privatekey.pem");
$rsaPublicKey   = file_get_contents("publickey.pem");
notice("info", "Attempted to acquired public and private keys from filesystem.");

// dump our keypair to stdout
if( $DEBUG ) {
    notice("debug", sprintf("Private Key:\n%s", $rsaPrivateKey));
    notice("debug", sprintf("Public Key:\n%s", $rsaPublicKey));
}

// define the payload that we want to sign
$payload = base64url_encode(json_encode([
    "username"  => "admin",
    "password"  => "password",
]));

notice("info", sprintf("Encoded the provided payload with url-safe base64 encoding:\n%s\n", $payload));

// compute the digest signature for our payload using $opensslDigest
$signature = null;
$signatureOK = openssl_sign( $payload, $signature, $rsaPrivateKey, $opensslDigest);

if( $signatureOK === true ) {
    notice("info", sprintf("Signature generation successful using %s. Your encoded payload signature is:\n\t%s\n", $opensslDigest, base64url_encode($signature)));
} else {
    notice("error", sprintf("Unable to sign the payload with the requested parameters. OpenSSL says: %s", openssl_error_string()));
}

notice("mark", "------------------------------------------------------------------------------>>>");

notice("info", sprintf("Attempting to verify signature for payload using public key:\n%s", $rsaPublicKey));
$verifyOK = openssl_verify($payload, $signature, $rsaPublicKey, $opensslDigest);

if( $verifyOK === 1 ) {
    notice("info", sprintf("Successfully verified that the signature for the given payload is valid."));
    // decode the payload
    notice("info", sprintf("Decoding payload:\n\n\t%s", base64url_decode($payload)));
} else {
    notice("error", sprintf("Unable to verify payload signature using the provided public key. OpenSSL says: %s", openssl_error_string()));
}
