#!/bin/bash
_yesterday=$(date +"%Y%m%d" -d "yesterday")
sudo mkdir logs_old/$_yesterday
sudo mv logs/* logs_old/$_yesterday/
sudo chown -R ubuntu:ubuntu logs_old/

