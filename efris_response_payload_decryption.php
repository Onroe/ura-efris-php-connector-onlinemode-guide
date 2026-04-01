<?php
$pkcs12_file = 'private_key.p12'; // Replace with actual path to your PKCS#12 file
$password = 'Certificate_Password'; // Replace with actual certificate password
$cipherAlgorithm = "aes-128-ecb";
$tin = "TIN_NUMBER"; // Replace with actual TIN

//Add Interface T104 Response for decryption here
$interface_t104_response = 'eyJwYXNzb3dyZERlcyI6Iml2RDV3YkZJNEk4UGw0NE81VHNKQmhXV3hOTkRobXp3VjZINUxlQ25ja1lBbzNXT0VMOGtuNGRRdDlERTJ1ME9DNTFKVTFQRmkwd2ZZTFdPSGl6WlhqY3gxMHF3Qkp1bkdUcDZpaVc2Z1hlWUJPVDZMQ0o5cU9yVkR4bC9EOThjVnBFZkRhSk9EdTB1N2NrQXg0eXhWV1FTaWsyU1N5VlIxT3pidDVrUHU2ZGdQWUQ2Q2RJRTBoWngyTWJ6TUFiSE5XYWVudkZMYVUyVVNkdGZGSzZvNER6eHIrQTF3T05mWmE5Qkt0VUxDVXNzS2h0ckliYklUbUpyOVlZN2dtcnpFUWhleEdhaytzMXB4eno4MmhFK1JQdzBPTWVNTDBXWElSVWErZ25pRnBOZGJVWEdveGYyNk1QYW5EdEtiQ25oQm9wNHVUYkpDSnJzZXRBQjJxa2F3dz09Iiwic2lnbiI6IllmUmx3aUN6RlhuLThMeXl3R2RaWExsNlZWZXpTX1V5Zjlkd0hQQ1UyNE9qbmxyQURKTGVYWTNQQjYxSlY5NkQyTmVfeURHdmg0bTJnQ2lmeGRQdXF0bmQ0VFQ3WEFraG1kZjhGR1J1UmxieXJKU3ljamRYU3BFbWxDOWl2dDl1MkJQZXNhT3dWQ04xend4RUVBeVUzdDlIZXBMclRVajcybmlUYmlSMGZUVGNtNXZTaFE1M3B3d1Y3anliX1JMbU5vOVFFb21YOVdpQjQ0YlpOeDkzN2dVNC1DeGdxWjVtM2w5amNiekRpOUdwTVBmM2xwVjlJX01CR0RvRU5tTFl6UjJ5NUlyRHFUT2FJdGhOUVlLTmN0ejNtWElaVzVpR1JaSTN2UGFVX0RmTnoyc3YyMDA4dFhpQU5ZLU1Vc0JuYXBHRHpZczJlM25HYkRDRWhNNnJiUSJ9'; // Replace with actual response data

$efris_encrypted_response = '';

function privateDecryptData($encryptedData, $privateKey, $padding = OPENSSL_PKCS1_PADDING)
{
    $decryptedData = '';

    if (!openssl_private_decrypt($encryptedData, $decryptedData, $privateKey, $padding)) {
        return false;
    }

    return $decryptedData;
}

function decryptData($plainText, $cipherAlgorithm, $encryptionKey)
{
    $encryptedData = openssl_decrypt($encoded_data, $cipherAlgorithm, base64_decode($encryptionKey));

    if ($encryptedData === false) {
        return false;
    }

    return $encryptedData;
}



$pkcs12_content = file_get_contents($pkcs12_file);

if ($pkcs12_content === false) {
    die("Failed to read PKCS#12 file.");
}




$certs = [];
$decryptedData;
if (openssl_pkcs12_read($pkcs12_content, $certs, $password)) {
    echo "PKCS#12 parsed successfully.\n";
    echo "<br>";
    echo "<br>";
    echo "Private Key:\n";
    echo "<br>";
    echo $certs['pkey'] ?? 'Not found' . "\n";
	echo "<pre>" . htmlspecialchars($certs['pkey']) . "</pre>\n";
	
    $jsonContent = base64_decode($interface_t104_response);
	$passowrdDes = base64_decode(json_decode($jsonContent)->passowrdDes);
	
	echo "Pass Des: " . base64_encode($passowrdDes) . "\n";
	echo "<br>";
    $decryptedData = privateDecryptData($passowrdDes, htmlspecialchars($certs['pkey']), OPENSSL_PKCS1_PADDING);
    if ($decryptedData !== false) {
    echo "<br>";
	echo "AES Decrypted Data/ AES Key: " . $decryptedData . "\n";
	echo "<br>";
	echo "<br>";
	$data = decryptData($efris_encrypted_response, $cipherAlgorithm, $decryptedData);
	
   
	echo "DECRYPTED RESPONSE:\n";
    
	echo $data . "\n";
    echo "<br>";
    echo "<br>";
	
	
	
    } else {
    echo "Decryption failed: " . openssl_error_string() . "\n";
    }
	
	
} else {
    echo "Failed to parse PKCS#12.\n";
    while (($e = openssl_error_string()) !== false) {
        echo $e . "\n";
    }
}
?>