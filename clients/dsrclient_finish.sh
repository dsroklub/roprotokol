#!/bin/bash
# only run when terminals set up and ready to install on DSR network

cp etc/network/interfaces /etc/network/interfaces
cat root/dot_bashrc >> /root/.bashrc
