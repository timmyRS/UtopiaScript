#!/bin/bash

cd docs/ || exit 1
gem install bundler jekyll
bundler install
cd .. || exit 1
sleep 1 && start http://localhost:8035/ &
jekyll serve -s docs -P 8035
