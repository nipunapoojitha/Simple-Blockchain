<?php
session_start();
include 'blockchain.php';

$response = [
    'pending_transactions' => count($_SESSION['blockchain']->transactionPool),
    'latest_block_index' => count($_SESSION['blockchain']->chain),
    'latest_block_hash' => $_SESSION['blockchain']->getLastBlock()->hash,
];

echo json_encode($response);
?>
