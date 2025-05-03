FROM php:8.3-fpm-alpine

# システムの依存関係をインストール
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    postgresql-dev

# PHPの拡張機能をインストール
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ワーキングディレクトリを設定
WORKDIR /var/www/html

# ユーザーを作成
RUN addgroup -g 1000 laravel && \
    adduser -G laravel -g laravel -s /bin/sh -D laravel

# ディレクトリの所有権を変更
RUN chown -R laravel:laravel /var/www/html

# ユーザーを切り替え
USER laravel

# コンテナ起動時のコマンド
CMD ["php-fpm"]

# ポートを公開
EXPOSE 9000