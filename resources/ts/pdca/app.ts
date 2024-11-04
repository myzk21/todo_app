import { PdcaApp } from './components/PdcaApp';

//ページが読み込まれた時にTodoAppを初期化
document.addEventListener('DOMContentLoaded', () => {

    //システムエラーが表示された場合、クリックしてその要素を削除する
    const errorContainer = document.getElementById('systemErrorContainer') as HTMLElement;
    errorContainer.addEventListener('click', async (event) => {
        const target = event.target as HTMLElement;
        if (target.id === 'closeSystemError') {
            errorContainer.innerHTML = '';
        }
    });

    //PDCAの星
    const weeklyStars = document.querySelectorAll<SVGElement>('.weekly-star');
    const monthlyStars = document.querySelectorAll<SVGElement>('.monthly-star');
    let rating = 0;

    const weeklyRatingInput = document.getElementById('weeklyRating') as HTMLInputElement | null;
    const monthlyRatingInput = document.getElementById('monthlyRating') as HTMLInputElement | null;

    //週間目標の星
    if(weeklyRatingInput) {
        rating =  Number(weeklyRatingInput.value);
        weeklyStars.forEach((star, index) => {
            if(index < rating) {
                star.setAttribute('fill', 'orange');
            }
        });
    }
    weeklyStars.forEach((star, index) => {
        //星をクリックしたときのイベントハンドラ
        star.addEventListener('click', () => {
            rating = index + 1;
            if (weeklyRatingInput) {
                weeklyRatingInput.value = rating.toString(); //inputの値を更新
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
        weeklyStars.forEach((star, index) => {
            if (index < rating) {
                star.setAttribute('fill', 'orange');
            } else {
                star.setAttribute('fill', 'none');
            }
        });
    }

    //星にホバーした際の表示更新
    function updateStarsOnHover(rating: number) {
        weeklyStars.forEach((star, index) => {
            if (index < rating) {
                star.setAttribute('fill', 'orange');
            } else {
                star.setAttribute('fill', 'none');
            }
        });
    }

    //月間目標の星
    if(monthlyRatingInput) {
        rating =  Number(monthlyRatingInput.value);
        monthlyStars.forEach((star, index) => {
            if(index < rating) {
                star.setAttribute('fill', 'orange');
            }
        });
    }
    monthlyStars.forEach((star, index) => {
        //星をクリックしたときのイベントハンドラ
        star.addEventListener('click', () => {
            rating = index + 1;
            if (monthlyRatingInput) {
                monthlyRatingInput.value = rating.toString(); //inputの値を更新
            }
            updateMonthlyStarsOnClick(rating);
        });

        //星にマウスを乗せたときのイベントハンドラ
        star.addEventListener('mouseover', () => {
            updateMonthlyStarsOnHover(index + 1);
        });

        //星からマウスが離れたときのイベントハンドラ
        star.addEventListener('mouseout', () => {
            updateStarsOnHover(rating);
        });
    });
    //星をクリックした際の表示更新
    function updateMonthlyStarsOnClick(rating: number) {
        monthlyStars.forEach((star, index) => {
            if (index < rating) {
                star.setAttribute('fill', 'orange');
            } else {
                star.setAttribute('fill', 'none');
            }
        });
    }

    //星にホバーした際の表示更新
    function updateMonthlyStarsOnHover(rating: number) {
        monthlyStars.forEach((star, index) => {
            if (index < rating) {
                star.setAttribute('fill', 'orange');
            } else {
                star.setAttribute('fill', 'none');
            }
        });
    }


    //タブの切り替え
    const weeklyTab = document.getElementById('weekly_tab') as HTMLElement;
    const monthlyTab = document.getElementById('monthly_tab') as HTMLElement;
    const weeklyContainer =  document.getElementById('weekly_container') as HTMLElement;
    const monthlyContainer =  document.getElementById('monthly_container') as HTMLElement;

    weeklyTab.addEventListener('click', () => {
        weeklyContainer.classList.remove('hidden');
        monthlyContainer.classList.add('hidden');
        weeklyTab.classList.remove('text-gray-500', 'hover:opacity-60');
        weeklyTab.classList.add('active');
        monthlyTab.classList.remove('active');
        monthlyTab.classList.add('text-gray-500', 'hover:opacity-60');
    });
    monthlyTab.addEventListener('click', () => {
        weeklyContainer.classList.add('hidden');
        monthlyContainer.classList.remove('hidden');
        monthlyTab.classList.remove('text-gray-500', 'hover:opacity-60');
        monthlyTab.classList.add('active');
        weeklyTab.classList.remove('active');
        weeklyTab.classList.add('text-gray-500', 'hover:opacity-60');
    });
});
