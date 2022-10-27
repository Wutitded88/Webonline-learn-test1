<?php
    header('content-type: text/plain');
    require 'ClassKbiz.php';
    $kbank = new KbankBiz(array(
        'username' => '', //ยูสเซอร์เนม
        'password' => '', //รหัสผ่าน
        'accountFrom' => '' //เลขบช.
    ));

    $isSuccess = false;
    $refresh = $kbank->refreshSession();
    if(array_key_exists('status', $refresh) && $refresh['status'] == "F")
    {
        $kbank->validateSession($kbank->login());
    }
    else
    {
        $isSuccess = true;
    }

    if($isSuccess)
    {
            $TransactionHistory = $kbank->getTransactionHistory();
            $GetNumberOtherBank = $kbank->GetNumberOtherBank($TransactionHistory);
            print_r($GetNumberOtherBank);

            // 1. โชว์ข้อมูลผู้ใช้ เช่น เลขบัญชี หรือ จำนวนเงินในบัญชี
            // Array
            // (
            //     [clientRefID] => 
            //     [serviceRefID] => ACS20220115233839260743
            //     [status] => S
            //     [timestamp] => 2022-01-15T23:38:39.405+07:00
            //     [data] => Array
            //         (
            //             [rowCount] => 0
            //             [accountSummaryList] => Array
            //                 (
            //                     [0] => Array
            //                         (
            //                             [rowCount] => 0
            //                             [accountId] => 20160526190410
            //                             [accountNo] => 0128291733
            //                             [accountNoNotFormat] => 7ef1223564a963e34d962c074f47e606
            //                             [accountNoMasking] => xxx-x-x9173-x
            //                             [accountName] => นาย วีรพงศ์ ยั่งยืน
            //                             [accountNameTh] => นาย วีรพงศ์ ยั่งยืน
            //                             [accountType] => SA
            //                             [accountTypeKey] => label.account.type.sa
            //                             [accountDisplayName] => MR. WEERAPONG YANGYUEN
            //                             [nicknameType] => OWNAC
            //                             [availableBalance] => 0.00
            //                             [acctBalance] => 0.00
            //                             [clearingAmt] => 0.00
            //                             [holdAmt] => 0.00
            //                             [accountLabelStatus] => ACTIVE
            //                         )
            //                 )
            //             [totalList] => 1
            //             [availableBalanceSum] => 0.00
            //         )
            // )
            // ===================================================


            // 2. ถ้าต้องการให้แสดงเลขบัญชีครบทุกหลัก ให้ใส่ฟังชั่นนี้ตามหลัง
            // Array
            // (
            //     [clientRefID] => 
            //     [serviceRefID] => ACS20220115235650841802
            //     [status] => S
            //     [timestamp] => 2022-01-15T23:56:50.997+07:00
            //     [data] => Array
            //         (
            //             [rowCount] => 0
            //             [totalList] => 2
            //             [navRefKey] => 2022-01-15|23:41:25|000128291733000000000366|0900|2022-01-15-23.41.25.844571
            //             [recentTransactionList] => Array
            //                 (
            //                     [0] => Array
            //                         (
            //                             [transDate] => 2022-01-15 23:41:25
            //                             [effectiveDate] => Sat Jan 15 07:00:00 ICT 2022
            //                             [transNameTh] => รับโอนเงิน
            //                             [transNameEn] => Transfer Deposit
            //                             [depositAmount] => 2
            //                             [withdrawAmount] => 
            //                             [accountPartner] => label.bank.other.account
            //                             [channelTh] => Internet/Mobile ต่างธนาคาร
            //                             [channelEn] => Internet/Mobile Across Banks
            //                             [origRqUid] => 001_20220115_014E1A0A94F157CA68A
            //                             [toAccountNumber] => 
            //                             [benefitAccountNameTh] => 
            //                             [benefitAccountNameEn] => 
            //                             [transType] => FTOB
            //                             [originalSourceId] => 1
            //                             [transCode] => 0900
            //                             [debitCreditIndicator] => CR
            //                             [proxyTypeCode] => A
            //                             [proxyId] => 0128291733
            //                             [data] => Array
            //                                 (
            //                                     [clientRefID] => 
            //                                     [serviceRefID] => ACS20220115235651068478
            //                                     [status] => S
            //                                     [timestamp] => 2022-01-15T23:56:51.125+07:00
            //                                     [data] => Array
            //                                         (
            //                                             [rowCount] => 0
            //                                             [bankNameTh] => SCBA
            //                                             [bankNameEn] => SCBA
            //                                             [toAccountNo] => 2452093910
            //                                             [toAccountNoMarking] => xxx-x-x9391-x
            //                                             [toAccountNameTh] => นาย วีรพงศ์ ยั่งยื
            //                                             [toAccountNameEn] => นาย วีรพงศ์ ยั่
            //                                             [memo] => 
            //                                         )
            //                                 )
            //                         )
            //                     [1] => Array
            //                         (
            //                             [transDate] => 2022-01-15 23:40:51
            //                             [effectiveDate] => Sat Jan 15 07:00:00 ICT 2022
            //                             [transNameTh] => รับโอนเงิน
            //                             [transNameEn] => Transfer Deposit
            //                             [depositAmount] => 1
            //                             [withdrawAmount] => 
            //                             [accountPartner] => label.bank.k2k.account
            //                             [channelTh] => K PLUS
            //                             [channelEn] => K PLUS
            //                             [origRqUid] => 509_20220115_75e104154405409e804b018bfea2f649
            //                             [toAccountNumber] => xxx-x-x0894-x
            //                             [benefitAccountNameTh] => 
            //                             [benefitAccountNameEn] => 
            //                             [transType] => FTOT
            //                             [originalSourceId] => 509
            //                             [transCode] => 0900
            //                             [debitCreditIndicator] => CR
            //                             [proxyTypeCode] => 
            //                             [proxyId] => 
            //                             [data] => Array
            //                                 (
            //                                     [clientRefID] => 
            //                                     [serviceRefID] => ACS20220115235651218654
            //                                     [status] => S
            //                                     [timestamp] => 2022-01-15T23:56:51.275+07:00
            //                                     [data] => Array
            //                                         (
            //                                             [rowCount] => 0
            //                                         )
            //                                 )
            //                         )
            //                 )
            //         )
            // )
            // ===================================================





    }
?>
