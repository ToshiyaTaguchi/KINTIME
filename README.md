# flea-market(Flea-market Application)

Laravel + Docker 環境で構築された勤怠管理アプリケーションです。

---

## 環境構築手順

### 1. リポジトリをクローン

1. git@github.com:ToshiyaTaguchi/flea-market-ver.03.git
2. cd flea-market-ver.03

### 2. 環境変数の設定(docker-compose.yml 用)

このアプリケーションでは、シークレット情報は .env ファイルに記載します。
プロジェクト直下に .env.docker を作り、以下の項目を追加してください。

## MySQL

```env
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_DATABASE=laravel_db
MYSQL_USER=laravel_user
MYSQL_PASSWORD=your_password
```

### 3. Docker の起動

1.  Docker Desktop を起動後、以下を実行

```bash
     docker-compose --env-file .env.docker up -d --build
```

> _Mac の M1・M2 チップの PC の場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
> エラーが発生する場合は、docker-compose.yml ファイルの「mysql」内に「platform」の項目を追加で記載してください_

```bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

### 4. Laravel 環境構築

1. コンテナに入る

```bash
docker-compose exec php bash
```

2. .env ファイルの作成

```bash
cp .env.example .env
```

##　環境変数(シークレット情報)
このアプリケーションでは、シークレット情報はファイル.env に記載されます。
Git リポジトリには.env 含まれていないため、以下の項目を.env 追加してください。

## Laravel

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

## Mailhog

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Laravelアプリ"
```

## Stripe(開発用)

```env
STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key
```

### 5.Laravel 環境構築・初期化

1. Laravel のキャッシュ・ログディレクトリを作成

```bash
mkdir -p storage/framework/{cache,views} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

```

2. アプリケーションキーの作成

```bash
php artisan key:generate
```

3. マイグレーションの実行

```bash
php artisan migrate
```

4. シーディングの実行

```bash
php artisan db:seed
```

5. シンボリックリンクの作成(画像表示用)

```bash
php artisan storage:link
```

## ストレージ管理（Git 管理対象・非対象）

1. Git 管理対象

```swift
storage/app/public/images
storage/app/public/products
```

2. Git 非対象

```swift
storage/app/public/profile_images
```

## PHPUnit テスト実行時の注意

1. .env.testing ファイルを作成し、テスト用データベースを設定

```bash
cp .env .env.testing
```

2. .env.testing に以下を追加

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=demo_test
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

3. テスト用データベースを作成

```bash
docker-compose exec mysql bash
mysql -u root -p
CREATE DATABASE demo_test;
```

4. PHPUnit 実行

```bash
docker-compose exec php bash
php artisan test
```

## 使用技術(実行環境)

- PHP8.4.2
- Laravel8.83.29
- MySQL8.0.42
- Docker
- Composer 2.2.6
- phpMyAdmin
- Mailhog（メール開発用）
- Stripe（決済機能）
- PHPUnit（単体テスト実装済み）

## ER 図

![alt](erd.png)

## URL

- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
- Mailhog: http://localhost:8025/
