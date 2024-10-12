import { TodoApp } from './components/TodoApp';

//ページが読み込まれた時にTodoAppを初期化
document.addEventListener('DOMContentLoaded', () => {
    const todoApp = new TodoApp('todo_add_btn', 'todo_create_form', 'todo_title_input', 'todo_description_input', 'percentage', 'priority', 'due');
});
