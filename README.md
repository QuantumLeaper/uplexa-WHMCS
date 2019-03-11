# uPlexa WHMCS
A WHMCS Payment Gateway for accepting uPlexa

## Dependencies
This plugin is rather simple but there are a few things that need to be set up beforehand.

* A web server! Ideally with the most recent versions of PHP and mysql

* The uPlexa wallet-cli and uPlexa wallet-rpc tools found [here](https://uplexa.com/downloads/)

* [WHMCS](https://www.whmcs.com/)
This uPlexa plugin is a payment gateway for WHMCS

## Step 1: Activating the plugin
* Downloading: First of all, you will need to download the plugin.  If you wish, you can also download the latest source code from GitHub. This can be done with the command `git clone https://github.com/uplexa/uplexa-whmcs.git` or can be downloaded as a zip file from the GitHub web page.


* Put the plugin in the correct directory: You will need to copy `uplexa.php` and the folder named `uplexa` from this repo/unzipped release into the WHMCS Payment Gateways directory. This can be found at `whmcspath/modules/gateways/`

* Activate the plugin from the WHMCS admin panel: Once you login to the admin panel in WHMCS, click on "Setup -> Payments -> Payment Gateways". Click on "All Payment Gateways". Then click on the "uPlexa" gateway to activate it.

* Enter a Module Secret Key.  This can be any random text and is used to verify payments.  

* Enter the values for Wallet RPC Host, Wallet RPC Port, Username, and Password (these are from uplexa-wallet-rpc below).  Optionally enter a percentage discount for all invoices paid via uPlexa.

* Optionally install the addon module to disable WHMCS fraud checking when using uPlexa. You will need to copy the folder `addons/uplexaenable/` from this repo/unzipped release into the WHMCS Addons directory. This can be found at `whmcspath/addons/`.  

* Activate the uPlexa Enabler addon from the WHMCS admin panel: Click on "Setup -> Addon Modules". Find "uPlexa Enabler" and click on "Activate". Click "Configure" and choose the uPlexa Payment Gateway in the drop down list. Check the box for "Enable checking for payment method by module" and click "Save Changes".

## Step 2: Get a uPlexa daemon to connect to

### Option 1: Running a full node yourself

To do this: start the uPlexa daemon on your server and leave it running in the background. This can be accomplished by running `./uplexad` inside your uPlexa downloads folder. The first time that you start your node, the uPlexa daemon will download and sync the entire uPlexa blockchain. This can take several hours and is best done on a machine with at least 4GB of ram, an SSD hard drive (with at least 15GB of free space), and a high speed internet connection.

### Option 2: Connecting to a remote node
It is probably easiest to use remote.uplexa.com:21061 which will automatically connect you to a random node.

## Step 3: Setup your uPlexa wallet-rpc

* Setup a uPlexa wallet using the uplexa-wallet-cli tool.

* Start the Wallet RPC and leave it running in the background. This can be accomplished by running `./uplexa-wallet-rpc --rpc-bind-port 21065 --rpc-login username:password --log-level 2 --wallet-file /path/walletfile` where "username:password" is the username and password that you want to use, separated by a colon and  "/path/walletfile" is your actual wallet file. If you wish to use a remote node you can add the `--daemon-address` flag followed by the address of the node. `--daemon-address remote.uplexa.com:21061` for example.



## Info on server authentication
It is recommended that you specify a username/password with your wallet rpc. This can be done by starting your wallet rpc with `uplexa-wallet-rpc --rpc-bind-port 21065 --rpc-login username:password --wallet-file /path/walletfile` where "username:password" is the username and password that you want to use, separated by a colon. Alternatively, you can use the `--restricted-rpc` flag with the wallet rpc like so `./uplexa-wallet-rpc --testnet --rpc-bind-port 21065 --restricted-rpc --wallet-file wallet/path`.
