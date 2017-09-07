```
composer create-project laravel/laravel site
```

```
cd site
composer require shopex/luban-site
```

生成vendor提供的文件
```
php artisan vendor:publish
php artisan make:auth
```

config/app.config 下增加

```        
//Providers...
        Shopex\LubanAdmin\Providers\LubanSiteProvider::class,


//Facade...      
        'Site' => Shopex\LubanAdmin\Facades\Site::class,        
```

resources/assets/js/app.js 下增加
```
    require('../vendor/site/js/site.js')
```

resources/assets/sass/app.scss 下增加
```
    @import "../vendor/site/sass/app";
```

routes/web.php
```
Site::routes();
```


编译js/css,  运行程序.
```
npm run dev
php artisan migrate
php artisan serve
```