#!/bin/bash

# Set up docker-compose in the Vagrant developer environment
curl -s -L https://github.com/docker/compose/releases/download/1.3.3/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
