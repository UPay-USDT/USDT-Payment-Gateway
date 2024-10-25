<?php

namespace kgsweb3;

use GuzzleHttp\Client as GuzzleHttp;

class Web3
{
    private $url;
    protected $requestId = 0;
    protected $client;
    protected $chainId;

    public function getProvider()
    {
        return $this->provider;
    }

    public function __construct($url)
    {
        $this->url = $url;
        $this->client = new GuzzleHttp(array_merge(['timeout' => 60, 'verify' => false], ['base_uri' => $url]));
    }

    /**
     * Returns the current client version
     * @throws \Exception
     * Returns: String - the current client version
     */
    public function clientVersion()
    {
        return $this->request('web3_clientVersion', []);
    }

    /**
     * Returns Keccak-256 (not the standardized SHA3-256) of the given data.
     * @param string data
     * @throws \Exception
     * Returns: DATA - The SHA3 result of the given string.
     */
    public function sha3(string $data)
    {
        return $this->request('web3_sha3', [$data]);
    }

    /**
     * Returns the current network id.
     * @throws \Exception
     * Returns: String - The current network id.
     *              "1": Ethereum Mainnet
     *              "2": Morden Testnet (deprecated)
     *              "3": Ropsten Testnet
     *              "4": Rinkeby Testnet
     *              "42": Kovan Testnet
     */

    public function netVersion()
    {
        if (empty($this->chainId)) {
            $this->chainId = $this->request('net_version', []);
        }
        return $this->chainId;
    }

    /**
     * Returns true if client is actively listening for network connections.
     * @throws \Exception
     * Returns: Boolean - true when listening, otherwise false.
     */

    public function netListening()
    {
        return $this->request('net_listening', []);
    }

    /**
     * Returns number of peers currently connected to the client.
     * @throws \Exception
     * Returns: QUANTITY - integer of the number of connected peers.
     */

    public function netPeerCount()
    {
        return $this->request('net_peerCount', []);
    }

    /**
     * Returns the current ethereum protocol version.
     * @throws \Exception
     * Returns: String - The current ethereum protocol version
     */

    public function protocolVersion()
    {
        return $this->request('eth_protocolVersion', []);
    }

    /**
     * Returns an object with data about the sync status or false.
     * @throws \Exception
     * Returns: Object|Boolean, An object with sync status data or FALSE, when not syncing:
     *              startingBlock: QUANTITY - The block at which the import started (will only be reset, after the sync reached his head)
     *              currentBlock: QUANTITY - The current block, same as eth_blockNumber
     *              highestBlock: QUANTITY - The estimated highest block
     */

    public function syncing()
    {
        return $this->request('eth_syncing', []);
    }

    /**
     * Returns the client coinbase address.
     * @throws \Exception
     * Returns: DATA, 20 bytes - the current coinbase address.
     */

    public function coinbase()
    {
        return $this->request('eth_coinbase', []);
    }

    /**
     * Returns true if client is actively mining new blocks.
     * @throws \Exception
     * Returns: Boolean - returns true of the client is mining, otherwise false.
     */

    public function mining()
    {
        return $this->request('eth_mining', []);
    }

    /**
     * Returns the number of hashes per second that the node is mining with.
     * @throws \Exception
     * Returns: QUANTITY - number of hashes per second.
     */

    public function hashrate()
    {
        return $this->request('eth_hashrate', []);
    }

    /**
     * Returns the current price per gas in wei.
     * @throws \Exception
     * Returns: QUANTITY - integer of the current gas price in wei.
     */

    public function gasPrice()
    {
        return $this->request('eth_gasPrice', []);
    }

    /**
     * Returns a list of addresses owned by client.
     * @throws \Exception
     * Returns: Array of DATA, 20 Bytes - addresses owned by the client.
     */

    public function accounts()
    {
        return $this->request('eth_accounts', []);
    }

    /**
     * Returns the balance of the account of given address.
     * @param $address , 20 Bytes - address to check for balance.
     * @param string $block - integer block number, or the string "latest", "earliest" or "pending", see the default block parameter
     * @throws \Exception
     * Returns: QUANTITY - integer of the current balance in wei.
     */

    public function getBalance($address, string $block = 'latest')
    {
        return $this->request('eth_getBalance', [$address, $block]);
    }

    /**
     * Returns the value from a storage position at a given address.
     * @param $data
     * @param $quantity1
     * @param string $quantity2
     * @return mixed
     * @throws \Exception
     * Returns: DATA - the value at this storage position.
     */
    public function getStorageAt($data, $quantity1, string $quantity2 = Quantity::latest)
    {
        return $this->request('eth_getStorageAt', [$data, $quantity1, $quantity2]);
    }

    /**
     * Returns the number of transactions sent from an address.
     * @param $address
     * @param $quantity
     * @throws \Exception
     * Returns: QUANTITY - integer of the number of transactions send from this address.
     */
    public function getTransactionCount($address, $quantity = Quantity::latest)
    {
        return $this->request('eth_getTransactionCount', [$address, $quantity]);
    }

    /**
     * Returns the number of transactions in a block from a block matching the given block hash.
     * @param $hash
     * @throws \Exception
     * Returns: QUANTITY - integer of the number of transactions in this block.
     */
    public function getBlockTransactionCountByHash($hash)
    {
        return $this->request('eth_getBlockTransactionCountByHash', [$hash]);
    }

    /**
     * Returns the number of transactions in a block matching the given block number.
     * @param string $quantity
     * @throws \Exception
     * Returns: QUANTITY - integer of the number of transactions in this block.
     */
    public function getBlockTransactionCountByNumber(string $quantity = Quantity::latest)
    {
        return $this->request('eth_getBlockTransactionCountByNumber', [$quantity]);
    }

    /**
     * Returns the number of uncles in a block from a block matching the given block hash.
     * @param $hash
     * @throws \Exception
     * Returns: QUANTITY - integer of the number of uncles in this block.
     */
    public function getUncleCountByBlockHash($hash)
    {
        return $this->request('eth_getUncleCountByBlockHash', [$hash]);
    }

    /**
     * Returns the number of uncles in a block from a block matching the given block number.
     * @param $quantity
     * @throws \Exception
     * Returns:  QUANTITY - integer of the number of uncles in this block.
     */
    public function getUncleCountByBlockNumber($quantity = Quantity::latest)
    {
        return $this->request('eth_getUncleCountByBlockNumber', [$quantity]);
    }

    /**
     * Returns code at a given address.
     * @param $data
     * @param string $quantity
     * @throws \Exception
     * Returns: DATA - the code from the given address.
     */
    public function getCode($data, string $quantity = Quantity::latest)
    {
        return $this->request('eth_getCode', [$data, $quantity]);
    }

    /**
     * The sign method calculates an Ethereum specific signature with:
     *      sign(keccak256("\x19Ethereum Signed Message:\n" + len(message) + message))).
     *
     * By adding a prefix to the message makes the calculated signature recognisable as an Ethereum specific signature.
     * This prevents misuse where a malicious DApp can sign arbitrary data (e.g. transaction) and use the signature to impersonate the victim.
     *
     * Note the address to sign with must be unlocked.
     * @param $address
     * @param $data
     * @throws \Exception
     * Returns: DATA: Signature
     */
    public function sign($address, $data)
    {
        return $this->request('eth_sign', [$address, $data]);
    }

    /**
     * Creates new message call transaction or a contract creation, if the data field contains code.
     * @param $from
     * @param $to
     * @param $gas
     * @param $value
     * @param $data
     * @param $gasPrice
     * @param $nonce
     * @throws \Exception
     * Returns: DATA, 32 Bytes - the transaction hash, or the zero hash if the transaction is not yet available.
     * Use eth_getTransactionReceipt to get the contract address, after the transaction was mined, when you created a contract.
     */
    public function sendTransaction($from, $to, $gas = 90000, $value = '0x0', $data = '0x0', $gasPrice = null, $nonce = null)
    {
        $data = [
            'from' => $from,
            'to' => $to,
            'gas' => $gas,
            'value' => $value,
            'data' => $data,
        ];
        if (!empty($nonce)) {
            $data['nonce'] = $nonce;
        }
        if (!empty($gasPrice)) {
            $data['gasPrice'] = $gasPrice;
        }
        return $this->request('eth_sendTransaction', $data);
    }

    /**
     * Creates new message call transaction or a contract creation for signed transactions.
     * @param $data
     * @throws \Exception
     * Returns: DATA, 32 Bytes - the transaction hash, or the zero hash if the transaction is not yet available.
     * Use eth_getTransactionReceipt to get the contract address, after the transaction was mined, when you created a contract.
     */
    public function sendRawTransaction($data)
    {
        return $this->request('eth_sendRawTransaction', [$data]);
    }

    /**
     * Executes a new message call immediately without creating a transaction on the block chain.
     * @param $to
     * @param string $data
     * @param $from
     * @param $gas
     * @param $value
     * @param $gasPrice
     * @return mixed
     * @throws \Exception Returns: DATA - the return value of executed contract.
     */
    public function call( $to, string $data = '0x0', $from = null, $gas = null, $value = null, $gasPrice = null,$quantity=Quantity::latest)
    {
        $data0 = [
            'to' => $to,
        ];

        if (!empty($gas)) {
            $data0['gas'] = $gas;
        }
        if (!empty($from)) {
            $data0['from'] = $from;
        }
        if (!empty($value)) {
            $data0['value'] = $value;
        }
        if (!empty($data)) {
            $data0['data'] = $data;
        }
        if (!empty($gasPrice)) {
            $data0['gasPrice'] = $gasPrice;
        }
        return $this->request('eth_call', [$data0,$quantity]);
    }

    /**
     * Generates and returns an estimate of how much gas is necessary to allow the transaction to complete.The transaction will not be added to the blockchain.
     * Note that the estimate may be significantly more than the amount of gas actually used by the transaction, for a variety of reasons including EVM mechanics and node performance.
     * @param $to
     * @param string $data
     * @param $from
     * @param $gas
     * @param $value
     * @param $gasPrice
     * @param $nonce
     * @throws \Exception
     * Returns: QUANTITY - the amount of gas used.
     */
    public function estimateGas($to, string $data = '0x0', $from = null, $gas = null, $value = null, $gasPrice = null, $nonce = null)
    {
        $data0 = [];
        if (!empty($from)) {
            $data0['from'] = $from;
        }
        $data0['to'] = $to;
        if (!empty($gas)) {
            $data0['gas'] = $gas;
        }

        if (!empty($value)) {
            $data0['value'] = $value;
        }
        if (!empty($data)) {
            $data0['data'] = $data;
        }
        if (!empty($gasPrice)) {
            $data0['gasPrice'] = $gasPrice;
        }
        return $this->request('eth_estimateGas', [$data0]);
    }

    /**
     * Returns information about a block by hash.
     * @param $hash
     * @throws \Exception
     * Returns: Object - A block object, or null when no block was found:
     *              number: QUANTITY - the block number. null when its pending block.
     *              hash: DATA, 32 Bytes - hash of the block. null when its pending block.
     *              parentHash: DATA, 32 Bytes - hash of the parent block.
     *              nonce: DATA, 8 Bytes - hash of the generated proof-of-work. null when its pending block.
     *              sha3Uncles: DATA, 32 Bytes - SHA3 of the uncles data in the block.
     *              logsBloom: DATA, 256 Bytes - the bloom filter for the logs of the block. null when its pending block.
     *              transactionsRoot: DATA, 32 Bytes - the root of the transaction trie of the block.
     *              stateRoot: DATA, 32 Bytes - the root of the final state trie of the block.
     *              receiptsRoot: DATA, 32 Bytes - the root of the receipts trie of the block.
     *              miner: DATA, 20 Bytes - the address of the beneficiary to whom the mining rewards were given.
     *              difficulty: QUANTITY - integer of the difficulty for this block.
     *              totalDifficulty: QUANTITY - integer of the total difficulty of the chain until this block.
     *              extraData: DATA - the “extra data” field of this block.
     *              size: QUANTITY - integer the size of this block in bytes.
     *              gasLimit: QUANTITY - the maximum gas allowed in this block.
     *              gasUsed: QUANTITY - the total used gas by all transactions in this block.
     *              timestamp: QUANTITY - the unix timestamp for when the block was collated.
     *              transactions: Array - Array of transaction objects, or 32 Bytes transaction hashes depending on the last given parameter.
     *              uncles: Array - Array of uncle hashes.
     */
    public function getBlockByHash($hash)
    {

        return $this->request('eth_getBlockByHash', [$hash]);
    }

    /**
     * Returns information about a block by block number.
     * @param string $quantity
     * @param bool $allObject
     * @throws \Exception
     * Returns: See eth_getBlockByHash
     */
    public function getBlockByNumber(string $quantity = Quantity::latest, bool $allObject = false)
    {

        return $this->request('eth_getBlockByNumber', [$quantity, $allObject]);
    }

    /**
     * Returns the information about a transaction requested by transaction hash.
     * @param $hash
     * @throws \Exception
     * Returns: Object - A transaction object, or null when no transaction was found:
     *          blockHash: DATA, 32 Bytes - hash of the block where this transaction was in. null when its pending.
     *          blockNumber: QUANTITY - block number where this transaction was in. null when its pending.
     *          from: DATA, 20 Bytes - address of the sender.
     *          gas: QUANTITY - gas provided by the sender.
     *          gasPrice: QUANTITY - gas price provided by the sender in Wei.
     *          hash: DATA, 32 Bytes - hash of the transaction.
     *          input: DATA - the data send along with the transaction.
     *          nonce: QUANTITY - the number of transactions made by the sender prior to this one.
     *          to: DATA, 20 Bytes - address of the receiver. null when its a contract creation transaction.
     *          transactionIndex: QUANTITY - integer of the transactions index position in the block. null when its pending.
     *          value: QUANTITY - value transferred in Wei.
     *          v: QUANTITY - ECDSA recovery id
     *          r: DATA, 32 Bytes - ECDSA signature r
     *          s: DATA, 32 Bytes - ECDSA signature s
     */
    public function getTransactionByHash($hash)
    {

        return $this->request('eth_getTransactionByHash', [$hash]);
    }

    /**
     * Returns information about a transaction by block hash and transaction index position.
     * @param $hash
     * @param int $index
     * @throws \Exception
     * Returns: See eth_getTransactionByHash
     */
    public function getTransactionByBlockHashAndIndex($hash, int $index = 0)
    {

        return $this->request('eth_getTransactionByBlockHashAndIndex', [$hash, $index]);
    }

    /**
     * Returns information about a transaction by block number and transaction index position.
     * @param string $quantity
     * @param int $index
     * @throws \Exception
     * Returns: See eth_getTransactionByHash
     */
    public function getTransactionByBlockNumberAndIndex(string $quantity = Quantity::latest, int $index = 0)
    {

        return $this->request('eth_getTransactionByBlockNumberAndIndex', [$quantity, $index]);
    }

    /**
     * Returns the receipt of a transaction by transaction hash.
     * Note That the receipt is not available for pending transactions.
     * @param $hash
     * @throws \Exception
     * Returns: Object - A transaction receipt object, or null when no receipt was found:
     *              transactionHash : DATA, 32 Bytes - hash of the transaction.
     *              transactionIndex: QUANTITY - integer of the transactions index position in the block.
     *              blockHash: DATA, 32 Bytes - hash of the block where this transaction was in.
     *              blockNumber: QUANTITY - block number where this transaction was in.
     *              from: DATA, 20 Bytes - address of the sender.
     *              to: DATA, 20 Bytes - address of the receiver. null when its a contract creation transaction.
     *              cumulativeGasUsed : QUANTITY  - The total amount of gas used when this transaction was executed in the block.
     *              gasUsed : QUANTITY  - The amount of gas used by this specific transaction alone.
     *              contractAddress : DATA, 20 Bytes - The contract address created, if the transaction was a contract creation, otherwise null.
     *              logs: Array - Array of log objects, which this transaction generated.
     *              logsBloom: DATA, 256 Bytes - Bloom filter for light clients to quickly retrieve related logs.
     *          It also returns either :
     *              root : DATA 32 bytes of post-transaction stateroot (pre Byzantium)
     *              status: QUANTITY either 1 (success) or 0 (failure)
     */
    public function getTransactionReceipt($hash)
    {

        return $this->request('eth_getTransactionReceipt', [$hash]);
    }

    /**
     * Returns information about a uncle of a block by hash and uncle index position.
     * @param $hash
     * @param int $index
     * @throws \Exception
     * Returns: See eth_getBlockByHash
     */
    public function getUncleByBlockHashAndIndex($hash, int $index = 0)
    {

        return $this->request('eth_getUncleByBlockHashAndIndex', [$hash, $index]);
    }

    /**
     * Returns information about a uncle of a block by number and uncle index position.
     * @param string $quantity
     * @param int $index
     * @throws \Exception
     * Returns: See eth_getBlockByHash
     *          Note: An uncle doesn’t contain individual transactions.
     */
    public function getUncleByBlockNumberAndIndex(string $quantity = Quantity::latest, int $index = 0)
    {

        return $this->request('eth_getUncleByBlockNumberAndIndex', [$quantity, $index]);
    }

    /**
     * Returns a list of available compilers in the client.
     * @throws \Exception
     * Returns: Array - Array of available compilers.
     */
    public function getCompilers()
    {

        return $this->request('eth_getCompilers', []);
    }

    /**
     * Returns compiled solidity code.
     * @param $source
     * @throws \Exception
     * Returns: DATA - The compiled source code.
     */
    public function compileSolidity($source)
    {

        return $this->request('eth_compileSolidity', [$source]);
    }

    /**
     * Returns compiled LLL code.
     * @param $source
     * @throws \Exception
     * Returns: DATA - The compiled source code.
     */
    public function compileLLL($source)
    {

        return $this->request('eth_compileLLL', [$source]);
    }

    /**
     * Returns compiled serpent code.
     * @param $source
     * @throws \Exception
     * Returns: DATA - The compiled source code.
     */
    public function compileSerpent($source)
    {

        return $this->request('eth_compileSerpent', [$source]);
    }

    /**
     * Creates a filter object, based on filter options, to notify when the state changes (logs).
     * To check if the state has changed, call eth_getFilterChanges.
     *
     * A note on specifying topic filters:
     * Topics are order-dependent. A transaction with a log with topics [A, B] will be matched by the following topic filters:
     *      [] “anything”
     *      [A] “A in first position (and anything after)”
     *      [null, B] “anything in first position AND B in second position (and anything after)”
     *      [A, B] “A in first position AND B in second position (and anything after)”
     *      [[A, B], [A, B]] “(A OR B) in first position AND (A OR B) in second position (and anything after)”
     *
     * @param $fromBlock
     * @param $toBlock
     * @param $address
     * @param $topic
     * @throws \Exception
     * Returns: QUANTITY - A filter id.
     */
    public function newFilter($fromBlock = null, $toBlock = null, $address = null, $topic = [])
    {
        $data = $this->getData($fromBlock, $toBlock, $address, $topic);
        return $this->request('eth_newFilter', [$data]);
    }

    /**
     * Creates a filter in the node, to notify when a new block arrives.To check if the state has changed, call eth_getFilterChanges.
     * @throws \Exception
     * Returns: QUANTITY - A filter id.
     */
    public function newBlockFilter()
    {
        return $this->request('eth_newBlockFilter', []);
    }

    /**
     * Creates a filter in the node, to notify when new pending transactions arrive.To check if the state has changed, call eth_getFilterChanges.
     * @throws \Exception
     * Returns: QUANTITY - A filter id.
     */
    public function newPendingTransactionFilter()
    {
        return $this->request('eth_newPendingTransactionFilter', []);
    }

    /**
     * Uninstalls a filter with given id. Should always be called when watch is no longer needed.
     * Additonally Filters timeout when they aren’t requested with eth_getFilterChanges for a period of time.
     * @param $filterNumber
     * @throws \Exception
     * Returns: Boolean - true if the filter was successfully uninstalled, otherwise false.
     */
    public function uninstallFilter($filterNumber)
    {
        return $this->request('eth_uninstallFilter', [$filterNumber]);
    }

    /**
     * Polling method for a filter, which returns an array of logs which occurred since last poll.
     * @param $filterNumber
     * @throws \Exception
     * Returns: Array - Array of log objects, or an empty array if nothing has changed since last poll.
     *          For filters created with eth_newBlockFilter the return are block hashes (DATA, 32 Bytes), e.g. ["0x3454645634534..."].
     *          For filters created with eth_newPendingTransactionFilter  the return are transaction hashes (DATA, 32 Bytes), e.g. ["0x6345343454645..."].
     *          For filters created with eth_newFilter logs are objects with following params:
     *          removed: TAG - true when the log was removed, due to a chain reorganization. false if its a valid log.
     *          logIndex: QUANTITY - integer of the log index position in the block. null when its pending log.
     *          transactionIndex: QUANTITY - integer of the transactions index position log was created from. null when its pending log.
     *          transactionHash: DATA, 32 Bytes - hash of the transactions this log was created from. null when its pending log.
     *          blockHash: DATA, 32 Bytes - hash of the block where this log was in. null when its pending. null when its pending log.
     *          blockNumber: QUANTITY - the block number where this log was in. null when its pending. null when its pending log.
     *          address: DATA, 20 Bytes - address from which this log originated.
     *          data: DATA - contains one or more 32 Bytes non-indexed arguments of the log.
     *          topics: Array of DATA - Array of 0 to 4 32 Bytes DATA of indexed log arguments.
     *          (In solidity: The first topic is the hash of the signature of the event (e.g. Deposit(address,bytes32,uint256)), except you declared the event with the anonymous specifier.)
     */
    public function getFilterChanges($filterNumber)
    {
        return $this->request('eth_getFilterChanges', [$filterNumber]);
    }

    /**
     * Returns an array of all logs matching filter with given id.
     * @param $filterNumber
     * @throws \Exception
     * Returns: See eth_getFilterChanges
     */
    public function getFilterLogs($filterNumber)
    {
        return $this->request('eth_getFilterLogs', [$filterNumber]);
    }

    /**
     * Returns an array of all logs matching a given filter object.
     * @param $fromBlock
     * @param $toBlock
     * @param $address
     * @param $topic
     * @throws \Exception
     * Returns: See eth_getFilterChanges
     */
    public function getLogs(string $fromBlock = null, string $toBlock = null, $address = null, $topic = [])
    {
        $data = $this->getData($fromBlock, $toBlock, $address, $topic);
        return $this->request('eth_getLogs', [$data]);
    }

    /**
     * Returns the hash of the current block, the seedHash, and the boundary condition to be met (“target”).
     * @throws \Exception
     * Returns: Array - Array with the following properties:
     *              DATA, 32 Bytes - current block header pow-hash
     *              DATA, 32 Bytes - the seed hash used for the DAG.
     *              DATA, 32 Bytes - the boundary condition (“target”), 2^256 / difficulty.
     */
    public function getWork()
    {
        return $this->request('eth_getWork', []);
    }

    /**
     * Used for submitting a proof-of-work solution.
     * @param $data1
     * @param $data2
     * @param $data3
     * @throws \Exception
     * Returns: Boolean - returns true if the provided solution is valid, otherwise false.
     */
    public function submitWork($data1, $data2, $data3)
    {
        return $this->request('eth_submitWork', [$data1, $data2 . $data3]);
    }

    /**
     * Used for submitting mining hashrate.
     * @param $hashRate
     * @param $id
     * @throws \Exception
     * Returns: Boolean - returns true if submitting went through succesfully and false otherwise.
     */
    public function submitHashrate($hashRate, $id)
    {
        return $this->request('eth_submitHashrate', [$hashRate, $id]);
    }

    /**
     * Returns the number of most recent block.
     * @throws \Exception
     * Returns: QUANTITY - integer of the current block number the client is on.
     */
    public function blockNumber()
    {
        return $this->request('eth_blockNumber', []);
    }

    /**
     * Returns the current whisper protocol version.
     * @throws \Exception
     * Returns: String - The current whisper protocol version
     */
    public function shhVersion()
    {
        return $this->request('shh_version', []);
    }

    /**
     * Returns the current network id.
     * @throws \Exception
     * Returns: String - The current network id.
     *              "1": Ethereum Mainnet
     *              "2": Morden Testnet (deprecated)
     *              "3": Ropsten Testnet
     *              "4": Rinkeby Testnet
     *              "42": Kovan Testnet
     */

    public function chainId(): int
    {
        return $this->netVersion();
    }

    public function request($method, $params = []){

        $data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => $this->requestId++,
            ]
        ];
        $res = $this->client->post('', $data);
        $body = json_decode($res->getBody());
        if (isset($body->error) && !empty($body->error)) {
            throw new \Exception($body->error->message . " [Method] {$method}", $body->error->code);
        }
        return $body->result;
    }

    /**
     * @param $fromBlock
     * @param $toBlock
     * @param $address
     * @param $topic
     * @return array
     */
    private function getData($fromBlock, $toBlock, $address, $topic): array
    {
        $data = [];

        if (!empty($fromBlock)) {
            $data['fromBlock'] = $fromBlock;
        }else{
            $data['fromBlock'] ="0x0";
        }
        if (!empty($toBlock)) {
            $data['toBlock'] = $toBlock;
        }
        if (!empty($address)) {
            $data['address'] = $address;
        }
        if (!empty($topic)) {
            $data['topic'] = $topic;
        }
        return $data;
    }
}