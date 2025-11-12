// MetaMask Integration
window.addEventListener('load', async () => {
    // Modern dapp browsers
    if (window.ethereum) {
        window.web3 = new Web3(ethereum);
        try {
            // Request account access if needed
            await ethereum.enable();
            // Accounts now exposed
            console.log("MetaMask connected!");
            
            // Listen for account changes
            ethereum.on('accountsChanged', function (accounts) {
                console.log('Account changed:', accounts[0]);
                window.location.reload();
            });
            
            // Listen for chain changes
            ethereum.on('chainChanged', function (chainId) {
                console.log('Network changed:', chainId);
                window.location.reload();
            });
            
        } catch (error) {
            console.error("User denied account access");
        }
    }
    // Legacy dapp browsers
    else if (window.web3) {
        window.web3 = new Web3(web3.currentProvider);
        console.log("Legacy web3 detected!");
    }
    // Non-dapp browsers
    else {
        console.log('Non-Ethereum browser detected. Consider installing MetaMask!');
    }
});

// Payment Processing Functions
async function processPayment(amount) {
    if (!window.ethereum) {
        alert('Please install MetaMask to make payments!');
        return false;
    }

    try {
        const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
        const account = accounts[0];
        
        // Convert amount to Wei (1 ETH = 10^18 Wei)
        const amountInWei = web3.utils.toWei(amount.toString(), 'ether');
        
        // Send transaction
        const transaction = await ethereum.request({
            method: 'eth_sendTransaction',
            params: [{
                from: account,
                to: '0xYourGameWalletAddress', // Replace with your wallet address
                value: web3.utils.toHex(amountInWei),
                gas: '21000', // Basic transaction gas
                gasPrice: web3.utils.toHex(await web3.eth.getGasPrice())
            }]
        });
        
        return transaction;
    } catch (error) {
        console.error('Payment failed:', error);
        alert('Payment failed: ' + error.message);
        return false;
    }
}

// Export functions for use in Alpine.js components
window.gamePayments = {
    async processTopUp(amount) {
        const tx = await processPayment(amount);
        if (tx) {
            // Send transaction hash to backend
            await fetch('/transactions/crypto-confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    transaction_hash: tx,
                    amount: amount
                })
            });
            return true;
        }
        return false;
    },
    
    async processPurchase(gameDetails, amount) {
        const tx = await processPayment(amount);
        if (tx) {
            // Send transaction hash to backend
            await fetch('/transactions/crypto-confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    transaction_hash: tx,
                    amount: amount,
                    game_name: gameDetails.name,
                    item_name: gameDetails.item
                })
            });
            return true;
        }
        return false;
    }
};