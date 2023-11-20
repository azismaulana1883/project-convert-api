<?php
// login.php

// Set header untuk mengatasi masalah CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Pastikan ini adalah permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Terima data dari formulir HTML
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ganti URL endpoint sesuai dengan URL endpoint login BeaCukai yang sesungguhnya
    $endpoint = "https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login";

    // Persiapkan data dalam format JSON
    $data = json_encode([
        'username' => $username,
        'password' => $password,
    ]);

    // Konfigurasi cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
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

    // Ubah respons JSON menjadi array asosiatif
    $responseData = json_decode($response, true);

    // Periksa apakah login berhasil
    if (isset($responseData['status']) && $responseData['status'] === 'success') {
        // Simpan access token ke dalam cookie
        setcookie('access_token', $responseData['item']['access_token'], time() + $responseData['item']['expires_in'], '/');

        // Redirect ke halaman selanjutnya
        header("Location: inputid.html");
        exit;
    } else {
        // Jika login gagal, kirim respons JSON dengan pesan kesalahan
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Tanggapi permintaan OPTIONS untuk CORS
    header("HTTP/1.1 200 OK");
} else {
    // Tanggapi metode lain dengan kesalahan tidak diizinkan
    header("HTTP/1.1 405 Method Not Allowed");
}
?>
