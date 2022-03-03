## 피엘라벤 D2C(S3) 프로젝트

1. 솔루션 변경
2. 매장관리 

- 기간 : 9월 1일 ~ 12월 31일

https://docs.google.com/spreadsheets/d/1VF2pbD4mxwCoNIeIBeRnIy_Y-5BgUO-8ZqpCIeF1N7c/edit#gid=1242931089

## 개발도구 및 Framework

- 개발툴 : phpstorm 또는 vscode
- 서버 Framework - [라라벨](https://laravel.com/).
- Client Framework - [Bootstrap]() 과 [Vuejs](https://vuejs.org).
- Ag-grid
- chartjs

## 설치

1. https://github.com/steve92son/fjallraven
2. git 과 composer 설치
3. 프로그램 설치

```
C:\Users\steve>d:
D:\>cd proj ( 원하는 디렉토리 )
D:\proj>git clone https://github.com/steve92son/fjallraven.git
Cloning into 'handle'...
remote: Enumerating objects: 2061, done.
remote: Counting objects: 100% (2061/2061), done.
remote: Compressing objects: 100% (1929/1929), done.
remote: Total 2061 (delta 114), reused 2053 (delta 107), pack-reused 0 eceiving objects:  98% (2020/2061), 1.72 MiB | 1.Receiving objects: 100% (2061/2061), 1.72 MiB | 1.72 MiB/s
 100% (2061/2061), 5.09 MiB | 3.76 MiB/s, done.
Resolving deltas: 100% (114/114), done.

D:\proj>cd fjallraven
D:\proj\fjallraven>composer update
~~
D:\proj\fjallraven>ren .env.example .env
```

.env 파일에 DB 정보를 아래와 같이 수정
   
   ```
   DB_CONNECTION=mysql
   DB_HOST=1.201.136.105
   DB_PORT=3306
   DB_DATABASE=fjallraven
   DB_USERNAME=*****
   DB_PASSWORD=*****
   ```

실행
```
D:\projects\netpx_v4>php artisan storage:link
The [D:\projects\netpx_v4\public\images] link has been connected to [D:\projects\netpx_v4\storage\app/public/images].
The system cannot find the file specified.
The [D:\projects\netpx_v4\public\data] link has been connected to [D:\projects\netpx_v4\storage\app/public/data].
The links have been created.

```

웹서버 실행 후 브라우저에서 http://127.0.0.1:8000/head 확인
```
D:\proj\fjallraven>php artisan serve
Starting Laravel development server: http://127.0.0.1:8000
[Tue Sep 15 12:02:13 2020] PHP 7.4.10 Development Server (http://127.0.0.1:8000) started

```

## 개발

- 상모소프트웨어
- 가치브라더

## 파트너

- 피엘라벤
- 넷피엑스
- 일월매트

