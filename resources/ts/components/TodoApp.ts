import { TodoService } from '../services/TodoService';
import { Todo } from '../classes/Todo';
import { UpdateTodo } from '../classes/Todo';
// import { ShowDetailTodoService } from '../services/ShowDetailTodoService';

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
        this.showTodo();
        //editTodoも作成（保存処理）
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

    private renderTodo(newTodo: Todo) {//ここでもしすでにidがあったらアペンドで追加など新規と更新で処理を分ける
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
                <a href="#" class="showBtn" todo-id="${newTodo.id}">詳細</a>
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

    private async showTodo() {
        const showBtns = document.querySelectorAll('.showBtn') as NodeListOf<HTMLElement>;

        showBtns.forEach((showBtn) => {
            const todoId = showBtn.getAttribute('todo-id');
            showBtn.addEventListener('click', async (event: MouseEvent) => {
                //詳細を表示
                const showTodo: Todo = await TodoService.showTodo(todoId);
                this.createDetailModal(showTodo);
            });
        });
    }

    // Todoの更新処理
    private async updateTodo() {
        const todoUpdateForm = document.getElementById('todo_update_form') as HTMLFormElement;
        const updateFormData = new FormData(todoUpdateForm);
        updateFormData.append('_token', csrfToken);
        if (!updateFormData) return;
        try {
            const updateTodo: UpdateTodo = await TodoService.updateTodo(updateFormData);//サービスクラスを呼び出してTodoを追加
            //ここにダイアログ表示などの処理
            console.log('Todoの更新に成功しました');
        } catch (error) {
            console.error('Todoの更新に失敗しました');
        }
    }

    private createDetailModal(showTodo: Todo) {
        const todoDetailModal = document.getElementById('todo_detail_modal') as HTMLElement;

        const modalHTML = `
            <div class="w-full h-full z-50 fixed insert-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="px-6 pb-5 pt-3 shadow-sm w-3/5 rounded bg-white" id="">
                    <div class="pointer-events-none flex justify-end">
                        <p class="text-4xl cursor-pointer hover:opacity-60 -mb-2 pointer-events-auto inline-block" id="close-show-todo">×</p>
                    </div>
                    <div id="updateErrorContainer"></div>
                    <form id="todo_update_form">
                        <input type="hidden" name="id" value="${showTodo.id}">
                        <label for="todo_title_input" class="block pb-1">タイトル</label>
                        <input type="text" class="border border-gray-500 rounded h-8 mb-2 placeholder:text-sm placeholder:text-gray-300 w-full" placeholder="TODOを入力" name="updateTitle" value="${showTodo.title}" id="todo_title_input">
                        <label for="todo_description_input" class="block mb-1">内容</label>
                        <textarea placeholder="内容" class="mb-1 placeholder:text-sm placeholder:text-gray-300 rounded w-full" name="updateDescription" id="todo_description_input">${showTodo.description ?? ''}</textarea>
                        <div class="flex flex-wrap pt-1">
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="percentage">進捗率</label>
                                <select id="percentage" class="rounded mr-4" name="updateProgress_rate">
                                    <option value="">--</option>
                                    ${Array.from({ length: 11 }, (_, i) => {
                                        const value = i * 10; // 0, 10, 20, ..., 100
                                        const showRateNum = Number(showTodo.progress_rate);
                                        const isSelected = showRateNum == value ? 'selected' : '';
                                        return `<option value="${value}" ${isSelected}>${value}%</option>`;
                                    }).join('')}
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="priority">優先度</label>
                                <select id="priority" class="rounded mr-4" name="updatePriority">
                                    <option value="">--</option>
                                    <option value="high" ${showTodo.priority == 'high' ? 'selected' : ''}>高</option>
                                    <option value="middle" ${showTodo.priority == 'middle' ? 'selected' : ''}>中</option>
                                    <option value="low" ${showTodo.priority == 'low' ? 'selected' : ''}>低</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="due">期日</label>
                                <input type="date" name="updateDue" id="due" class="rounded" value="${showTodo.due ?? ""}">
                            </div>
                        </div>
                        <div class="w-full flex mt-1">
                            <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded ml-auto hover:bg-opacity-80 select-none flex justify-end" id="todo_update_btn">保存</button>
                            <p class="hover:underline cursor-pointer px-4 py-2 text-gray-400 text-sm">削除</p>
                        </div>
                    </form>
                </div>
            </div>
            `;

        todoDetailModal.innerHTML = modalHTML;//詳細を表示
        const closeButton = todoDetailModal.querySelector('#close-show-todo') as HTMLElement;
        closeButton.addEventListener('click', () => { //バツボタンがクリックされたときにモーダルを閉じる
            todoDetailModal.innerHTML = '';
        });

        const addButton = document.getElementById('todo_update_btn') as HTMLElement;
        addButton.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            this.updateTodo();//TODOの更新
        });
    }

}
