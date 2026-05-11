<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    view('siswa.pendaftaran', [
        'pendaftaran' => null,
        'jurusans' => [],
        'berkasAktif' => [],
        'riwayatBerkas' => [],
        'errors' => new \Illuminate\Support\MessageBag()
    ])->render();
    echo "OK";
} catch (Throwable $e) {
    echo get_class($e) . ": " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine();
}
