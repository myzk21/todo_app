import axios, { AxiosResponse } from 'axios';
import { Todo } from '../classes/Todo';

export class TodoService {
    // Todoを追加するメソッド(APIに渡す)
    static async addTodo(formData: FormData): Promise<Todo> {
        try {
            // formDataを直接ボディに送信
            const response: AxiosResponse<Todo> = await axios.post('/add_todo', formData);
            return response.data;  // 新しいTodoを返す
        } catch (error) {
            // console.error('Todoの追加に失敗しました', error);
            // throw error;

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

    // エラーメッセージを1つずつ表示
    Object.keys(errors).forEach(field => {
        const fieldErrors = errors[field];
        fieldErrors.forEach((message: string) => {
            const errorElement = document.createElement('div');
            errorElement.className = 'text-sm text-red-500 ml-2 mt-1';
            errorElement.innerText = message;
            errorContainer.appendChild(errorElement);
        });
    });
}
