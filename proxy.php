<?php
header('Content-Type: application/pdf');

if (isset($_GET['path'])) {
    $apiUrl = 'https://apis-gw.beacukai.go.id/openapi/download-respon';
    $path = $_GET['path'];
    $url = $apiUrl . '?path=' . $path;

    // Tambahkan header Authorization
    $accessToken = $_COOKIE['access_token'];
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $accessToken"
        ]
    ];
    $context = stream_context_create($options);

    // Ambil data dari API
    $pdfData = file_get_contents($url, false, $context);

    // Kirimkan data sebagai respons
    echo $pdfData;
} else {
    echo 'Invalid path parameter';
}
?>
