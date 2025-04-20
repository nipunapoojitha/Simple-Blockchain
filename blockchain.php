<?php

class Transaction
{
    public $sender;
    public $recipient;
    public $amount;

    public function __construct($sender, $recipient, $amount)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->amount = $amount;
    }
}

class Block
{
    public $index;
    public $previousHash;
    public $timestamp;
    public $transactions;
    public $hash;
    public $nonce;

    public function __construct($index, $previousHash, $timestamp, $transactions, $hash, $nonce)
    {
        $this->index = $index;
        $this->previousHash = $previousHash;
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->hash = $hash;
        $this->nonce = $nonce;
    }

    public static function calculateHash($index, $previousHash, $timestamp, $transactions, $nonce)
    {
        return hash('sha256', $index . $previousHash . $timestamp . json_encode($transactions) . $nonce);
    }
}

class Blockchain
{
    public $chain;
    public $difficulty;
    public $transactionPool;

    public function __construct($difficulty = 2)
    {
        $this->chain = [];
        $this->difficulty = $difficulty;
        $this->transactionPool = [];
        $this->createBlock('Genesis Block', '0');
    }

    public function createBlock($transactions, $previousHash)
    {
        $index = count($this->chain) + 1;
        $timestamp = time();
        $nonce = 0;
        $hash = '';

        // Perform Proof of Work
        do {
            $hash = Block::calculateHash($index, $previousHash, $timestamp, $transactions, $nonce);
            $nonce++;
        } while (substr($hash, 0, $this->difficulty) !== str_repeat('0', $this->difficulty));

        // Create the new block
        $block = new Block($index, $previousHash, $timestamp, $transactions, $hash, $nonce);
        $this->chain[] = $block;
        $this->transactionPool = [];
        return $block;
    }

    public function addTransaction($transaction)
    {
        $this->transactionPool[] = $transaction;

        // Automatically mine block if conditions are met
        if (count($this->transactionPool) >= 5) { // Adjust this threshold as needed
            $this->createBlock($this->transactionPool, $this->getLastBlock()->hash);
        }
    }

    public function getLastBlock()
    {
        return end($this->chain);
    }

    public function isChainValid()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            if ($currentBlock->hash !== Block::calculateHash($currentBlock->index, $currentBlock->previousHash, $currentBlock->timestamp, $currentBlock->transactions, $currentBlock->nonce)) {
                return false;
            }

            if ($currentBlock->previousHash !== $previousBlock->hash) {
                return false;
            }
        }
        return true;
    }
}

// Start a session to store blockchain state
session_start();

if (!isset($_SESSION['blockchain'])) {
    $_SESSION['blockchain'] = new Blockchain();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_transaction'])) {
        $sender = $_POST['sender'];
        $recipient = $_POST['recipient'];
        $amount = $_POST['amount'];

        if ($sender && $recipient && $amount) {
            $transaction = new Transaction($sender, $recipient, $amount);
            $_SESSION['blockchain']->addTransaction($transaction);
        }
    }
}

?>
