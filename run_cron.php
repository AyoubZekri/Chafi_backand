<?php
$basePath = __DIR__ . '/chafi';

exec("php $basePath/artisan schedule:run >> $basePath/storage/logs/cron.log 2>&1");
echo "Cron executed at " . date('Y-m-d H:i:s');
