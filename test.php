<?php

// The version number (9_5_0) should match version of the Chilkat extension used, omitting the micro-version number.
// For example, if using Chilkat v9.5.0.48, then include as shown here:
include("./chilkat-9.5.0-php-8.0-nts-x64/chilkat_9_5_0.dll");
// include("./chilkat-9.5.0-php-8.0-nts-x64/chilkat_9_5_0.php");

// This example requires the Chilkat API to have been previously unlocked.
// See Global Unlock Sample for sample code.

// To create an ECDSA signature, the data first needs to be hashed.  Then the hash
// is signed.

// Use Chilkat Crypt2 to generate a hash for any of the following
// hash algorithms: SHA256, SHA384, SHA512, SHA1, MD5, MD2, HAVAL, RIPEMD128/160/256/320

$crypt = new CkCrypt2();
$crypt->put_HashAlgorithm('SHA256');
$crypt->put_Charset('utf-8');
$crypt->put_EncodingMode('base64');

// Hash a string.
$hash1 = $crypt->hashStringENC('The quick brown fox jumps over the lazy dog');
print 'hash1 = ' . $hash1 . "\n";

// Or hash a file..
$hash2 = $crypt->hashFileENC('qa_data/hamlet.xml');
print 'hash2 = ' . $hash2 . "\n";

// (The Crypt2 API provides many other ways to hash data..)

// -----------------------------------------------------------
// An ECDSA private key is used for signing.  The public key is for signature verification.
// Load our ECC private key.
// Our private key file contains this:

//  // -----BEGIN PRIVATE KEY-----
//  MIGHAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBG0wawIBAQQg3J8q/24D1sEKGdP9
//  72MGYElLGpw/a56Y3t6pfON3uhShRANCAATlSmoizyhAwoYZAOuFBATl07/1RR54
//  a1Dzfm16grxJe666AGKR+bSs24hk7TEpaeCTvT8YOOM3l+xKFg7zq6Q9
//  -----END PRIVATE KEY-----

$privKey = new CkPrivateKey();
$success = $privKey->LoadPemFile('qa_data/ecc/secp256r1-key-pkcs8.pem');
if ($success != true) {
    print $privKey->lastErrorText() . "\n";
    exit;
}

// We'll need a PRNG source for random number generation.
// Use Chilkat's PRNG (for the Fortuna PRNG algorithm).
$prng = new CkPrng();

// Sign the hash..
$ecdsa = new CkEcc();
$ecdsaSigBase64 = $ecdsa->signHashENC($hash1,'base64',$privKey,$prng);
if ($ecdsa->get_LastMethodSuccess() != true) {
    print $ecdsa->lastErrorText() . "\n";
    exit;
}

print 'ECDSA signature = ' . $ecdsaSigBase64 . "\n";

// -----------------------------------------------------------
// Now let's verify the signature using the public key.

$pubKey = new CkPublicKey();
$success = $pubKey->LoadFromFile('qa_data/ecc/secp256r1-pubkey.pem');
if ($success != true) {
    print $pubKey->lastErrorText() . "\n";
    exit;
}

$result = $ecdsa->VerifyHashENC($hash1,$ecdsaSigBase64,'base64',$pubKey);
if ($result == 1) {
    print 'Signature is valid.' . "\n";
    exit;
}

if ($result == 0) {
    print 'Signature is invalid.' . "\n";
    exit;
}

if ($result < 0) {
    print $ecdsa->lastErrorText() . "\n";
    print 'The VerifyHashENC method call failed.' . "\n";
    exit;
}


?>
