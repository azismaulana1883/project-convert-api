<?php
// status.php

// Set header untuk mengatasi masalah CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Authorization");

// Pastikan ini adalah permintaan GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Periksa apakah ada parameter statusId dalam URL
    if (isset($_GET['statusId'])) {
        // Ambil nilai statusId dari parameter URL
        $statusId = $_GET['statusId'];

        // Ambil access_token dari cookie
        $accessToken = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : null;

        // Pastikan ada access_token sebelum membuat permintaan ke endpoint
        if ($accessToken) {
            // Ganti URL endpoint sesuai dengan URL endpoint status BeaCukai yang sesungguhnya
            $endpoint = "https://apis-gw.beacukai.go.id/openapi/status/$statusId";

            // Konfigurasi cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $accessToken",
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Eksekusi cURL dan ambil respons
            $response = curl_exec($ch);

            // Tangani kesalahan cURL jika ada
            if (curl_errno($ch)) {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(['status' => 'error', 'message' => 'Error in cURL request']);
                exit;
            }

            // Tutup sesi cURL
            curl_close($ch);

            // Set header untuk mengatasi masalah CORS
            header("Content-Type: application/json");

            // Kembalikan respons BeaCukai ke client
            echo $response;
        } else {
            // Jika access_token tidak tersedia, kirim respons JSON dengan pesan kesalahan
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
        }
    } else {
        // Jika parameter statusId tidak tersedia, kirim respons JSON dengan pesan kesalahan
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['status' => 'error', 'message' => 'Missing statusId parameter']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Tanggapi permintaan OPTIONS untuk CORS
    header("HTTP/1.1 200 OK");
} else {
    // Tanggapi metode lain dengan kesalahan tidak diizinkan
    header("HTTP/1.1 405 Method Not Allowed");
}
?>
