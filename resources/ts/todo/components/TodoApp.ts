import { TodoService } from '../services/TodoService';
import { Todo } from '../classes/Todo';
// import { ok } from 'assert';
// import { UpdateTodo } from '../classes/Todo';


// let screenWidth = window.innerWidth;//画面幅を計測
// window.addEventListener("resize", () => {
//     screenWidth = window.innerWidth;
// });
// let width = screenWidth <= 640 ? "narrow" : "wide";

export class TodoApp {
    private addButton: HTMLButtonElement;
    private openSmartPhoneAddModal: HTMLButtonElement;
    private smallWidthAddButton: HTMLButtonElement;
    private todoCreateForm: HTMLFormElement;
    private smallWidthTodoCreateForm: HTMLFormElement;

    private todoDetailModal = document.getElementById('todo_detail_modal') as HTMLElement;

    constructor(
        addButtonId: string,
        openSmartPhoneAddModalId: string,
        smallWidthAddButtonId: string,
        todoCreateFormId: string,
        smallWidthTodoCreateFormId: string,
    ) {
        this.addButton = document.getElementById(addButtonId) as HTMLButtonElement;
        this.openSmartPhoneAddModal = document.getElementById(openSmartPhoneAddModalId) as HTMLButtonElement;
        this.smallWidthAddButton = document.getElementById(smallWidthAddButtonId) as HTMLButtonElement;
        this.todoCreateForm = document.getElementById(todoCreateFormId) as HTMLFormElement;
        this.smallWidthTodoCreateForm = document.getElementById(smallWidthTodoCreateFormId) as HTMLFormElement;

        this.addButton.addEventListener('click', (event) => {
            event.preventDefault();
            this.addTodo(this.addButton, this.todoCreateForm, "wide");
        });
        this.openSmartPhoneAddModal.addEventListener('click', (event) => {
            event.preventDefault();
            this.displaySmartPhoneModal();
        });
        this.changeTodoStatus();
        this.showTodo();
        this.deleteTodo();
    }

    private displaySmartPhoneModal() {
        let smartPhoneCreateModal = document.getElementById('small_width_todo_create_container') as HTMLElement;
        let closeModal = document.getElementById('close_smart_modal') as HTMLElement;
        smartPhoneCreateModal.classList.remove('hidden');
        closeModal.addEventListener('click', () => {
            smartPhoneCreateModal.classList.add('hidden');
        });
        this.smallWidthAddButton.addEventListener('click', (event) => {
            event.preventDefault();
            this.addTodo(this.smallWidthAddButton, this.smallWidthTodoCreateForm, "narrow");//TODOの追加
        });
    }

    //todo完了処理
    private async changeTodoStatus() {
        const todoListContainer = document.getElementById('todo_list') as HTMLElement;
        todoListContainer.addEventListener('click', async (event) => {
            const target = event.target as HTMLElement;
            if (target.classList.contains('todo-checkbox')) {
                const  todoContainer = target.closest('.todo-container') as HTMLTableRowElement | HTMLDivElement;
                const todoId = todoContainer.id;
                const userActionDialog = document.getElementById('user_action_dialog') as HTMLElement;
                try {
                    if (todoId) {
                        const newTodo: Todo = await TodoService.changeTodoStatus(todoId);
                        if(newTodo.is_completed == true) {
                            todoContainer.remove();//未完了から削除
                            this.createTodoItem(newTodo);
                            userActionDialog.classList.remove('hidden');
                            userActionDialog.textContent = `「${newTodo.title}」を完了にしました`;
                            setTimeout(() => {
                                userActionDialog.classList.add('hidden'); // 3秒後に非表示
                            }, 3000);
                        } else {
                            todoContainer.remove();//完了のところから削除
                            this.createTodoItem(newTodo);
                            userActionDialog.classList.remove('hidden');
                            userActionDialog.textContent = `「${newTodo.title}」を未完了にしました`;
                            setTimeout(() => {
                                userActionDialog.classList.add('hidden'); // 3秒後に非表示
                            }, 3000);
                        }
                    } else {
                        console.error('Todo IDが見つかりません。');
                    }
                } catch(error) {
                    const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
                    errorContainer.innerHTML = '';
                    errorContainer.innerHTML = `
                    <div class="relative bg-red-500 w-2/5 rounded mb-2 p-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <p class="text-white font-semibold">システムエラーが発生しました</p>
                        <p class="text-right text-white absolute -top-1 right-2 cursor-pointer text-3xl" id="closeSystemError">×</p>
                    </div>`;
                    setTimeout(() => {
                        errorContainer.innerHTML = ''; // 3秒後に非表示
                    }, 3000);
                }
            }
        });
    }
    private isProcessing = false;//処理中かどうかを判定

    // Todoの追加処理
    private async addTodo(addBtn: HTMLButtonElement, form: HTMLFormElement, displayWidth: string) {
        if (this.isProcessing) return;//処理中なら処理を中止
        this.isProcessing = true;
        addBtn.disabled = true;
        const formData = new FormData(form);

        if (!formData) return;
        try {
            const newTodo: Todo = await TodoService.addTodo(formData, displayWidth);//サービスクラスを呼び出してTodoを追加
            let errorContainer: HTMLElement | null = null;
            if (displayWidth == "wide") {
                errorContainer = document.getElementById('errorContainer') as HTMLElement;
            } else if (displayWidth == "narrow") {
                errorContainer = document.getElementById('smallWidthErrorContainer') as HTMLElement;
            }
            this.renderTodo(newTodo);
            form.reset();//入力フィールドをクリア
            if (errorContainer) {
                errorContainer.innerHTML = ''; //既存のエラーをクリア
            }
            if (displayWidth == "narrow") {
                let smartPhoneCreateModal = document.getElementById('small_width_todo_create_container') as HTMLElement;
                smartPhoneCreateModal.classList.add('hidden');
            }
            const userActionDialog = document.getElementById('user_action_dialog') as HTMLElement;
            userActionDialog.classList.remove('hidden');
            userActionDialog.textContent = `「${newTodo.title}」を追加しました`;
            setTimeout(() => {
                userActionDialog.classList.add('hidden'); // 3秒後に非表示
            }, 3000);
            console.log('Todoの追加に成功しました');
        } catch (error) {
            const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
            errorContainer.innerHTML = '';
            errorContainer.innerHTML = `
            <div class="relative bg-red-500 w-2/5 rounded mb-2 p-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                  </svg>
                <p class="text-white font-semibold">TODOの追加に失敗しました</p>
                <p class="text-right text-white absolute -top-1 right-2 cursor-pointer text-3xl" id="closeSystemError">×</p>
            </div>`;
            setTimeout(() => {
                errorContainer.innerHTML = ''; // 3秒後に非表示
            }, 3000);
            console.error('Todoの追加に失敗しました');
        } finally {
            setTimeout(() => {
                addBtn.disabled = false;
                this.isProcessing = false; //ボタンを再起動
            }, 1000);
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
    private async updateTodo(updateButton: HTMLButtonElement) {
        if (this.isProcessing) return;//処理中なら処理を中止
        this.isProcessing = true;
        updateButton.disabled = true;
        const todoUpdateForm = document.getElementById('todo_update_form') as HTMLFormElement;
        const updateFormData = new FormData(todoUpdateForm);
        if (!updateFormData) return;
        try {
            const updateTodo: Todo = await TodoService.updateTodo(updateFormData);//サービスクラスを呼び出してTodoを追加
            this.renderTodo(updateTodo);
            this.todoDetailModal.innerHTML = '';
            const userActionDialog = document.getElementById('user_action_dialog') as HTMLElement;
            userActionDialog.classList.remove('hidden');
            userActionDialog.textContent = `「${updateTodo.title}」を更新しました`;
            setTimeout(() => {
                userActionDialog.classList.add('hidden'); // 3秒後に非表示
            }, 3000);
            console.log('Todoの更新に成功しました');
        } catch (error) {
            const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
            errorContainer.innerHTML = '';
            errorContainer.innerHTML = `
            <div class="relative bg-red-500 w-2/5 rounded mb-2 p-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                  </svg>
                <p class="text-white font-semibold">TODOの更新に失敗しました</p>
                <p class="text-right text-white absolute -top-1 right-2 cursor-pointer text-3xl" id="closeSystemError">×</p>
            </div>`;
            setTimeout(() => {
                errorContainer.innerHTML = ''; // 3秒後に非表示
            }, 3000);
            console.error('Todoの更新に失敗しました');
        } finally {
            setTimeout(() => {
                this.isProcessing = false;
                updateButton.disabled = false;
            }, 1000);
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
                    const deleteBtn = target.closest('.todo_delete_btn') as HTMLAnchorElement; //クリックされた<a>要素を取得
                    try{
                        deleteBtn.classList.add('pointer-events-none');
                        const todoId = deleteBtn.getAttribute('todo-id'); //todo-id属性を取得
                        if (todoId) {
                            const deleteTodo: Todo = await TodoService.deleteTodo(todoId);
                            deleteBtn.classList.remove('pointer-events-none');
                            this.renderTodo(deleteTodo);
                        } else {
                            throw new Error('Todo IDが見つかりません。');
                        }
                    } catch(error) {
                        const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
                        errorContainer.innerHTML = '';
                        errorContainer.innerHTML = `
                        <div class="relative bg-red-500 w-2/5 rounded mb-2 p-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                              </svg>
                            <p class="text-white font-semibold">TODOの削除に失敗しました</p>
                            <p class="text-right text-white absolute -top-1 right-2 cursor-pointer text-3xl" id="closeSystemError">×</p>
                        </div>`;
                        deleteBtn.classList.remove('pointer-events-none');
                        setTimeout(() => {
                            errorContainer.innerHTML = ''; // 3秒後に非表示
                        }, 3000);
                        console.error('Todoの削除に失敗しました');
                    }
                }
            });
    }

    private createDetailModal(showTodo: Todo) {//todo作成モーダル
        let authenticatedByGoogle = false;
        if (showTodo.user.google_user) {//Googleユーザーがあるかどうかを調べる
            authenticatedByGoogle = true;
        }
        const modalHTML = `
            <div class="w-full h-full z-50 fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="px-6 pb-5 pt-3 shadow-sm w-3/5 rounded bg-white max-sm:w-4/5 max-sm:overflow-y-scroll max-sm:h-[calc(100%-32px)]" id="">
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
                        <div class="sm:flex sm:flex-wrap pt-1">
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="percentage">進捗率</label>
                                <select id="percentage" class="rounded mr-4 max-sm:block max-sm:w-full" name="updateProgress_rate">
                                    <option value="">--</option>
                                    ${Array.from({ length: 11 }, (_, i) => {
                                        const value = i * 10; // 0, 10, 20, ..., 100
                                        const showRateNum = showTodo.progress_rate !== null ? Number(showTodo.progress_rate) : null;
                                        const isSelected = showRateNum === value ? 'selected' : '';
                                        return `<option value="${value}" ${isSelected}>${value}%</option>`;
                                    }).join('')}
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="priority">優先度</label>
                                <select id="priority" class="rounded mr-4 max-sm:block max-sm:w-full" name="updatePriority">
                                    <option value="">--</option>
                                    <option value="高" ${showTodo.priority == '高' ? 'selected' : ''}>高</option>
                                    <option value="中" ${showTodo.priority == '中' ? 'selected' : ''}>中</option>
                                    <option value="低" ${showTodo.priority == '低' ? 'selected' : ''}>低</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-auto mb-4 sm:mb-0">
                                <label for="due">期日</label>
                                <input type="date" name="updateDue" id="due" class="rounded max-sm:block max-sm:w-full" value="${showTodo.due ?? ""}">
                            </div>
                            <input type="hidden" name="googleUser" value="${showTodo.user.google_user ? showTodo.user.google_user.id : ''}">
                            <input type="hidden" name="event_id" value="${showTodo.event_id ? showTodo.event_id : ''}">
                            <label class="inline-flex items-center cursor-pointer ml-2 sm:mt-1">
                                <input type="checkbox" class="hidden peer" name="updateToCalendar" id="addToCalendarCheckbox" value="1" ${showTodo.event_id ? 'checked' : ''}  ${authenticatedByGoogle ? '' : 'disabled'}>
                                <div class="w-5 h-5 border border-gray-500 rounded-sm peer-checked:bg-[#8b8a8e] relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <p class="ml-1 select-none ${authenticatedByGoogle ? '' : 'line-through'}">Googleカレンダーに追加</p>
                            </label>
                        </div>
                        <div class="w-full sm:flex mt-1 max-sm:mt-3">
                            <button class="bg-[#8b8a8e] text-white text-sm px-4 py-2 rounded ml-auto hover:bg-opacity-80 select-none sm:flex sm:justify-end max-sm:w-full max-sm:text-center" id="todo_update_btn">保存</button>
                            <p class="hover:underline cursor-pointer px-4 py-2 text-gray-400 text-sm todo_delete_btn max-sm:w-full max-sm:text-center max-sm:rounded max-sm:bg-red-500 max-sm:mt-2 max-sm:text-white" todo-id="${showTodo.id}"><a>削除</a></p>
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

        const updateButton = document.getElementById('todo_update_btn') as HTMLButtonElement;
        updateButton.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            this.updateTodo(updateButton);//TODOの更新
        });
        //詳細画面での削除
        const deleteButton = document.querySelector('.todo_delete_btn') as HTMLElement;
        deleteButton.addEventListener('click', async (event: MouseEvent) => {
            // this.deleteTodo();
            if(!confirm('本当に削除しますか？')) return;
            try{
                const todoId = deleteButton.getAttribute('todo-id'); //todo-id属性を取得
                deleteButton.classList.add('pointer-events-none');
                if (todoId) {
                    const deleteTodo: Todo = await TodoService.deleteTodo(todoId);
                    deleteButton.classList.remove('pointer-events-none');
                    this.renderTodo(deleteTodo);
                    //モーダルをとじる
                    if(this.todoDetailModal)this.todoDetailModal.innerHTML = '';
                } else {
                    throw new Error('Todo IDが見つかりません。');
                }
            } catch(error) {
                const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
                errorContainer.innerHTML = '';
                errorContainer.innerHTML = `
                <div class="relative bg-red-500 w-2/5 rounded mb-2 p-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                      </svg>
                    <p class="text-white font-semibold">TODOの削除に失敗しました</p>
                    <p class="text-right text-white absolute -top-1 right-2 cursor-pointer text-3xl" id="closeSystemError">×</p>
                </div>`;
                deleteButton.classList.remove('pointer-events-none');
                if(this.todoDetailModal)this.todoDetailModal.innerHTML = '';
                setTimeout(() => {
                    errorContainer.innerHTML = ''; // 4秒後に非表示
                }, 4000);
                console.error('Todoの削除に失敗しました');
            }
        });
    }

    private updateTodoItem(existingTodoItem: HTMLTableRowElement | HTMLDivElement, todo: Todo) {
        const title = existingTodoItem.querySelector('.title') as HTMLInputElement;
        const description = existingTodoItem.querySelector('.description') as HTMLTextAreaElement;
        const progress = existingTodoItem.querySelector('.progress_rate') as HTMLSelectElement;
        const priority = existingTodoItem.querySelector('.priority') as HTMLSelectElement;
        const due = existingTodoItem.querySelector('.due') as HTMLInputElement;

        title.textContent = todo.title.slice(0, 7) + (todo.title.length > 7 ? '...' : '');
        description.textContent = todo.description ? todo.description.slice(0, 7) + (todo.description.length > 7 ? '...' : '') : '--';
        progress.textContent = `${todo.progress_rate ?? '--'}%`;
        priority.textContent = todo.priority ?? '--';
        due.textContent = todo.due ?? '--';
    }

    private createTodoItem(todo: Todo) {//新しいtodoのレイアウトを作成
        let todoTableBody: HTMLTableSectionElement;
        if(todo.is_completed == true) {
            const completeTaskTable = document.getElementById('completeTaskTable') as HTMLElement;
            todoTableBody = completeTaskTable.querySelector('.completeTaskTableBody') as HTMLTableSectionElement;
            const firstTodayMessage = document.getElementById('first-completed-todo-message');
            if(firstTodayMessage) firstTodayMessage.remove();//初期メッセージを削除
        } else {
            const incompleteTaskTable = document.getElementById('incompleteTaskTable') as HTMLElement;
            todoTableBody = incompleteTaskTable.querySelector('.incompleteTaskTableBody') as HTMLTableSectionElement;
            const firstTodayMessage = document.getElementById('first-incompleted-todo-message');
            if(firstTodayMessage) firstTodayMessage.remove();//初期メッセージを削除
        }
        const newRow = document.createElement('tr');
        newRow.className = `border border-gray-100 todo-item todo-container ${todo.is_completed ? 'opacity-30' : ''}`;
        newRow.id = `${todo.id}`;
        newRow.setAttribute('data-created-at', todo.created_at);
        newRow.innerHTML = `
            <td class="px-4 py-3 text-center">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="hidden peer todo-checkbox">
                    <div class="w-5 h-5 border border-gray-400 rounded-sm peer-checked:bg-gray-500 relative ${todo.is_completed ? 'bg-[#8b8a8e]' : ''}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 peer-checked:block w-4 h-4 text-white text-center absolute inset-0 m-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </label>
            </td>
            <td class="px-4 py-3 text-center title">${todo.title.slice(0, 7) + (todo.title.length > 7 ? '...' : '')}</td>
            <td class="px-4 py-3 text-center description max-sm:hidden">${(todo.description ? todo.description.slice(0, 7) + (todo.description.length > 7 ? '...' : '') : '--')}</td>
            <td class="px-4 py-3 text-center progress_rate max-sm:hidden">${todo.progress_rate ?? '--'}%</td>
            <td class="px-4 py-3 text-center priority max-sm:hidden">${todo.priority ?? '--'}</td>
            <td class="px-4 py-3 text-center due">${todo.due ?? '--'}</td>
            <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center">
                <a href="#" class="showBtn whitespace-nowrap" todo-id="${todo.id}">詳細</a>
            </td>
            <td class="px-4 py-3 text-gray-400 text-sm hover:underline text-center max-sm:hidden">
                <a href="#" class="todo_delete_btn" todo-id="${todo.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-400">
                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </td>
        `;
        if(todo.is_completed == true) {
            todoTableBody.insertBefore(newRow, todoTableBody.firstChild);//todoTableBodyの一番上に追加
        } else {
            //未完了のタスクの場合created_atの降順になるように挿入（完了から未完了にする場合もあるためこの方式にする）
            const tableRow = todoTableBody.querySelectorAll('.todo-container') as NodeListOf<HTMLTableRowElement>;
            if(tableRow.length != 0) {
                let added = false;
                for(let i = 0; i < tableRow.length; i++) {
                    const row = tableRow[i];
                    const createdAt = row.getAttribute('data-created-at');
                    if(createdAt) {
                        const existingCreatedAt = new Date(createdAt);
                        const newTodoCreatedAt = new Date(todo.created_at);
                        if(existingCreatedAt.getTime() < newTodoCreatedAt.getTime()) {
                            added = true;
                            todoTableBody.insertBefore(newRow, row);
                            break;
                        }
                        if(!added) {//まだ追加されていなかった場合（未完了リストにある全てのTODOの作成日よりも前だった場合）
                            todoTableBody.appendChild(newRow);
                        }
                    }
                }
            } else {
                todoTableBody.insertBefore(newRow, todoTableBody.firstChild);
            }
        }
    }
}
