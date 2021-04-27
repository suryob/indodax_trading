#!/bin/bash

while true; do
  # Do something
  /usr/bin/php -q indodaxapi.php
  /usr/bin/php -q getTransactionStatus.php
  sleep 30;
done
