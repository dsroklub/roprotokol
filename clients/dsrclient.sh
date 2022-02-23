#!/bin/bash
timedatectl set-timezone Europe/Copenhagen
apt install firefox-esr firefox-esr-l10n-da joe matchbox ssh ssh-server xserver-common nodm ntp xserver-xorg-core term xserver-xorg-input-libinput xserver-xorg-input-evdev tinysshd numlockx

#necessary with DVI adapter
apt install xscreensaver xscreensaver-data-extra

apt purge exim4-base usb-modeswitch avahi-daemon
apt autoremove
cp etc/{hosts,ntp,locale.gen} /etc/
cp etc/wpa_supplicant/wpa_supplicant.conf /etc/wpa_supplicant/wpa_supplicant.conf
cp etc/default/{locale,nodm,keyboard} /etc/default/
cp dot_xscreensaver /home/dsr/.xscreensaver
chown dsr /home/dsr/.xscreensaver
adduser dsr
cp dot_xsession /home/dsr/.xsession
chown dsr dot_xsession
mkdir /home/dsr/.ssh/
chown dsr /home/dsr/.ssh
chmod 700 /home/dsr/.ssh
echo 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIHY4y7gxPL3csnApOv1+RCm2EykISrAQuhK9djwlAPLv roprotokol@roprotokol' > /home/dsr/.ssh/authorized_keys
chmod 600 /home/dsr/.ssh/authorized_keys
chown dsr /home/dsr/.ssh/authorized_keys
locale-gen
usermod -L pi
cp usr.lib.firefox.distribution.policies.json /usr/lib/firefox-esr/distribution/policies.json
cp user.js /home/dsr/.mozilla/firefox/*.default-esr/


