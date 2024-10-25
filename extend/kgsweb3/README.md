# Web3

A simple encapsulation of Web3 framework in PHP environment.

# install
```bash
composer require kgs/web3
```
## Usage
### web3
```php
$web3 = new Web3('https://kovan.infura.io/v3/9aa3d95b3bc440fa88ea12eaa4456161');
var_dump($web3->accounts());

echo $web3->gasPrice();
echo \Web3\Utils::hexToDec($web3->getBalance("0xdB7D1B76D262D31c51d740C6fb98047B8498D851"));
```


### Wallet
```php
$key = "e872122c04df93040ede8996c0e738f35a0ea44e77642d97eb5c3deedbdd4201"; 
$wallet =Wallet::createByPrivate($key);
echo $wallet->getAddress();
//0xdb7d1b76d262d31c51d740c6fb98047b8498d851
$wallet = Wallet::create();
echo $wallet->getAddress();
echo $wallet->getPrivateKey();


```
### Contract
```php
$key = "e872122c04df93040ede8996c0e738f35a0ea44e77642d97eb5c3deedbdd4201"; 
$wallet =Wallet::createByPrivate($key);
$abi = '[{"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"string","name":"symbol","type":"string"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"}]';
$web3 = new Web3('https://kovan.infura.io/v3/9aa3d95b3bc440fa88ea12eaa4456161');
$contractAddress='0x7ef08Db1E4121b71177B828d5b5ff7a1BCB8305D';
$contract = Contract::at($web3,$abi,$contractAddress);
$toAccount = "0x6aba7cd6750225f9d732a256F0f334916C866264";
$res =$contract->send($wallet,'transfer',[$toAccount,\Web3\Utils::ethToWei(1)]);
echo $res;
//0x43b287554146748780d00af8c7d9d42c499ba03759b759f0f4244b072ec0cab2y
echo  $contract->decodeEvent("0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef");
//Transfer

$res =$contract->call('balanceOf',[$toAccount]);
```


# DONATE

```
 eth/dai: 0x6aba7cd6750225f9d732a256F0f334916C866264
```