# Slim Tour CMS
CMS for Tour Company built with SLIM Microframework.

I've only just stripped this back to Whitelabel so its not tested. It needs some TLC.

The database isn't include please contact me on montane@protonmail.com.


## Reqs

- Mysql 5+ 
- PHP 5.6+
- npm
- composer

### Deployment

- Ruby
- Capistrano

## Install

- clone this repo
- cd into directory

```bash
composer install
```

- import the database file

```bash
mysql -uroot slimtest < /Users/User1/vprojects/Slim-Tour-CMS/migrations/slimtest.sql
```

## Development

```bash
php -S localhost:8888 -t public
```

## References

- Based partly on https://github.com/slimphp/Slim-Skeleton/
- [Introduction to SlimPHP by Michael Heap](https://michaelheap.com/series/slimphp-introduction/)
- [IBM Slim Tutorial](https://www.ibm.com/developerworks/library/x-slim-rest/index.html)
- https://github.com/damianopetrungaro/slim-boilerplate
- https://github.com/bradtraversy/slimapp
