<使用技術><br>
・Laravel11<br>
・Tailwind css<br>
・Google API Client<br>
・Laravel Socialite<br>
・Laravel Breeze<br>

<データベース設計><br>
[ER図]<br>
![スクリーンショット 2024-11-06 232638](https://github.com/user-attachments/assets/630c24ed-6501-4bd4-974b-eb1fede42743)


<レイアウトルール＞<br>
・背景色　#f9fafb<br>
・header背景色　#8b8a8e<br>
・padding　px-6 py-3に統一<br>

<フロントエンド構造><br>
resources/<br>
  └── ts/<br>
       └── todo (pdca)/<br>
              ├── classes/            クラスを格納<br>
              │   └── Todo.ts           Todoモデルクラス<br>
              ├── services/           サービスクラス（API通信などのロジック）<br>
              │   └── TodoService.ts    Todoに関連するAjax（API）通信処理<br>
              ├── components/         UIやアプリケーションのロジック<br>
              │   └── TodoApp.ts        Todoアプリのコントローラー的役割<br>
              └── app.ts              アプリ全体のエントリーポイント<br>
              
