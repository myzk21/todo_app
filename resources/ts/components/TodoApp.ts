import { TodoService } from '../services/TodoService';
import { Todo } from '../classes/Todo';

const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content;

export class TodoApp {
    private addButton: HTMLElement;
    private todoCreateForm: HTMLFormElement;

    constructor(addButtonId: string, inputFieldId: string) {
        this.addButton = document.getElementById(addButtonId) as HTMLElement;
        this.todoCreateForm = document.getElementById(inputFieldId) as HTMLFormElement;

        //clickされたらaddTodoを実行
        this.addButton.addEventListener('click', (event) => {
            event.preventDefault();
            this.addTodo();
        });
    }

    // Todoの追加処理
    private async addTodo() {
        const formData = new FormData(this.todoCreateForm);
        formData.append('_token', csrfToken);
        if (!formData) return;

        try {
            // サービスクラスを呼び出してTodoを追加
            const newTodo: Todo = await TodoService.addTodo(formData);
            // this.renderTodo(newTodo);
            this.todoCreateForm.value = '';  // 入力フィールドをクリア
            console.log('Todoの追加に成功しました');
        } catch (error) {
            console.error('Todoの追加に失敗しました');
        }
    }

    //Todoリストに新しいTodoを描画
    // private renderTodo(todo: Todo) {
    //     const listItem = document.createElement('li');
    //     listItem.textContent = todo.title;
    //     // this.todoList.appendChild(listItem);
    // }
}
