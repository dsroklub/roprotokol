#! /usr/bin/php
<?php
# Command line cache invalidation.
# use when e.g. updating DB manually


$mem  = new Memcached();
print("invalidate cache\n");
$mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
$mem->addServer('127.0.0.1',11211);
$mem->increment("boat", 1, time());
$mem->increment("event", 1, time());
$mem->increment("file", 1, time());
$mem->increment("trip", 1, time());
$mem->increment("stats", 1, time());
$mem->increment("fora", 1, time());
$mem->increment("message", 1, time());
$mem->increment("member", 1, time());
$mem->increment("destination", 1, time());

echo "all invalidated";