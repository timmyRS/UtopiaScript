#!/bin/bash

sleep 1 && start http://localhost:8035/ &
jekyll serve -s docs -P 8035
