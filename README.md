# coachtech勤怠管理アプリ

## 環境構築
### リポジトリ取得
1. git clone git@github.com:iwmz-c/attendance-app.git
2. cd attendance-app

### Dockerビルド
1. docker-compose up -d --build

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
4. php artisan key:generate
5. php artisan migrate --seed

## 使用技術（実行環境）
- PHP 8.1.34
- Laravel 8.83.8
- MySQL 8.0.26 (MariaDB 11.8.3)
- Docker / Docker Compose
- Laravel Fortify

## ER図
<img src="/src/public/img/attendance_ER.png" width="800">

## ログイン情報
### 一般ユーザー
- メールアドレス：user@test.com
- パスワード：password

### 管理者ユーザー
- メールアドレス：admin@test.com
- パスワード：password

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
