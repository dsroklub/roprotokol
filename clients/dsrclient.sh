#!/bin/bash
timedatectl set-timezone Europe/Copenhagen
apt install  firefox-l10n-da joe matchbox openssh-client xserver-common nodm xserver-xorg-core termit xserver-xorg-input-libinput xserver-xorg-input-evdev tinysshd numlockx bash-completion rsync

# firefox-esr

apt install lightdm lightdm-settings
#xserver-xorg-video-fbturbo # for use with multiseat

apt install lightdm-autologin-greeter
## cp etc/default/nodm /etc/default/

#necessary with DVI adapter
###apt install xscreensaver xscreensaver-data-extra

apt purge exim4-base usb-modeswitch avahi-daemon wolfram-engine libreoffice libreoffice-core modemmanager openssh-server openssh-sftp-server cups gvfs openbox-lxde-session
apt autoremove
cp etc/{hosts,ntp,locale.gen} /etc/
cp etc/systemd/timesyncd.conf /etc/systemd/
cp -i /etc/network/interfaces /etc/network/interfaces.bu
cp etc/network/interfaces /etc/network/
cp etc/wpa_supplicant/wpa_supplicant.conf /etc/wpa_supplicant/wpa_supplicant.conf
cp etc/default/{locale,nodm,keyboard} /etc/default/
cp dot_xscreensaver /home/dsr/.xscreensaver
chown dsr /home/dsr/.xscreensaver
adduser --gecos dsrbruger dsr
adduser --gecos dsrbruger2 dsr2 # for dualseat use
cp dot_xsession /home/dsr/.xsession
cp dot_xsession /home/dsr2/.xsession
chown dsr:dsr /home/dsr/.xsession
chown dsr2:dsr2 /home/dsr2/.xsession
mkdir -p /home/dsr/.ssh/ /home/dsr2/.ssh/
chown dsr /home/dsr/.ssh
chmod 700 /home/dsr/.ssh


echo 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIHY4y7gxPL3csnApOv1+RCm2EykISrAQuhK9djwlAPLv roprotokol@roprotokol' > /home/dsr/.ssh/authorized_keys
chmod 600 /home/dsr/.ssh/authorized_keys
chown dsr.dsr /home/dsr/.ssh/authorized_keys
locale-gen
#usermod -L pi
#cp usr.lib.firefox.distribution.policies.json /usr/lib/firefox-esr/distribution/policies.json
cp usr.lib.firefox.distribution.policies.json /usr/lib/firefox/distribution/policies.json
sudo -u dsr firefox -headless -CreateProfile  dsr
sudo -u dsr2 firefox -headless -CreateProfile  dsr

cp user.js /home/dsr/.mozilla/firefox/*.dsr/
echo NUMLOCK=off > /etc/default/numlockx
if [ -d /etc/lightdm/ ]
then
  echo "[Seat:seat1]" >> /etc/lightdm/lightdm.conf
  echo "autologin-user=dsr2" >> /etc/lightdm/lightdm.conf
  echo "[Seat:seat0]" >> /etc/lightdm/lightdm.conf
  echo "autologin-user=dsr" >> /etc/lightdm/lightdm.conf
fi

echo 'export http_proxy=http://10.21.55.1:4444/' >> /root/.bashrc



sed -i -e "s/^autologin-session=LXDE-pi-x/autologin-session=matchbox/" /etc/lightdm/lightdm.conf
sed -i -e "s/^user-session=LXDE-pi-x/user-session=matchbox/" /etc/lightdm/lightdm.conf


#for raspberry pi
if [ -d /boot/UUUconfig.txt ]
then
    # disable autologin
  ## TODO cp seat.rules /etc/udev/rules.d/99-seat.rules
  echo -e "[Service]\nExecStart=-/sbin/agetty  --noclear %I $TERM\n" > /etc/systemd/system/getty@tty1.service.d/autologin.conf
  ## TODO sed -i -e "s/^dtoverlay/#dtoverlay/" /boot/config.txt
  ## OLD cp etc.X11.xorg.conf.d.99-fbturbo.conf /etc/X11/xorg.conf.d/99-fbturbo.conf
fi
