import { TodoApp } from './components/TodoApp';

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

    const connectToGoogleBtn = document.getElementById('connectToGoogle') as HTMLElement;
    connectToGoogleBtn.addEventListener('click', (event: MouseEvent) => {
        const isConfirmed = confirm('Googleアカウントの再接続をしますか？');
        if(!isConfirmed) {
            event.preventDefault();
        }
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
});
