#! /usr/bin/php
<?php
# Command line cache invalidation.
# use when e.g. updating DB manually


$mem  = new Memcached();
print("invalidate cache\n");
$mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
$mem->addServer('127.0.0.1',11211);
$mem->increment("gym", 1, time());


