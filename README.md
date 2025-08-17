# お問い合わせフォーム

## 環境構築

Dockerビルド

1.git clone git@github.com:kurage-ginga/attendance_App.git

2.docker-compose up -d --build

laravel環境構築

1.docker-compose exec php bash

2.composer install

3..env.exampleファイルから.envを作成し、環境変数を変更

4.Mailhogを設定

5.php artisan key:generate

6.php artisan migrate

7.php artisan db:seed

## 使用技術

・PHP　７.４.９

・Laravel　８.８３.２９

・MySQL　１５.１

## URL

・開発環境：http://lpcalhost/

・phpMyAdmin：http://localhost:8080
