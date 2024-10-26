import axios, { AxiosResponse } from 'axios';
import { Todo } from '../classes/Todo';

export class TodoService {
    static async changeTodoStatus(todoId: string | null): Promise<Todo> {
        try {
            const response: AxiosResponse<{ success: boolean, todo: Todo }> = await axios.patch(`/changeTodoStatus/${todoId}`);
            // const response = await axios.patch(`/changeTodoStatus/${todoId}`, {
            //     headers: {
            //         'Content-Type': 'application/json'
            //     }
            // });
            return response.data.todo;
        } catch(error){
            throw new Error('Todoの更新に失敗しました');//この方式で書くとブラウザにエラーを表示させられる
        }
    }

    static async addTodo(formData: FormData): Promise<Todo> {//todo追加
        try {
            const response: AxiosResponse<{ success: boolean, message: string, todo: Todo }> = await axios.post('/add_todo', formData);
            return response.data.todo;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                // バリデーションエラーの処理
                const validationErrors = error.response.data.errors;
                displayCreateValidationErrors(validationErrors);
            } else {
                console.error('リクエスト中にエラーが発生しました', error);
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
            // const response = await axios.patch(`/update_todo/${todoId}`, updateFormData);→これではなぜか送信されない（nullになる）
            const response = await axios.patch(`/update_todo/${todoId}`, updateFormData, {
                headers: {
                    'Content-Type': 'application/json'
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
            const response: AxiosResponse<{ success: boolean, todo: Todo }> = await axios.delete(`/delete_todo/${todoId}`);
            return response.data.todo;
        } catch (error) {
            throw new Error('Todoの削除に失敗しました');
        }
    }
}
function displayCreateValidationErrors(errors: any) {
    const errorContainer = document.getElementById('errorContainer') as HTMLElement;
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

