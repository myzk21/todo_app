import axios, { AxiosResponse } from 'axios';
import { Todo } from '../classes/Todo';

export class TodoService {
    static async addTodo(formData: FormData): Promise<Todo> {
        try {
            const response: AxiosResponse<{ success: boolean, message: string, todo: Todo }> = await axios.post('/add_todo', formData);
            return response.data.todo;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                // バリデーションエラーの処理
                const validationErrors = error.response.data.errors;
                displayValidationErrors(validationErrors);
            } else {
                console.error('リクエスト中にエラーが発生しました', error);
            }
            throw new Error('Todoの追加に失敗しました');
        }
    }
}
function displayValidationErrors(errors: any) {
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
