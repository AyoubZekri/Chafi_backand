<?php
$file = 'd:\MyProject\laravel\Chafi\Decmont\Code_de_Timbre_2026_ar.json';
$data = json_decode(file_get_contents($file), true);
echo "Keys in root: " . implode(", ", array_keys($data)) . "\n";
if(isset($data['nodes'][0])) {
    echo "Keys in nodes[0]: " . implode(", ", array_keys($data['nodes'][0])) . "\n";
}
if(isset($data['articles'][0])) {
    echo "Keys in articles[0]: " . implode(", ", array_keys($data['articles'][0])) . "\n";
}
if(isset($data['notes'][0])) {
    echo "Keys in notes[0]: " . implode(", ", array_keys($data['notes'][0])) . "\n";
}
if(isset($data['article_tables'][0])) {
    echo "Keys in article_tables[0]: " . implode(", ", array_keys($data['article_tables'][0])) . "\n";
}
if(isset($data['article_table_cells'][0])) {
    echo "Keys in article_table_cells[0]: " . implode(", ", array_keys($data['article_table_cells'][0])) . "\n";
}
