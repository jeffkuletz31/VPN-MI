#!/bin/bash
#StormBitVPN Easy-RSA 2.0 Keyfile Management Backend Script
# \|/ put it in this folder below \|/, then modify where it says "Change this!" as you wish. 

#DO NOT EDIT ANY OTHER VARIABLES FOR THE RSA CONFIGURATION, AS YOU CAN AND WILL VIOLENTLY BREAK THINGS.
cd /etc/openvpn/easy-rsa/2.0/

#Easy-RSA config. Careful what you touch.
export EASY_RSA="${EASY_RSA:-.}"
export EASY_RSA="`pwd`"
export OPENSSL="openssl"
export PKCS11TOOL="pkcs11-tool"
export GREP="grep"
export KEY_CONFIG=`$EASY_RSA/whichopensslcnf $EASY_RSA`
export KEY_DIR="$EASY_RSA/keys"
export PKCS11_MODULE_PATH="dummy"
export PKCS11_PIN="dummy"
export KEY_SIZE=1024
export CA_EXPIRE=3650
export KEY_EXPIRE=3650
export KEY_COUNTRY="US" #change this!
export KEY_PROVINCE="WA" #change this!
export KEY_CITY="Seattle" #change this!
export KEY_ORG="StormBit" #change this!
export KEY_EMAIL="$2"
export KEY_CN=vpn.stormbit.net #Change this!
export KEY_NAME=server
export KEY_OU=server
export CRL="crl.pem"
export RT="revoke-test.pem"
#End easy-rs config

if [ $2 == "--delete" ]; then
	deleteKey $1
fi

function deleteKey($keyname) {
	if [ "$KEY_DIR" ]; then
		cd "$KEY_DIR"
		rm -f "$RT"
		export KEY_CN=""
		export KEY_OU=""
		export KEY_NAME=""
		$OPENSSL ca -revoke "$keyname.crt" -config "$KEY_CONFIG"
		$OPENSSL ca -gencrl -out "$CRL" -config "$KEY_CONFIG"
		if [ -e export-ca.crt ]; then
			cat export-ca.crt "$CRL" >"$RT"
		else
			cat ca.crt "$CRL" >"$RT"
		fi
		$OPENSSL verify -CAfile "$RT" -crl_check "$keyname.crt"
	fi
fi
}
"$EASY_RSA/pkitool" $1
#this used to be able to generate a pkcs12 cert file for use with Android scripts, but I can't automate it, so users'll have to do it themselves. user.crt is from the <cert> tags in the config, user.key from <key> tags, and ca.crt from <ca> tags. Just copy the contents of each tag, sans-tags themselves to the files, and run this:
#openssl pkcs12 -export -in user.crt -inkey user.key -certfile ca.crt -name user -out user.p12

#generate config. Edit as necessary.
echo 'dev tun
proto udp
remote vpn.stormbit.net 52
pull
nobind
resolv-retry infinite
persist-key
persist-tun
cipher BF-CBC
comp-lzo
verb 3
tls-client

<ca>' > keys/$1.ovpn
cat keys/ca.crt >> keys/$1.ovpn
echo '</ca>

<cert>' >> keys/$1.ovpn
cat keys/$1.crt >> keys/$1.ovpn
echo '</cert>

<key>' >> keys/$1.ovpn
cat keys/$1.key >> keys/$1.ovpn
echo '</key>' >> keys/$1.ovpn
