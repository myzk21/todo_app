<使用技術><br>
・Laravel11<br>
・Tailwind Css<br>

<パッケージ><br>
・Laravel Breeze<br>

<レイアウトルール＞<br>
・背景色　#f9fafb<br>
・header背景色　#8b8a8e<br>
・padding　px-6 py-3に統一<br>

<フロントエンド構造><br>
resources/<br>
  └── ts/<br>
      ├── classes/            OOPのクラスを格納<br>
      │   └── Todo.ts           Todoモデルクラス<br>
      ├── services/           サービスクラス（API通信などのロジック）<br>
      │   └── TodoService.ts    Todoに関連するAjax（API）通信処理<br>
      ├── components/         UIやアプリケーションのロジック<br>
      │   └── TodoApp.ts        Todoアプリのコントローラー的役割<br>
      └── app.ts              アプリ全体のエントリーポイント<br>
