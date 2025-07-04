import { TodoApp } from './components/TodoApp';
import { TodoTimer } from './services/TodoTimer';///NOTE:UIでの操作はここで行い、DBなどロジックの処理はTodoTimerというクラスを作成し、そこで操作することにするー＞コードが読みやすくなる

//ページが読み込まれた時にTodoAppを初期化
document.addEventListener('DOMContentLoaded', () => {
    const todoApp = new TodoApp('todo_add_btn', 'open_smartphone_add_modal', 'small_width_todo_add_btn', 'todo_create_form', 'small_width_todo_create_form');


    //システムエラーが表示された場合、クリックしてその要素を削除する
    const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
    errorContainer.addEventListener('click', async (event) => {
        const target = event.target as HTMLElement;
        if (target.id === 'closeSystemError') {
            errorContainer.innerHTML = '';
        }
    });

    const connectToGoogleBtns = document.querySelectorAll<HTMLElement>('.connectToGoogle');
    connectToGoogleBtns.forEach((e) => {
        e.addEventListener('click', (event: MouseEvent) => {
            const isConfirmed = confirm('Googleアカウントの再接続をしますか？');
            if(!isConfirmed) {
                event.preventDefault();
            }
        });
    });

    //以下月間目標表示処理
    const checkBoxes = document.querySelectorAll('.monthly_check_box') as NodeListOf<HTMLInputElement>;
    const monthlyGoal = document.getElementById('monthly_goal') as HTMLElement;

    monthlyGoal.classList.add('opacity-0', 'transition-opacity', 'duration-500');

    checkBoxes.forEach((check) => {
        check.addEventListener('change', () => {
            if (check.checked) {
                //チェックボックスがチェックされた場合
                monthlyGoal.style.display = 'block';
                setTimeout(() => {
                    monthlyGoal.classList.remove('opacity-0');
                    monthlyGoal.classList.add('opacity-100');
                }, 10);
            } else {
                monthlyGoal.classList.remove('opacity-100');
                monthlyGoal.classList.add('opacity-0');
                monthlyGoal.style.display = 'none';
            }
        });
    });

    //TODO入力詳細の展開
    const detailBtn = document.getElementById('detail') as HTMLInputElement;
    const todoContent = document.getElementById('todo_content') as HTMLElement;
    const downArrow = document.getElementById('down_arrow') as HTMLElement;
    const detailSign = document.getElementById('detail_sign') as HTMLElement;

    todoContent.classList.add('opacity-0', 'transition-opacity', 'duration-500');

    detailBtn.addEventListener('click', () => {

        if (todoContent.style.display === 'block') {
            todoContent.style.display = 'none';
            detailSign.textContent = '詳細';
        } else {
            todoContent.style.display = 'block';
            detailSign.textContent = '閉じる';
        }

        setTimeout(() => {
            todoContent.classList.toggle('opacity-0');
        }, 10);
        downArrow.classList.toggle('custom-rotate-180');//矢印回転
    });
    //未完了、完了タブの切り替え
    const incompleteTab = document.getElementById('incompleteTask_tab') as HTMLElement;
    const completeTab = document.getElementById('completeTask_tab') as HTMLElement;
    const incompleteTaskContainer =  document.getElementById('incompleteTaskContainer') as HTMLElement;
    const completeTaskContainer =  document.getElementById('completeTaskContainer') as HTMLElement;

    incompleteTab.addEventListener('click', () => {
        const url = new URL(window.location.href);
        const page = url.searchParams.get('page');
        if (page) {
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        sessionStorage.setItem("activeTab", "incomplete");
        incompleteTaskContainer.classList.remove('hidden');
        completeTaskContainer.classList.add('hidden');
        incompleteTab.classList.remove('text-gray-500', 'hover:opacity-60');
        incompleteTab.classList.add('active');
        completeTab.classList.remove('active');
        completeTab.classList.add('text-gray-500', 'hover:opacity-60');
    });
    completeTab.addEventListener('click', () => {
        const url = new URL(window.location.href);
        const page = url.searchParams.get('page');
        if (page) {
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        sessionStorage.setItem("activeTab", "complete");
        incompleteTaskContainer.classList.add('hidden');
        completeTaskContainer.classList.remove('hidden');
        completeTab.classList.remove('text-gray-500', 'hover:opacity-60');
        completeTab.classList.add('active');
        incompleteTab.classList.remove('active');
        incompleteTab.classList.add('text-gray-500', 'hover:opacity-60');
    });
    //セッションに保存されている方のタブをアクティブにする
    if (sessionStorage.getItem('activeTab') && sessionStorage.getItem('activeTab') == 'incomplete') {
        incompleteTaskContainer.classList.remove('hidden');
        completeTaskContainer.classList.add('hidden');
        incompleteTab.classList.remove('text-gray-500', 'hover:opacity-60');
        incompleteTab.classList.add('active');
        completeTab.classList.remove('active');
        completeTab.classList.add('text-gray-500', 'hover:opacity-60');
    } else if(sessionStorage.getItem('activeTab') && sessionStorage.getItem('activeTab') == 'complete') {
        incompleteTaskContainer.classList.add('hidden');
        completeTaskContainer.classList.remove('hidden');
        completeTab.classList.remove('text-gray-500', 'hover:opacity-60');
        completeTab.classList.add('active');
        incompleteTab.classList.remove('active');
        incompleteTab.classList.add('text-gray-500', 'hover:opacity-60');
    }

    //タイマー表示
    const TimerStatus = ['start', 'stop', 'finish'];
    const timerContainer = document.getElementById('timer_container') as HTMLElement;
    const stopBtn = document.getElementById('stop_btn') as HTMLElement;
    const timerNumber = document.getElementById('timer_number') as HTMLElement;
    const todoTitleContainer = document.getElementById('todo_title') as HTMLElement;
    const restartBtn = document.getElementById('restart_btn') as HTMLElement;
    const finishBtn = document.getElementById('finish_btn') as HTMLElement;
    let seconds: number = 0;
    let time: number | undefined;

    interface TimerResponse {
        success: boolean;
        data?: any;
        message?: string;
    }

    fetchTimerData();//アクセス時に起動しているタイマーがないかチェック
    handleStartTimer();
    handleStopTimer();
    handleFinishTimer();
    handleRestartTimer();

    async function fetchTimerData() {
        let response = await TodoTimer.fetchTimerData();
        let data = response.data;
        const startBtn = document.querySelectorAll('.start_btn') as NodeListOf<HTMLElement>;
        if (!data) {
            return;
        }
        if (data.status == TimerStatus[0]) {
            //継続してタイマーを作動させる処理
            todoTitleContainer.textContent = data.todo.title;
            timerContainer.classList.remove('hidden');
            timerContainer.dataset.todoId = data.todo.id;
            seconds = calculateTimer(data);//開始時刻から何秒経過しているかを算出
            if (seconds != 0) {
                timerNumber.textContent = formatTime(seconds);
            }
            time = window.setInterval(() => {
                seconds++;
                timerNumber.textContent = formatTime(seconds);
            }, 1000);

            startBtn.forEach((b) => {
                b.classList.remove('text-green-500');
                b.classList.add('text-gray-300');
            });

        } else if(data.status == TimerStatus[1]) {
            //停止した状態のタイマーを表示
            restartBtn.classList.remove('hidden');
            finishBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            todoTitleContainer.textContent = data.todo.title;
            timerContainer.classList.remove('hidden');
            timerContainer.dataset.todoId = data.todo.id;
            seconds = data.elapsed_time_at_stop;
            timerNumber.textContent = formatTime(seconds);
            startBtn.forEach((b) => {
                b.classList.remove('text-green-500');
                b.classList.add('text-gray-300');
            });
        }
    }

    function calculateTimer(data: any): number {
        const startTime = new Date(data.started_at);
        const now = new Date();
        const startTimestamp = Math.floor(startTime.getTime() / 1000);
        const nowTimestamp = Math.floor(now.getTime() / 1000);
        let time = nowTimestamp - startTimestamp;
        let elapsedTime = data.elapsed_time_at_stop + time;
        return elapsedTime;
    }

    function handleStartTimer() {
        incompleteTaskContainer.addEventListener('click', async (event) => {
            const target = event.target as HTMLElement;
            if (target.classList.contains('start_btn')) {///NOTE:非同期で追加された要素にもクリックした処理が行われるように、親要素にクリックイベントを設定して正確なデータを取得できるようにする
                const startBtn = document.querySelectorAll('.start_btn') as NodeListOf<HTMLElement>;
                ///スタートボタンは各タスクに設置されるため、非同期で追加された場合に、それも含む全てのボタンを取得するためにこのタイミングで取得
                if (!timerContainer.classList.contains('hidden')) {
                    return;//タイマー表示状態ならクリックできない
                }
                let todoId = target.dataset.todoId as string;
                if(!todoId) {
                    alert('タイマーの記録に失敗しました');
                    location.reload();
                    return;
                }
                let status = TimerStatus[0];
                let response = await TodoTimer.storeTimerData(todoId, status);
                if (!response.success) {
                    alert('タイマーの記録に失敗しました');
                    location.reload();
                    return;
                } else {
                    const todoTitle = target.dataset.todoTitle as string;
                    todoTitleContainer.textContent = todoTitle;
                    timerContainer.classList.remove('hidden');
                    timerContainer.dataset.todoId = todoId;
                    seconds = response.data.elapsed_time_at_stop;
                    if (seconds != 0) {//すでに記録があるタイマーをスタートさせたとき
                        timerNumber.textContent = formatTime(seconds);
                    }
                    time = window.setInterval(() => {
                        seconds++;
                        timerNumber.textContent = formatTime(seconds);
                    }, 1000);

                    startBtn.forEach((b) => {
                        b.classList.remove('text-green-500');
                        b.classList.add('text-gray-300');
                    });
                }
            }

        });
    }

    function handleStopTimer() {
        stopBtn.addEventListener('click', async () => {
            restartBtn.classList.remove('hidden');
            finishBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            let todoId = timerContainer.dataset.todoId as string;
            if (time != undefined) {
                clearInterval(time);//タイマーストップ
                //経過時間を記録
                let status = TimerStatus[1];
                let response = await TodoTimer.storeTimerData(todoId, status);
                if(!response.success) {
                    alert('タイマーの記録に失敗しました');
                    location.reload();
                    return;
                }
            }
        });
    }

    function handleFinishTimer() {
        finishBtn.addEventListener('click', async () => {
            let todoId = timerContainer.dataset.todoId as string;
            let status = TimerStatus[2];
            let response = await TodoTimer.storeTimerData(todoId, status);
            if(!response.success) {
                alert('タイマーの記録に失敗しました');
                location.reload();
                return;
            }
            timerContainer.classList.add('hidden');
            restartBtn.classList.add('hidden');
            finishBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            timerNumber.textContent = "";
            todoTitleContainer.textContent = "";

            const startBtn = document.querySelectorAll('.start_btn') as NodeListOf<HTMLElement>;
            startBtn.forEach((btn) => {
                btn.classList.add('text-green-500');
                btn.classList.remove('text-gray-300');
            });
        });
    }

    function handleRestartTimer() {
        restartBtn.addEventListener('click', async () => {
            let todoId = timerContainer.dataset.todoId as string;
            restartBtn.classList.add('hidden');
            finishBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');

            let status = TimerStatus[0];
            let response = await TodoTimer.storeTimerData(todoId, status);
            if (!response.success) {
                alert('タイマーの記録に失敗しました');
                location.reload();
                return;
            }
            seconds = response.data.elapsed_time_at_stop;
            if (seconds != 0) {
                timerNumber.textContent = formatTime(seconds);
            }
            time = window.setInterval(() => {
                seconds++;
                timerNumber.textContent = formatTime(seconds);
            }, 1000);
        });
    }

    function formatTime(sec: number) {
        let hours = 0;
        let minutes = 0;
        let seconds = 0;

        hours = Math.floor(sec / 3600);//Math.floorで整数部分のみ求める、一時間もたってなければ0になるからOK
        minutes = Math.floor((sec % 3600) / 60);
        seconds = (sec % 3600) % 60;

        return `${String(hours).padStart(2, "0")} : ${String(minutes).padStart(2, "0")} : ${String(seconds).padStart(2, "0")}`;
    }

});
