<?php 
	require 'config.php';
    require 'db.php';
    $stmt = $db->prepare("SELECT * FROM image ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->fetchALL();
 
?>
    <?php foreach ($result as $row) { ?>
        <tr class="">
            <td><?=$row->transRef?></td>
            <td><?=number_format($row->amount,2)?></td>
            <td><?=$row->date?></td>
        </tr>
    <?php } ?>