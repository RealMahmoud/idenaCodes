<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
if (!isset($argv[1])) {
    die("");
}
if (!$argv[1] == CRON_PASSWORD) {
    die("");
}

$lastBlockRecorded = $conn->query("SELECT `value` FROM `config` WHERE `key` = 'blockHeight';")->fetch_row()[0];
while (true) {
    $bcn_blockAt = json_encode(array("method" => "bcn_blockAt", "params" => [(int) $lastBlockRecorded], "id" => 1, "key" => RPC_KEY));
    $bcn_blockAt_result = curl_post(RPC_BASE_URL, $bcn_blockAt);
    if (isset($bcn_blockAt_result['result'])) {
        if ($bcn_blockAt_result['result']['transactions'] !== null) {
            foreach (($bcn_blockAt_result['result']['transactions']) as $txHash) {

                $bcn_transaction = json_encode(array("method" => "bcn_transaction", "params" => [$txHash], "id" => 1, "key" => RPC_KEY));
                $bcn_transaction_result = curl_post(RPC_BASE_URL, $bcn_transaction);
                if (isset($bcn_transaction_result['result'])) {

                    if ($bcn_transaction_result['result']['to'] == DEPOSITS_ADDRESS && $bcn_transaction_result['result']['type'] == "send") {
                        echo "< new Deposit >";
                        if (isset($conn->query("SELECT `id` FROM `users` WHERE `address` = '" . $bcn_transaction_result['result']['from'] . "';")->fetch_row()[0])) {
                            $userID = $conn->query("SELECT `id` FROM `users` WHERE `address` = '" . $bcn_transaction_result['result']['from'] . "';")->fetch_row()[0];
                            $conn->query("UPDATE `users` SET `balance` = `balance` + '" . $bcn_transaction_result['result']['amount'] . "' WHERE `id` = '" . $userID . "';");
                            $conn->query("INSERT INTO `deposits`( `txHash`, `userID`, `credited`, `amount`, `address`) VALUES ('" . $bcn_transaction_result['result']['hash'] . "','" . $userID . "','1','" . $bcn_transaction_result['result']['amount'] . "','" . $bcn_transaction_result['result']['from'] . "');");
                        } elseif (isset($conn->query("SELECT `id` FROM `users` WHERE `id` = '" . (hexdec($bcn_transaction_result['result']['payload'])) . "';")->fetch_row()[0])) {
                            $userID = hexdec($bcn_transaction_result['result']['payload']);
                            $conn->query("UPDATE `user` SET `balance` = `balance` + '" . $bcn_transaction_result['result']['amount'] . "' WHERE `id` = '" . $userID . "';");
                            $conn->query("INSERT INTO `deposits`( `txHash`, `userID`, `credited`, `amount`, `address`) VALUES ('" . $bcn_transaction_result['result']['hash'] . "','" . $userID . "','1','" . $bcn_transaction_result['result']['amount'] . "','" . $bcn_transaction_result['result']['from'] . "');");
                        } else {
                            $conn->query("INSERT INTO `deposits`( `txHash`, `credited`, `amount`, `address`) VALUES ('" . $bcn_transaction_result['result']['hash'] . "','0','" . $bcn_transaction_result['result']['amount'] . "','" . $bcn_transaction_result['result']['from'] . "');");
                        }
                    }
                }
            }
        }
        $lastBlockRecorded++;
        $conn->query("UPDATE `config` SET `value` = '" . $lastBlockRecorded . "' WHERE `key` = 'blockHeight' ;");
    } else {
        sleep(30);
    }
}
