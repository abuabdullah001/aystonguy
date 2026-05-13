<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\VideoController;

$request = Request::create('/admin/videos/data', 'GET', [
    'draw' => 1,
    'start' => 0,
    'length' => 10,
]);

app()->instance('request', $request);

$response = app(VideoController::class)->data($request);
$data = json_decode($response->getContent(), true);

$first = $data['data'][0] ?? null;

if (!is_array($first)) {
    echo "No rows returned\n";
    exit(0);
}

echo "action:\n";
echo ($first['action'] ?? '(missing action field)') . "\n";
