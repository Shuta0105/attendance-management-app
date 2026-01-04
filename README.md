# アプリケーション名

attendance-management-app

## 環境構築

### Docker ビルド

1. git clone git@github.com:Shuta0105/attendance-management-app.git
2. docker-compose up -d --build

### Laravel 環境構築

1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
4. cp .env.example .env.testing
5. 以下、「環境変数の変更」完了後
6. php artisan key:generate
7. php artisan key:generate --env=testing
8. php artisan migrate --seed

### 環境変数の変更

#### .env

1. 

#### .env.testing