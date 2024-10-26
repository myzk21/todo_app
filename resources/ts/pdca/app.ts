import { PdcaApp } from './components/PdcaApp';

//ページが読み込まれた時にTodoAppを初期化
document.addEventListener('DOMContentLoaded', () => {
    // const todoApp = new TodoApp('todo_add_btn', 'todo_create_form', 'todo_title_input', 'todo_description_input', 'percentage', 'priority', 'due');

    //PDCAの星
    const stars = document.querySelectorAll<SVGElement>('.check-star');
    let rating = 0;

    const ratingInput = document.getElementById('rating') as HTMLInputElement | null;

    stars.forEach((star, index) => {
        //星をクリックしたときのイベントハンドラ
        star.addEventListener('click', () => {
            rating = index + 1;
            if (ratingInput) {
                ratingInput.value = rating.toString(); //inputの値を更新
            }
            updateStarsOnClick(rating);
        });

        //星にマウスを乗せたときのイベントハンドラ
        star.addEventListener('mouseover', () => {
            updateStarsOnHover(index + 1);
        });

        //星からマウスが離れたときのイベントハンドラ
        star.addEventListener('mouseout', () => {
            updateStarsOnHover(rating);
        });
    });

    //星をクリックした際の表示更新
    function updateStarsOnClick(rating: number) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.setAttribute('fill', 'orange');
            } else {
                star.setAttribute('fill', 'none');
            }
        });
    }

    //星にホバーした際の表示更新
    function updateStarsOnHover(rating: number) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.setAttribute('fill', 'orange');
            } else {
                star.setAttribute('fill', 'none');
            }
        });
    }
});
