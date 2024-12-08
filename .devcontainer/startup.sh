#!/bin/bash

# Configure git safe directory
git config --global --add safe.directory /workspace

# Install pre-commit
pip install pre-commit

# Install pre-commit hooks
pre-commit install
