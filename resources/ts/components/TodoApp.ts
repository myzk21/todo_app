import { TodoService } from '../services/TodoService';
import { Todo } from '../classes/Todo';

const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content;

export class TodoApp {
    private addButton: HTMLElement;
    private todoCreateForm: HTMLFormElement;
    private todoTitleInput: HTMLInputElement;
    private todoDescriptionInput: HTMLInputElement;
    private percentage: HTMLInputElement;
    private priority: HTMLInputElement;
    private due: HTMLInputElement;


    constructor(
        addButtonId: string,
        todoCreateFormId: string,
        todoTitleInputId: string,
        todoDescriptionInputId: string,
        percentageId: string,
        priorityId: string,
        dueId: string
    ) {
        this.addButton = document.getElementById(addButtonId) as HTMLElement;
        this.todoCreateForm = document.getElementById(todoCreateFormId) as HTMLFormElement;
        this.todoTitleInput = document.getElementById(todoTitleInputId) as HTMLInputElement;
        this.todoDescriptionInput = document.getElementById(todoDescriptionInputId) as HTMLInputElement;
        this.percentage = document.getElementById(percentageId) as HTMLInputElement;
        this.priority = document.getElementById(priorityId) as HTMLInputElement;
        this.due = document.getElementById(dueId) as HTMLInputElement;

        this.addButton.addEventListener('click', (event) => {//clickされたらaddTodoを実行
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
            const newTodo: Todo = await TodoService.addTodo(formData);//サービスクラスを呼び出してTodoを追加
            const errorContainer = document.getElementById('errorContainer') as HTMLElement;
            this.renderTodo(newTodo);
            this.todoTitleInput.value = ''; //入力フィールドをクリア
            this.todoDescriptionInput.value = '';
            this.percentage.value = '';
            this.priority.value = '';
            this.due.value = '';
            errorContainer.innerHTML = ''; //既存のエラーをクリア
            console.log('Todoの追加に成功しました');
        } catch (error) {
            console.error('Todoの追加に失敗しました');
        }
    }

    private renderTodo(newTodo: Todo) {
        console.log(newTodo);
        const todoTableBody = document.getElementById('todo-table') as HTMLElement;
        const newRow = document.createElement('tr');
        newRow.className = 'border border-gray-100';
        newRow.innerHTML = `
            <td class="px-4 py-3 text-center">
                <input type="checkbox" class="form-checkbox">
            </td>
            <td class="px-4 py-3 text-center">${newTodo.title}</td>
            <td class="px-4 py-3 text-center">${newTodo.description ?? '--'}</td>
            <td class="px-4 py-3 text-center">${newTodo.progress_rate ?? '--'}%</td>
            <td class="px-4 py-3 text-center">${newTodo.priority ?? '--'}</td>
            <td class="px-4 py-3 text-center">${newTodo.due ?? '--'}</td>
            <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                <a href="#">詳細</a>
            </td>
            <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                        </svg>
                </a>
            </td>
        `;
        todoTableBody.appendChild(newRow);//追加
    }
}
