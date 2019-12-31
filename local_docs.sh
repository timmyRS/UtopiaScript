#!/bin/bash

cd docs/ || exit 1
gem install bundler jekyll
bundler install
sleep 1 && start http://localhost:8035/ &
bundler exec jekyll serve -P 8035
