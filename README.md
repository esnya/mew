# Mew [![Build Status](https://travis-ci.org/ukatama/mew.svg)](https://travis-ci.org/ukatama/mew)
Mew = Markdown Easy Wiki system

Markdownで記事を記述可能な簡単なWikiシステムを目指して開発中です。

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
* Markdownとしてダウンロード
* Markdownファイルのインポート（アップロード）

## 未実装
* ドキュメント
* パスワード
* プラグイン

## バグ・要望・プルリク
[githubのissues](https://github.com/ukatama/mew/issues)に

## ライセンス表示
[MITライセンス](https://github.com/ukatama/mew/blob/master/LICENSE.txt)

[PHP Markdown Extra](https://github.com/michelf/php-markdown/blob/lib/License.md)

## v0.1について
v0.1とv0.2以降のページデータは互換性がありません。
以下の方法で移行することができます。

* メニューバーの"zip"からページを保存
* zipファイルを解凍
* Mewを更新 (例：git pull)
* メニューバーの"uplaod"から解答した.mdファイルをアップロード

"v0.2" or later versions are incompatible with "v0.1".
Update way from v0.1 to other versions is shown in the following.

* Save all pages from "zip"
* Decompless downloaded zip file
* Update Mew (e.g. git pull)
* Uplaod decomplessed .md files using "uplaod"
