# System Patterns *Optional*

このファイルはプロジェクトで使用される繰り返しパターンと標準を文書化します。
オプションですが、プロジェクトの進化に合わせて更新することをお勧めします。
2025-05-04 08:19:53 - 更新ログ。

*

## コーディングパターン

* Dockerfileは各サービスごとに最小限の構成で作成
* 環境変数は.envファイルで一元管理
* コンテナ名は役割を明確に示す命名規則を使用
* Laravelのコーディング規約に従ったAPI実装

## アーキテクチャパターン

* マイクロサービスアーキテクチャの原則に基づいたコンテナ分離
* APIゲートウェイパターン（Laravelがフロントエンドとバックエンドサービスの中間に位置）
* 環境変数によるサービス間の疎結合
* ボリュームマウントによるデータ永続化パターン

## テストパターン

* コンテナの正常起動テスト
* サービス間接続テスト（特にAPI-DB間）
* Laravelマイグレーションによるデータベーススキーマ検証
* Mailhogを使用したメール送信テスト
* MinIOへのファイルアップロード/ダウンロードテスト