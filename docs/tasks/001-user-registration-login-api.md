# ユーザー登録とログインAPIの実装

## 概要
このタスクでは、仮登録と本登録の概念を持つユーザー登録APIと、仮登録状態でもログイン可能なログインAPIを実装しました。

## 実装内容

### 1. ユーザー登録API
- 仮登録機能
  - エンドポイント: `POST /api/auth/register`
  - 機能: ユーザー情報を受け取り、データベースに保存し、確認メールを送信
  - 検証: メールアドレスの一意性、パスワードの確認など

- 本登録機能
  - エンドポイント: `GET /api/auth/verify/{token}`
  - 機能: メールで送信されたトークンを検証し、ユーザーのメールアドレスを確認済みとしてマーク
  - 検証: トークンの有効性

### 2. ログインAPI
- エンドポイント: `POST /api/auth/login`
- 機能: メールアドレスとパスワードを検証し、認証トークンを発行
- 特徴: 仮登録状態（メール未確認）でもログイン可能
- レスポンス: ユーザー情報、認証トークン、メール確認状態（`is_verified`）を返却

### 3. ログアウトAPI
- エンドポイント: `POST /api/auth/logout`
- 機能: 認証トークンを無効化
- 認証: Sanctumによる認証が必要

## 技術的な実装詳細

### データベース
- `users`テーブルに`verification_token`フィールドを追加
- `email_verified_at`フィールドを使用してメール確認状態を管理

### 認証
- Laravel Sanctumを使用したトークンベースの認証
- APIルートにSanctumミドルウェアを適用

### メール送信
- SMTPを使用してMailhogにメールを送信
- メール設定:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=mail
  MAIL_PORT=1025
  ```
- メールテンプレート: `resources/views/emails/verification.blade.php`

## テスト
- 自動テスト: `tests/Feature/Auth/RegistrationTest.php`と`tests/Feature/Auth/LoginTest.php`
- テスト実行コマンド: `docker compose exec api php artisan test --filter=Auth`

## 関連ファイル
- コントローラー:
  - `app/Http/Controllers/Auth/RegisterController.php`
  - `app/Http/Controllers/Auth/LoginController.php`
- モデル: `app/Models/User.php`
- ルート: `routes/api.php`
- メールテンプレート: `resources/views/emails/verification.blade.php`
- 設定: `config/mail.php`

## 今後の改善点
- パスワードリセット機能の追加
- トークンの有効期限設定
- メール送信の非同期処理化
- ユーザープロフィール管理機能の追加
