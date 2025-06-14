import axios from 'axios';

export class TodoTimer {
//NOTE:UIでの操作はapp.tsで行い、DBなどロジックの処理はTodoTimerというクラスを作成し、app.tsからクラス内の関数を呼び出すことで使用する

    static async storeTimerData(todoId: string, status: string) {
        try {
            let token = await fetchToken();
            if (!token) {
                throw new Error('CSRFトークンが見つかりません。');
            }
            const response = await axios.post('/store_timer_data', {'todo_id': todoId, 'status': status}, { headers: {'X-CSRF-TOKEN': token,} });
            return response.data;
        } catch (error) {
            return error;
        }
    }

    static async fetchTimerData() {
        try {
            let token = await fetchToken();
            if (!token) {
                throw new Error('CSRFトークンが見つかりません。');
            }
            const response = await axios.patch('/fetch_timer_data', {}, {headers: {'X-CSRF-TOKEN': token,}});
            return response.data;
        } catch (error) {
            return error;
        }
    }
}

async function fetchToken() {
    return fetch('/csrf-token')
    .then(response => response.json())
    .then(data => data.token);
}
