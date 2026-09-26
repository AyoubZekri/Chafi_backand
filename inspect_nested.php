<?php
$file = 'd:\MyProject\laravel\Chafi\Decmont\Code_de_Timbre_2026_ar.json';
$data = json_decode(file_get_contents($file), true);
$hasTables = false;
foreach($data['articles'] as $art) {
    if(!empty($art['tables'])) {
        $hasTables = true;
        echo "Article table keys: " . implode(", ", array_keys($art['tables'][0])) . "\n";
        if(!empty($art['tables'][0]['cells'])) {
            echo "Article table cell keys: " . implode(", ", array_keys($art['tables'][0]['cells'][0])) . "\n";
        }
    }
    if($hasTables) break;
}
