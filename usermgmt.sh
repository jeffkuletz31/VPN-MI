#!/bin/bash
#StormBitVPN Easy-RSA 2.0 Keyfile Management Backend Script

#Open and edit /etc/openvpn/easy-rsa/2.0/vars to match whatever values you like.
#Don't change things that say 'server'!
cd /etc/openvpn/easy-rsa/2.0/
source ./vars
#Don't touch these
export KEY_EMAIL="$2"
export CRL="crl.pem"
export RT="revoke-test.pem"


if [ $2 == "--delete" ]; then
	deleteKey $1
fi

deleteKey () {
	if [ "$KEY_DIR" ]; then
		cd "$KEY_DIR"
		rm -f "$RT"
		export KEY_CN=""
		export KEY_OU=""
		export KEY_NAME=""
		$OPENSSL ca -revoke "$1.crt" -config "$KEY_CONFIG"
		$OPENSSL ca -gencrl -out "$CRL" -config "$KEY_CONFIG"
		if [ -e export-ca.crt ]; then
			cat export-ca.crt "$CRL" >"$RT"
		else
			cat ca.crt "$CRL" >"$RT"
		fi
		$OPENSSL verify -CAfile "$RT" -crl_check "$1.crt"
	fi
}
"$EASY_RSA/pkitool" $1
