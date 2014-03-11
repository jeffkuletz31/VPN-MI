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

server-example.conf should get you started in the right direction. Copy it to /etc/openvpn/server.conf and make edits as neccessary.

####Generating server certificates

	cd /etc/openvpn/easy-rsa/2.0/

Edit ./vars to match values you like. Don't change the 'server' parts! Then:

	source ./vars
	./clean-all
	./build-ca
	./build-dh
	./build-key-server server

####Setting up StormBitVPN MI

*  Copy the index.php script to a desired location. You can change the name if you wish.

*  Copy the usermgmt.sh backend script and template.ovpn to your easy-rsa/2.0 folder and make usermgmt.sh executable (chmod +x usrmgmt.sh)

*  Start your server! You may need to make and then revoke a key to get it to start:

		./usermgmt.sh revoked-key revoked@key
		./usermgmt.sh revoked-key --delete
		
####Troubleshooting

*	If you're not able to connect, and your VPN configuration looks like it's all mushed up on one line, or has ^Ms on the end of each line you may need to change the line endings on it. Notepad++ is capable of doing this. By default the configs will use Windows (CRLF) Line Endings.

*	If your server starts, but when you connect, it exits, you have not created a Certificate Revocation File. That can be done like so:

	./usermgmt.sh revoked-key revoked@key
	./usermgmt.sh revoked-key --delete

*	If your clients disconnect with a message that resembles "Certification is not yet valid", your clients' clocks and the server clock are not synced.

*	If there's something wrong with this software that wasn't noted in a Git commit message, [jump on IRC and let me know!](http://iris.stormbit.net/?nick=Guest.....&channels=stormbitgames&prompt=1)

*	You will need to set some way to chmod and chown the entirety of openvpn to allow both openvpn and your web user to both be able to read and write files in /etc/openvpn! I suggest telling openvpn to run as the httpduser, and chown/chmod the files to mode 0700, and owned by the webuser within the /etc/init.d script for openvpn.

Sources and helpful links
---------------
*	General setup: [DigitalOcean's Debian 6 Guide](https://www.digitalocean.com/community/articles/how-to-setup-and-configure-an-openvpn-server-on-debian-6)

*	Android Client Certification File setup: [OpenVPN Forums' Thread](https://forums.openvpn.net/topic9062.html)

*	Some extra configuration ideas: [Sachin Sharma's OpenVPN on CentOS guide.](http://sachinsharm.wordpress.com/2013/08/09/installationsetup-and-configure-an-openvpn-server-on-centosrhel-6-3/)

*	Helpful tips and additional parts (Includes WINS client setup!): [Christoph's OpenVPN Mini-FAQ](https://workaround.org/openvpn-faq)
