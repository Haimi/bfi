BFI
===

#BFI Fast Integration PHP Framework

Why another PHP Framework? 
Because we need to go faster!

Targeting pages for people with cognitive disabilities, the attention span of there people is rather short.

So I decided to make a Framework to only fit the main requirements like
* Creating an MVC
* Database abstraction
* Session & State management
* Form abstraction
* Caching
* Authentication & Authorization
* Emails
* SQL Query building

I will add an ready-to-start example application soon.

I have tested this only on nginx and you can use the following full config for this
```
# Rewrite URLs without "www"
server {
        listen 80;
        server_name mypage.de;
        return 301 $scheme://www.mypage.de$request_uri;
}

server {
        listen 80;
        server_name www.mypage.de;

        root /path/to/mypage/public;
        index index.php index.html;

        error_page 404 /error/404.html;
        error_log /var/log/nginx/mypage.err;

        location / {
                try_files $uri $uri/ /index.php$is_args$args;
        }

        location ~ [^/]\.php(/|$) {
                fastcgi_split_path_info ^(.+?\.php)(/.*)$;
                if (!-f $document_root$fastcgi_script_name) {
                        return 404;
                }

                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                include fastcgi_params;
        }

        location ~ /\.ht {
                deny all;
        }
}
```

## Third-Party tools
### Lessphp
I am Using a PHP 5.4 namespace-flavoured fork of

lessphp v0.4.0

http://leafo.net/lessphp

LESS css compiler, adapted from http://lesscss.org

Copyright 2013, Leaf Corcoran <leafot@gmail.com>

Licensed under MIT or GPLv3

### JsMinPlus
I am using JsMinPlus 1.4

http://files.tweakers.net/jsminplus/jsminplus.zip

By Tino Zijdel <crisp@tweakers.net>

Licensed under MPL 1.1/GPL 2.0/LGPL 2.1
