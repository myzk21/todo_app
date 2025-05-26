import axios, { AxiosResponse } from 'axios';
import { Todo } from '../classes/Todo';

export class TodoService {
    static async changeTodoStatus(todoId: string | null): Promise<Todo> {
        try {
            let token = await fetchToken();//本来は非同期の関数を書くとその処理が終了するのを待たずに次の行に行く->変数に代入されないー＞でもawaitすることで非同期の処理が終わるのを待ち、変数に代入されるのを待ってから次の行のプログラムが実行される
            if (!token) {
                throw new Error('CSRFトークンが見つかりません。');
            }
            const response: AxiosResponse<{ success: boolean, todo: Todo }> = await axios.patch(
                `/changeTodoStatus/${todoId}`, {},
            {
                headers: {
                    'X-CSRF-TOKEN': token,
                },
            });
            return response.data.todo;
        } catch(error){
            throw new Error('Todoの更新に失敗しました');
        }
    }

    static async addTodo(formData: FormData, displayWidth: string): Promise<Todo> {//todo追加
        try {
            let token = await fetchToken();
            // const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                throw new Error('CSRFトークンが見つかりません。');
            }
            const response: AxiosResponse<{ success: boolean, message: string, todo: Todo, invalidRefreshToken: string|null }> = await axios.post(
                '/add_todo',
                formData,
            {
                headers: {
                    'X-CSRF-TOKEN': token,
                },
            });
            if (!response.data.success) {
                throw new Error(response.data.invalidRefreshToken ?? response.data.message);
            }
            return response.data.todo;
        } catch (error: any) {
            if (axios.isAxiosError(error) && error.response) {
                // バリデーションエラーの処理
                const validationErrors = error.response.data.errors;
                displayCreateValidationErrors(validationErrors, displayWidth);
            } else {
                throw new Error(error.message);
            }
            throw new Error('Todoの追加に失敗しました');
        }
    }
    //詳細表示
    static async showTodo(todoId: string | null): Promise<Todo> {
        try {
            const response: AxiosResponse<{ success: boolean, todo: Todo }> = await axios.get(`/show_todo/${todoId}`);
            return response.data.todo;
        } catch (error) {
            throw new Error('Todoの表示に失敗しました');
        }
    }
    //編集処理
    static async updateTodo(updateFormData: FormData): Promise<Todo> {
        try {
            const todoId = updateFormData.get('id');
            let token = await fetchToken();
            if (!token) {
                throw new Error('CSRFトークンが見つかりません。');
            }
            const response = await axios.patch(`/update_todo/${todoId}`, updateFormData, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                }
            });
            return response.data.todo;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                console.error('Axios Error:', error.response?.data);
                // バリデーションエラーの処理
                const validationErrors = error.response.data.errors;
                displayUpdateValidationErrors(validationErrors);
            } else {
                console.error('リクエスト中にエラーが発生しました', error);
            }
            throw new Error('Todoの更新に失敗しました');
        }
    }
    //削除処理
    static async deleteTodo(todoId: string | null): Promise<Todo> {
        try {
            let token = await fetchToken();
            if (!token) {
                throw new Error('CSRFトークンが見つかりません。');
            }
            const response: AxiosResponse<{ success: boolean, todo: Todo }> = await axios.delete(
                `/delete_todo/${todoId}`,
                {
                    headers: {
                        'X-CSRF-TOKEN': token,
                    },
                }
            );
            return response.data.todo;
        } catch (error) {
            throw new Error('Todoの削除に失敗しました');
        }
    }
}

async function fetchToken() {
    return fetch('/csrf-token')
        .then(response => response.json())
        .then(data => data.token);
}

function displayCreateValidationErrors(errors: any, displayWidth: string) {
    let errorContainer: HTMLElement | null = null;
    if (displayWidth == "wide") {
        errorContainer = document.getElementById('errorContainer') as HTMLElement;
    } else if(displayWidth == "narrow") {
        errorContainer = document.getElementById('smallWidthErrorContainer') as HTMLElement;
    }
    if (errorContainer) {
        errorContainer.innerHTML = ''; //既存のエラーをクリア
        Object.keys(errors).forEach(field => { //エラーメッセージを1つずつ表示
            const fieldErrors = errors[field];
            fieldErrors.forEach((message: string) => {
                const errorElement = document.createElement('div');
                errorElement.className = 'text-sm text-red-500 ml-2 mt-1';
                errorElement.innerText = message;
                errorContainer.appendChild(errorElement);
            });
        });
    }
}

function displayUpdateValidationErrors(errors: any) {
    const errorContainer = document.getElementById('updateErrorContainer') as HTMLElement;
    errorContainer.innerHTML = ''; //既存のエラーをクリア
    Object.keys(errors).forEach(field => { //エラーメッセージを1つずつ表示
        const fieldErrors = errors[field];
        fieldErrors.forEach((message: string) => {
            const errorElement = document.createElement('div');
            errorElement.className = 'text-sm text-red-500 ml-2 mt-1';
            errorElement.innerText = message;
            errorContainer.appendChild(errorElement);
        });
    });
}

