#!/bin/bash
set -e

echo "🚀 Setting up development environment..."

# Update package lists
echo "📦 Updating package lists..."
apt-get update -qq

# Install zsh, pipx, and build dependencies for pre-commit
# Note: git is installed via devcontainer feature, so it's available at container startup
echo "📦 Installing zsh, pipx, and build dependencies..."
apt-get install -y --no-install-recommends \
    zsh \
    pipx \
    build-essential \
    python3-dev \
    > /dev/null

# Install oh-my-zsh (non-interactive)
echo "✨ Installing oh-my-zsh..."
if [ ! -d "$HOME/.oh-my-zsh" ]; then
    RUNZSH=no CHSH=no sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended
    echo "✅ oh-my-zsh installed successfully"
else
    echo "ℹ️  oh-my-zsh already installed, skipping..."
fi

# Configure zsh to show full path in prompt and inherit bash configuration
echo "⚙️  Configuring zsh prompt and bash inheritance..."
if [ -f "$HOME/.zshrc" ]; then
    # Source bash configuration files if they exist
    if [ -f "$HOME/.bashrc" ]; then
        if ! grep -q "source.*\.bashrc" "$HOME/.zshrc"; then
            echo "" >> "$HOME/.zshrc"
            echo "# Inherit bash configuration" >> "$HOME/.zshrc"
            echo "[ -f ~/.bashrc ] && source ~/.bashrc" >> "$HOME/.zshrc"
        fi
    fi
    if [ -f "$HOME/.bash_profile" ]; then
        if ! grep -q "source.*\.bash_profile" "$HOME/.zshrc"; then
            echo "[ -f ~/.bash_profile ] && source ~/.bash_profile" >> "$HOME/.zshrc"
        fi
    fi
    if [ -f "$HOME/.profile" ]; then
        if ! grep -q "source.*\.profile" "$HOME/.zshrc"; then
            echo "[ -f ~/.profile ] && source ~/.profile" >> "$HOME/.zshrc"
        fi
    fi

    # Add pipx to PATH if not already present
    if ! grep -q "\$HOME/.local/bin" "$HOME/.zshrc"; then
        echo "" >> "$HOME/.zshrc"
        echo "# Add pipx to PATH" >> "$HOME/.zshrc"
        echo "export PATH=\"\$HOME/.local/bin:\$PATH\"" >> "$HOME/.zshrc"
    fi

    # Customize prompt to show full path
    # The default robbyrussell theme uses %1~ which shows only last directory
    # We'll override it at the end of .zshrc (after oh-my-zsh loads) to show full path
    # Using zsh built-in color codes %F{color} and %f for reset
    if ! grep -q "# Custom prompt: show full path" "$HOME/.zshrc"; then
        echo "" >> "$HOME/.zshrc"
        echo "# Custom prompt: show full path" >> "$HOME/.zshrc"
        echo "# Override the default prompt to show full path instead of just directory name" >> "$HOME/.zshrc"
        echo "# This must be at the end, after oh-my-zsh loads" >> "$HOME/.zshrc"
        echo "# Using zsh built-in color escape sequences: %F{cyan} for cyan, %f to reset" >> "$HOME/.zshrc"
        echo "PROMPT='%F{cyan}%~%f \$(git_prompt_info)%F{cyan}$%f '" >> "$HOME/.zshrc"
    fi
    echo "✅ zsh prompt configured"
fi

# Install pre-commit using pipx (recommended for Python applications)
echo "🔧 Installing pre-commit..."
pipx install pre-commit > /dev/null

# Ensure pipx bin directory is in PATH
# Add to multiple profile files to ensure it works in all shells
PIPX_BIN_PATH="$HOME/.local/bin"
if [ -d "$PIPX_BIN_PATH" ]; then
    # Add to .bashrc
    if [ -f "$HOME/.bashrc" ]; then
        if ! grep -q "$PIPX_BIN_PATH" "$HOME/.bashrc"; then
            echo "" >> "$HOME/.bashrc"
            echo "# Add pipx to PATH" >> "$HOME/.bashrc"
            echo "export PATH=\"$PIPX_BIN_PATH:\$PATH\"" >> "$HOME/.bashrc"
        fi
    else
        echo "# Add pipx to PATH" >> "$HOME/.bashrc"
        echo "export PATH=\"$PIPX_BIN_PATH:\$PATH\"" >> "$HOME/.bashrc"
    fi

    # Add to .profile (loaded by all shells)
    if [ -f "$HOME/.profile" ]; then
        if ! grep -q "$PIPX_BIN_PATH" "$HOME/.profile"; then
            echo "" >> "$HOME/.profile"
            echo "# Add pipx to PATH" >> "$HOME/.profile"
            echo "export PATH=\"$PIPX_BIN_PATH:\$PATH\"" >> "$HOME/.profile"
        fi
    else
        echo "# Add pipx to PATH" >> "$HOME/.profile"
        echo "export PATH=\"$PIPX_BIN_PATH:\$PATH\"" >> "$HOME/.profile"
    fi

    # Ensure it's in current session PATH
    export PATH="$PIPX_BIN_PATH:$PATH"

    # Also create a symlink in /usr/local/bin for system-wide access
    if [ -f "$PIPX_BIN_PATH/pre-commit" ] && [ ! -f "/usr/local/bin/pre-commit" ]; then
        ln -s "$PIPX_BIN_PATH/pre-commit" /usr/local/bin/pre-commit 2>/dev/null || true
    fi

    echo "✅ PATH configured for pipx"
fi

# Pre-commit hook environments will be downloaded automatically on first run
# Just ensure we're in the right directory and config exists
if [ -f "/var/www/html/.pre-commit-config.yaml" ]; then
    echo "✅ pre-commit configuration found"
    echo "   Note: Hook environments will download automatically on first 'pre-commit run'"
fi

echo "✅ pre-commit installed successfully"

# Set zsh as default shell
echo "🐚 Setting zsh as default shell..."
ZSH_PATH=$(which zsh)
if [ -z "$ZSH_PATH" ]; then
    ZSH_PATH="/usr/bin/zsh"
fi

# Update /etc/passwd to set zsh as default shell for root
if [ "$(id -u)" = "0" ]; then
    # Running as root, update /etc/passwd directly
    if ! grep -q "^root:.*:.*:.*:.*:.*:.*$ZSH_PATH" /etc/passwd; then
        sed -i "s|^root:\(.*\):\(.*\):\(.*\):\(.*\):\(.*\):\(.*\):.*|root:\1:\2:\3:\4:\5:\6:$ZSH_PATH|" /etc/passwd
        echo "✅ Updated /etc/passwd to set zsh as default shell for root"
    fi
fi

# Also try chsh as a fallback
if command -v chsh >/dev/null 2>&1; then
    chsh -s "$ZSH_PATH" 2>/dev/null || true
fi

# Verify the change
CURRENT_SHELL=$(getent passwd "$(whoami)" | cut -d: -f7)
if [ "$CURRENT_SHELL" = "$ZSH_PATH" ]; then
    echo "✅ zsh is now the default shell"
else
    echo "⚠️  Shell is currently: $CURRENT_SHELL (expected: $ZSH_PATH)"
    echo "   You may need to restart the devcontainer for the change to take effect"
fi

echo "🎉 Development environment setup complete!"
echo ""
echo "Installed tools:"
echo "  - git: $(git --version 2>/dev/null | cut -d' ' -f3 || echo 'installed')"
echo "  - zsh: $(zsh --version 2>/dev/null | cut -d' ' -f2 || echo 'installed')"
echo "  - pre-commit: $(pre-commit --version 2>/dev/null | head -n1 || echo 'installed')"
echo ""
echo "Note: You may need to restart your terminal or devcontainer for zsh to take effect."
