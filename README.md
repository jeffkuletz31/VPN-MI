StormBit VPN Management Interface
===========

What the heck is it?
---------------------

It's a management interface for our VPN service. It allows users to register, login, and download their generated client config and keys, and have the ability to revoke and approve users. We don't plan to add a payment portal, but maybe later :)

Why? Isn't there stuff out there that does just that?
-----------------

Yes, there is. We wanted to put together our own software, and make it available to those who want it, be it some business, or just a community like us looking for some solution.

How do I work this thing?!
---------------

1. Get your server set up. If you're on a Debian or Ubuntu machine:

####/etc/openvpn/server.conf

	mode server
	tls-server
	port 52 #Change this
	proto udp
	dev tap
	persist-key
	persist-tun
	ca /etc/openvpn/easy-rsa/2.0/keys/ca.crt
	cert /etc/openvpn/easy-rsa/2.0/keys/server.crt
	key /etc/openvpn/easy-rsa/2.0/keys/server.key
	dh /etc/openvpn/easy-rsa/2.0/keys/dh1024.pem
	cipher BF-CBC
	comp-lzo
	ifconfig 10.8.0.1 10.8.0.2
	server 10.8.0.0 255.255.255.252
	push "redirect-gateway def1 bypass-dhcp"
	push "dhcp-option DNS 8.8.8.8"
	push "dhcp-option DNS 8.8.4.4"
	push "dhcp-option WINS 10.8.0.1"
	#If you want a WINS server (samba) setup and visible on the VPN's server, set this, and read some of the links at the end of this document.
	push "dhcp-option DOMAIN supernova.arghargh200.net"  #Change this!
	keepalive 10 120
	status /var/log/openvpn-status.log
	log-append /etc/openvpn/server.log
	verb 3
	duplicate-cn #Allows our clients to use multiple clients at a time with one key.
	client-to-client #allows our clients to talk to each other
	comp-lzo
	crl-verify /etc/openvpn/easy-rsa/2.0/keys/crl.pem 
	
IMPORTANT! That last line is used for banning users and revoking their keys! Removing it or not having it renders the function useless!

####Generating server certificates

	cd /etc/openvpn/easy-rsa/2.0/
	Edit ./vars to match values you like. Don't change the 'server' parts!
	Then:
	source ./vars
	./clean-all
	./build-ca
	./build-dh
	./build-key-server server

####Setting up StormBitVPN MI

*  Copy the index.php script to a desired location. You can change the name if you wish.

*  Copy the usermgmt.sh backend script to your easy-rsa/2.0 folder and make it executable (chmod +x usrmgmt.sh)

*  Start your server! You may need to make and then revoke a key to get it to start:


		./usermgmt.sh revoked-test revoked@test
		./usermgmt.sh revoked-test --delete

Sources and helpful links
---------------
General setup: [DigitalOcean's Debian 6 Guide](https://www.digitalocean.com/community/articles/how-to-setup-and-configure-an-openvpn-server-on-debian-6)

Android Client Certification File setup: [OpenVPN Forums' Thread](https://forums.openvpn.net/topic9062.html)

Configuration files: My own, sanitized and changed.

Some extra configuration ideas: [Sachin Sharma's OpenVPN on CentOS guide.](http://sachinsharm.wordpress.com/2013/08/09/installationsetup-and-configure-an-openvpn-server-on-centosrhel-6-3/)

Helpful tips and additional parts (Includes WINS client setup!): [Christoph's OpenVPN Mini-FAQ](https://workaround.org/openvpn-faq)
