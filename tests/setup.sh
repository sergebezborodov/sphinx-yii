#!/bin/sh

# we don't use --rotate for tests
searchd --config sphinx.conf --stop
indexer --config sphinx.conf --all
searchd --config sphinx.conf