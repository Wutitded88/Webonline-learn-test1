<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'Service/amb.php';

//////////////////////////////////////////////////////////////////////////

header('Content-Type: application/json; charset=utf-8;');
$username = '0970084065';
$credit = 10;
$q_topup_db = dd_q('SELECT SUM(w_amount) AS total FROM withdraw_tb WHERE w_date_create = ? AND w_user = ?', [date("Y-m-d"),$username])->fetchColumn();
$q_4 = dd_q('SELECT * FROM user_tb WHERE (u_user = ?)', [$username]);
$row_l = $q_4->fetch(PDO::FETCH_ASSOC);
$limitcredit = $credit + $q_topup_db;
echo "ยอดถอนไปแล้ว + ยอดถอนล่าสุด : ".$limitcredit;
echo "<br>จำกัดยอด : ". $row_l['u_limitcredit'];
if($limitcredit >= $row_l['u_limitcredit'])
{
	echo "ยอดถอนมากกว่ายอดสูงสุด";
}
