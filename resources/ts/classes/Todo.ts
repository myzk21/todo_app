// Todoクラス: Todoアイテムを表現
export class Todo {
    constructor(
        public id: number,
        public title: string,
        public description: string | null,
        public due: Date | null,
        public is_completed: boolean = false,
        public progress_rate: string | null,
        public priority: string | null,
        public label: string | null,
    ) {}
}
