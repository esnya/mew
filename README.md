# Mew
[![Build Status](https://img.shields.io/travis/ukatama/mew/master.svg?style=flat-square)](https://travis-ci.org/ukatama/mew)
[![Coverage Status](https://img.shields.io/coveralls/ukatama/mew.svg?style=flat-square)](https://coveralls.io/github/ukatama/mew)
[![VersionEye](https://www.versioneye.com/user/projects/56d63e27d71695003886b7e7/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56d63e27d71695003886b7e7)

Markdown Easy Wiki.

Pages are written by Markdown([PHP Markdown Extra](https://github.com/michelf/php-markdown)).

## Usage
1. Clone this repository.
   ```bash
   $ git clone https://github.com/ukatama/mew
   $ cd mew
   ```

2. Install dependencies by composer.
   ```bash
   $ composer Install
   ```

3. Copy default config.
   ```bash
   $ cp config/wiki.default.php config/wiki.php
   ```

4. Allow HTTP access and PHP execution.
    * Copy into document root of Apache.
    * Run PHP built in web server.

      ```bash
      $ php -S localhosr:80
      ```

## Features
* Create/Read/Update/Remove pages
* Upload attached files
* Export (download) to Markdown
* Import (upload) from Markdown

## ToDo
* Documentations
* Password
* Plugin system

## Bugs, Issues, PR
Create new issue or pull request.

## License
* MIT License
