# Mew
Mew = Markdown Easy Wiki system

Markdownで記事を記述可能な完結なWikiシステムを目指して開発中です。

[PHP Markdown Extra](https://github.com/michelf/php-markdown)によってMarkdownをHTML化しています。
そのため、文法は基本的にPHP Markdown Extraに準じます。

## 使い方
1. git clone https://github.com/ukatama/mew wiki
2. cd wiki
3. composer update
4. cp config/wiki.default.php config/wiki.php
5. HTTPアクセス可能にする
  * PHPが実行可能な場所ならこれで終わり
  * ローカル環境なら php -S localhost:80
  * FTPでアップロードも可？（未検証）
    * page,fileディレクトリとその中身を書き込み可能にすること

## 機能
* ページの編集
* ページの追加
* ページの削除
* ファイルのアップロード

## 未実装
* ドキュメント
* パスワード
* プラグイン

## ライセンス表示
[MITライセンス](https://github.com/ukatama/mew/blob/master/LICENSE.txt)

[PHP Markdown Extra](https://github.com/michelf/php-markdown/blob/lib/License.md)
