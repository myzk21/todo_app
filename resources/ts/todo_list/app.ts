const checkBox = document.getElementById('monthly_check_box') as HTMLInputElement;
const monthlyGoal = document.getElementById('monthly_goal') as HTMLElement;

monthlyGoal.classList.add('opacity-0', 'transition-opacity', 'duration-500');

checkBox.addEventListener('change', () => {
    if (checkBox.checked) {
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
