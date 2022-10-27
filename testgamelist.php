 
<?php
 
 
require_once 'api/Service/amb.php';
$api = new AMBAPI();
 
$data = $api->AMBGameList($_GET['game']);
$data = json_decode(json_encode($data->data), true);

echo json_encode($data);
?>
 