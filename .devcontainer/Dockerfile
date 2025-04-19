FROM mcr.microsoft.com/devcontainers/php:1-8.3-bookworm

# Install MariaDB client and network debug tools
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y mariadb-client libpng-dev libicu-dev libzip-dev inetutils-ping netcat-openbsd tcpdump\ 
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql gd intl zip

# [Optional] Uncomment this section to install additional OS packages.

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1

