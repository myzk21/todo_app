import { TodoService } from '../services/TodoService';
import { Todo } from '../classes/Todo';
// import { ok } from 'assert';
// import { UpdateTodo } from '../classes/Todo';

const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content;

export class TodoApp {
    private addButton: HTMLElement;
    private todoCreateForm: HTMLFormElement;
    private todoTitleInput: HTMLInputElement;
    private todoDescriptionInput: HTMLInputElement;
    private percentage: HTMLInputElement;
    private priority: HTMLInputElement;
    private due: HTMLInputElement;
    private todoDetailModal = document.getElementById('todo_detail_modal') as HTMLElement;

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
        this.changeTodoStatus();
        this.showTodo();
        this.deleteTodo();
    }

    //todo完了処理
    private async changeTodoStatus() {
        const todoListContainer = document.getElementById('todo_list') as HTMLElement;
        todoListContainer.addEventListener('click', async (event) => {
            const target = event.target as HTMLElement;
            if (target.classList.contains('todo-checkbox')) {
                const  todoContainer = target.closest('.todo-container') as HTMLTableRowElement | HTMLDivElement;
                const title = todoContainer.querySelector('.title') as HTMLInputElement;
                const description = todoContainer.querySelector('.description') as HTMLTextAreaElement;
                const progress = todoContainer.querySelector('.progress_rate') as HTMLSelectElement;
                const priority = todoContainer.querySelector('.priority') as HTMLSelectElement;
                const due = todoContainer.querySelector('.due') as HTMLInputElement;

                const todoId = todoContainer.id;

                if (todoId) {
                    const newTodoStatus: Todo = await TodoService.changeTodoStatus(todoId);
                    if(newTodoStatus.when_completed) {
                        todoContainer.classList.add('opacity-25');
                        title.classList.add('line-through');
                        description.classList.add('line-through');
                        progress.classList.add('line-through');
                        priority.classList.add('line-through');
                        due.classList.add('line-through');
                    } else {
                        todoContainer.classList.remove('opacity-25');
                        title.classList.remove('line-through');
                        description.classList.remove('line-through');
                        progress.classList.remove('line-through');
                        priority.classList.remove('line-through');
                        due.classList.remove('line-through');
                    }
                    //ここでtodo.when_completedがnullならcompletedクラスを削除ー＞そうでなけえればcompletedをつける
                } else {
                    console.error('Todo IDが見つかりません。');
                }
            }
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

    private renderTodo(todo: Todo) {
        let existingTodoItem = document.getElementById(`${todo.id}`) as HTMLTableRowElement;//変更したTodoを取得
        if(existingTodoItem) {
            if (todo.deleted_at) {
                //DOMから削除する
                existingTodoItem.remove();
                console.log(`Todo ID ${todo.id} が削除されました。`);
            } else {
                //Todoを更新
                this.updateTodoItem(existingTodoItem, todo);
            }
        } else {
            //新規登録(viewに追加)
            this.createTodoItem(todo);
        }
    }

    private async showTodo() {
        const todoListContainer = document.getElementById('todo_list') as HTMLElement;
            todoListContainer.addEventListener('click', async (event) => {
                const target = event.target as HTMLElement;
                //showBtnクラスを持つ要素がクリックされた場合
                if (target.classList.contains('showBtn')) {
                    const showBtn = target as HTMLAnchorElement; //クリックされた<a>要素を取得
                    const todoId = showBtn.getAttribute('todo-id'); //todo-id属性を取得

                    if (todoId) {
                        const showTodo: Todo = await TodoService.showTodo(todoId);
                        this.createDetailModal(showTodo);
                    } else {
                        console.error('Todo IDが見つかりません。');
                    }
                }
            });
    }

    // Todoの更新処理
    private async updateTodo() {
        const todoUpdateForm = document.getElementById('todo_update_form') as HTMLFormElement;
        const updateFormData = new FormData(todoUpdateForm);
        updateFormData.append('_token', csrfToken);
        if (!updateFormData) return;
        try {
            const updateTodo: Todo = await TodoService.updateTodo(updateFormData);//サービスクラスを呼び出してTodoを追加
            this.renderTodo(updateTodo);
            this.todoDetailModal.innerHTML = '';
            console.log('Todoの更新に成功しました');
        } catch (error) {
            console.error('Todoの更新に失敗しました');
            console.error('Todoの更新に失敗しました。エラーの詳細: ', {
                message: error instanceof Error ? error.message : '未知のエラー',
                stack: error instanceof Error ? error.stack : 'スタックトレースなし',
            });
        }
    }

    //TODO削除
    private async deleteTodo() {
        const todoListContainer = document.getElementById('todo_list') as HTMLElement;
            todoListContainer.addEventListener('click', async (event) => {
                const target = event.target as HTMLElement;
                //todo_delete_btnクラスを持つ要素がクリックされた場合
                if (target.closest('.todo_delete_btn')) {
                    if(!confirm('本当に削除しますか？')) return;
                    try{
                        const deleteBtn = target.closest('.todo_delete_btn') as HTMLAnchorElement; //クリックされた<a>要素を取得
                        const todoId = deleteBtn.getAttribute('todo-id'); //todo-id属性を取得
                        if (todoId) {
                            const deleteTodo: Todo = await TodoService.deleteTodo(todoId);
                            this.renderTodo(deleteTodo);
                        } else {
                            console.error('Todo IDが見つかりません。');
                        }
                    } catch(error) {
                        console.error('Todoの削除に失敗しました');
                    }
                }
            });
    }

    private createDetailModal(showTodo: Todo) {//todo作成モーダル
        const modalHTML = `
            <div class="w-full h-full z-50 fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
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
                                    ${Array.from({ length: 7 }, (_, i) => {
                                        const value = i * 10; // 0, 10, 20, ..., 100
                                        const showRateNum = showTodo.progress_rate !== null ? Number(showTodo.progress_rate) : null;
                                        const isSelected = showRateNum === value ? 'selected' : '';
                                        return `<option value="${value}" ${isSelected}>${value}%</option>`;
                                    }).join('')}
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="priority">優先度</label>
                                <select id="priority" class="rounded mr-4" name="updatePriority">
                                    <option value="">--</option>
                                    <option value="高" ${showTodo.priority == '高' ? 'selected' : ''}>高</option>
                                    <option value="中" ${showTodo.priority == '中' ? 'selected' : ''}>中</option>
                                    <option value="低" ${showTodo.priority == '低' ? 'selected' : ''}>低</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="due">期日</label>
                                <input type="date" name="updateDue" id="due" class="rounded" value="${showTodo.due ?? ""}">
                            </div>
                        </div>
                        <div class="w-full flex mt-1">
                            <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded ml-auto hover:bg-opacity-80 select-none flex justify-end" id="todo_update_btn">保存</button>
                            <p class="hover:underline cursor-pointer px-4 py-2 text-gray-400 text-sm todo_delete_btn" todo-id="${showTodo.id}"><a>削除</a></p>
                        </div>
                    </form>
                </div>
            </div>
            `;

        this.todoDetailModal.innerHTML = modalHTML;//詳細を表示
        const closeButton = this.todoDetailModal.querySelector('#close-show-todo') as HTMLElement;
        closeButton.addEventListener('click', () => { //バツボタンがクリックされたときにモーダルを閉じる
            this.todoDetailModal.innerHTML = '';
        });

        const addButton = document.getElementById('todo_update_btn') as HTMLElement;
        addButton.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            this.updateTodo();//TODOの更新
        });
        //詳細画面での削除
        const deleteButton = document.querySelector('.todo_delete_btn') as HTMLElement;
        deleteButton.addEventListener('click', async (event: MouseEvent) => {
            // this.deleteTodo();
            if(!confirm('本当に削除しますか？')) return;
            try{
                const todoId = deleteButton.getAttribute('todo-id'); //todo-id属性を取得
                if (todoId) {
                    const deleteTodo: Todo = await TodoService.deleteTodo(todoId);
                    this.renderTodo(deleteTodo);
                    //モーダルをとじる
                    if(this.todoDetailModal)this.todoDetailModal.innerHTML = '';
                } else {
                    console.error('Todo IDが見つかりません。');
                }
            } catch(error) {
                console.error('Todoの削除に失敗しました');
            }
        });
    }

    private isTodaysTodo(existingTodoItem: HTMLTableRowElement | HTMLDivElement) {//変更するTODOの場所を特定(本日か本日じゃないか)
        const parent = existingTodoItem.parentElement;
        if (parent && parent.id === 'todo-table') {
            return true;
        }
        return false;
    }

    private createNotTodayTodoItem(todo: Todo, dueDate: Date) {
        const notTodayTodosList = document.getElementById('not-today-todos-list') as HTMLElement;
        // 新しいTODO要素を作成
        const todoItem = document.createElement('div');
        todoItem.className = `bg-white shadow-sm rounded-lg px-6 py-4 max-w-md mx-auto my-3 todo-container ${todo.when_completed ? 'opacity-25' : ''}`;
        todoItem.id = String(todo.id);

        // TODOの内容を設定
        todoItem.innerHTML = `
            <div class="w-full flex mb-2">
                <label class="inline-flex items-center cursor-pointer -ml-2">
                    <input type="checkbox" class="hidden peer todo-checkbox">
                    <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-[#8b8a8e] ${todo.when_completed ? 'bg-[#8b8a8e]' : ''} relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </label>
                <a href="#" class="showBtn ml-auto cursor-pointer text-sm hover:underline select-none mr-3" todo-id="${todo.id}">詳細</a>
                <a href="#" class="todo_delete_btn hover:underline text-center" todo-id="${todo.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-bold">タイトル</p>
                <p class="text-gray-900 ml-2 title ${todo.when_completed ? 'line-through' : ''}">${todo.title.slice(0, 7) + (todo.title.length > 7 ? '...' : '')}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-bold">内容</p>
                <p class="text-gray-900 ml-2 description ${todo.when_completed ? 'line-through' : ''}">${(todo.description ? todo.description.slice(0, 7) + (todo.description.length > 7 ? '...' : '') : '--')}</p>
            </div>
            <div class="flex justify-between">
                <div class="">
                    <p class="text-gray-700 font-bold">進捗率</p>
                    <p class="text-gray-900 ml-2 progress_rate ${todo.when_completed ? 'line-through' : ''}">${todo.progress_rate ?? '--'}%</p>
                </div>
                <div class="">
                    <p class="text-gray-700 font-bold">優先度</p>
                    <p class="text-gray-900 ml-2 priority ${todo.when_completed ? 'line-through' : ''}">${todo.priority ?? '--'}</p>
                </div>
                <div class="">
                    <p class="text-gray-700 font-bold">期日</p>
                    <p class="text-gray-900 ml-2 due ${todo.when_completed ? 'line-through' : ''}">${todo.due}</p>
                </div>
            </div>
        `;
        //サイドに期日順に挿入
        const todos = Array.from(notTodayTodosList.children);
        let inserted = false;

        todos.forEach((existingTodo) => {
            const dueText = existingTodo.querySelector('.due')?.textContent;
            const existingDue = dueText ? new Date(dueText) : null;
            if(!existingDue) {//サイドに元々なかったらそのまま追加
                notTodayTodosList.appendChild(todoItem);
            } else if (dueDate < existingDue && !inserted) {//insertedを使用することでもしdueDateよりも後の既存のTODOが複数あった場合にループで何回も追加されないようにする→一度追加したらinsertedがtrueになるから
                // console.log(existingTodo);
                notTodayTodosList.insertBefore(todoItem, existingTodo);
                inserted = true;
            }
        });
        // もし適切な場所がなければ最後に追加
        if (!inserted) {
            const firstMessage = document.getElementById('first-message');
            if(firstMessage) firstMessage.remove();//初期メッセージを削除
            notTodayTodosList.appendChild(todoItem);//一番最初のタスク
        }
    }


    private updateTodoItem(existingTodoItem: HTMLTableRowElement | HTMLDivElement, todo: Todo) {//todoのレイアウトを編集
        const title = existingTodoItem.querySelector('.title') as HTMLInputElement;
        const description = existingTodoItem.querySelector('.description') as HTMLTextAreaElement;
        const progress = existingTodoItem.querySelector('.progress_rate') as HTMLSelectElement;
        const priority = existingTodoItem.querySelector('.priority') as HTMLSelectElement;
        const due = existingTodoItem.querySelector('.due') as HTMLInputElement;

        title.textContent = todo.title.slice(0, 7) + (todo.title.length > 7 ? '...' : '');
        description.textContent = todo.description ? todo.description.slice(0, 7) + (todo.description.length > 7 ? '...' : '') : '--';
        progress.textContent = `${todo.progress_rate ?? '--'}%`;
        priority.textContent = todo.priority ?? '--';

        if(todo.due) {
            due.textContent = todo.due;
            const dueDate = new Date(todo.due); //todo.dueをDate型に変換
            dueDate.setHours(0, 0, 0, 0);
            const today = new Date(); //今日の日付を取得
            today.setHours(0, 0, 0, 0);//時間を無視するために0にする

            if(this.isTodaysTodo(existingTodoItem) && dueDate.getTime() > today.getTime()) {//変更するTODOが本日のタスク&&期日が今日より遅い
                // console.log('1');
                existingTodoItem.remove();
                this.createNotTodayTodoItem(todo, dueDate);//サイドに順番通りに表示
                return;
            } else if(this.isTodaysTodo(existingTodoItem) && dueDate.getTime() === today.getTime()) {//期日が今日の場合
                // console.log('2');
                due.textContent = todo.due;
                return;
            }
            if(!this.isTodaysTodo(existingTodoItem) && dueDate.getTime() === today.getTime()) {//既存がサイドにある＋期日本日に変更
                // console.log('3');
                existingTodoItem.remove();
                this.createTodoItem(todo);//本日の方に追加
                return;
            } else if(!this.isTodaysTodo(existingTodoItem) && dueDate.getTime() > today.getTime()) {//既存がサイド＋期日今日より遅い
                // console.log('4');
                existingTodoItem.remove();
                this.createNotTodayTodoItem(todo, dueDate);//サイドに順番通りに表示
                return;
            }
        } else {
            if(!this.isTodaysTodo(existingTodoItem)) {//変更対象がサイドにある場合
                existingTodoItem.remove();
                this.createTodoItem(todo);//本日の方に追加
                return;
            } else {
                due.textContent = '--';
            }
        }
    }

    private createTodoItem(todo: Todo) {//新しいtodoのレイアウトを作成
        const todoTableBody = document.getElementById('todo-table') as HTMLElement;
        const newRow = document.createElement('tr');
        newRow.className = `border border-gray-100 todo-item todo-container ${todo.when_completed ? 'opacity-25' : ''}`;
        newRow.id = `${todo.id}`;
        newRow.innerHTML = `
            <td class="px-4 py-3 text-center">
               <label class="inline-flex items-center">
                    <input type="checkbox" class="hidden peer todo-checkbox">
                    <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-gray-500  ${todo.when_completed ? 'bg-[#8b8a8e]' : ''}  relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </label>
            </td>
            <td class="px-4 py-3 text-center title ${todo.when_completed ? 'line-through' : ''}">${todo.title.slice(0, 7) + (todo.title.length > 7 ? '...' : '')}</td>
            <td class="px-4 py-3 text-center description ${todo.when_completed ? 'line-through' : ''}">${(todo.description ? todo.description.slice(0, 7) + (todo.description.length > 7 ? '...' : '') : '--')}</td>
            <td class="px-4 py-3 text-center progress_rate ${todo.when_completed ? 'line-through' : ''}">${todo.progress_rate ?? '--'}%</td>
            <td class="px-4 py-3 text-center priority ${todo.when_completed ? 'line-through' : ''}">${todo.priority ?? '--'}</td>
            <td class="px-4 py-3 text-center due ${todo.when_completed ? 'line-through' : ''}">${todo.due ?? '--'}</td>
            <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                <a href="#" class="showBtn" todo-id="${todo.id}">詳細</a>
            </td>
            <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                <a href="#" class="todo_delete_btn" todo-id="${todo.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </td>
        `;

        if(todo.due) {
            const dueDate = new Date(todo.due); //todo.dueをDate型に変換
            dueDate.setHours(0, 0, 0, 0);
            const today = new Date(); //今日の日付を取得
            today.setHours(0, 0, 0, 0);//時間を無視するために0にする

            if(dueDate > today) {
                this.createNotTodayTodoItem(todo, dueDate);//もし期日が今日以降ならサイドに表示
                return;
            }
        }
        const firstTodayMessage = document.getElementById('first-today-todo-message');
        if(firstTodayMessage) firstTodayMessage.remove();//初期メッセージを削除
        todoTableBody.insertBefore(newRow, todoTableBody.firstChild);//todoTableBodyの一番上に追加
    }
}
