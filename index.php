<?php
include 'blockchain.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Blockchain</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateBlockchainStatus() {
            $.ajax({
                url: 'ajax.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('.monitor').html(`
                        <p><strong>Pending Transactions:</strong> ${data.pending_transactions}</p>
                        <p><strong>Latest Block Index:</strong> ${data.latest_block_index}</p>
                        <p><strong>Latest Block Hash:</strong> ${data.latest_block_hash}</p>
                        ${data.pending_transactions > 0 ? '<p><strong>Mining in Progress...</strong></p>' : '<p><strong>No Mining in Progress</strong></p>'}
                    `);
                },
                error: function() {
                    console.error('Error fetching blockchain status');
                }
            });
        }

        // Update the status every 5 seconds
        setInterval(updateBlockchainStatus, 5000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Simple Blockchain</h1>

        <h2>Add Transaction</h2>
        <form method="POST">
            <input type="text" name="sender" placeholder="Sender" required>
            <input type="text" name="recipient" placeholder="Recipient" required>
            <input type="number" name="amount" placeholder="Amount" required>
            <button type="submit" name="add_transaction">Add Transaction</button>
        </form>

        <h2>Blockchain Status</h2>
        <div class="monitor">
            <p><strong>Pending Transactions:</strong> <?= count($_SESSION['blockchain']->transactionPool) ?></p>
            <p><strong>Latest Block Index:</strong> <?= count($_SESSION['blockchain']->chain) ?></p>
            <?php if ($_SESSION['blockchain']->transactionPool): ?>
                <p><strong>Mining in Progress...</strong></p>
            <?php else: ?>
                <p><strong>No Mining in Progress</strong></p>
            <?php endif; ?>
        </div>

        <h2>Blockchain</h2>
        <div class="blockchain">
    <?php foreach ($_SESSION['blockchain']->chain as $block): ?>
        <div class="block">
            <h3>Block <?= $block->index ?></h3>
            <p>Hash: <?= $block->hash ?></p>
            <p>Previous Hash: <?= $block->previousHash ?></p>
            <p>Timestamp: <?= date('Y-m-d H:i:s', $block->timestamp) ?></p>
            <p>Nonce: <?= $block->nonce ?></p>
            <h4>Transactions:</h4>
            <ul>
                <?php 
                // Check if transactions is an array or traversable object
                if (is_array($block->transactions) || $block->transactions instanceof Traversable): 
                    foreach ($block->transactions as $transaction): ?>
                        <li>Sender: <?= $transaction->sender ?>, Recipient: <?= $transaction->recipient ?>, Amount: <?= $transaction->amount ?></li>
                    <?php endforeach; 
                else: ?>
                    <li>No transactions or invalid transaction format</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
    </div>
</body>
</html>
