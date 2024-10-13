// Todoクラス: Todoアイテムを表現
export class Todo {
    constructor(
        public id: number,
        public title: string,
        public description: string | null,
        public due: Date | null,
        public when_completed: Date | null,
        public progress_rate: string | null,
        public priority: string | null,
        public label: string | null,
    ) {}
}
export class UpdateTodo {
    constructor(
        public id: number,
        public updateTitle: string,
        public updateDescription: string | null,
        public updateDue: Date | null,
        public when_completed: Date | null,
        public updateProgress_rate: string | null,
        public updatePriority: string | null,
        public updateLabel: string | null,
    ) {}
}
