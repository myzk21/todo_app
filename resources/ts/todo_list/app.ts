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
