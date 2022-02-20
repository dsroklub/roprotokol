#!/bin/bash
apt install firefox-esr joe matchbox ssh xserver-common nodm ntp xserver-xorg-core term xserver-xorg-input-libinput xserver-xorg-input-evdev
cp etc/{hosts,ntp} /etc/
cp etc/default/nodm /etc/default/nodm
adduser dsr
cp dot_xsession /home/dsr/.xsession
chown dsr dot_xsession

mkdir -p /usr/lib/firefox/distribution
cp usr.lib.firefox.distribution.policies.json /usr/lib/firefox/distribution/

#cp etc/network/interfaces /etc/network/interfaces
#cat root/dot_bashrc >> /root/.bashrc
