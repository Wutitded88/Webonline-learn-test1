<?php
require_once 'api/config.php';
require_once 'api/Service/amb.php';
$api = new AMBAPI();
header('Content-Type: application/json; charset=utf-8;');

$_suer = "0989192928"; // แก้ไข

$sum_winloass = 0;
$q_winloss_receive_tb = dd_q('SELECT * FROM winloss_receive_tb WHERE wl_user = ? AND wl_date = ?', [
  $_suer,
  date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'))
]);
if ($q_winloss_receive_tb->rowCount() == 0)
{
  $q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ? AND u_block_agent = 0', [$_suer]);
  if ($q_user_tb->rowCount() > 0)
  {
    $row_user_tb = $q_user_tb->fetch(PDO::FETCH_ASSOC);
    $q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_system_create_date = ? AND t_transaction_id != ? AND t_status = ?', [
      $row_user_tb['u_user'],
      date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days')),
      "",
      "1"
    ]);
    if ($q_topup_tb->rowCount() > 0)
    {
      $amount_winloss = 0;
      while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
      {
        if($row_topup_tb['t_transaction_id'] != "")
        {
          $winloss = $api->GetWinLose($row_user_tb['u_agent_id'], $row_topup_tb['t_transaction_id']);
          if ($winloss->success == true)
          {
            $winloss = json_decode(json_encode($winloss->data), true);
            var_dump($winloss);
          }
        }
      }
    }
    else
    {
      echo "ไม่พบข้อมูล topup_db ".date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
    }
  }
  else
  {
    echo "ไม่พบข้อมูล user_tb";
  }
}
else
{
  echo "มีข้อมูล winloss_receive_tb แล้ว ".date('Y-m-d', strtotime(date('Y-m-d'). ' - 1 days'));
}
?>