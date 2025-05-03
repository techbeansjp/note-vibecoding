# Laravel API 開発環境

このプロジェクトは、Docker を使用した Laravel API 開発環境です。複数のサービスをコンテナ化し、開発の一貫性と再現性を確保しています。

## 技術スタック

- **PHP**: 8.3 (PHP-FPM Alpine)
- **Laravel**: 12.x
- **Nginx**: Alpine 最新版
- **MySQL**: 8.0
- **Mailhog**: メール送信テスト用
- **MinIO**: S3互換オブジェクトストレージ
- **PHPMyAdmin**: データベース管理ツール

## 環境構成

このプロジェクトは以下のコンテナで構成されています：

1. **api**: Laravel アプリケーションを実行する PHP-FPM コンテナ
2. **nginx**: Web サーバーとして機能する Nginx コンテナ
3. **db**: データを永続化する MySQL コンテナ
4. **mail**: メール送信をテストするための Mailhog コンテナ
5. **storage**: ファイルストレージとして機能する MinIO コンテナ
6. **phpmyadmin**: データベース管理用の PHPMyAdmin コンテナ

## 前提条件

- Docker
- Docker Compose

## セットアップ手順

### 1. リポジトリのクローン

```bash
git clone <repository-url>
cd <repository-directory>
```

### 2. 環境変数の設定

`.env` ファイルが既に存在しますが、必要に応じて環境変数を変更できます。

### 3. Docker コンテナのビルドと起動

```bash
docker-compose up -d
```

### 4. Laravel 依存関係のインストール

```bash
docker-compose exec api composer install
```

### 5. アプリケーションキーの生成

```bash
docker-compose exec api php artisan key:generate
```

### 6. データベースのマイグレーション

```bash
docker-compose exec api php artisan migrate
```

### 7. ストレージディレクトリの権限設定

```bash
docker-compose exec api chmod -R 777 storage bootstrap/cache
```

## 使用方法

### アクセスポイント

- **Laravel アプリケーション**: http://localhost:80
- **PHPMyAdmin**: http://localhost:8080
- **Mailhog Web UI**: http://localhost:8025
- **MinIO Console**: http://localhost:9001

### データベース接続情報

- **ホスト**: db
- **ポート**: 3306
- **データベース名**: laravel
- **ユーザー名**: laravel_user
- **パスワード**: secret_password

### メール設定

Mailhog を使用してメール送信をテストできます。Laravel の `.env` ファイルには既に適切な設定が含まれています。

### オブジェクトストレージ

MinIO は S3 互換のオブジェクトストレージとして機能します。以下の情報で接続できます：

- **エンドポイント**: http://storage:9000
- **アクセスキー**: minio_user
- **シークレットキー**: minio_password
- **デフォルトバケット**: laravel-bucket

## コード品質ツール

このプロジェクトには PHP CS Fixer が組み込まれています。以下のコマンドでコードスタイルを修正できます：

```bash
docker-compose exec api composer cs
```

コードスタイルをチェックするだけの場合：

```bash
docker-compose exec api composer cs-check
```

## コンテナの停止

```bash
docker-compose down
```

データベースやMinIOのデータを削除せずにコンテナを停止します。

## コンテナとボリュームの完全削除

```bash
docker-compose down -v
```

これにより、コンテナとボリュームが完全に削除されます。

## トラブルシューティング

### コンテナの状態確認

```bash
docker-compose ps
```

### コンテナログの確認

```bash
docker-compose logs -f [サービス名]
```

例: `docker-compose logs -f api`

### PHP-FPM 設定

PHP-FPM の設定は docker-compose.yml の environment セクションで調整できます。

## ライセンス

MIT
