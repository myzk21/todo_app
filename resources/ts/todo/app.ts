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
    const startBtn = document.querySelectorAll('.start_btn') as NodeListOf<HTMLElement>;
    const stopBtn = document.getElementById('stop_btn') as HTMLElement;
    const timerNumber = document.getElementById('timer_number') as HTMLElement;
    const todoTitleContainer = document.getElementById('todo_title') as HTMLElement;
    let seconds: number = 0;
    let time: number | undefined;

    ///スタートボタン押したときの処理
    startBtn.forEach((btn) => {
        btn.addEventListener('click', async () => {
            if (!timerContainer.classList.contains('hidden')) {
                return;//タイマー表示状態ならクリックできない
            }
            let todoId = btn.dataset.todoId as string;
            let status = TimerStatus[0];
            let response = await TodoTimer.storeTimerData(todoId, status);
            if (!response.success) {
                alert('タイマーの記録に失敗しました');
                location.reload();
                return;
            } else {
                const todoTitle = btn.dataset.todoTitle as string;
                timerContainer.dataset.todoId = todoId;
                todoTitleContainer.textContent = todoTitle;
                timerContainer.classList.remove('hidden');
                seconds = response.data.elapsed_time_at_stop;
                time = window.setInterval(() => {
                    seconds++;
                    timerNumber.textContent = formatTime(seconds);
                }, 1000);

                startBtn.forEach((b) => {
                    b.classList.remove('text-green-500');
                    b.classList.add('text-gray-300');
                });
            }
        });
    });

    ///タイマー停止したときの処理
    stopBtn.addEventListener('click', async () => {
        const restartBtn = document.getElementById('restart_btn') as HTMLElement;
        const finishBtn = document.getElementById('finish_btn') as HTMLElement;
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

        //リスタートの処理
        restartBtn.addEventListener('click', () => {
            restartBtn.classList.add('hidden');
            finishBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
        });

        //フィニッシュボタン
        finishBtn.addEventListener('click', () => {
            timerContainer.classList.add('hidden');
            restartBtn.classList.add('hidden');
            finishBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            timerNumber.textContent = "";
            todoTitleContainer.textContent = "";

            startBtn.forEach((btn) => {
                btn.classList.add('text-green-500');
                btn.classList.remove('text-gray-300');
            });
        });

    });

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
