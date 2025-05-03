FROM nginx:alpine

# タイムゾーンを設定
RUN apk add --no-cache tzdata && \
    cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && \
    echo "Asia/Tokyo" > /etc/timezone && \
    apk del tzdata

# Nginxユーザーを作成
RUN adduser -D -H -u 1000 -s /sbin/nologin -G www-data www-data

# ワーキングディレクトリを設定
WORKDIR /var/www/html

# ポートを公開
EXPOSE 80