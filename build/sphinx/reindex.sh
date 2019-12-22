#!/bin/bash

echo -n "Indexing content... "
indexer --rotate --all --quiet
echo "done"